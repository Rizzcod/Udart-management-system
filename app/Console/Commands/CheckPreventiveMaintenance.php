<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\PreventiveMaintenance;
use Illuminate\Console\Command;

class CheckPreventiveMaintenance extends Command
{
    protected $signature   = 'maintenance:check-due';
    protected $description = 'Mark overdue preventive maintenance schedules and create notifications';

    public function handle(): void
    {
        $overdue = PreventiveMaintenance::with('bus')
            ->where('status', '!=', 'completed')
            ->where('next_service_date', '<', now()->toDateString())
            ->get();

        foreach ($overdue as $pm) {
            $pm->update(['status' => 'overdue']);

            Notification::firstOrCreate(
                [
                    'title'   => "PM Overdue: {$pm->bus->registration_number}",
                    'type'    => 'alert',
                    'is_read' => false,
                ],
                [
                    'message' => "{$pm->service_type} for bus {$pm->bus->registration_number} was due on {$pm->next_service_date->format('d M Y')}.",
                ]
            );
        }

        $this->info("Checked preventive maintenance: {$overdue->count()} overdue schedule(s) updated.");
    }
}
