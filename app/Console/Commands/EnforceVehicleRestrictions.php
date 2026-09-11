<?php

namespace App\Console\Commands;

use App\Models\Bus;
use App\Models\Notification;
use App\Models\PreventiveMaintenance;
use Illuminate\Console\Command;

class EnforceVehicleRestrictions extends Command
{
    protected $signature   = 'maintenance:enforce-restrictions';
    protected $description = 'Lock buses with overdue PM, send advance warnings, and unlock buses once maintenance is cleared';

    public function handle(): void
    {
        $locked   = $this->lockOverdueBuses();
        $unlocked = $this->unlockClearedBuses();
        $this->sendAdvanceWarnings();

        $this->info("Vehicle restrictions enforced: {$locked} locked, {$unlocked} unlocked.");
    }

    private function lockOverdueBuses(): int
    {
        $count = 0;

        Bus::whereHas('preventiveMaintenances', fn($q) => $q->where('status', 'overdue'))
            ->where('maintenance_locked', false)
            ->each(function (Bus $bus) use (&$count) {
                $bus->lockForMaintenance();

                $overdueTypes = $bus->preventiveMaintenances()
                    ->where('status', 'overdue')
                    ->pluck('service_type')
                    ->implode(', ');

                Notification::firstOrCreate(
                    [
                        'title'   => "Vehicle Restricted: {$bus->registration_number}",
                        'type'    => 'alert',
                        'is_read' => false,
                    ],
                    [
                        'message' => "Bus {$bus->registration_number} has been automatically placed Out of Service. "
                            . "This vehicle is not allowed to operate until the required maintenance has been completed and approved. "
                            . "Overdue: {$overdueTypes}.",
                    ]
                );

                $count++;
            });

        return $count;
    }

    private function unlockClearedBuses(): int
    {
        $count = 0;

        Bus::where('maintenance_locked', true)
            ->whereDoesntHave('preventiveMaintenances', fn($q) => $q->where('status', 'overdue'))
            ->each(function (Bus $bus) use (&$count) {
                $bus->unlockFromMaintenance();

                Notification::create([
                    'title'   => "Vehicle Cleared for Operations: {$bus->registration_number}",
                    'message' => "Bus {$bus->registration_number} has been restored to Active status. "
                        . "All overdue preventive maintenance has been completed and the vehicle is cleared to operate.",
                    'type'    => 'info',
                ]);

                $count++;
            });

        return $count;
    }

    private function sendAdvanceWarnings(): void
    {
        // 7-day advance notice
        PreventiveMaintenance::with('bus')
            ->where('status', 'upcoming')
            ->whereDate('next_service_date', now()->addDays(7)->toDateString())
            ->each(function (PreventiveMaintenance $pm) {
                Notification::firstOrCreate(
                    [
                        'title'   => "PM Due in 7 Days: {$pm->bus->registration_number}",
                        'type'    => 'warning',
                        'is_read' => false,
                    ],
                    [
                        'message' => "{$pm->service_type} for bus {$pm->bus->registration_number} is due on "
                            . "{$pm->next_service_date->format('d M Y')}. "
                            . "Schedule maintenance soon to avoid vehicle restriction.",
                    ]
                );
            });

        // 3-day urgent notice
        PreventiveMaintenance::with('bus')
            ->where('status', 'upcoming')
            ->whereDate('next_service_date', now()->addDays(3)->toDateString())
            ->each(function (PreventiveMaintenance $pm) {
                Notification::firstOrCreate(
                    [
                        'title'   => "URGENT — PM Due in 3 Days: {$pm->bus->registration_number}",
                        'type'    => 'alert',
                        'is_read' => false,
                    ],
                    [
                        'message' => "URGENT: {$pm->service_type} for bus {$pm->bus->registration_number} is critically due on "
                            . "{$pm->next_service_date->format('d M Y')}. "
                            . "Failure to complete this service will automatically restrict the vehicle from operations.",
                    ]
                );
            });
    }
}
