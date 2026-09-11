<x-app-layout title="Inventory Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Inventory Report</h5>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('reports.inventory.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.inventory.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.inventory.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                </ul>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-boxes"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-primary">{{ $parts->count() }}</div>
                    <div class="text-muted small">Total Part Types</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-danger">{{ $lowStock->count() }}</div>
                    <div class="text-muted small">Low Stock Items</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="fs-4 fw-bold text-success">TZS {{ number_format($totalValue) }}</div>
                    <div class="text-muted small">Total Inventory Value</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card p-3">
                <div class="fw-semibold mb-3"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Top 5 Most Used Parts</div>
                <div style="position:relative;height:240px;">
                    <canvas id="topUsedChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card p-3">
                <div class="fw-semibold mb-3"><i class="bi bi-pie-chart me-2 text-warning"></i>Stock Status Distribution</div>
                <div style="position:relative;height:200px;">
                    <canvas id="stockStatusChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-3" style="font-size:.82rem;">
                    <span><span class="badge" style="background:#198754">&nbsp;&nbsp;</span> In Stock ({{ $parts->count() - $lowStock->count() }})</span>
                    <span><span class="badge" style="background:#dc3545">&nbsp;&nbsp;</span> Low Stock ({{ $lowStock->count() }})</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Value chart --}}
    <div class="card p-3 mb-4">
        <div class="fw-semibold mb-3"><i class="bi bi-currency-dollar me-2 text-success"></i>Inventory Value by Part (Top 10)</div>
        <div style="position:relative;height:240px;">
            <canvas id="valueChart"></canvas>
        </div>
    </div>

    {{-- Table + side panel --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card p-3">
                <div class="fw-semibold mb-2">All Parts</div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr><th>Part Name</th><th class="text-center">Stock</th><th class="text-center">Min</th><th class="text-center">Times Used</th><th class="text-end">Value (TZS)</th></tr>
                        </thead>
                        <tbody>
                            @foreach($parts as $part)
                            <tr class="{{ $part->isLowStock() ? 'table-warning' : '' }}">
                                <td>
                                    {{ $part->part_name }}
                                    @if($part->isLowStock())<span class="badge bg-danger ms-1">Low</span>@endif
                                </td>
                                <td class="text-center">{{ $part->quantity }}</td>
                                <td class="text-center">{{ $part->minimum_stock }}</td>
                                <td class="text-center">{{ $part->usages_sum_quantity_used ?? 0 }}</td>
                                <td class="text-end">{{ number_format($part->quantity * $part->unit_price) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @if($lowStock->count())
            <div class="card p-3 mb-3">
                <div class="fw-semibold mb-2 text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock Alert</div>
                @foreach($lowStock as $part)
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <div class="small">{{ $part->part_name }}</div>
                    <span class="badge bg-danger">{{ $part->quantity }} left</span>
                </div>
                @endforeach
            </div>
            @endif
            <div class="card p-3">
                <div class="fw-semibold mb-2"><i class="bi bi-trophy me-1 text-warning"></i>Top 5 Most Used</div>
                @foreach($topUsed as $i => $part)
                <div class="d-flex align-items-center gap-2 py-1 border-bottom">
                    <span class="badge rounded-circle" style="background:#1c3faa;min-width:22px;">{{ $i+1 }}</span>
                    <div class="small flex-grow-1">{{ $part->part_name }}</div>
                    <span class="badge bg-primary">{{ $part->usages_sum_quantity_used ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        const PALETTE = ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1','#fd7e14','#20c997','#6c757d','#d63384'];

        const topUsed = @json($topUsedChart);
        new Chart(document.getElementById('topUsedChart'), {
            type: 'bar',
            data: {
                labels: topUsed.map(p => p.label),
                datasets: [{
                    label: 'Quantity Used',
                    data: topUsed.map(p => p.quantity),
                    backgroundColor: PALETTE.slice(0,5).map(c => c + 'bb'),
                    borderColor: PALETTE.slice(0,5),
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        new Chart(document.getElementById('stockStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['In Stock', 'Low Stock'],
                datasets: [{
                    data: [{{ $parts->count() - $lowStock->count() }}, {{ $lowStock->count() }}],
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed } }
                }
            }
        });

        const valueData = @json($valueChart);
        new Chart(document.getElementById('valueChart'), {
            type: 'bar',
            data: {
                labels: valueData.map(p => p.label),
                datasets: [{
                    label: 'Value (TZS)',
                    data: valueData.map(p => Number(p.value)),
                    backgroundColor: 'rgba(25,135,84,0.2)',
                    borderColor: '#198754',
                    borderWidth: 2,
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' TZS ' + Number(ctx.parsed.y).toLocaleString() } }
                },
                scales: { y: { beginAtZero: true, ticks: { callback: v => 'TZS ' + Number(v).toLocaleString() } } }
            }
        });
    })();
    </script>
    @endpush

</x-app-layout>
