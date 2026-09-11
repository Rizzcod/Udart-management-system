<x-app-layout title="Bus Performance Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-bus-front me-2"></i>Bus Performance Report</h5>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('reports.fleet.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.fleet.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.fleet.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                </ul>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-bus-front"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-primary">{{ $buses->count() }}</div>
                    <div class="text-muted small">Total Buses</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-success">{{ $statusCounts['active'] }}</div>
                    <div class="text-muted small">Active</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-wrench"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-warning">{{ $statusCounts['under_repair'] }}</div>
                    <div class="text-muted small">Under Repair</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-slash-circle"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-secondary">{{ $statusCounts['inactive'] }}</div>
                    <div class="text-muted small">Inactive</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card p-3">
                <div class="fw-semibold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>Work Orders per Bus</div>
                <div style="position:relative;height:280px;">
                    <canvas id="woChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-3">
                <div class="fw-semibold mb-3"><i class="bi bi-pie-chart me-2 text-success"></i>Bus Status</div>
                <div style="position:relative;height:220px;">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-3" style="font-size:.82rem;">
                    <span><span class="badge bg-success">&nbsp;&nbsp;</span> Active ({{ $statusCounts['active'] }})</span>
                    <span><span class="badge bg-warning text-dark">&nbsp;&nbsp;</span> Repair ({{ $statusCounts['under_repair'] }})</span>
                    <span><span class="badge bg-secondary">&nbsp;&nbsp;</span> Inactive ({{ $statusCounts['inactive'] }})</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MTTR chart --}}
    <div class="card p-3 mb-4">
        <div class="fw-semibold mb-3">
            <i class="bi bi-clock-history me-2 text-danger"></i>Mean Time To Repair (MTTR) per Bus
            <span class="text-muted fw-normal small ms-1">— lower is better</span>
        </div>
        <div style="position:relative;height:240px;">
            <canvas id="mttrChart"></canvas>
        </div>
    </div>

    {{-- Info --}}
    <div class="alert alert-info py-2 mb-3 small">
        <strong>MTTR</strong> = Mean Time To Repair (avg days to complete a repair) &nbsp;|&nbsp;
        <strong>Total WOs</strong> = all work orders ever raised for the bus
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Bus</th><th>Model</th><th class="text-end">Mileage (km)</th>
                        <th>Status</th><th class="text-center">Total WOs</th>
                        <th class="text-center">Completed</th><th class="text-center">Maint. Records</th>
                        <th class="text-center">MTTR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fleetStats as $stat)
                    <tr>
                        <td><a href="{{ route('buses.show', $stat['bus']) }}" class="fw-semibold text-decoration-none">{{ $stat['bus']->registration_number }}</a></td>
                        <td>{{ $stat['bus']->model }}</td>
                        <td class="text-end">{{ number_format($stat['bus']->mileage) }}</td>
                        <td>
                            @php $statusMap = ['active'=>'success','inactive'=>'secondary','under_repair'=>'warning']; @endphp
                            <span class="badge bg-{{ $statusMap[$stat['bus']->status] ?? 'secondary' }}">{{ str_replace('_',' ',$stat['bus']->status) }}</span>
                        </td>
                        <td class="text-center">{{ $stat['total_wo'] }}</td>
                        <td class="text-center">{{ $stat['completed_wo'] }}</td>
                        <td class="text-center">{{ $stat['total_records'] }}</td>
                        <td class="text-center">
                            @if($stat['mttr'] > 0)
                                <span class="badge {{ $stat['mttr'] > 5 ? 'bg-danger' : ($stat['mttr'] > 2 ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $stat['mttr'] }} days
                                </span>
                            @else —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        const fleet = @json($fleetChartData);
        const labels = fleet.map(s => s.label);

        new Chart(document.getElementById('woChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total WOs',
                        data: fleet.map(s => s.total_wo),
                        backgroundColor: 'rgba(13,110,253,0.25)',
                        borderColor: '#0d6efd',
                        borderWidth: 2,
                        borderRadius: 4,
                    },
                    {
                        label: 'Completed WOs',
                        data: fleet.map(s => s.completed_wo),
                        backgroundColor: 'rgba(25,135,84,0.25)',
                        borderColor: '#198754',
                        borderWidth: 2,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { boxWidth: 14, font: { size: 12 } } } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Under Repair', 'Inactive'],
                datasets: [{
                    data: [{{ $statusCounts['active'] }}, {{ $statusCounts['under_repair'] }}, {{ $statusCounts['inactive'] }}],
                    backgroundColor: ['#198754', '#ffc107', '#6c757d'],
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: { legend: { display: false } }
            }
        });

        new Chart(document.getElementById('mttrChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'MTTR (days)',
                    data: fleet.map(s => s.mttr),
                    backgroundColor: fleet.map(s => s.mttr > 5 ? 'rgba(220,53,69,0.3)' : s.mttr > 2 ? 'rgba(255,193,7,0.35)' : 'rgba(25,135,84,0.25)'),
                    borderColor: fleet.map(s => s.mttr > 5 ? '#dc3545' : s.mttr > 2 ? '#e6a800' : '#198754'),
                    borderWidth: 2,
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' MTTR: ' + ctx.parsed.y + ' days' } }
                },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Days' } } }
            }
        });
    })();
    </script>
    @endpush

</x-app-layout>
