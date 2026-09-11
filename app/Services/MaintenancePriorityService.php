<?php

namespace App\Services;

use App\Models\Bus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MaintenancePriorityService
{
    /**
     * Composite priority score for a bus (0–120).
     *
     * Components:
     *   Age factor        max 40 pts — 4 pts/year, capped at 10 years (≥10 yr = full 40)
     *   Breakdown factor  max 40 pts — 8 pts per work-order in last 2 years (not cancelled), capped at 5 WOs
     *   Maint-frequency   max 20 pts — 5 pts per maintenance record in last 12 months, capped at 4 records
     *   Mileage factor    max 20 pts — 1 pt per 6 250 km, capped at 125 000 km
     */
    public function scoreForBus(Bus $bus): array
    {
        $year = (int) Carbon::now()->year;

        $busAge   = max(0, $year - (int) $bus->year);
        $agePts   = min($busAge * 4, 40);

        $woCount  = $bus->workOrders()
            ->where('created_at', '>=', Carbon::now()->subYears(2))
            ->where('status', '!=', 'cancelled')
            ->count();
        $breakPts = min($woCount * 8, 40);

        $mrCount  = $bus->maintenanceRecords()
            ->where('maintenance_date', '>=', Carbon::now()->subYear()->toDateString())
            ->count();
        $mrPts    = min($mrCount * 5, 20);

        $mileagePts = min((int) floor($bus->mileage / 6250), 20);

        return [
            'score'         => $agePts + $breakPts + $mrPts + $mileagePts,
            'age_pts'       => $agePts,
            'breakdown_pts' => $breakPts,
            'mr_pts'        => $mrPts,
            'mileage_pts'   => $mileagePts,
            'bus_age'       => $busAge,
            'wo_count'      => $woCount,
            'mr_count'      => $mrCount,
        ];
    }

    public function priorityLevel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'critical',
            $score >= 50 => 'high',
            $score >= 25 => 'medium',
            default      => 'low',
        };
    }

    /**
     * Fraction of the standard interval to apply (lower = more frequent).
     *   critical → 50%   high → 65%   medium → 80%   low → 100%
     */
    public function intervalMultiplier(string $level): float
    {
        return match ($level) {
            'critical' => 0.50,
            'high'     => 0.65,
            'medium'   => 0.80,
            default    => 1.00,
        };
    }

    public function adjustedIntervalDays(Bus $bus, int $standardDays): int
    {
        $sd    = $this->scoreForBus($bus);
        $level = $this->priorityLevel($sd['score']);
        return max(7, (int) round($standardDays * $this->intervalMultiplier($level)));
    }

    /** Human-readable reasons explaining the priority classification. */
    public function getInsights(Bus $bus, array $sd): array
    {
        $insights = [];

        if ($sd['bus_age'] >= 8) {
            $insights[] = "Vehicle is {$sd['bus_age']} years old — aging fleet requires more frequent checks.";
        } elseif ($sd['bus_age'] >= 5) {
            $insights[] = "Vehicle is {$sd['bus_age']} years old — mid-life fleet needs closer monitoring.";
        }

        if ($sd['wo_count'] >= 3) {
            $insights[] = "{$sd['wo_count']} work orders in the last 2 years — frequent breakdowns shorten recommended intervals.";
        } elseif ($sd['wo_count'] >= 1) {
            $insights[] = "{$sd['wo_count']} work order(s) in the last 2 years — breakdown history considered.";
        }

        if ($sd['mr_count'] >= 3) {
            $insights[] = "{$sd['mr_count']} maintenance records this year — high-frequency history detected.";
        } elseif ($sd['mr_count'] >= 1) {
            $insights[] = "{$sd['mr_count']} maintenance record(s) in the past year.";
        }

        if ($bus->mileage >= 100000) {
            $insights[] = number_format($bus->mileage) . " km on odometer — high-mileage vehicle.";
        } elseif ($bus->mileage >= 60000) {
            $insights[] = number_format($bus->mileage) . " km on the odometer.";
        }

        if (empty($insights)) {
            $insights[] = "New or low-usage vehicle — standard maintenance intervals apply.";
        }

        return $insights;
    }

    /**
     * Build a bus_id → priority profile map.
     * Pass an eager-loaded Bus collection to avoid N+1 from calling this in a loop.
     */
    public function buildPriorityMap(Collection $buses): array
    {
        $map = [];
        foreach ($buses as $bus) {
            $sd            = $this->scoreForBus($bus);
            $level         = $this->priorityLevel($sd['score']);
            $map[$bus->id] = [
                'score'      => $sd['score'],
                'level'      => $level,
                'multiplier' => $this->intervalMultiplier($level),
                'insights'   => $this->getInsights($bus, $sd),
                'score_data' => $sd,
            ];
        }
        return $map;
    }
}
