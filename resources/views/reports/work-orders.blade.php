<x-app-layout title="Work Orders Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-wrench-adjustable me-2"></i>Work Orders Report</h5>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        style="background:#ea580c;border-color:#ea580c;color:#fff;border-radius:8px;">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('reports.work-orders.export', array_merge(request()->query(), ['format'=>'xlsx'])) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.work-orders.export', array_merge(request()->query(), ['format'=>'csv'])) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.work-orders.export', array_merge(request()->query(), ['format'=>'pdf'])) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                </ul>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card p-3 mb-4" style="border-radius:12px;border:1px solid #e5e7eb;">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                <select name="status" class="form-select form-select-sm" style="border-radius:8px;">
                    <option value="">All Statuses</option>
                    @foreach(['reported','assessment','pending_approval','assigned','awaiting_parts','in_progress','testing','completed','returned_to_service'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $st)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small text-muted fw-semibold mb-1">Priority</label>
                <select name="priority" class="form-select form-select-sm" style="border-radius:8px;">
                    <option value="">All</option>
                    <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small text-muted fw-semibold mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" style="border-radius:8px;">
            </div>
            <div class="col-sm-2">
                <label class="form-label small text-muted fw-semibold mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" style="border-radius:8px;">
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-sm btn-primary" type="submit" style="border-radius:8px;background:#1c3faa;border-color:#1c3faa;">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('reports.work-orders') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">Reset</a>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#fff7ed;">
                        <i class="bi bi-list-check" style="font-size:1.3rem;color:#ea580c;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#ea580c;line-height:1;">{{ $totalWO }}</div>
                        <div class="text-muted small">Total Work Orders</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#eff6ff;">
                        <i class="bi bi-hourglass-split" style="font-size:1.3rem;color:#1c3faa;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#1c3faa;line-height:1;">{{ $openWO }}</div>
                        <div class="text-muted small">Open / In Progress</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#f0fdf4;">
                        <i class="bi bi-check-circle" style="font-size:1.3rem;color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#16a34a;line-height:1;">{{ $completedWO }}</div>
                        <div class="text-muted small">Completed</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#fef2f2;">
                        <i class="bi bi-exclamation-triangle" style="font-size:1.3rem;color:#dc2626;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#dc2626;line-height:1;">{{ $highPriority }}</div>
                        <div class="text-muted small">High Priority Open</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card p-4" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">Work Orders by Status</h6>
                <div style="position:relative;min-height:240px;">
                    <canvas id="statusChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card p-4" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">By Priority</h6>
                <div style="position:relative;min-height:240px;">
                    <canvas id="priorityChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">Monthly Trend</h6>
                <div style="position:relative;min-height:240px;">
                    <canvas id="monthChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Work Orders Table --}}
    <div class="card" style="border-radius:12px;border:1px solid #e5e7eb;">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                <span class="fw-bold" style="font-size:.95rem;">Work Orders List</span>
                <span class="text-muted small">{{ $workOrders->total() }} records</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr style="border-bottom:1px solid #f0f3fb;">
                            <th class="ps-4 py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Title / Bus</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Reported By</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Priority</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                            <th class="py-2 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrders as $wo)
                        @php
                            $priColor = match($wo->priority) {
                                'high'   => ['bg'=>'#fef2f2','text'=>'#dc2626'],
                                'medium' => ['bg'=>'#fff7ed','text'=>'#ea580c'],
                                default  => ['bg'=>'#f0fdf4','text'=>'#16a34a'],
                            };
                        @endphp
                        <tr style="border-bottom:1px solid #f9fafb;">
                            <td class="ps-4 py-3">
                                <div class="fw-semibold" style="font-size:.875rem;color:#111827;">{{ $wo->title }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $wo->bus->registration_number ?? '—' }}</div>
                            </td>
                            <td class="py-3" style="font-size:.875rem;">{{ $wo->reporter->name ?? '—' }}</td>
                            <td class="py-3">
                                <span class="badge" style="background:{{ $priColor['bg'] }};color:{{ $priColor['text'] }};font-size:.72rem;font-weight:600;border-radius:6px;">
                                    {{ ucfirst($wo->priority) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:.72rem;font-weight:600;border-radius:6px;">
                                    {{ ucwords(str_replace('_', ' ', $wo->status)) }}
                                </span>
                            </td>
                            <td class="py-3 pe-4" style="font-size:.82rem;color:#6b7280;">{{ $wo->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-wrench-adjustable d-block" style="font-size:2.5rem;opacity:.2;"></i>
                                <div class="mt-2">No work orders found.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($workOrders->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
                <div class="text-muted" style="font-size:.82rem;">
                    Showing {{ $workOrders->firstItem() }}–{{ $workOrders->lastItem() }} of {{ $workOrders->total() }}
                </div>
                {{ $workOrders->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

@push('scripts')
<script>
(function () {
    var byStatus   = @json($byStatus);
    var byPriority = @json($byPriority);
    var byMonth    = @json($byMonth);

    var statusColors = {
        'reported':'#6b7280','assessment':'#1c3faa','pending_approval':'#d97706',
        'assigned':'#7c3aed','awaiting_parts':'#ea580c','in_progress':'#0891b2',
        'testing':'#ca8a04','completed':'#16a34a','returned_to_service':'#15803d'
    };

    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: byStatus.map(s => s.label.replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase())),
            datasets: [{
                label: 'Work Orders',
                data: byStatus.map(s => s.total),
                backgroundColor: byStatus.map(s => (statusColors[s.label] || '#6b7280') + '33'),
                borderColor:     byStatus.map(s => statusColors[s.label] || '#6b7280'),
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    new Chart(document.getElementById('priorityChart'), {
        type: 'doughnut',
        data: {
            labels: byPriority.map(p => p.label.charAt(0).toUpperCase() + p.label.slice(1)),
            datasets: [{
                data: byPriority.map(p => p.total),
                backgroundColor: ['#16a34a','#d97706','#dc2626'],
                hoverOffset: 6,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '60%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
    });

    new Chart(document.getElementById('monthChart'), {
        type: 'line',
        data: {
            labels: byMonth.map(m => m.month),
            datasets: [{
                label: 'Work Orders',
                data: byMonth.map(m => m.total),
                borderColor: '#ea580c',
                backgroundColor: '#ea580c22',
                fill: true,
                tension: 0.35,
                pointRadius: 4,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
}());
</script>
@endpush

</x-app-layout>
