<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusAssignmentController;
use App\Http\Controllers\FleetBoardController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\PreventiveMaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\FailurePatternController;
use App\Http\Controllers\IotController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MileageLogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\OtpVerificationController;
use Illuminate\Support\Facades\Route;

// Public project overview for guests; signed-in users go straight to their dashboard
Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : view('welcome'))->name('home');

// Language switcher — no auth required
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'sw'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back()->withInput();
})->name('lang.switch');

// OTP email verification — no auth required
Route::get('/verify-otp',  [OtpVerificationController::class, 'show'])->name('otp.verify');
Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])->name('otp.verify.submit');

// ─── All authenticated users ───────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('Driver')) {
            return redirect()->route('driver.dashboard');
        }
        return app(DashboardController::class)->index();
    })->name('dashboard');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ── Messages ──────────────────────────────────────────────────────
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{thread}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{thread}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::delete('messages/{thread}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // ── Announcements ─────────────────────────────────────────────────
    Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

// ─── Driver portal ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverController::class, 'index'])->name('dashboard');
    Route::get('/my-reports', [DriverController::class, 'myReports'])->name('my-reports');
    Route::get('/report-breakdown', [DriverController::class, 'reportBreakdown'])->name('report-breakdown');
    Route::post('/report-breakdown', [DriverController::class, 'storeBreakdown'])->name('store-breakdown');
    Route::get('/reports/{workOrder}', [DriverController::class, 'showReport'])->name('show-report');
    Route::post('/mileage-log', [MileageLogController::class, 'store'])->name('mileage-log.store');
    Route::get('/my-assignment', [BusAssignmentController::class, 'myAssignment'])->name('my-assignment');
    Route::get('/trip-started', [DriverController::class, 'tripStarted'])->name('trip-started');
    Route::get('/trip-arrived', [DriverController::class, 'tripArrived'])->name('trip-arrived');
    Route::post('/trip/start',      [DriverController::class, 'startTrip'])->name('trip.start');
    Route::post('/trip/arrived',    [DriverController::class, 'arrivedAtDestination'])->name('trip.arrived');
    Route::post('/trip/end',        [DriverController::class, 'endTrip'])->name('trip.end');
    Route::get('/trip/return-suggested', [DriverController::class, 'returnSuggested'])->name('return-suggested');
    Route::post('/trip/finish-day', [DriverController::class, 'finishDay'])->name('trip.finish-day');
    Route::get('/my-activity', [DriverController::class, 'myActivity'])->name('my-activity');
    Route::get('/my-activity/export', [DriverController::class, 'exportMyActivity'])->name('my-activity.export');
});

