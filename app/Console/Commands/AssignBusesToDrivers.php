<?php

namespace App\Console\Commands;

use App\Models\Bus;
use App\Models\DailyBusAssignment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class AssignBusesToDrivers extends Command
{
    protected $signature   = 'buses:assign-daily {--force : Re-run even if assignments already exist for today}';
    protected $description = 'Automatically assign dispatchable buses to drivers for today';

    public function handle(): int
    {
        $today = today();

        // Already-assigned (non-cancelled) records for today
        $existingToday = DailyBusAssignment::where('assigned_date', $today)
            ->where('status', '!=', 'cancelled')
            ->get();

        if ($existingToday->isNotEmpty() && ! $this->option('force')) {
            $this->info("Assignments for {$today->toDateString()} already exist ({$existingToday->count()} total). Use --force to re-run.");
            return self::SUCCESS;
        }

        $assignedBusIds    = $existingToday->pluck('bus_id')->toArray();
        $assignedDriverIds = $existingToday->pluck('driver_id')->toArray();

        $drivers   = User::role('Driver')->get()->shuffle();
        $allRoutes = config('bus_routes');
        $cutoff    = $today->copy()->addDays(3);

        // Dispatchable buses not yet assigned today, shuffled so daily order varies
        $busPool = Bus::whereIn('status', ['active', 'arrived'])
            ->where('maintenance_locked', false)
            ->whereNotIn('id', $assignedBusIds)
            ->whereDoesntHave('preventiveMaintenances', fn($q) => $q
                ->where('status', '!=', 'completed')
                ->whereNotNull('next_service_date')
                ->whereDate('next_service_date', '<=', $cutoff)
            )
            ->orderBy('registration_number')
            ->get()
            ->values()
            ->toBase();

        if ($busPool->isEmpty()) {
            $this->warn('No dispatchable buses available for assignment today.');
            return self::SUCCESS;
        }

        $routeIndex = 0;
        $created    = 0;
        $skipped    = [];

        foreach ($drivers as $driver) {
            if (in_array($driver->id, $assignedDriverIds)) {
                $this->line("  Skipping {$driver->name} — already assigned today.");
                continue;
            }

            if ($busPool->isEmpty()) {
                $this->warn("  No bus available for {$driver->name} — skipped.");
                $skipped[] = $driver->name;
                continue;
            }

            $bus   = $busPool->shift();
            $route = $allRoutes[$routeIndex % count($allRoutes)];
            $routeIndex++;

            DailyBusAssignment::create([
                'bus_id'        => $bus->id,
                'driver_id'     => $driver->id,
                'assigned_date' => $today,
                'assigned_by'   => null,
                'status'        => 'active',
                'route'         => $route,
            ]);

            Notification::create([
                'user_id' => $driver->id,
                'title'   => "Today's Assignment — {$bus->registration_number}",
                'message' => "You are assigned bus {$bus->registration_number} on route {$route} for today, {$today->format('d M Y')}.",
                'type'    => 'info',
            ]);

            $this->info("  {$bus->registration_number} on [{$route}] → {$driver->name}");
            $created++;
        }

        if ($created > 0) {
            Notification::create([
                'user_id' => null,
                'title'   => 'Daily Bus Assignments Complete',
                'message' => "{$created} driver(s) assigned for {$today->format('d M Y')}."
                    . (count($skipped) ? ' Could not assign: ' . implode(', ', $skipped) . '.' : ''),
                'type'    => count($skipped) ? 'warning' : 'info',
            ]);
        }

        $this->info("Done: {$created} new assignment(s) for {$today->toDateString()}.");
        return self::SUCCESS;
    }
}
