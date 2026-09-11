<?php

namespace App\Console\Commands;

use App\Models\Bus;
use App\Models\Notification;
use Illuminate\Console\Command;

class CheckPmThresholds extends Command
{
    protected $signature   = 'pm:check-thresholds';
    protected $description = 'Check all buses against PM km intervals and flag any that are overdue';

    public function handle(): void
    {
        $buses = Bus::with('preventiveMaintenances')->get();
        $flagged = 0;

        foreach ($buses as $bus) {
            $anyNewOverdue = false;

            foreach ($bus->preventiveMaintenances->where('status', 'upcoming') as $pm) {
                if (! $pm->interval_km) {
                    continue;
                }
                $kmSince = $bus->mileage - ($pm->last_service_km ?? 0);
                if ($kmSince >= $pm->interval_km) {
                    $pm->update(['status' => 'overdue']);
                    $anyNewOverdue = true;
                }
            }

            $hasOverdue = $bus->preventiveMaintenances()->where('status', 'overdue')->exists();

            if ($hasOverdue && ! in_array($bus->status, [
                'breakdown_reported', 'under_repair', 'awaiting_spare_parts',
                'under_preventive_maintenance', 'out_of_service',
            ])) {
                if ($bus->status !== 'maintenance_due') {
                    $bus->update(['status' => 'maintenance_due', 'maintenance_locked' => true]);
                    $flagged++;

                    Notification::create([
                        'title'   => "PM Due: Bus {$bus->registration_number}",
                        'message' => "Bus {$bus->registration_number} has reached its preventive maintenance interval. It has been restricted from dispatch until serviced.",
                        'type'    => 'warning',
                    ]);
                }
            }
        }

        $this->info("PM threshold check complete. Buses flagged: {$flagged}.");
    }
}