// ─── Staff portal ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Admin|Supervisor|Technician|Storekeeper'])->group(function () {

    // ── Fleet Board ──────────────────────────────────────────────────
    Route::middleware('can:view bus-assignments')->group(function () {
        Route::get('fleet-board', [FleetBoardController::class, 'index'])->name('fleet-board.index');
    });

    // ── Daily Bus Assignments ────────────────────────────────────────
    Route::middleware('can:view bus-assignments')->group(function () {
        Route::get('bus-assignments', [BusAssignmentController::class, 'index'])->name('bus-assignments.index');
    });
    Route::middleware('can:manage bus-assignments')->group(function () {
        Route::post('bus-assignments/auto-assign', [BusAssignmentController::class, 'autoAssign'])->name('bus-assignments.auto-assign');
        Route::post('bus-assignments', [BusAssignmentController::class, 'store'])->name('bus-assignments.store');
        Route::patch('bus-assignments/{busAssignment}/route', [BusAssignmentController::class, 'updateRoute'])->name('bus-assignments.update-route');
        Route::delete('bus-assignments/{busAssignment}', [BusAssignmentController::class, 'destroy'])->name('bus-assignments.destroy');
    });

    // ── Buses ───────────────────────────────────────────────────────
    // All staff can view; create/edit/delete are permission-gated
    Route::get('buses', [BusController::class, 'index'])->name('buses.index');
    Route::middleware('can:create buses')->group(function () {
        Route::get('buses/create', [BusController::class, 'create'])->name('buses.create');
        Route::post('buses', [BusController::class, 'store'])->name('buses.store');
    });
    Route::get('buses/{bus}', [BusController::class, 'show'])->name('buses.show');
    Route::middleware('can:edit buses')->group(function () {
        Route::get('buses/{bus}/edit', [BusController::class, 'edit'])->name('buses.edit');
        Route::put('buses/{bus}', [BusController::class, 'update'])->name('buses.update');
        Route::patch('buses/{bus}', [BusController::class, 'update']);
    });
    Route::delete('buses/{bus}', [BusController::class, 'destroy'])
        ->middleware('can:delete buses')->name('buses.destroy');

    // ── Work Orders ──────────────────────────────────────────────────
    Route::get('work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
    Route::get('work-orders/my-assignments', [WorkOrderController::class, 'myAssignments'])->name('work-orders.my-assignments');
    Route::middleware('can:create work-orders')->group(function () {
        Route::get('work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
        Route::post('work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
    });
    Route::get('work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('work-orders.show');
    Route::middleware('can:edit work-orders')->group(function () {
        Route::get('work-orders/{workOrder}/edit', [WorkOrderController::class, 'edit'])->name('work-orders.edit');
        Route::put('work-orders/{workOrder}', [WorkOrderController::class, 'update'])->name('work-orders.update');
    });
    Route::patch('work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus'])
        ->middleware('can:update work-order status')->name('work-orders.status');
    Route::delete('work-orders/{workOrder}', [WorkOrderController::class, 'destroy'])
        ->middleware('can:delete work-orders')->name('work-orders.destroy');

    // ── Maintenance Records ──────────────────────────────────────────
    Route::get('maintenance-records', [MaintenanceRecordController::class, 'index'])->name('maintenance-records.index');
    Route::middleware('can:create maintenance-records')->group(function () {
        Route::get('maintenance-records/create', [MaintenanceRecordController::class, 'create'])->name('maintenance-records.create');
        Route::post('maintenance-records', [MaintenanceRecordController::class, 'store'])->name('maintenance-records.store');
    });
    Route::get('maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'show'])->name('maintenance-records.show');
    Route::middleware('can:edit maintenance-records')->group(function () {
        Route::get('maintenance-records/{maintenanceRecord}/edit', [MaintenanceRecordController::class, 'edit'])->name('maintenance-records.edit');
        Route::put('maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'update'])->name('maintenance-records.update');
    });
    Route::delete('maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'destroy'])
        ->middleware('can:delete maintenance-records')->name('maintenance-records.destroy');

    // ── Preventive Maintenance ───────────────────────────────────────
    Route::get('preventive-maintenance', [PreventiveMaintenanceController::class, 'index'])->name('preventive-maintenance.index');
    // Static/action routes BEFORE wildcard routes to avoid model-binding collisions
    Route::middleware('can:create preventive-maintenance')->group(function () {
        Route::get('preventive-maintenance/create', [PreventiveMaintenanceController::class, 'create'])->name('preventive-maintenance.create');
        Route::post('preventive-maintenance', [PreventiveMaintenanceController::class, 'store'])->name('preventive-maintenance.store');
        Route::post('preventive-maintenance/generate-all', [PreventiveMaintenanceController::class, 'generateAll'])->name('preventive-maintenance.generate-all');
    });
    Route::post('preventive-maintenance/bulk-status', [PreventiveMaintenanceController::class, 'bulkUpdateStatus'])
        ->middleware('can:edit preventive-maintenance')
        ->name('preventive-maintenance.bulk-status');
    // Wildcard routes (numeric IDs only to prevent slug collisions)
    Route::get('preventive-maintenance/{preventiveMaintenance}', [PreventiveMaintenanceController::class, 'show'])
        ->whereNumber('preventiveMaintenance')->name('preventive-maintenance.show');
    Route::middleware('can:edit preventive-maintenance')->group(function () {
        Route::get('preventive-maintenance/{preventiveMaintenance}/edit', [PreventiveMaintenanceController::class, 'edit'])
            ->whereNumber('preventiveMaintenance')->name('preventive-maintenance.edit');
        Route::put('preventive-maintenance/{preventiveMaintenance}', [PreventiveMaintenanceController::class, 'update'])
            ->whereNumber('preventiveMaintenance')->name('preventive-maintenance.update');
    });
    Route::delete('preventive-maintenance/{preventiveMaintenance}', [PreventiveMaintenanceController::class, 'destroy'])
        ->whereNumber('preventiveMaintenance')
        ->middleware('can:delete preventive-maintenance')->name('preventive-maintenance.destroy');

    // ── Spare Parts (Storekeeper + Admin/Supervisor) ─────────────────
    Route::middleware('can:view spare-parts')->group(function () {
        Route::get('spare-parts', [SparePartController::class, 'index'])->name('spare-parts.index');
    });
    Route::middleware('can:create spare-parts')->group(function () {
        Route::get('spare-parts/create', [SparePartController::class, 'create'])->name('spare-parts.create');
        Route::post('spare-parts', [SparePartController::class, 'store'])->name('spare-parts.store');
    });
    Route::middleware('can:view spare-parts')->group(function () {
        Route::get('spare-parts/{sparePart}', [SparePartController::class, 'show'])->name('spare-parts.show');
    });
    Route::middleware('can:edit spare-parts')->group(function () {
        Route::get('spare-parts/{sparePart}/edit', [SparePartController::class, 'edit'])->name('spare-parts.edit');
        Route::put('spare-parts/{sparePart}', [SparePartController::class, 'update'])->name('spare-parts.update');
    });
    Route::delete('spare-parts/{sparePart}', [SparePartController::class, 'destroy'])
        ->middleware('can:delete spare-parts')->name('spare-parts.destroy');

    // ── Intelligence (Admin, Supervisor, Technician) ─────────────────
    Route::middleware('can:view iot')->group(function () {
        Route::get('/iot', [IotController::class, 'dashboard'])->name('iot.dashboard');
        Route::get('/iot/data', [IotController::class, 'dashboardData'])->name('iot.dashboard.data');
        Route::get('/iot/bus/{bus}', [IotController::class, 'busReadings'])->name('iot.bus');
        Route::get('/iot/bus/{bus}/data', [IotController::class, 'busReadingsData'])->name('iot.bus.data');
    });

    Route::middleware('can:view predictions')->group(function () {
        Route::get('predictions', [PredictionController::class, 'index'])->name('predictions.index');
        Route::get('predictions/{prediction}', [PredictionController::class, 'show'])->name('predictions.show');
        Route::post('predictions/run-all', [PredictionController::class, 'runAll'])->name('predictions.run-all');
        Route::post('predictions/run/{bus}', [PredictionController::class, 'runForBus'])->name('predictions.run');
    });

    Route::middleware('can:view failure-patterns')->group(function () {
        Route::get('/failure-patterns', [FailurePatternController::class, 'index'])->name('failure-patterns.index');
        Route::get('/failure-patterns/{bus}', [FailurePatternController::class, 'show'])->name('failure-patterns.show');
    });

    // ── Reports ──────────────────────────────────────────────────────
    Route::middleware('can:view reports')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/maintenance', [ReportController::class, 'maintenance'])->name('reports.maintenance');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/fleet', [ReportController::class, 'fleet'])->name('reports.fleet');
        Route::get('/reports/users', [ReportController::class, 'users'])->name('reports.users');
        Route::get('/reports/work-orders', [ReportController::class, 'workOrders'])->name('reports.work-orders');
        Route::get('/reports/preventive-maintenance', [ReportController::class, 'preventiveMaintenanceReport'])->name('reports.preventive-maintenance');
        Route::get('/reports/maintenance/export', [ReportController::class, 'exportMaintenance'])->name('reports.maintenance.export');
        Route::get('/reports/inventory/export', [ReportController::class, 'exportInventory'])->name('reports.inventory.export');
        Route::get('/reports/fleet/export', [ReportController::class, 'exportFleet'])->name('reports.fleet.export');
        Route::get('/reports/work-orders/export', [ReportController::class, 'exportWorkOrders'])->name('reports.work-orders.export');
        Route::get('/reports/preventive-maintenance/export', [ReportController::class, 'exportPreventiveMaintenance'])->name('reports.preventive-maintenance.export');
        Route::get('/reports/users/export', [ReportController::class, 'exportUsers'])->name('reports.users.export');
    });

    // ── Technician personal report ──────────────────────────────────
    Route::middleware('can:view own-reports')->group(function () {
        Route::get('/reports/my-work-orders', [ReportController::class, 'technicianReport'])->name('reports.technician');
        Route::get('/reports/my-work-orders/export', [ReportController::class, 'exportTechnician'])->name('reports.technician.export');
    });

    // ── User Management (granular) ────────────────────────────────────
    Route::middleware('can:view users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
    Route::middleware('can:create users')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('can:edit users')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::post('/users/{user}/resend-otp', [OtpVerificationController::class, 'resend'])->name('users.resend-otp');
    });
    Route::middleware('can:delete users')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    Route::middleware('can:manage user-permissions')->group(function () {
        Route::get('/users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
        Route::post('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
    });

    // ── Roles & Permissions management ───────────────────────────────
    Route::middleware('can:manage users')->group(function () {
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::get('/roles-permissions/{role}', [RolePermissionController::class, 'show'])->name('roles.show');
        Route::post('/roles-permissions/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
    });
});

require __DIR__.'/auth.php';
