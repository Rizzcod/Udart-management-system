<x-app-layout title="Maintenance Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-clipboard2-check me-2"></i>Maintenance Report</h5>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('reports.maintenance.export', array_merge(request()->query(), ['format'=>'xlsx'])) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.maintenance.export', array_merge(request()->query(), ['format'=>'csv'])) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.maintenance.export', array_merge(request()->query(), ['format'=>'pdf'])) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                </ul>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card p-3 mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <select name="bus_id" class="form-select form-select-sm">
                    <option value="">All Buses</option>
                    @foreach($buses as $bus)
                    <option value="{{ $bus->id }}" {{ request('bus_id') == $bus->id ? 'selected' : '' }}>{{ $bus->registration_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-sm-3">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('reports.maintenance') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>

    {{-- KPI Card --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-clipboard2-check"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-primary">{{ $totalRecords }}</div>
                    <div class="text-muted small">Total Records</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card p-3">
                <div class="fw-semibold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Maintenance Activity</div>
                <div style="position:relative;height:260px;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-3">
                <div class="fw-semibold mb-3"><i class="bi bi-pie-chart me-2 text-info"></i>Records by Bus</div>
                <div style="position:relative;height:260px;">
                    <canvas id="busChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Date</th><th>Bus</th><th>Description</th><th>Technician</th></tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                    <tr>
                        <td>{{ $rec->maintenance_date->format('d M Y') }}</td>
                        <td>{{ $rec->bus->registration_number }}</td>
                        <td>{{ Str::limit($rec->description, 70) }}</td>
                        <td>{{ $rec->technician->name }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="p-3 d-flex justify-content-end">{{ $records->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>

    @push('scripts')
    <script>
    (function () {
        const PALETTE = ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1','#fd7e14','#20c997','#6c757d','#d63384'];

        const activityData = @json($recordsByMonth);
        new Chart(document.getElementById('activityChart'), {
            type: 'bar',
            data: {
                labels: activityData.map(d => d.month),
                datasets: [{
                    label: 'Records',
                    data: activityData.map(d => d.total),
                    backgroundColor: 'rgba(13,110,253,0.2)',
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        const busData = @json($recordsByBus);
        new Chart(document.getElementById('busChart'), {
            type: 'doughnut',
            data: {
                labels: busData.map(d => d.label),
                datasets: [{ data: busData.map(d => d.total), backgroundColor: PALETTE, borderWidth: 2 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 10 } } }
            }
        });
    })();
    </script>
    @endpush

</x-app-layout>
