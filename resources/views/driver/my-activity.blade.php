<x-app-layout title="My Activity Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2"></i>My Activity Report</h5>
            <p class="text-muted small mb-0 mt-1">Your trip history and breakdown reports</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        style="background:#0d9488;color:#fff;border-color:#0d9488;border-radius:8px;">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('driver.my-activity.export', array_merge(request()->query(), ['format'=>'xlsx'])) }}">
                        <i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('driver.my-activity.export', array_merge(request()->query(), ['format'=>'csv'])) }}">
                        <i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('driver.my-activity.export', array_merge(request()->query(), ['format'=>'pdf'])) }}">
                        <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                </ul>
            </div>
            <a href="{{ route('driver.dashboard') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon" style="background:#f0fdfa;color:#0d9488;width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                    <i class="bi bi-calendar2-check"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold" style="color:#0d9488;">{{ $totalLogs }}</div>
                    <div class="text-muted small">Total Trips</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-speedometer"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-primary">{{ number_format($totalKm) }}</div>
                    <div class="text-muted small">Total KM</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($thisMonthKm) }}</div>
                    <div class="text-muted small">This Month KM</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-danger">{{ $breakdownCount }}</div>
                    <div class="text-muted small">Breakdowns Filed</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Monthly KM Chart --}}
        <div class="col-lg-8">
            <div class="card p-3 h-100">
                <div class="fw-semibold mb-3"><i class="bi bi-bar-chart me-2" style="color:#0d9488;"></i>Monthly KM Traveled</div>
                <div style="position:relative;height:240px;">
                    <canvas id="kmChart"></canvas>
                </div>
            </div>
        </div>
        {{-- Date Filter --}}
        <div class="col-lg-4">
            <div class="card p-3 h-100">
                <div class="fw-semibold mb-3"><i class="bi bi-funnel me-2 text-muted"></i>Filter Period</div>
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-semibold mb-1">From Date</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" style="border-radius:8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-semibold mb-1">To Date</label>
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" style="border-radius:8px;">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm flex-grow-1" style="background:#0d9488;color:#fff;border-color:#0d9488;border-radius:8px;">
                            <i class="bi bi-funnel me-1"></i>Apply
                        </button>
                        @if(request('from') || request('to'))
                        <a href="{{ route('driver.my-activity') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Trip Logs Table --}}
    <div class="card mb-4">
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;"><i class="bi bi-list-ul me-2"></i>Trip Logs</span>
            <span class="text-muted" style="font-size:.82rem;">{{ $logs->total() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr style="border-bottom:1px solid #f0f3fb;">
                        <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                        <th class="py-3 text-end" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">KM Traveled</th>
                        <th class="py-3 text-end" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Odometer</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Route</th>
                        <th class="py-3 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-4 py-3 fw-semibold" style="font-size:.875rem;color:#111827;">{{ $log->trip_date->format('d M Y') }}</td>
                        <td class="py-3" style="font-size:.875rem;color:#374151;">{{ $log->bus->registration_number }}</td>
                        <td class="py-3 text-end fw-semibold" style="font-size:.875rem;color:#0d9488;">{{ number_format($log->km_traveled) }} km</td>
                        <td class="py-3 text-end" style="font-size:.82rem;color:#6b7280;">{{ number_format($log->odometer_reading) }} km</td>
                        <td class="py-3" style="font-size:.82rem;color:#6b7280;">{{ $log->route ?? '—' }}</td>
                        <td class="py-3 pe-4" style="font-size:.82rem;color:#6b7280;">{{ Str::limit($log->notes ?? '', 40) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-speedometer d-block" style="font-size:2.5rem;opacity:.2;"></i>
                        <div class="mt-2">No trip logs recorded yet.</div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</div>
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    {{-- Breakdown Reports --}}
    @if($breakdowns->count())
    <div class="card">
        <div class="px-4 py-3 border-bottom">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>My Breakdown Reports</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr style="border-bottom:1px solid #f0f3fb;">
                        <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Issue</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Priority</th>
                        <th class="py-3 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($breakdowns as $wo)
                    <tr style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-4 py-3" style="font-size:.875rem;color:#374151;">{{ $wo->created_at->format('d M Y') }}</td>
                        <td class="py-3" style="font-size:.875rem;">{{ $wo->bus->registration_number }}</td>
                        <td class="py-3" style="font-size:.875rem;">
                            <a href="{{ route('driver.show-report', $wo) }}" class="text-decoration-none" style="color:#111827;">{{ Str::limit($wo->title, 50) }}</a>
                        </td>
                        <td class="py-3">
                            @php $pc=['critical'=>['#fee2e2','#991b1b'],'high'=>['#ffedd5','#c2410c'],'medium'=>['#dbeafe','#1e40af'],'low'=>['#f3f4f6','#374151']]; $c=$pc[$wo->priority]??$pc['low']; @endphp
                            <span class="badge" style="background:{{ $c[0] }};color:{{ $c[1] }};font-size:.72rem;padding:.3em .6em;border-radius:6px;">{{ strtoupper($wo->priority) }}</span>
                        </td>
                        <td class="py-3 pe-4">
                            <span class="badge" style="background:#f0f3fb;color:#374151;font-size:.72rem;padding:.3em .6em;border-radius:6px;">{{ ucwords(str_replace('_',' ',$wo->status)) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

@push('scripts')
<script>
(function () {
    const km = @json($kmByMonth);
    new Chart(document.getElementById('kmChart'), {
        type: 'bar',
        data: {
            labels: km.map(d => d.month),
            datasets: [{
                label: 'KM Traveled',
                data: km.map(d => d.total_km),
                backgroundColor: 'rgba(13,148,136,0.2)',
                borderColor: '#0d9488',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() + ' km' } } }
        }
    });
})();
</script>
@endpush

</x-app-layout>
