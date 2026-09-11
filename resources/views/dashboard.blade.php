<x-app-layout title="Dashboard">

    {{-- ── Welcome Banner (all roles) ── --}}
    <div class="welcome-card mb-4 d-flex align-items-center justify-content-between">
        <div style="position:relative;z-index:1;">
            @php
                $hour = now()->hour;
                $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                $user = auth()->user();
                $role = $user->getRoleNames()->first();
            @endphp
            <div style="font-size:1.55rem;font-weight:700;line-height:1.2;" class="mb-1">
                {{ $greeting }}, {{ explode(' ', $user->name)[0] }}!
            </div>
            <div style="color:rgba(255,255,255,.78);font-size:.9rem;" class="mb-2">
                UDART Maintenance Management System
                @if($user->department) &nbsp;•&nbsp; {{ $user->department }} @endif
                &nbsp;•&nbsp; <span class="badge" style="background:rgba(255,255,255,.2);font-weight:500;font-size:.78rem;">{{ $role }}</span>
            </div>
            <div style="color:rgba(255,255,255,.58);font-size:.78rem;">
                <i class="bi bi-clock me-1"></i>{{ now()->format('l, d F Y') }}
                @if($user->last_login_at)
                &nbsp;•&nbsp; Last login: {{ $user->last_login_at->diffForHumans() }}
                @endif
            </div>
        </div>
        <div class="welcome-avatar d-none d-md-flex">
            <i class="bi bi-person-fill" style="font-size:2rem;"></i>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         ADMIN / SUPERVISOR DASHBOARD
    ═══════════════════════════════════════════════════════════ --}}
    @if($dashboardRole === 'admin')

    {{-- 4 Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-bus-front-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['total_buses'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Total Buses</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3" style="border-left:3px solid #2563eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;position:relative;">
                        <i class="bi bi-arrow-right-circle-fill"></i>
                        @if($stats['buses_on_trip'] > 0)
                        <span style="position:absolute;top:-2px;right:-2px;width:10px;height:10px;background:#2563eb;border-radius:50%;animation:pulse 1.5s infinite;"></span>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#1d4ed8;">{{ $stats['buses_on_trip'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">On Trip Now</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ffedd5;color:#c2410c;"><i class="bi bi-wrench-adjustable-circle-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['open_work_orders'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Open Work Orders</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['overdue_pm'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Overdue PM Schedules</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Live Operations Today --}}
    @if($liveAssignments->count())
    <div class="card mb-4">
        <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span style="display:inline-block;width:9px;height:9px;background:#2563eb;border-radius:50%;animation:pulse 1.5s infinite;"></span>
                <i class="bi bi-broadcast text-primary ms-1"></i>
                <span class="fw-bold" style="font-size:.95rem;">Live Operations — Today</span>
                <span class="badge ms-1" style="background:#dbeafe;color:#1d4ed8;font-size:.75rem;">{{ $liveAssignments->count() }} assigned</span>
            </div>
            @can('manage bus-assignments')
            <a href="{{ route('fleet-board.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.8rem;">
                <i class="bi bi-map me-1"></i>Fleet Board
            </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:.875rem;">
                <thead>
                    <tr>
                        <th class="ps-4">Bus</th>
                        <th>Driver</th>
                        <th>Route</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($liveAssignments as $assignment)
                    @php
                        $busStatus = $assignment->bus->status;
                        [$statusLabel, $statusStyle] = match($busStatus) {
                            'on_trip'  => ['On Trip',          'background:#dbeafe;color:#1d4ed8;'],
                            'arrived'  => ['At Terminal',      'background:#fef9c3;color:#854d0e;'],
                            'active'   => ['Ready to Depart',  'background:#dcfce7;color:#15803d;'],
                            default    => [$assignment->bus->getStatusLabel(), 'background:#fee2e2;color:#991b1b;'],
                        };
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('buses.show', $assignment->bus) }}" class="fw-semibold text-decoration-none" style="color:#1c3faa;">
                                {{ $assignment->bus->registration_number }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;border-radius:50%;background:#e0e7ff;color:#3730a3;font-size:.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    {{ strtoupper(substr($assignment->driver->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $assignment->driver->name)[1] ?? '', 0, 1)) }}
                                </div>
                                <span>{{ $assignment->driver->name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($assignment->route)
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-signpost-2 text-muted" style="font-size:.8rem;"></i>
                                {{ $assignment->route }}
                            </span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="{{ $statusStyle }}">
                                @if($busStatus === 'on_trip')
                                <span style="display:inline-block;width:7px;height:7px;background:#2563eb;border-radius:50%;animation:pulse 1.5s infinite;margin-right:4px;"></span>
                                @elseif($busStatus === 'arrived')
                                <span style="display:inline-block;width:7px;height:7px;background:#d97706;border-radius:50%;margin-right:4px;"></span>
                                @elseif($busStatus === 'active')
                                <span style="display:inline-block;width:7px;height:7px;background:#16a34a;border-radius:50%;margin-right:4px;"></span>
                                @endif
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Buses Overview + Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-line text-primary"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Buses Overview</span>
                </div>
                <div class="card-body px-4">
                    <table class="table table-borderless mb-0" style="font-size:.875rem;">
                        <tr><td class="ps-0 text-muted">Total Buses</td><td class="text-end pe-0 fw-semibold">{{ $stats['total_buses'] }}</td></tr>
                        <tr>
                            <td class="ps-0 text-muted">
                                <span style="display:inline-block;width:7px;height:7px;background:#2563eb;border-radius:50%;animation:pulse 1.5s infinite;margin-right:5px;"></span>
                                Currently On Trip
                            </td>
                            <td class="text-end pe-0">
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8;">{{ $stats['buses_on_trip'] }} On Road</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted">
                                <span style="display:inline-block;width:7px;height:7px;background:#d97706;border-radius:50%;margin-right:5px;"></span>
                                At Terminal
                            </td>
                            <td class="text-end pe-0">
                                <span class="badge" style="background:#fef9c3;color:#854d0e;">{{ $stats['buses_at_terminal'] }} At Terminal</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted">Available / Ready</td>
                            <td class="text-end pe-0"><span class="badge" style="background:#dcfce7;color:#15803d;">{{ $stats['buses_at_depot'] }} Active</span></td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted">Under Repair</td>
                            <td class="text-end pe-0"><span class="badge" style="background:#ffedd5;color:#c2410c;">{{ $stats['under_repair'] }} Buses</span></td>
                        </tr>
                        <tr><td class="ps-0 text-muted">Open Work Orders</td><td class="text-end pe-0 fw-semibold">{{ $stats['open_work_orders'] }}</td></tr>
                        <tr>
                            <td class="ps-0 text-muted">Overdue PM</td>
                            <td class="text-end pe-0">
                                @if($stats['overdue_pm'] > 0)
                                <span class="badge bg-danger">{{ $stats['overdue_pm'] }} Overdue</span>
                                @else
                                <span class="badge" style="background:#dcfce7;color:#15803d;">All on track</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted">Low Stock Parts</td>
                            <td class="text-end pe-0">
                                @if($stats['low_stock_parts'] > 0)
                                <span class="badge bg-warning text-dark">{{ $stats['low_stock_parts'] }} Items</span>
                                @else
                                <span class="badge" style="background:#dcfce7;color:#15803d;">Fully stocked</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-transparent px-4 py-2">
                    <a href="{{ route('buses.index') }}" class="text-decoration-none" style="color:#1c3faa;font-size:.82rem;font-weight:600;">
                        View All Buses <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Quick Actions</span>
                </div>
                <div class="card-body px-4">
                    <div class="row g-2">
                        @can('create work-orders')
                        <div class="col-6">
                            <a href="{{ route('work-orders.create') }}" class="quick-btn quick-btn-blue">
                                <i class="bi bi-plus-circle"></i> New Work Order
                            </a>
                        </div>
                        @endcan
                        @can('create preventive-maintenance')
                        <div class="col-6">
                            <a href="{{ route('preventive-maintenance.create') }}" class="quick-btn quick-btn-green">
                                <i class="bi bi-calendar-plus"></i> Schedule PM
                            </a>
                        </div>
                        @endcan
                        @can('create buses')
                        <div class="col-6">
                            <a href="{{ route('buses.create') }}" class="quick-btn quick-btn-purple">
                                <i class="bi bi-plus-square"></i> Add Bus
                            </a>
                        </div>
                        @endcan
                        @can('create spare-parts')
                        <div class="col-6">
                            <a href="{{ route('spare-parts.create') }}" class="quick-btn quick-btn-orange">
                                <i class="bi bi-box-seam"></i> Add Spare Part
                            </a>
                        </div>
                        @endcan
                        @can('view predictions')
                        <div class="col-6">
                            <a href="{{ route('predictions.index') }}" class="quick-btn quick-btn-teal">
                                <i class="bi bi-graph-up-arrow"></i> AI Predictions
                            </a>
                        </div>
                        @endcan
                        @can('view reports')
                        <div class="col-6">
                            <a href="{{ route('reports.index') }}" class="quick-btn" style="background:#6b7280;color:#fff;">
                                <i class="bi bi-file-earmark-bar-graph"></i> View Reports
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card p-4 h-100 d-flex flex-column">
                <div class="fw-bold mb-3" style="font-size:.95rem;">Work Orders by Status</div>
                <div class="flex-grow-1" style="position:relative;min-height:260px;">
                    <canvas id="woStatusChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card p-4 h-100 d-flex flex-column">
                <div class="fw-bold mb-3" style="font-size:.95rem;">Monthly Maintenance Activity ({{ now()->year }})</div>
                <div class="flex-grow-1" style="position:relative;min-height:260px;">
                    <canvas id="activityChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Work Orders --}}
    <div class="card mb-4">
        <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                <span class="fw-bold" style="font-size:.95rem;">Recent Work Orders</span>
            </div>
            <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.8rem;">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Bus</th><th>Issue</th><th>Priority</th>
                        <th>Status</th><th>Assigned To</th><th>Reported</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentWorkOrders as $wo)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('buses.show', $wo->bus) }}" class="fw-semibold text-decoration-none" style="color:#1c3faa;">{{ $wo->bus->registration_number }}</a>
                        </td>
                        <td><a href="{{ route('work-orders.show', $wo) }}" class="text-decoration-none text-dark">{{ Str::limit($wo->title, 40) }}</a></td>
                        <td><span class="badge bg-{{ $wo->getPriorityBadgeClass() }}">{{ ucfirst($wo->priority) }}</span></td>
                        <td><span class="badge bg-{{ $wo->getStatusBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $wo->status)) }}</span></td>
                        <td style="color:#6b7280;">{{ $wo->assignee?->name ?? '—' }}</td>
                        <td style="color:#9ca3af;font-size:.82rem;">{{ $wo->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox d-block fs-2 mb-1 opacity-25"></i>No work orders yet
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upcoming PM + Low Stock --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-check text-success"></i>
                        <span class="fw-bold" style="font-size:.95rem;">Upcoming / Overdue PM</span>
                    </div>
                    <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;">View All</a>
                </div>
                <div class="card-body px-4 py-2">
                    @forelse($upcomingPM as $pm)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <div class="fw-semibold" style="font-size:.875rem;">{{ $pm->bus->registration_number }}</div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $pm->service_type }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $pm->getStatusBadgeClass() }}">{{ ucfirst($pm->status) }}</span>
                            <div class="text-muted mt-1" style="font-size:.72rem;">{{ $pm->next_service_date?->format('d M Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="py-4 text-center text-muted">
                        <i class="bi bi-calendar-check d-block fs-2 mb-1 opacity-25"></i>
                        <span style="font-size:.875rem;">No upcoming schedules</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                        <span class="fw-bold" style="font-size:.95rem;">Low Stock Alerts</span>
                    </div>
                    <a href="{{ route('spare-parts.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;">View All</a>
                </div>
                <div class="card-body px-4 py-2">
                    @forelse($lowStockParts as $part)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <div class="fw-semibold" style="font-size:.875rem;">{{ $part->part_name }}</div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $part->part_number }}</div>
                        </div>
                        <span class="badge bg-danger">{{ $part->quantity }} left</span>
                    </div>
                    @empty
                    <div class="py-4 text-center text-muted">
                        <i class="bi bi-check-circle d-block fs-2 mb-1 opacity-25 text-success"></i>
                        <span style="font-size:.875rem;">All parts adequately stocked</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .4; transform: scale(1.4); }
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    const woStatusColors = {
        pending: '#9ca3af', assigned: '#38bdf8',
        in_progress: '#1c3faa', completed: '#16a34a', cancelled: '#374151'
    };
    const woData = @json($workOrdersByStatus);
    new Chart(document.getElementById('woStatusChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(woData).map(k => k.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                data: Object.values(woData),
                backgroundColor: Object.keys(woData).map(k => woStatusColors[k] ?? '#e5e7eb'),
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, font: { size: 12 }, usePointStyle: true, pointStyleWidth: 10 }
                }
            },
            cutout: '68%',
            layout: { padding: { top: 8, bottom: 8 } }
        }
    });

    const activityData = @json($monthlyRecords);
    new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels: activityData.map(d => d.month),
            datasets: [{
                label: 'Maintenance Records',
                data: activityData.map(d => d.total),
                backgroundColor: 'rgba(28,63,170,.12)',
                borderColor: '#1c3faa',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} record${ctx.parsed.y !== 1 ? 's' : ''}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { size: 11 } },
                    grid: { color: '#f0f3fb' },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } },
                    border: { display: false }
                }
            },
            layout: { padding: { top: 8 } }
        }
    });
    </script>
    @endpush


    {{-- ════════════════════════════════════════════════════════
         TECHNICIAN DASHBOARD
    ═══════════════════════════════════════════════════════════ --}}
    @elseif($dashboardRole === 'technician')

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ffedd5;color:#c2410c;"><i class="bi bi-wrench-adjustable-circle-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['my_open_wos'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">My Open Work Orders</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dcfce7;color:#15803d;"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['completed_month'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Completed This Month</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-clipboard2-pulse"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['my_records_month'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Records This Month</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['overdue_pm'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Overdue PM (All Buses)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- My Assigned Work Orders --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-gear text-primary"></i>
                        <span class="fw-bold" style="font-size:.95rem;">My Active Work Orders</span>
                    </div>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.8rem;">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($myWorkOrders as $wo)
                    <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                        <div class="flex-shrink-0 mt-1">
                            @php
                                $colors = ['critical'=>'#b91c1c','high'=>'#c2410c','medium'=>'#b45309','low'=>'#4b5563'];
                                $color = $colors[$wo->priority] ?? '#4b5563';
                            @endphp
                            <div class="rounded-circle" style="width:10px;height:10px;background:{{ $color }};margin-top:4px;"></div>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate" style="font-size:.875rem;color:#111827;">{{ $wo->title }}</div>
                            <div class="text-muted" style="font-size:.78rem;">
                                <i class="bi bi-bus-front me-1"></i>{{ $wo->bus->registration_number }}
                                &nbsp;•&nbsp; <span class="badge bg-{{ $wo->getStatusBadgeClass() }} px-2" style="font-size:.7rem;">{{ ucwords(str_replace('_',' ',$wo->status)) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('work-orders.show', $wo) }}" class="btn btn-sm btn-outline-secondary flex-shrink-0" style="border-radius:7px;font-size:.75rem;">Open</a>
                    </div>
                    @empty
                    <div class="py-5 text-center text-muted">
                        <i class="bi bi-check2-all d-block fs-2 mb-2 text-success opacity-50"></i>
                        <span style="font-size:.875rem;">No open work orders assigned to you</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Upcoming PM + Quick actions --}}
        <div class="col-lg-5">
            {{-- Quick Actions --}}
            <div class="card mb-3">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Quick Actions</span>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('work-orders.create') }}" class="quick-btn quick-btn-blue">
                            <i class="bi bi-plus-circle"></i> Create Work Order
                        </a>
                        <a href="{{ route('maintenance-records.create') }}" class="quick-btn quick-btn-green">
                            <i class="bi bi-clipboard2-plus"></i> Log Maintenance
                        </a>
                        <a href="{{ route('work-orders.index') }}" class="quick-btn quick-btn-teal">
                            <i class="bi bi-list-task"></i> All Work Orders
                        </a>
                    </div>
                </div>
            </div>

            {{-- Upcoming PM --}}
            <div class="card">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-check text-success"></i>
                        <span class="fw-bold" style="font-size:.95rem;">Upcoming PM</span>
                    </div>
                    <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;">View All</a>
                </div>
                <div class="card-body px-4 py-2">
                    @forelse($upcomingPM as $pm)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <div class="fw-semibold" style="font-size:.875rem;">{{ $pm->bus->registration_number }}</div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $pm->service_type }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $pm->getStatusBadgeClass() }}">{{ ucfirst($pm->status) }}</span>
                            <div class="text-muted mt-1" style="font-size:.72rem;">{{ $pm->next_service_date?->format('d M Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="py-3 text-center text-muted" style="font-size:.875rem;">No upcoming schedules</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- My Recent Maintenance Records --}}
    <div class="card">
        <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clipboard2-check text-primary"></i>
                <span class="fw-bold" style="font-size:.95rem;">My Recent Maintenance Records</span>
            </div>
            <a href="{{ route('maintenance-records.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.8rem;">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th class="ps-4">Bus</th><th>Service Type</th><th>Date</th><th>Cost</th></tr></thead>
                <tbody>
                    @forelse($recentRecords as $rec)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('buses.show', $rec->bus) }}" class="fw-semibold text-decoration-none" style="color:#1c3faa;">{{ $rec->bus->registration_number }}</a>
                        </td>
                        <td style="font-size:.875rem;">{{ $rec->service_type }}</td>
                        <td style="font-size:.875rem;color:#6b7280;">{{ \Carbon\Carbon::parse($rec->maintenance_date)->format('d M Y') }}</td>
                        <td style="font-size:.875rem;">{{ $rec->cost ? 'TZS '.number_format($rec->cost) : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox d-block fs-2 mb-1 opacity-25"></i>No records yet
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ════════════════════════════════════════════════════════
         STOREKEEPER DASHBOARD
    ═══════════════════════════════════════════════════════════ --}}
    @elseif($dashboardRole === 'storekeeper')

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-box-seam-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['total_parts'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Total Part Types</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef9c3;color:#a16207;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['low_stock_parts'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Low Stock Items</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="bi bi-slash-circle"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['out_of_stock'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Out of Stock</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ffedd5;color:#c2410c;"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['awaiting_parts'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">WOs Awaiting Parts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Low Stock Alerts --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                        <span class="fw-bold" style="font-size:.95rem;">Low Stock Inventory</span>
                    </div>
                    <a href="{{ route('spare-parts.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th class="ps-4">Part</th><th>Part No.</th><th>In Stock</th><th>Minimum</th><th class="pe-4">Status</th></tr></thead>
                        <tbody>
                            @forelse($lowStockParts as $part)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('spare-parts.show', $part) }}" class="fw-semibold text-decoration-none" style="color:#1c3faa;font-size:.875rem;">{{ $part->part_name }}</a>
                                </td>
                                <td style="font-size:.82rem;color:#6b7280;">{{ $part->part_number }}</td>
                                <td>
                                    <span class="fw-bold" style="color:{{ $part->quantity == 0 ? '#b91c1c' : '#a16207' }};">{{ $part->quantity }}</span>
                                </td>
                                <td style="font-size:.875rem;color:#6b7280;">{{ $part->minimum_stock }}</td>
                                <td class="pe-4">
                                    @if($part->quantity == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle d-block fs-2 mb-2 text-success opacity-50"></i>
                                All parts adequately stocked
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="col-lg-5">
            {{-- Quick Actions --}}
            <div class="card mb-3">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Quick Actions</span>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('spare-parts.create') }}" class="quick-btn quick-btn-blue">
                            <i class="bi bi-plus-circle"></i> Add Spare Part
                        </a>
                        <a href="{{ route('spare-parts.index') }}" class="quick-btn quick-btn-teal">
                            <i class="bi bi-box-seam"></i> Manage Inventory
                        </a>
                        @can('view reports')
                        <a href="{{ route('reports.inventory') }}" class="quick-btn" style="background:#7c3aed;color:#fff;">
                            <i class="bi bi-file-earmark-bar-graph"></i> Inventory Report
                        </a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- WOs Awaiting Parts --}}
            <div class="card">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split text-warning"></i>
                        <span class="fw-bold" style="font-size:.95rem;">Awaiting Parts</span>
                    </div>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;">View All</a>
                </div>
                <div class="card-body px-4 py-2">
                    @forelse($workOrdersNeedingParts as $wo)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <div class="fw-semibold" style="font-size:.875rem;">{{ $wo->bus->registration_number }}</div>
                            <div class="text-muted" style="font-size:.78rem;">{{ Str::limit($wo->title, 30) }}</div>
                        </div>
                        <a href="{{ route('work-orders.show', $wo) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;font-size:.75rem;">View</a>
                    </div>
                    @empty
                    <div class="py-3 text-center text-muted" style="font-size:.875rem;">No work orders awaiting parts</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Most Used Parts This Month --}}
    @if($recentUsage->count())
    <div class="card">
        <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
            <i class="bi bi-graph-up text-primary"></i>
            <span class="fw-bold" style="font-size:.95rem;">Most Used Parts — {{ now()->format('F Y') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th class="ps-4">Part Name</th><th>Part Number</th><th>Units Used This Month</th></tr></thead>
                <tbody>
                    @foreach($recentUsage as $usage)
                    <tr>
                        <td class="ps-4 fw-semibold" style="font-size:.875rem;">{{ $usage->part_name }}</td>
                        <td style="font-size:.82rem;color:#6b7280;">{{ $usage->part_number }}</td>
                        <td><span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:.82rem;">{{ $usage->total_used }} units</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @endif

</x-app-layout>
