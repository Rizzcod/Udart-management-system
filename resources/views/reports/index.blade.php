<x-app-layout title="Reports">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reports</h4>
            <p class="text-muted small mb-0 mt-1">System-wide reports and analytics for all modules</p>
        </div>
    </div>

    {{-- Section: Operations --}}
    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.72rem;letter-spacing:.08em;">Operations</h6>
    <div class="row g-3 mb-4">
        {{-- Maintenance Report --}}
        <div class="col-md-4">
            <div class="card p-4 h-100 d-flex flex-column" style="border-radius:14px;border:1px solid #e5e7eb;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                          style="width:48px;height:48px;background:#eff6ff;">
                        <i class="bi bi-clipboard2-check fs-4" style="color:#1c3faa;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Maintenance Report</h6>
                <p class="text-muted small mb-3">View maintenance history, filter by bus or date range, and analyse activity trends.</p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('reports.maintenance') }}" class="btn btn-sm flex-grow-1"
                       style="background:#1c3faa;border-color:#1c3faa;color:#fff;border-radius:8px;">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                style="background:#eff6ff;border:1px solid #bfdbfe;color:#1c3faa;border-radius:8px;">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Export As</h6></li>
                            <li><a class="dropdown-item" href="{{ route('reports.maintenance.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.maintenance.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.maintenance.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Work Orders Report --}}
        <div class="col-md-4">
            <div class="card p-4 h-100 d-flex flex-column" style="border-radius:14px;border:1px solid #e5e7eb;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                          style="width:48px;height:48px;background:#fff7ed;">
                        <i class="bi bi-wrench-adjustable fs-4" style="color:#ea580c;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Work Orders Report</h6>
                <p class="text-muted small mb-3">Track work order statuses, priorities, and resolution timelines across the fleet.</p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('reports.work-orders') }}" class="btn btn-sm flex-grow-1"
                       style="background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;border-radius:8px;">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                style="background:#fff7ed;border:1px solid #fed7aa;color:#ea580c;border-radius:8px;">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Export As</h6></li>
                            <li><a class="dropdown-item" href="{{ route('reports.work-orders.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.work-orders.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.work-orders.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preventive Maintenance Report --}}
        <div class="col-md-4">
            <div class="card p-4 h-100 d-flex flex-column" style="border-radius:14px;border:1px solid #e5e7eb;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                          style="width:48px;height:48px;background:#f0fdf4;">
                        <i class="bi bi-calendar2-check fs-4" style="color:#16a34a;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Preventive Maintenance Report</h6>
                <p class="text-muted small mb-3">Monitor PM schedules, overdue services, upcoming tasks, and completion rates per bus.</p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('reports.preventive-maintenance') }}" class="btn btn-sm flex-grow-1"
                       style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:8px;">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                style="background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;border-radius:8px;">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Export As</h6></li>
                            <li><a class="dropdown-item" href="{{ route('reports.preventive-maintenance.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.preventive-maintenance.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.preventive-maintenance.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Buses & Inventory --}}
    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.72rem;letter-spacing:.08em;">Buses &amp; Inventory</h6>
    <div class="row g-3 mb-4">
        {{-- Fleet Report --}}
        <div class="col-md-6">
            <div class="card p-4 h-100 d-flex flex-column" style="border-radius:14px;border:1px solid #e5e7eb;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                          style="width:48px;height:48px;background:#f0fdf4;">
                        <i class="bi bi-bus-front fs-4" style="color:#16a34a;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Bus Performance Report</h6>
                <p class="text-muted small mb-3">View per-bus downtime, work order counts, MTTR, and overall bus health metrics.</p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('reports.fleet') }}" class="btn btn-success btn-sm flex-grow-1" style="border-radius:8px;">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle btn-success" type="button" data-bs-toggle="dropdown"
                                style="border-radius:8px;">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Export As</h6></li>
                            <li><a class="dropdown-item" href="{{ route('reports.fleet.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.fleet.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.fleet.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventory Report --}}
        <div class="col-md-6">
            <div class="card p-4 h-100 d-flex flex-column" style="border-radius:14px;border:1px solid #e5e7eb;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                          style="width:48px;height:48px;background:#fefce8;">
                        <i class="bi bi-box-seam fs-4" style="color:#ca8a04;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Inventory Report</h6>
                <p class="text-muted small mb-3">Analyse spare parts consumption, identify low stock items, and view total inventory value.</p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('reports.inventory') }}" class="btn btn-warning btn-sm flex-grow-1" style="border-radius:8px;">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle btn-warning" type="button" data-bs-toggle="dropdown"
                                style="border-radius:8px;">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Export As</h6></li>
                            <li><a class="dropdown-item" href="{{ route('reports.inventory.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.inventory.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.inventory.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Administration --}}
    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.72rem;letter-spacing:.08em;">Administration</h6>
    <div class="row g-3">
        {{-- Users Report --}}
        <div class="col-md-4">
            <div class="card p-4 h-100 d-flex flex-column" style="border-radius:14px;border:1px solid #e5e7eb;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                          style="width:48px;height:48px;background:#f5f3ff;">
                        <i class="bi bi-people fs-4" style="color:#7c3aed;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Users Report</h6>
                <p class="text-muted small mb-3">View user counts by role, verification status, login activity, and recently added accounts.</p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('reports.users') }}" class="btn btn-sm flex-grow-1"
                       style="background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe;border-radius:8px;">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                style="background:#f5f3ff;border:1px solid #ddd6fe;color:#7c3aed;border-radius:8px;">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Export As</h6></li>
                            <li><a class="dropdown-item" href="{{ route('reports.users.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.users.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.users.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
