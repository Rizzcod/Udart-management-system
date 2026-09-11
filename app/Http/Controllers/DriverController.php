<?php

namespace App\Http\Controllers;

use App\Exports\DriverActivityExport;
use App\Models\Bus;
use App\Models\DailyBusAssignment;
use App\Models\MileageLog;
use App\Models\Notification;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DriverController extends Controller
{
    public function index()
    {
        $user            = auth()->user();
        $todayAssignment = DailyBusAssignment::todayFor($user->id);

        // If no daily assignment exists, fall back to the static bus.driver_id assignment.
        // Admins sometimes assign drivers via Edit Bus rather than the Bus Assignments page,
        // which sets bus.driver_id but never creates a DailyBusAssignment record.
        $staticBusAssignment = $todayAssignment ? null : $user->assignedBus;

        $stats = [
            'total'       => WorkOrder::where('reported_by', $user->id)->count(),
            'pending'     => WorkOrder::where('reported_by', $user->id)
                                ->whereIn('status', ['reported', 'pending', 'assessment', 'pending_approval'])
                                ->count(),
            'in_progress' => WorkOrder::where('reported_by', $user->id)
                                ->whereIn('status', ['assigned', 'awaiting_parts', 'in_progress', 'testing'])
                                ->count(),
            'completed'   => WorkOrder::where('reported_by', $user->id)
                                ->whereIn('status', ['completed', 'returned_to_service'])
                                ->count(),
        ];

        $recentReports = WorkOrder::with('bus')
            ->where('reported_by', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentMileageLogs = MileageLog::with('bus')
            ->where('driver_id', $user->id)
            ->latest('trip_date')
            ->take(7)
            ->get();

        return view('driver.dashboard', compact('todayAssignment', 'staticBusAssignment', 'stats', 'recentReports', 'recentMileageLogs'));
    }

    public function myReports(Request $request)
    {
        $reports = WorkOrder::with('bus')
            ->where('reported_by', auth()->id())
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('driver.my-reports', compact('reports'));
    }

    public function reportBreakdown()
    {
        $bus  = auth()->user()->assignedBus;
        $buses = $bus ? null : Bus::whereIn('status', ['active', 'on_trip'])->orderBy('registration_number')->get();

        return view('driver.report-breakdown', compact('bus', 'buses'));
    }

    public function storeBreakdown(Request $request)
    {
        $data = $request->validate([
            'bus_id'      => 'required|exists:buses,id',
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'location'    => 'nullable|string|max:255',
            'priority'    => 'required|in:low,medium,high,critical',
        ]);

        $data['reported_by'] = auth()->id();
        $data['status']      = 'reported';

        $workOrder = WorkOrder::create($data);

        // Flag the bus immediately as breakdown_reported
        $workOrder->bus->update(['status' => 'breakdown_reported']);

        Notification::create([
            'title'   => "Breakdown Reported: {$workOrder->bus->registration_number}",
            'message' => "Driver " . auth()->user()->name . " reported a breakdown on bus {$workOrder->bus->registration_number}: {$workOrder->title}.",
            'type'    => in_array($data['priority'], ['critical', 'high']) ? 'alert' : 'warning',
        ]);

        return redirect()->route('driver.show-report', $workOrder)
            ->with('success', 'Breakdown reported. The maintenance team has been notified.');
    }

    public function showReport(WorkOrder $workOrder)
    {
        abort_if($workOrder->reported_by !== auth()->id(), 403);

        $workOrder->load(['bus', 'assignee']);

        return view('driver.show-report', compact('workOrder'));
    }

    public function startTrip()
    {
        $assignment = DailyBusAssignment::todayFor(auth()->id());

        if (! $assignment) {
            return back()->with('error', 'You have no bus assigned for today.');
        }

        $bus = $assignment->bus;

        // Allow departure from depot (active) or return from terminal (arrived)
        if ($bus->status === 'arrived') {
            $bus->update(['status' => 'on_trip']);
            return redirect()->route('driver.trip-started');
        }

        if (! $bus->isDispatchable()) {
            return back()->with('error', "Bus {$bus->registration_number} is not dispatchable ({$bus->getStatusLabel()}).");
        }

        $bus->update(['status' => 'on_trip']);

        return redirect()->route('driver.trip-started');
    }

    public function tripStarted()
    {
        $assignment = DailyBusAssignment::with(['bus', 'assigner'])
            ->where('driver_id', auth()->id())
            ->where('assigned_date', today())
            ->where('status', 'active')
            ->first();

        if (! $assignment || $assignment->bus->status !== 'on_trip') {
            return redirect()->route('driver.dashboard');
        }

        return view('driver.trip-started', [
            'assignment' => $assignment,
            'driver'     => auth()->user(),
        ]);
    }

    public function arrivedAtDestination()
    {
        $assignment = DailyBusAssignment::with(['bus'])
            ->where('driver_id', auth()->id())
            ->where('assigned_date', today())
            ->where('status', 'active')
            ->first();

        if (! $assignment || $assignment->bus->status !== 'on_trip') {
            return back()->with('error', 'No active trip to mark as arrived.');
        }

        $bus = $assignment->bus;
        $bus->update(['status' => 'arrived']);

        $routeInfo = $assignment->route ? " on route {$assignment->route}" : '';
        Notification::create([
            'title'   => "Bus Arrived: {$bus->registration_number}",
            'message' => "Driver " . auth()->user()->name . " has arrived at the terminal with bus {$bus->registration_number}{$routeInfo}. Bus is ready for return trip or end of shift.",
            'type'    => 'info',
        ]);

        return redirect()->route('driver.trip-arrived');
    }

    public function tripArrived()
    {
        $userId = auth()->id();

        $assignment = DailyBusAssignment::with(['bus', 'assigner'])
            ->where('driver_id', $userId)
            ->where('assigned_date', today())
            ->where('status', 'active')
            ->first();

        if (! $assignment || $assignment->bus->status !== 'arrived') {
            return redirect()->route('driver.dashboard');
        }

        // Surface any unread route-change notification so the driver knows their next route
        $routeNotif = Notification::where('user_id', $userId)
            ->where('title', 'Route Changed')
            ->where('is_read', false)
            ->latest()
            ->first();

        if ($routeNotif) {
            $routeNotif->update(['is_read' => true]);
        }

        return view('driver.arrived', [
            'assignment' => $assignment,
            'driver'     => auth()->user(),
            'routeNotif' => $routeNotif,
        ]);
    }

    public function endTrip()
    {
        $assignment = DailyBusAssignment::todayFor(auth()->id());

        if (! $assignment || ! in_array($assignment->bus->status, ['on_trip', 'arrived'])) {
            return back()->with('error', 'No active trip found for your assigned bus.');
        }

        $bus = $assignment->bus;

        if ($bus->status !== 'arrived') {
            $bus->update(['status' => 'arrived']);
        }

        // Auto-suggest the return route and update the assignment
        $suggestedRoute = $this->suggestReturnRoute($assignment->route);
        if ($suggestedRoute) {
            $assignment->update(['route' => $suggestedRoute]);

            Notification::create([
                'title'   => "Return Route Suggested: Bus {$bus->registration_number}",
                'message' => "Driver " . auth()->user()->name . " has ended their outbound trip. Return route auto-suggested: \"{$suggestedRoute}\". You can change it via Bus Assignments before the driver departs.",
                'type'    => 'info',
            ]);
        }

        return redirect()->route('driver.return-suggested');
    }

    public function returnSuggested()
    {
        $assignment = DailyBusAssignment::with(['bus', 'assigner'])
            ->where('driver_id', auth()->id())
            ->where('assigned_date', today())
            ->where('status', 'active')
            ->first();

        if (! $assignment || $assignment->bus->status !== 'arrived') {
            return redirect()->route('driver.dashboard');
        }

        return view('driver.return-suggested', [
            'assignment' => $assignment,
            'driver'     => auth()->user(),
        ]);
    }

    public function finishDay()
    {
        $assignment = DailyBusAssignment::todayFor(auth()->id());
        $busNumber  = $assignment?->bus->registration_number ?? '';

        // Bus stays at whichever terminal it arrived at — operations staff handle depot return
        return redirect()->route('driver.dashboard')
            ->with('success', "Great work today! Bus {$busNumber} remains at the terminal until operations staff return it to depot.");
    }

    private function suggestReturnRoute(?string $currentRoute): ?string
    {
        if (! $currentRoute) {
            return null;
        }
        $parts = explode(' → ', $currentRoute);
        if (count($parts) !== 2) {
            return null;
        }
        $reverse = trim($parts[1]) . ' → ' . trim($parts[0]);
        return in_array($reverse, config('bus_routes', [])) ? $reverse : null;
    }

    public function myActivity(Request $request)
    {
        $userId = auth()->id();

        $logs = MileageLog::with('bus')
            ->where('driver_id', $userId)
            ->when($request->from, fn($q, $f) => $q->where('trip_date', '>=', $f))
            ->when($request->to,   fn($q, $t) => $q->where('trip_date', '<=', $t))
            ->latest('trip_date')
            ->paginate(20)
            ->withQueryString();

        $totalLogs    = MileageLog::where('driver_id', $userId)->count();
        $totalKm      = MileageLog::where('driver_id', $userId)->sum('km_traveled');
        $thisMonthKm  = MileageLog::where('driver_id', $userId)
            ->whereMonth('trip_date', now()->month)->whereYear('trip_date', now()->year)
            ->sum('km_traveled');
        $breakdownCount = WorkOrder::where('reported_by', $userId)->count();

        $breakdowns = WorkOrder::with('bus')
            ->where('reported_by', $userId)
            ->latest()->take(10)->get();

        $kmByMonth = MileageLog::where('driver_id', $userId)
            ->select(DB::raw('DATE_FORMAT(trip_date, "%Y-%m") as month'), DB::raw('SUM(km_traveled) as total_km'))
            ->groupBy('month')->orderBy('month')->get();

        return view('driver.my-activity', compact(
            'logs', 'totalLogs', 'totalKm', 'thisMonthKm', 'breakdownCount', 'breakdowns', 'kmByMonth'
        ));
    }

    public function exportMyActivity(Request $request)
    {
        $userId     = auth()->id();
        $driverName = auth()->user()->name;

        $logs = MileageLog::with('bus')
            ->where('driver_id', $userId)
            ->when($request->from, fn($q, $f) => $q->where('trip_date', '>=', $f))
            ->when($request->to,   fn($q, $t) => $q->where('trip_date', '<=', $t))
            ->latest('trip_date')->get();

        $filename     = 'my-activity-' . now()->format('Y-m-d');
        $totalLogs    = $logs->count();
        $totalKm      = $logs->sum('km_traveled');
        $thisMonthKm  = MileageLog::where('driver_id', $userId)
            ->whereMonth('trip_date', now()->month)->whereYear('trip_date', now()->year)
            ->sum('km_traveled');
        $breakdownCount = WorkOrder::where('reported_by', $userId)->count();
        $fromDate     = $request->from;
        $toDate       = $request->to;

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('exports.driver-pdf', compact(
                'logs', 'driverName', 'totalLogs', 'totalKm', 'thisMonthKm', 'breakdownCount', 'fromDate', 'toDate'
            ))->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $logs->map(fn($log) => [
            $log->trip_date->format('d M Y'),
            $log->bus->registration_number,
            $log->km_traveled,
            $log->odometer_reading,
            $log->route ?? '',
            $log->notes ?? '',
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new DriverActivityExport($rows, $driverName), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new DriverActivityExport($rows, $driverName), $filename . '.xlsx');
    }
}
