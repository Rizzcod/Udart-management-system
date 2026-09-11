<?php

namespace App\Http\Controllers;

use App\Exports\DriverActivityExport;
use App\Exports\FleetReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\MaintenanceReportExport;
use App\Exports\PmReportExport;
use App\Exports\TechnicianReportExport;
use App\Exports\UsersReportExport;
use App\Exports\WorkOrderReportExport;
use App\Models\MileageLog;
use App\Models\Bus;
use App\Models\MaintenanceRecord;
use App\Models\PreventiveMaintenance;
use App\Models\SparePart;
use App\Models\SparePartUsage;
use App\Models\User;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function maintenance(Request $request)
    {
        $query = MaintenanceRecord::with(['bus', 'technician'])
            ->when($request->bus_id, fn($q, $b) => $q->where('bus_id', $b))
            ->when($request->from, fn($q, $f) => $q->where('maintenance_date', '>=', $f))
            ->when($request->to, fn($q, $t) => $q->where('maintenance_date', '<=', $t))
            ->latest('maintenance_date');

        $records = $query->paginate(20)->withQueryString();

        $totalRecords = $query->count();

        $recordsByMonth = MaintenanceRecord::select(
                DB::raw('DATE_FORMAT(maintenance_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->when($request->bus_id, fn($q, $b) => $q->where('bus_id', $b))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $recordsByBusRaw = MaintenanceRecord::select('bus_id', DB::raw('COUNT(*) as total'))
            ->with('bus:id,registration_number')
            ->when($request->bus_id, fn($q, $b) => $q->where('bus_id', $b))
            ->groupBy('bus_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $recordsByBus = $recordsByBusRaw->map(fn($r) => [
            'label' => $r->bus?->registration_number ?? 'Unknown',
            'total' => (int) $r->total,
        ])->values();

        $buses = Bus::orderBy('registration_number')->get();

        return view('reports.maintenance', compact(
            'records', 'totalRecords', 'recordsByMonth', 'recordsByBus', 'buses'
        ));
    }

    public function inventory(Request $request)
    {
        $parts = SparePart::withCount('usages')
            ->withSum('usages', 'quantity_used')
            ->orderBy('part_name')
            ->get();

        $lowStock   = $parts->filter(fn($p) => $p->isLowStock());
        $topUsed    = $parts->sortByDesc('usages_sum_quantity_used')->take(5);
        $totalValue = $parts->sum(fn($p) => $p->quantity * $p->unit_price);

        // Minimal chart-safe data
        $topUsedChart = $topUsed->map(fn($p) => [
            'label'    => $p->part_name,
            'quantity' => (int) ($p->usages_sum_quantity_used ?? 0),
        ])->values();

        $valueChart = $parts->sortByDesc(fn($p) => $p->quantity * $p->unit_price)->take(10)->map(fn($p) => [
            'label' => $p->part_name,
            'value' => round($p->quantity * $p->unit_price, 2),
        ])->values();

        return view('reports.inventory', compact('parts', 'lowStock', 'topUsed', 'topUsedChart', 'valueChart', 'totalValue'));
    }

    // ── Exports ──────────────────────────────────────────────────────────────

    public function exportMaintenance(Request $request)
    {
        $records = MaintenanceRecord::with(['bus', 'technician'])
            ->when($request->bus_id, fn($q, $b) => $q->where('bus_id', $b))
            ->when($request->from,   fn($q, $f) => $q->where('maintenance_date', '>=', $f))
            ->when($request->to,     fn($q, $t) => $q->where('maintenance_date', '<=', $t))
            ->latest('maintenance_date')
            ->get();

        $filename = 'maintenance-report-' . now()->format('Y-m-d');

        if ($request->format === 'pdf') {
            $totalRecords = $records->count();
            $filters = [
                'bus'  => $request->bus_id ? Bus::find($request->bus_id)?->registration_number : null,
                'from' => $request->from,
                'to'   => $request->to,
            ];
            $pdf = Pdf::loadView('exports.maintenance-pdf', compact('records', 'totalRecords', 'filters'))
                      ->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $records->map(fn($r) => [
            $r->maintenance_date->format('d M Y'),
            $r->bus->registration_number,
            $r->description,
            $r->technician->name,
            $r->parts_replaced ?? '',
            $r->notes ?? '',
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new MaintenanceReportExport($rows), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new MaintenanceReportExport($rows), $filename . '.xlsx');
    }

    public function exportInventory(Request $request)
    {
        $parts = SparePart::withCount('usages')
            ->withSum('usages', 'quantity_used')
            ->orderBy('part_name')
            ->get();

        $filename = 'inventory-report-' . now()->format('Y-m-d');

        if ($request->format === 'pdf') {
            $lowStock   = $parts->filter(fn($p) => $p->isLowStock());
            $totalValue = $parts->sum(fn($p) => $p->quantity * $p->unit_price);
            $pdf = Pdf::loadView('exports.inventory-pdf', compact('parts', 'lowStock', 'totalValue'))
                      ->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $parts->map(fn($p) => [
            $p->part_name,
            $p->part_number,
            $p->category ?? '',
            $p->quantity,
            $p->minimum_stock,
            $p->isLowStock() ? 'Low Stock' : 'OK',
            $p->usages_count ?? 0,
            (int) ($p->usages_sum_quantity_used ?? 0),
            $p->unit_price,
            round($p->quantity * $p->unit_price, 2),
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new InventoryReportExport($rows), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new InventoryReportExport($rows), $filename . '.xlsx');
    }

    public function exportFleet(Request $request)
    {
        $buses = Bus::withCount(['workOrders', 'maintenanceRecords'])
            ->with(['workOrders' => fn($q) => $q->where('status', 'completed')])
            ->get();

        $fleetStats = $buses->map(function ($bus) {
            $completed = $bus->workOrders->filter(fn($w) => $w->status === 'completed');
            $totalRepairDays = $completed->sum(function ($wo) {
                if ($wo->start_date && $wo->completion_date) {
                    return $wo->start_date->diffInDays($wo->completion_date) ?: 1;
                }
                return 1;
            });
            return [
                'bus'           => $bus,
                'total_wo'      => $bus->work_orders_count,
                'completed_wo'  => $completed->count(),
                'total_records' => $bus->maintenance_records_count,
                'mttr'          => $completed->count() > 0 ? round($totalRepairDays / $completed->count(), 1) : 0,
            ];
        });

        $filename = 'fleet-report-' . now()->format('Y-m-d');

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('exports.fleet-pdf', compact('buses', 'fleetStats'))
                      ->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $fleetStats->map(fn($s) => [
            $s['bus']->registration_number,
            $s['bus']->model,
            $s['bus']->year,
            $s['bus']->manufacturer,
            $s['bus']->getStatusLabel(),
            $s['bus']->mileage,
            $s['total_wo'],
            $s['completed_wo'],
            $s['total_records'],
            $s['mttr'] ?: 0,
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new FleetReportExport($rows), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new FleetReportExport($rows), $filename . '.xlsx');
    }

    public function users()
    {
        $totalUsers   = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $pendingUsers  = User::whereNull('email_verified_at')->whereNotNull('otp')->where('otp_expires_at', '>', now())->count();
        $expiredUsers  = User::whereNull('email_verified_at')->where(fn($q) => $q->whereNull('otp')->orWhere('otp_expires_at', '<=', now()))->count();

        $roles = Role::withCount('users')->orderBy('name')->get();

        $usersByRole = $roles->map(fn($r) => [
            'label' => $r->name,
            'count' => (int) $r->users_count,
        ])->values();

        $recentUsers = User::with('roles')->latest()->take(10)->get();

        $lastLoginStats = [
            'today'   => User::whereDate('last_login_at', today())->count(),
            'week'    => User::where('last_login_at', '>=', now()->subDays(7))->count(),
            'month'   => User::where('last_login_at', '>=', now()->subDays(30))->count(),
            'never'   => User::whereNull('last_login_at')->count(),
        ];

        return view('reports.users', compact(
            'totalUsers', 'verifiedUsers', 'pendingUsers', 'expiredUsers',
            'roles', 'usersByRole', 'recentUsers', 'lastLoginStats'
        ));
    }

    public function workOrders(Request $request)
    {
        $query = WorkOrder::with(['bus', 'reporter', 'assignee'])
            ->when($request->status,   fn($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->when($request->from,     fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->to,       fn($q, $t) => $q->whereDate('created_at', '<=', $t))
            ->latest();

        $workOrders   = $query->paginate(20)->withQueryString();
        $totalWO      = WorkOrder::count();
        $openWO       = WorkOrder::whereNotIn('status', ['completed', 'returned_to_service'])->count();
        $completedWO  = WorkOrder::whereIn('status', ['completed', 'returned_to_service'])->count();
        $highPriority = WorkOrder::where('priority', 'high')->whereNotIn('status', ['completed', 'returned_to_service'])->count();

        $byStatus = WorkOrder::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn($r) => ['label' => $r->status, 'total' => (int) $r->total])
            ->values();

        $byPriority = WorkOrder::select('priority', DB::raw('COUNT(*) as total'))
            ->groupBy('priority')
            ->get()
            ->map(fn($r) => ['label' => $r->priority, 'total' => (int) $r->total])
            ->values();

        $byMonth = WorkOrder::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $buses = Bus::orderBy('registration_number')->get();

        return view('reports.work-orders', compact(
            'workOrders', 'totalWO', 'openWO', 'completedWO', 'highPriority',
            'byStatus', 'byPriority', 'byMonth', 'buses'
        ));
    }

    // ── Technician's own report ───────────────────────────────────────────────

    public function technicianReport(Request $request)
    {
        $techId = auth()->id();

        $query = WorkOrder::with(['bus'])
            ->where('assigned_to', $techId)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->from,   fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->to,     fn($q, $t) => $q->whereDate('created_at', '<=', $t))
            ->latest();

        $workOrders = $query->paginate(20)->withQueryString();

        $totalWO     = WorkOrder::where('assigned_to', $techId)->count();
        $completedWO = WorkOrder::where('assigned_to', $techId)
            ->whereIn('status', ['completed', 'returned_to_service'])->count();
        $inProgressWO = WorkOrder::where('assigned_to', $techId)
            ->whereIn('status', ['assigned', 'awaiting_parts', 'in_progress', 'testing'])->count();

        $avgCompletionDays = WorkOrder::where('assigned_to', $techId)
            ->whereIn('status', ['completed', 'returned_to_service'])
            ->whereNotNull('start_date')->whereNotNull('completion_date')
            ->get()
            ->avg(fn($wo) => $wo->start_date->diffInDays($wo->completion_date) ?: 1) ?? 0;

        $byStatus = WorkOrder::where('assigned_to', $techId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->get()
            ->map(fn($r) => ['label' => $r->status, 'total' => (int) $r->total])
            ->values();

        return view('reports.technician', compact(
            'workOrders', 'totalWO', 'completedWO', 'inProgressWO', 'avgCompletionDays', 'byStatus'
        ));
    }

    public function exportTechnician(Request $request)
    {
        $techId   = auth()->id();
        $techName = auth()->user()->name;

        $workOrders = WorkOrder::with(['bus'])
            ->where('assigned_to', $techId)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->from,   fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->to,     fn($q, $t) => $q->whereDate('created_at', '<=', $t))
            ->latest()->get();

        $filename = 'my-work-orders-' . now()->format('Y-m-d');

        $totalWO      = $workOrders->count();
        $completedWO  = $workOrders->whereIn('status', ['completed', 'returned_to_service'])->count();
        $inProgressWO = $workOrders->whereIn('status', ['assigned', 'awaiting_parts', 'in_progress', 'testing'])->count();
        $avgDays      = round($workOrders->whereIn('status', ['completed', 'returned_to_service'])
            ->filter(fn($wo) => $wo->start_date && $wo->completion_date)
            ->avg(fn($wo) => $wo->start_date->diffInDays($wo->completion_date) ?: 1) ?? 0, 1);

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('exports.technician-pdf', compact(
                'workOrders', 'techName', 'totalWO', 'completedWO', 'inProgressWO', 'avgDays'
            ))->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $workOrders->map(fn($wo) => [
            '#' . $wo->id,
            $wo->title,
            $wo->bus->registration_number,
            $wo->priority,
            $wo->status,
            $wo->created_at->format('d M Y'),
            $wo->start_date?->format('d M Y') ?? '',
            $wo->completion_date?->format('d M Y') ?? '',
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new TechnicianReportExport($rows, $techName), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new TechnicianReportExport($rows, $techName), $filename . '.xlsx');
    }

    // ── Exports for existing reports ─────────────────────────────────────────

    public function exportWorkOrders(Request $request)
    {
        $workOrders = WorkOrder::with(['bus', 'reporter', 'assignee'])
            ->when($request->status,   fn($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->when($request->from,     fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->to,       fn($q, $t) => $q->whereDate('created_at', '<=', $t))
            ->latest()->get();

        $filename    = 'work-orders-report-' . now()->format('Y-m-d');
        $totalWO     = $workOrders->count();
        $openWO      = $workOrders->whereNotIn('status', ['completed', 'returned_to_service', 'cancelled'])->count();
        $completedWO = $workOrders->whereIn('status', ['completed', 'returned_to_service'])->count();
        $highPriority = $workOrders->where('priority', 'high')
            ->whereNotIn('status', ['completed', 'returned_to_service', 'cancelled'])->count();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('exports.work-orders-pdf', compact(
                'workOrders', 'totalWO', 'openWO', 'completedWO', 'highPriority'
            ))->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $workOrders->map(fn($wo) => [
            $wo->id,
            $wo->title,
            $wo->bus->registration_number,
            $wo->priority,
            $wo->status,
            $wo->reporter->name,
            $wo->assignee?->name ?? '',
            $wo->created_at->format('d M Y'),
            $wo->start_date?->format('d M Y') ?? '',
            $wo->completion_date?->format('d M Y') ?? '',
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new WorkOrderReportExport($rows), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new WorkOrderReportExport($rows), $filename . '.xlsx');
    }

    public function exportPreventiveMaintenance(Request $request)
    {
        $schedules = PreventiveMaintenance::with('bus')->orderBy('next_service_date')->get();
        $filename  = 'pm-report-' . now()->format('Y-m-d');

        $totalSchedules = $schedules->count();
        $overdueCount   = $schedules->where('status', 'overdue')->count();
        $upcomingCount  = $schedules->where('status', 'upcoming')->count();
        $completedCount = $schedules->where('status', 'completed')->count();

        $scheduledCount = $schedules->where('status', 'scheduled')->count();
        $overdue        = $schedules->where('status', 'overdue')->sortBy('next_service_date');
        $upcoming       = $schedules->where('status', 'upcoming')->sortBy('next_service_date')->take(10);

        $byServiceType = $schedules->groupBy('service_type')->map(fn($g) => $g->count())
            ->sortDesc()->map(fn($count, $type) => ['label' => $type, 'total' => $count]);

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('exports.pm-pdf', compact(
                'schedules', 'totalSchedules', 'overdueCount', 'upcomingCount',
                'completedCount', 'scheduledCount', 'overdue', 'upcoming', 'byServiceType'
            ))->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $schedules->map(fn($pm) => [
            $pm->bus->registration_number,
            $pm->service_type,
            $pm->status,
            $pm->last_service_date?->format('d M Y') ?? '',
            $pm->next_service_date?->format('d M Y') ?? '',
            $pm->last_service_km ?? '',
            $pm->interval_km ?? '',
            $pm->notes ?? '',
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new PmReportExport($rows), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PmReportExport($rows), $filename . '.xlsx');
    }

    public function exportUsers(Request $request)
    {
        $allUsers = User::with('roles')->orderBy('name')->get();
        $filename = 'users-report-' . now()->format('Y-m-d');

        $totalUsers    = $allUsers->count();
        $verifiedUsers = $allUsers->filter(fn($u) => $u->email_verified_at)->count();
        $pendingUsers  = 0;
        $lastLoginStats = ['today' => $allUsers->filter(fn($u) => $u->last_login_at?->isToday())->count()];
        $recentUsers   = $allUsers;

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('exports.users-pdf', compact(
                'recentUsers', 'totalUsers', 'verifiedUsers', 'pendingUsers', 'lastLoginStats'
            ))->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        $rows = $allUsers->map(fn($u) => [
            $u->name,
            $u->email,
            $u->getRoleNames()->first() ?? '',
            $u->department ?? '',
            $u->position ?? '',
            $u->phone ?? '',
            $u->email_verified_at ? 'Yes' : 'No',
            $u->last_login_at?->format('d M Y') ?? 'Never',
        ])->toArray();

        if ($request->format === 'csv') {
            return Excel::download(new UsersReportExport($rows), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new UsersReportExport($rows), $filename . '.xlsx');
    }

    public function preventiveMaintenanceReport()
    {
        $schedules = PreventiveMaintenance::with('bus')->orderBy('next_service_date')->get();

        $totalSchedules  = $schedules->count();
        $overdueCount    = $schedules->where('status', 'overdue')->count();
        $upcomingCount   = $schedules->where('status', 'upcoming')->count();
        $completedCount  = $schedules->where('status', 'completed')->count();
        $scheduledCount  = $schedules->where('status', 'scheduled')->count();

        $byStatus = collect([
            ['label' => 'Overdue',   'total' => $overdueCount,   'color' => '#dc2626'],
            ['label' => 'Upcoming',  'total' => $upcomingCount,  'color' => '#d97706'],
            ['label' => 'Completed', 'total' => $completedCount, 'color' => '#16a34a'],
            ['label' => 'Scheduled', 'total' => $scheduledCount, 'color' => '#1c3faa'],
        ]);

        $byServiceType = PreventiveMaintenance::select('service_type', DB::raw('COUNT(*) as total'))
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => ['label' => $r->service_type, 'total' => (int) $r->total])
            ->values();

        $overdue  = $schedules->where('status', 'overdue')->sortBy('next_service_date');
        $upcoming = $schedules->where('status', 'upcoming')->sortBy('next_service_date')->take(10);

        return view('reports.preventive-maintenance', compact(
            'schedules', 'totalSchedules', 'overdueCount', 'upcomingCount',
            'completedCount', 'scheduledCount', 'byStatus', 'byServiceType',
            'overdue', 'upcoming'
        ));
    }

    // ── Pages ─────────────────────────────────────────────────────────────────

    public function fleet(Request $request)
    {
        $buses = Bus::withCount(['workOrders', 'maintenanceRecords'])
            ->with(['workOrders' => fn($q) => $q->where('status', 'completed')])
            ->get();

        $fleetStats = $buses->map(function ($bus) {
            $completed = $bus->workOrders->filter(fn($w) => $w->status === 'completed');
            $totalRepairDays = $completed->sum(function ($wo) {
                if ($wo->start_date && $wo->completion_date) {
                    return $wo->start_date->diffInDays($wo->completion_date) ?: 1;
                }
                return 1;
            });

            $mttr = $completed->count() > 0 ? round($totalRepairDays / $completed->count(), 1) : 0;

            return [
                'bus'            => $bus,
                'total_wo'       => $bus->work_orders_count,
                'completed_wo'   => $completed->count(),
                'total_records'  => $bus->maintenance_records_count,
                'mttr'           => $mttr,
            ];
        });

        $statusCounts = [
            'active'       => $buses->where('status', 'active')->count(),
            'under_repair' => $buses->where('status', 'under_repair')->count(),
            'inactive'     => $buses->where('status', 'inactive')->count(),
        ];

        // Minimal chart-safe data (no Eloquent models in JS)
        $fleetChartData = $fleetStats->map(fn($s) => [
            'label'        => $s['bus']->registration_number,
            'total_wo'     => $s['total_wo'],
            'completed_wo' => $s['completed_wo'],
            'mttr'         => $s['mttr'],
        ])->values();

        return view('reports.fleet', compact('buses', 'fleetStats', 'statusCounts', 'fleetChartData'));
    }
}
