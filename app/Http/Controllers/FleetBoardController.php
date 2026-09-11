<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\DailyBusAssignment;

class FleetBoardController extends Controller
{
    // Buses with PM due within this many days are held back from assignment.
    const PM_LOOKAHEAD_DAYS = 3;

    public function index()
    {
        $today   = today();
        $cutoff  = $today->copy()->addDays(self::PM_LOOKAHEAD_DAYS);

        // All active assignments for today with bus + driver
        $assignments = DailyBusAssignment::with(['bus', 'bus.preventiveMaintenances', 'driver', 'assigner'])
            ->where('assigned_date', $today)
            ->where('status', 'active')
            ->get();

        $assignedBusIds = $assignments->pluck('bus_id');

        // Split assigned buses by live status
        $onRoad     = $assignments->filter(fn($a) => $a->bus->status === 'on_trip');
        $atTerminal = $assignments->filter(fn($a) => $a->bus->status === 'arrived');
        $atDepot    = $assignments->filter(fn($a) => ! in_array($a->bus->status, ['on_trip', 'arrived']));

        $busRoutes = config('bus_routes', []);

        // Dispatchable buses not assigned today AND with no PM due within the lookahead window
        $unassigned = Bus::whereIn('status', ['active', 'arrived'])
            ->where('maintenance_locked', false)
            ->whereNotIn('id', $assignedBusIds)
            ->whereDoesntHave('preventiveMaintenances', fn($q) => $q
                ->where('status', '!=', 'completed')
                ->whereNotNull('next_service_date')
                ->whereDate('next_service_date', '<=', $cutoff)
            )
            ->orderBy('registration_number')
            ->get();

        // Dispatchable unlocked buses with PM due within the next 3 days (held back from assignment)
        $maintenanceSoon = Bus::whereIn('status', ['active', 'arrived'])
            ->where('maintenance_locked', false)
            ->with(['preventiveMaintenances' => fn($q) => $q
                ->where('status', '!=', 'completed')
                ->whereNotNull('next_service_date')
                ->whereDate('next_service_date', '<=', $cutoff)
                ->orderBy('next_service_date')
            ])
            ->whereHas('preventiveMaintenances', fn($q) => $q
                ->where('status', '!=', 'completed')
                ->whereNotNull('next_service_date')
                ->whereDate('next_service_date', '<=', $cutoff)
            )
            ->orderBy('registration_number')
            ->get();

        // Buses fully unavailable (restricted status or locked)
        $unavailable = Bus::where(fn($q) => $q
                ->whereIn('status', Bus::RESTRICTED_STATUSES)
                ->orWhere('maintenance_locked', true)
            )
            ->orderBy('registration_number')
            ->get()
            ->unique('id');

        return view('fleet-board.index', compact(
            'today', 'cutoff', 'onRoad', 'atTerminal', 'atDepot',
            'unassigned', 'maintenanceSoon', 'unavailable', 'assignments', 'busRoutes'
        ));
    }
}
