<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\MaintenanceRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FailurePatternService
{
    /**
     * Top N most frequent failure descriptions across all buses.
     */
    public function topFailureTypes(int $limit = 10): Collection
    {
        return MaintenanceRecord::select('description', DB::raw('COUNT(*) as occurrences'))
            ->groupBy('description')
            ->orderByDesc('occurrences')
            ->limit($limit)
            ->get();
    }

    /**
     * Buses with failure count above threshold in the last $days days.
     */
    public function highRiskBuses(int $days = 90, int $threshold = 3): Collection
    {
        $counts = DB::table('maintenance_records')
            ->select('bus_id', DB::raw('COUNT(*) as failure_count'))
            ->where('maintenance_date', '>=', now()->subDays($days))
            ->groupBy('bus_id')
            ->having('failure_count', '>=', $threshold)
            ->orderByDesc('failure_count')
            ->pluck('failure_count', 'bus_id');

        return Bus::whereIn('id', $counts->keys())
            ->get()
            ->map(function ($bus) use ($counts) {
                $bus->failure_count = $counts[$bus->id];
                return $bus;
            })
            ->sortByDesc('failure_count')
            ->values();
    }

    /**
     * Per-bus failure pattern with AI-enhanced fields:
     *  - occurrences, avg_interval_days, last_occurrence
     *  - predicted_next_date   (last + avg interval)
     *  - days_until_next       (how many days from today)
     *  - severity_score        (0-100 AI risk score per pattern)
     *  - severity_label        (critical / high / medium / low)
     */
    public function busFailurePattern(Bus $bus): Collection
    {
        $records = $bus->maintenanceRecords()
            ->orderBy('maintenance_date')
            ->get(['description', 'maintenance_date']);

        return $records->groupBy('description')->map(function ($group, $description) {
            $dates     = $group->pluck('maintenance_date')->sort()->values();
            $intervals = [];
            for ($i = 1; $i < $dates->count(); $i++) {
                $intervals[] = $dates[$i - 1]->diffInDays($dates[$i]);
            }

            $occurrences      = $group->count();
            $avgInterval      = count($intervals) ? round(array_sum($intervals) / count($intervals)) : null;
            $lastOccurrence   = $dates->last();
            $lastOccurrenceStr = $lastOccurrence?->format('Y-m-d');

            // Predicted next occurrence
            $predictedNextDate = null;
            $daysUntilNext     = null;
            if ($avgInterval && $lastOccurrence) {
                $predictedNextDate = $lastOccurrence->copy()->addDays($avgInterval)->format('Y-m-d');
                $daysUntilNext     = now()->diffInDays($lastOccurrence->copy()->addDays($avgInterval), false);
                $daysUntilNext     = (int) round($daysUntilNext);
            }

            // AI severity scoring (0-100)
            $score = $this->computeSeverityScore($occurrences, $avgInterval, $lastOccurrence);
            $severityLabel = $this->severityLabel($score);

            return [
                'description'        => $description,
                'occurrences'        => $occurrences,
                'avg_interval_days'  => $avgInterval,
                'last_occurrence'    => $lastOccurrenceStr,
                'predicted_next_date' => $predictedNextDate,
                'days_until_next'    => $daysUntilNext,
                'severity_score'     => $score,
                'severity_label'     => $severityLabel,
            ];
        })->sortByDesc('severity_score')->values();
    }

    /**
     * Root cause analysis: correlate high sensor readings with nearby maintenance records.
     * For each maintenance event, check if any sensor reading within ±3 days was Warning or Critical.
     */
    public function rootCauseAnalysis(Bus $bus): Collection
    {
        $records = $bus->maintenanceRecords()
            ->orderByDesc('maintenance_date')
            ->limit(20)
            ->get(['id', 'description', 'maintenance_date', 'cost']);

        return $records->map(function ($record) use ($bus) {
            $windowStart = $record->maintenance_date->copy()->subDays(3);
            $windowEnd   = $record->maintenance_date->copy()->addDays(1);

            $sensorAlerts = $bus->sensorReadings()
                ->whereBetween('recorded_at', [$windowStart, $windowEnd])
                ->get()
                ->filter(fn($s) => $s->getHealthStatus() !== 'normal')
                ->map(fn($s) => [
                    'recorded_at' => $s->recorded_at->format('Y-m-d H:i'),
                    'status'      => $s->getHealthStatus(),
                    'alerts'      => $s->getSensorAlerts(),
                ])
                ->values();

            return [
                'description'   => $record->description,
                'date'          => $record->maintenance_date->format('Y-m-d'),
                'cost'          => $record->cost,
                'sensor_alerts' => $sensorAlerts,
                'has_sensor_link' => $sensorAlerts->isNotEmpty(),
            ];
        });
    }

    /**
     * Monthly maintenance count for the last 12 months (for chart).
     */
    public function monthlyTrend(): Collection
    {
        return MaintenanceRecord::selectRaw("DATE_FORMAT(maintenance_date, '%Y-%m') as month, COUNT(*) as count")
            ->where('maintenance_date', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Fleet-wide AI summary: total patterns, recurring rate, most dangerous failure type.
     */
    public function fleetAiSummary(): array
    {
        $total      = MaintenanceRecord::count();
        $recurring  = MaintenanceRecord::select('description')
            ->groupBy('description')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $topDangerous = MaintenanceRecord::select('description', DB::raw('COUNT(*) as cnt'))
            ->groupBy('description')
            ->orderByDesc('cnt')
            ->first();

        $recentSurge = MaintenanceRecord::where('maintenance_date', '>=', now()->subDays(30))->count();

        return [
            'total_maintenance_events' => $total,
            'recurring_failure_types'  => $recurring,
            'top_failure'              => $topDangerous?->description ?? '—',
            'top_failure_count'        => $topDangerous?->cnt ?? 0,
            'events_last_30_days'      => $recentSurge,
        ];
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    /**
     * Severity scoring (0-100) per failure pattern based on:
     *  - frequency (more occurrences → higher score)
     *  - recency (recent last occurrence → higher score)
     *  - short interval (failures repeating quickly → higher score)
     */
    private function computeSeverityScore(int $occurrences, ?int $avgInterval, $lastOccurrence): int
    {
        $score = 0;

        // Frequency component (max 40)
        $score += min($occurrences * 8, 40);

        // Recency component (max 30): more points the more recently it last happened
        if ($lastOccurrence) {
            $daysSince = now()->diffInDays($lastOccurrence);
            if ($daysSince <= 7)        $score += 30;
            elseif ($daysSince <= 30)   $score += 22;
            elseif ($daysSince <= 90)   $score += 12;
            else                         $score += 4;
        }

        // Short-interval component (max 30): failures that repeat quickly are most dangerous
        if ($avgInterval !== null) {
            if ($avgInterval <= 14)      $score += 30;
            elseif ($avgInterval <= 30)  $score += 22;
            elseif ($avgInterval <= 60)  $score += 12;
            elseif ($avgInterval <= 90)  $score += 6;
        }

        return min($score, 100);
    }

    private function severityLabel(int $score): string
    {
        if ($score >= 70) return 'critical';
        if ($score >= 45) return 'high';
        if ($score >= 20) return 'medium';
        return 'low';
    }
}
