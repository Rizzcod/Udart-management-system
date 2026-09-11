<x-app-layout title="My Work Orders Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0"><i class="bi bi-wrench-adjustable me-2"></i>My Work Orders Report</h5>
            <p class="text-muted small mb-0 mt-1">Your assigned work orders performance and history</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        style="background:#1c3faa;border-color:#1c3faa;border-radius:8px;">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('reports.technician.export', array_merge(request()->query(), ['format'=>'xlsx'])) }}">
                        <i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.technician.export', array_merge(request()->query(), ['format'=>'csv'])) }}">
                        <i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.technician.export', array_merge(request()->query(), ['format'=>'pdf'])) }}">
                        <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-primary">{{ $totalWO }}</div>
                    <div class="text-muted small">Total Assigned</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-success">{{ $completedWO }}</div>
                    <div class="text-muted small">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-warning">{{ $inProgressWO }}</div>
                    <div class="text-muted small">In Progress</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-info">{{ $avgCompletionDays > 0 ? round($avgCompletionDays, 1) . 'd' : '—' }}</div>
                    <div class="text-muted small">Avg Completion</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Chart --}}
        <div class="col-lg-5">
            <div class="card p-3 h-100">
                <div class="fw-semibold mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>Work Orders by Status</div>
                <div style="position:relative;height:220px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        {{-- Filters --}}
        <div class="col-lg-7">
            <div class="card p-3 h-100">
                <div class="fw-semibold mb-3"><i class="bi bi-funnel me-2 text-muted"></i>Filter</div>
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-sm-4">
                        <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm" style="border-radius:8px;">
                            <option value="">All Statuses</option>
                            @foreach(['assigned','awaiting_parts','in_progress','testing','completed','returned_to_service','cancelled'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$st)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small text-muted fw-semibold mb-1">From</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" style="border-radius:8px;">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small text-muted fw-semibold mb-1">To</label>
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" style="border-radius:8px;">
                    </div>
                    <div class="col-sm-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1"
                                style="background:#1c3faa;border-color:#1c3faa;border-radius:8px;"><i class="bi bi-funnel"></i></button>
                        @if(request('status') || request('from') || request('to'))
                        <a href="{{ route('reports.technician') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x"></i></a>
                        @endif
                    </div>
                </form>

                {{-- completion rate --}}
                @if($totalWO > 0)
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-muted">Completion Rate</span>
                        <span class="small fw-bold" style="color:#16a34a;">{{ round(($completedWO / $totalWO) * 100) }}%</span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:4px;">
                        <div class="progress-bar bg-success" style="width:{{ round(($completedWO / $totalWO) * 100) }}%;border-radius:4px;"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Work Orders Table --}}
    <div class="card">
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">My Work Orders</span>
            <span class="text-muted" style="font-size:.82rem;">{{ $workOrders->total() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr style="border-bottom:1px solid #f0f3fb;">
                        <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">#</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Title</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Priority</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Assigned</th>
                        <th class="py-3 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Completed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                    @php
                        $pBg  = ['critical'=>'#fee2e2','high'=>'#ffedd5','medium'=>'#dbeafe','low'=>'#f3f4f6'];
                        $pTxt = ['critical'=>'#991b1b','high'=>'#c2410c','medium'=>'#1e40af','low'=>'#374151'];
                    @endphp
                    <tr style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-4 py-3" style="font-size:.85rem;color:#6b7280;">#{{ $wo->id }}</td>
                        <td class="py-3">
                            <a href="{{ route('work-orders.show', $wo) }}" class="fw-semibold text-decoration-none" style="font-size:.875rem;color:#111827;">
                                {{ Str::limit($wo->title, 50) }}
                            </a>
                        </td>
                        <td class="py-3" style="font-size:.875rem;color:#374151;">{{ $wo->bus->registration_number }}</td>
                        <td class="py-3">
                            <span class="badge" style="background:{{ $pBg[$wo->priority] ?? '#f3f4f6' }};color:{{ $pTxt[$wo->priority] ?? '#374151' }};font-size:.72rem;padding:.3em .6em;border-radius:6px;">
                                {{ strtoupper($wo->priority) }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background:#f0f3fb;color:#374151;font-size:.72rem;padding:.3em .6em;border-radius:6px;">
                                {{ ucwords(str_replace('_', ' ', $wo->status)) }}
                            </span>
                        </td>
                        <td class="py-3" style="font-size:.82rem;color:#6b7280;">{{ $wo->created_at->format('d M Y') }}</td>
                        <td class="py-3 pe-4" style="font-size:.82rem;color:#6b7280;">{{ $wo->completion_date?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-list-check d-block" style="font-size:2.5rem;opacity:.2;"></i>
                        <div class="mt-2">No work orders assigned yet.</div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workOrders->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $workOrders->firstItem() }}–{{ $workOrders->lastItem() }} of {{ $workOrders->total() }}</div>
            {{ $workOrders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

@push('scripts')
<script>
(function () {
    const data = @json($byStatus);
    const PALETTE = ['#1c3faa','#ea580c','#0891b2','#16a34a','#7c3aed','#dc2626','#6b7280','#0d9488','#d97706'];
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.label.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase())),
            datasets: [{ data: data.map(d => d.total), backgroundColor: PALETTE, borderWidth: 2, hoverOffset: 6 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '55%',
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 10 } } }
        }
    });
})();
</script>
@endpush

</x-app-layout>
