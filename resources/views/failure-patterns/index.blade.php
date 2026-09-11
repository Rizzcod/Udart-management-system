<x-app-layout title="Failure Pattern Analysis">

    {{-- Fleet AI Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md col-lg">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $fleetSummary['total_maintenance_events'] }}</div>
                    <div class="small text-muted">Total Maintenance Events</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $fleetSummary['recurring_failure_types'] }}</div>
                    <div class="small text-muted">Recurring Failure Types</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $highRiskBuses->count() }}</div>
                    <div class="small text-muted">High-Risk Buses</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-info">{{ $fleetSummary['events_last_30_days'] }}</div>
                    <div class="small text-muted">Events Last 30 Days</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md col-lg">
            <div class="card text-center border-danger border h-100">
                <div class="card-body py-3">
                    <div class="fw-bold text-danger">{{ $fleetSummary['top_failure'] }}</div>
                    <div class="small text-muted">Most Frequent Failure ({{ $fleetSummary['top_failure_count'] }}x)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Top failure types chart --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Top Failure Types (All Buses, All Time)</h6>
                </div>
                <div class="card-body">
                    <canvas id="failureChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly trend chart --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Monthly Maintenance Trend (12 Months)</h6>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- High-risk buses --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>High-Risk Buses</h6>
                    <span class="badge bg-secondary">≥3 failures in 90 days</span>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($highRiskBuses as $bus)
                    <a href="{{ route('failure-patterns.show', $bus) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $bus->registration_number }}</div>
                            <div class="small text-muted">{{ $bus->model }}</div>
                        </div>
                        <span class="badge bg-danger rounded-pill">{{ $bus->failure_count }} failures</span>
                    </a>
                    @empty
                    <div class="list-group-item text-center text-muted py-4">
                        <i class="bi bi-check-circle text-success me-1"></i>No high-risk buses detected.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Per-bus drill down --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-cpu me-2"></i>AI Pattern Analysis — Select a Bus</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <select id="busSelect" class="form-select">
                                <option value="">Choose a bus to analyse…</option>
                                @foreach($buses as $b)
                                <option value="{{ route('failure-patterns.show', $b) }}">
                                    {{ $b->registration_number }} — {{ $b->model }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button onclick="goToBus()" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Analyse
                            </button>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0 small">
                        <i class="bi bi-cpu me-2"></i>
                        <strong>AI Failure Pattern Analysis</strong> — drill into any bus to see:
                        <ul class="mb-0 mt-1">
                            <li>Recurring failure types with AI severity scores (0–100)</li>
                            <li>Predicted next failure date based on repeat intervals</li>
                            <li>Root cause analysis linking IoT sensor anomalies to maintenance events</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Top failure types bar chart
    const failureLabels = @json($topFailures->pluck('description'));
    const failureCounts = @json($topFailures->pluck('occurrences'));

    new Chart(document.getElementById('failureChart'), {
        type: 'bar',
        data: {
            labels: failureLabels,
            datasets: [{
                label: 'Occurrences',
                data: failureCounts,
                backgroundColor: failureCounts.map((c, i) =>
                    i === 0 ? 'rgba(220,53,69,0.85)' :
                    i <= 2  ? 'rgba(255,193,7,0.85)'  :
                              'rgba(13,110,253,0.65)'
                ),
                borderColor: 'rgba(0,0,0,0.1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { maxRotation: 25 } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Monthly trend line chart
    const trendLabels = @json($monthlyTrend->pluck('month'));
    const trendCounts = @json($monthlyTrend->pluck('count'));

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Maintenance Events',
                data: trendCounts,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#0d6efd',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    function goToBus() {
        const url = document.getElementById('busSelect').value;
        if (url) window.location.href = url;
    }
    </script>
    @endpush

</x-app-layout>
