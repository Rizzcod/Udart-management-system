<?php

namespace App\Console\Commands;

use App\Models\Bus;
use App\Models\DailyBusAssignment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EndOfService extends Command
{
    protected $signature   = 'buses:end-service {--date= : Service date to close (default: today, YYYY-MM-DD)}';
    protected $description = 'End of daily service at 1 AM: return all operational buses to depot and close assignments';

    // These statuses reflect a real problem — do NOT auto-reset them
    private const PROBLEM_STATUSES = [
        'maintenance_due',
        'under_preventive_maintenance',
        'breakdown_reported',
        'under_repair',
        'awaiting_spare_parts',
        'out_of_service',
        'inactive',
    ];

    public function handle(): int
    {
        // If a specific date is passed, target only that date's assignments.
        // Otherwise close ALL stale active assignments from any past day —
        // the previous implementation used today() here which meant assignments
        // from yesterday were never closed (they were assigned_date = yesterday
        // but the query looked for assigned_date = today).
        if ($this->option('date')) {
            $targetDate = Carbon::parse($this->option('date'))->toDateString();
            $label      = Carbon::parse($targetDate)->format('l, d M Y');
            $dateClause = fn($q) => $q->where('assigned_date', $targetDate);
        } else {
            $label      = today()->subDay()->format('l, d M Y');
            // Close every assignment from any day strictly before today
            $dateClause = fn($q) => $q->where('assigned_date', '<', today()->toDateString());
        }

        $this->info("=== End of Service: {$label} ===");

        // 1. Return every bus that is still on a trip or at a terminal back to depot.
        //    This is intentionally NOT filtered by date — any bus stuck on on_trip
        //    at midnight is stale regardless of which day the assignment was made.
        $operational = Bus::whereIn('status', ['on_trip', 'arrived'])->get();
        $returned    = 0;

        foreach ($operational as $bus) {
            $bus->update(['status' => 'active']);
            $this->line("  Returned to depot: {$bus->registration_number}");
            $returned++;
        }

        // 2. Mark every stale active assignment as auto_completed so we can
        //    distinguish system-ended trips from driver-ended ones in reports.
        $closed = DailyBusAssignment::where('status', 'active')
            ->when(true, $dateClause)
            ->update(['status' => 'auto_completed']);

        $this->info("  {$returned} bus(es) returned to depot.");
        $this->info("  {$closed} stale assignment(s) auto-closed.");

        // 3. Report buses still on a problem status that need attention
        $stuck = Bus::whereIn('status', self::PROBLEM_STATUSES)->get();

        if ($stuck->isNotEmpty()) {
            $this->newLine();
            $this->warn("  Buses still requiring attention ({$stuck->count()}):");
            foreach ($stuck as $bus) {
                $this->warn("    - {$bus->registration_number}: {$bus->getStatusLabel()}");
            }
        }

        // 4. System-wide notification for supervisors / admins
        $stuckText = $stuck->isNotEmpty()
            ? ' Buses still requiring attention: '
              . $stuck->map(fn($b) => "{$b->registration_number} ({$b->getStatusLabel()})")->join(', ') . '.'
            : '';

        if ($returned > 0 || $closed > 0) {
            Notification::create([
                'user_id' => null,
                'title'   => 'End of Service — All Buses at Depot',
                'message' => "Daily service for {$label} has ended. "
                    . "{$returned} bus(es) returned to depot and {$closed} trip(s) auto-closed at midnight. "
                    . "Buses will be ready for assignment from 05:00 AM.{$stuckText}",
                'type'    => $stuck->isNotEmpty() ? 'warning' : 'info',
            ]);
        }

        $this->newLine();
        $this->info('End of service complete. Buses are at depot and ready for tomorrow.');

        return self::SUCCESS;
    }
}
