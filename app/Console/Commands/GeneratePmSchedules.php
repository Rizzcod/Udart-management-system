<?php

namespace App\Console\Commands;

use App\Models\Bus;
use App\Models\PreventiveMaintenance;
use Illuminate\Console\Command;

class GeneratePmSchedules extends Command
{
    protected $signature   = 'maintenance:generate-schedules';
    protected $description = 'Auto-generate PM schedules for all buses and advance completed cycles';

    public function handle(): void
    {
        $templates = config('pm_templates');
        $buses     = Bus::all();
        $created   = 0;
        $advanced  = 0;

        // Ensure every bus has an active schedule for every standard service type
        foreach ($buses as $bus) {
            foreach ($templates as $serviceType => $template) {
                $active = PreventiveMaintenance::where('bus_id', $bus->id)
                    ->where('service_type', $serviceType)
                    ->whereIn('status', ['upcoming', 'overdue'])
                    ->exists();

                if (! $active) {
                    $this->createSchedule($bus->id, $serviceType, $template);
                    $created++;
                }
            }
        }

        // Advance completed schedules that have no active successor
        $completed = PreventiveMaintenance::where('status', 'completed')
            ->whereNotNull('last_service_date')
            ->get();

        foreach ($completed as $pm) {
            $hasSuccessor = PreventiveMaintenance::where('bus_id', $pm->bus_id)
                ->where('service_type', $pm->service_type)
                ->whereIn('status', ['upcoming', 'overdue'])
                ->exists();

            if (! $hasSuccessor) {
                $template = $templates[$pm->service_type] ?? null;
                if ($template) {
                    $this->createSchedule(
                        $pm->bus_id,
                        $pm->service_type,
                        $template,
                        $pm->last_service_date->toDateString(),
                        $pm->last_service_km
                    );
                    $advanced++;
                }
            }
        }

        $this->info("PM schedules: {$created} created, {$advanced} advanced.");
    }

    private function createSchedule(int $busId, string $serviceType, array $template, ?string $lastServiceDate = null, ?int $lastServiceKm = null): void
    {
        $baseDate = $lastServiceDate
            ? \Carbon\Carbon::parse($lastServiceDate)
            : now();

        PreventiveMaintenance::create([
            'bus_id'            => $busId,
            'service_type'      => $serviceType,
            'interval_days'     => $template['interval_days'],
            'interval_km'       => $template['interval_km'],
            'last_service_date' => $lastServiceDate,
            'last_service_km'   => $lastServiceKm,
            'next_service_date' => $baseDate->copy()->addDays($template['interval_days'])->toDateString(),
            'status'            => 'upcoming',
        ]);
    }
}
