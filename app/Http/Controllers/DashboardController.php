<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\DailyBusAssignment;
use App\Models\MaintenanceRecord;
use App\Models\Notification;
use App\Models\PreventiveMaintenance;
use App\Models\SparePart;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Technician')) {
            return $this->technicianDashboard($user);
        }

        if ($user->hasRole('Storekeeper')) {
            return $this->storekeeperDashboard($user);
        }

        return $this->mainDashboard();
    }

    // Silently reset buses that are stuck on 'on_trip' or 'arrived' from a previous day.
    // Runs on every admin/supervisor dashboard load as a safety-net for nights when
    // the scheduler was not running (Windows dev environments, server restarts, etc.).
    private function autoEndStaleTrips(): void
    {
        // A bus is stale if it's still on_trip/arrived but has NO active assignment for today
        $stale = Bus::whereIn('status', ['on_trip', 'arrived'])
            ->whereDoesntHave('dailyAssignments', fn($q) =>
                $q->where('assigned_date', today()->toDateString())
                  ->where('status', 'active')
            )
            ->get();

        if ($stale->isEmpty()) {
            return;
        }

        foreach ($stale as $bus) {
            $bus->update(['status' => 'active']);
        }

        // Close any stale active assignments from past days
        DailyBusAssignment::where('status', 'active')
            ->where('assigned_date', '<', today()->toDateString())
            ->update(['status' => 'auto_completed']);

        Notification::create([
            'user_id' => null,
            'title'   => 'Stale Trips Auto-Ended',
            'message' => $stale->count() . ' bus(es) were still marked as on-trip from a previous day and have been automatically returned to depot: '
                . $stale->map(fn($b) => $b->registration_number)->join(', ') . '.',
            'type'    => 'warning',
        ]);
    }

    // Admin / Supervisor — full fleet overview
    private function mainDashboard()
    {
        $this->autoEndStaleTrips();
        $stats = [
            'total_buses'          => Bus::count(),
            'buses_on_trip'        => Bus::where('status', 'on_trip')->count(),
            'buses_at_terminal'    => Bus::where('status', 'arrived')->count(),
            'buses_at_depot'       => Bus::where('status', 'active')->count(),
            'under_repair'         => Bus::where('status', 'under_repair')->count(),
            'open_work_orders'     => WorkOrder::whereNotIn('status', ['completed', 'cancelled'])->count(),
            'low_stock_parts'      => SparePart::whereColumn('quantity', '<=', 'minimum_stock')->count(),
            'overdue_pm'           => PreventiveMaintenance::where('status', 'overdue')->count(),
            'unread_notifications' => Notification::where('is_read', false)->count(),
        ];

        // Today's assigned buses with live status for the Live Operations panel
        $liveAssignments = DailyBusAssignment::with(['bus', 'driver'])
            ->where('assigned_date', today())
            ->where('status', 'active')
            ->get()
            ->sortBy(fn($a) => match ($a->bus->status) {
                'on_trip'  => 0,
                'arrived'  => 1,
                default    => 2,
            });

        $recentWorkOrders = WorkOrder::with(['bus', 'assignee'])
            ->latest()
            ->take(5)
            ->get();

        $workOrdersByStatus = WorkOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $monthlyRecords = DB::table('maintenance_records')
            ->select(DB::raw('DATE_FORMAT(maintenance_date, "%Y-%m") as month'), DB::raw('COUNT(*) as total'))
            ->whereYear('maintenance_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $upcomingPM = PreventiveMaintenance::with('bus')
            ->whereIn('status', ['upcoming', 'overdue'])
            ->orderBy('next_service_date')
            ->take(5)
            ->get();

        $lowStockParts = SparePart::whereColumn('quantity', '<=', 'minimum_stock')
            ->orderBy('quantity')
            ->take(5)
            ->get();

        return view('dashboard', array_merge(compact(
            'stats', 'liveAssignments', 'recentWorkOrders', 'workOrdersByStatus',
            'monthlyRecords', 'upcomingPM', 'lowStockParts'
        ), ['dashboardRole' => 'admin']));
    }

    // Technician — task-focused view
    private function technicianDashboard($user)
    {
        $myWorkOrders = WorkOrder::with('bus')
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled', 'returned_to_service'])
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->get();

        $stats = [
            'my_open_wos'     => $myWorkOrders->count(),
            'completed_month' => WorkOrder::where('assigned_to', $user->id)
                ->whereIn('status', ['completed', 'returned_to_service'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
            'overdue_pm'      => PreventiveMaintenance::where('status', 'overdue')->count(),
            'my_records_month' => MaintenanceRecord::where('technician_id', $user->id)
                ->whereMonth('maintenance_date', now()->month)
                ->whereYear('maintenance_date', now()->year)
                ->count(),
        ];

        $upcomingPM = PreventiveMaintenance::with('bus')
            ->whereIn('status', ['upcoming', 'overdue'])
            ->orderBy('next_service_date')
            ->take(5)
            ->get();

        $recentRecords = MaintenanceRecord::with('bus')
            ->where('technician_id', $user->id)
            ->latest('maintenance_date')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'myWorkOrders', 'upcomingPM', 'recentRecords'))
            ->with('dashboardRole', 'technician');
    }

    // Storekeeper — inventory-focused view
    private function storekeeperDashboard($user)
    {
        $stats = [
            'total_parts'     => SparePart::count(),
            'low_stock_parts' => SparePart::whereColumn('quantity', '<=', 'minimum_stock')->count(),
            'out_of_stock'    => SparePart::where('quantity', 0)->count(),
            'awaiting_parts'  => WorkOrder::where('status', 'awaiting_parts')->count(),
        ];

        $lowStockParts = SparePart::whereColumn('quantity', '<=', 'minimum_stock')
            ->orderBy('quantity')
            ->get();

        $workOrdersNeedingParts = WorkOrder::with('bus')
            ->where('status', 'awaiting_parts')
            ->latest()
            ->take(8)
            ->get();

        $recentUsage = DB::table('spare_part_usages')
            ->join('spare_parts', 'spare_parts.id', '=', 'spare_part_usages.spare_part_id')
            ->join('work_orders', 'work_orders.id', '=', 'spare_part_usages.work_order_id')
            ->select(
                'spare_parts.part_name',
                'spare_parts.part_number',
                DB::raw('SUM(spare_part_usages.quantity_used) as total_used')
            )
            ->whereMonth('spare_part_usages.created_at', now()->month)
            ->whereYear('spare_part_usages.created_at', now()->year)
            ->groupBy('spare_parts.id', 'spare_parts.part_name', 'spare_parts.part_number')
            ->orderByDesc('total_used')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'lowStockParts', 'workOrdersNeedingParts', 'recentUsage'))
            ->with('dashboardRole', 'storekeeper');
    }
}
