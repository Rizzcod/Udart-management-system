<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\DailyBusAssignment;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BusAssignmentController extends Controller
{
    // Staff: list all assignments for a given date (default today)
    public function index(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date)->startOfDay() : today();

        $assignments = DailyBusAssignment::with(['bus', 'driver', 'assigner'])
            ->where('assigned_date', $date)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at')
            ->get();

        $assignedDriverIds = $assignments->pluck('driver_id');
        $assignedBusIds    = $assignments->pluck('bus_id');

        $unassignedDrivers = User::role('Driver')
            ->whereNotIn('id', $assignedDriverIds)
            ->orderBy('name')
            ->get();

        $availableBuses = Bus::whereIn('status', ['active', 'arrived'])
            ->where('maintenance_locked', false)
            ->whereNotIn('id', $assignedBusIds)
            ->orderBy('registration_number')
            ->get();

        $busRoutes = config('bus_routes');

        return view('bus-assignments.index', compact(
            'assignments', 'date', 'unassignedDrivers', 'availableBuses', 'busRoutes'
        ));
    }

    // Staff: manually create an assignment
    public function store(Request $request)
    {
        $busRoutes = config('bus_routes');

        $data = $request->validate([
            'driver_id'     => 'required|exists:users,id',
            'bus_id'        => 'required|exists:buses,id',
            'assigned_date' => 'required|date',
            'route'         => 'required|string|in:' . implode(',', $busRoutes),
            'notes'         => 'nullable|string|max:500',
        ]);

        $date = Carbon::parse($data['assigned_date']);

        if (DailyBusAssignment::where('driver_id', $data['driver_id'])
                ->where('assigned_date', $date)->where('status', '!=', 'cancelled')->exists()) {
            return back()->with('error', 'This driver already has an active assignment on that date.');
        }

        if (DailyBusAssignment::where('bus_id', $data['bus_id'])
                ->where('assigned_date', $date)->where('status', '!=', 'cancelled')->exists()) {
            return back()->with('error', 'This bus is already assigned to another driver on that date.');
        }

        $bus = Bus::findOrFail($data['bus_id']);
        if (! $bus->isDispatchable()) {
            return back()->with('error', "Bus {$bus->registration_number} is not currently dispatchable ({$bus->getStatusLabel()}).");
        }

        DailyBusAssignment::create([
            'bus_id'        => $data['bus_id'],
            'driver_id'     => $data['driver_id'],
            'assigned_date' => $date,
            'assigned_by'   => auth()->id(),
            'status'        => 'active',
            'route'         => $data['route'],
            'notes'         => $data['notes'] ?? null,
        ]);

        $driver = User::find($data['driver_id']);
        Notification::create([
            'user_id' => $driver->id,
            'title'   => "Bus Assignment: {$bus->registration_number}",
            'message' => "You have been assigned bus {$bus->registration_number} on route {$data['route']} for {$date->format('d M Y')} by " . auth()->user()->name . ".",
            'type'    => 'info',
        ]);

        return redirect()->route('bus-assignments.index', ['date' => $date->toDateString()])
            ->with('success', "Bus {$bus->registration_number} assigned to {$driver->name} on route {$data['route']}.");
    }

    // Supervisor: update only the route of an existing assignment
    public function updateRoute(Request $request, DailyBusAssignment $busAssignment)
    {
        $busRoutes = config('bus_routes');

        $data = $request->validate([
            'route' => 'required|string|in:' . implode(',', $busRoutes),
        ]);

        $oldRoute = $busAssignment->route ?? '(none)';
        $busAssignment->update(['route' => $data['route']]);

        Notification::create([
            'user_id' => $busAssignment->driver_id,
            'title'   => 'Route Changed',
            'message' => "Your route for {$busAssignment->assigned_date->format('d M Y')} has been changed from \"{$oldRoute}\" to \"{$data['route']}\" by " . auth()->user()->name . ".",
            'type'    => 'info',
        ]);

        return back()->with('success', "Route updated to \"{$data['route']}\".");
    }

    // Staff: cancel an assignment
    public function destroy(DailyBusAssignment $busAssignment)
    {
        $busAssignment->update(['status' => 'cancelled']);

        Notification::create([
            'user_id' => $busAssignment->driver_id,
            'title'   => 'Bus Assignment Cancelled',
            'message' => "Your assignment (bus {$busAssignment->bus->registration_number}, route {$busAssignment->route}) for {$busAssignment->assigned_date->format('d M Y')} has been cancelled by " . auth()->user()->name . ".",
            'type'    => 'warning',
        ]);

        return back()->with('success', 'Assignment cancelled.');
    }

    // Supervisor/Admin: trigger the auto-assignment command on demand
    public function autoAssign()
    {
        $exitCode = Artisan::call('buses:assign-daily', ['--force' => true]);
        $output   = trim(Artisan::output());

        if ($exitCode === 0) {
            return redirect()->route('bus-assignments.index')
                ->with('success', 'Auto-assignment complete. ' . ($output ?: 'All drivers have been assigned.'));
        }

        return redirect()->route('bus-assignments.index')
            ->with('error', 'Auto-assignment encountered an issue. ' . ($output ?: 'Check the logs.'));
    }

    // Driver: view their own today's assignment
    public function myAssignment()
    {
        $assignment = DailyBusAssignment::todayFor(auth()->id());
        $busRoutes  = config('bus_routes');

        return view('bus-assignments.my-assignment', compact('assignment', 'busRoutes'));
    }
}
