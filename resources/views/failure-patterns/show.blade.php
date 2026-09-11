<x-app-layout title="Failure Patterns — {{ $bus->registration_number }}">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">{{ $bus->registration_number }}</h4>
            <div class="text-muted">{{ $bus->model }} · {{ $bus->manufacturer }} · {{ $bus->year }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('buses.show', $bus) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-bus-front me-1"></i>Bus Detail
            </a>
            <a href="{{ route('failure-patterns.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>All Patterns
            </a>
        </div>
    </div>

    @if($patterns->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No maintenance records found for this bus. Failure pattern analysis requires at least one maintenance record.
    </div>
    @else

    {{-- AI Summary Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $patterns->sum('occurrences') }}</div>
                    <div class="small text-muted">Total Maintenance Events</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $patterns->count() }}</div>
                    <div class="small text-muted">Distinct Issue Types</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $patterns->where('occurrences', '>', 1)->count() }}</div>
                    <div class="small text-muted">Recurring Issues</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body py-3">
                    @php
                        $upcomingPatterns = $patterns->filter(fn($p) => $p['days_until_next'] !== null && $p['days_until_next'] <= 30 && $p['days_until_next'] >= 0);
                    @endphp
                    <div class="fs-2 fw-bold {{ $upcomingPatterns->isNotEmpty() ? 'text-danger' : 'text-success' }}">
                        {{ $upcomingPatterns->count() }}
                    </div>
                    <div class="small text-muted">Failures Predicted ≤30 Days</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Frequency Chart --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Failure Frequency by Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="patternChart" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- Severity Score Chart --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-cpu me-2"></i>AI Severity Score per Failure Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="severityChart" height="220"></canvas>
                </div>
                <div class="card-footer text-muted small">
                    Score (0–100) computed from frequency, recency, and repeat interval. Higher = more urgent.
                </div>
            </div>
        </div>
    </div>

    {{-- Predicted Next Failure Alerts --}}
    @php $soonPatterns = $patterns->filter(fn($p) => $p['days_until_next'] !== null && $p['days_until_next'] <= 30); @endphp
    @if($soonPatterns->isNotEmpty())
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h6 class="mb-0"><i class="bi bi-alarm me-2"></i>AI Predicted Upcoming Failures (within 30 days)</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($soonPatterns->sortBy('days_until_next') as $p)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $p['description'] }}</div>
                    <div class="small text-muted">
                        Based on {{ $p['occurrences'] }} past occurrences · avg interval {{ $p['avg_interval_days'] }} days · last on {{ $p['last_occurrence'] }}
                    </div>
                </div>
                <div class="text-end">
                    @if($p['days_until_next'] <= 0)
                        <span class="badge bg-danger fs-6">OVERDUE</span>
                    @elseif($p['days_until_next'] <= 7)
                        <span class="badge bg-danger">In {{ $p['days_until_next'] }} days</span>
                    @else
                        <span class="badge bg-warning text-dark">In {{ $p['days_until_next'] }} days</span>
                    @endif
                    <div class="small text-muted mt-1">~{{ $p['predicted_next_date'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Full Pattern Table --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Failure Pattern Details — AI Enhanced</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Issue / Failure Type</th>
                        <th class="text-center">Count</th>
                        <th class="text-center">Avg Interval</th>
                        <th>Last Occurred</th>
                        <th>AI Predicted Next</th>
                        <th class="text-center">Severity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patterns as $p)
                    @php
                        $sev = $p['severity_label'];
                        $sevColor = match($sev) {
                            'critical' => 'danger',
                            'high'     => 'warning',
                            'medium'   => 'info',
                            default    => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $p['description'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $p['occurrences'] >= 3 ? 'danger' : ($p['occurrences'] >= 2 ? 'warning text-dark' : 'secondary') }}">
                                {{ $p['occurrences'] }}
                            </span>
                        </td>
                        <td class="text-center text-muted small">
                            {{ $p['avg_interval_days'] ? $p['avg_interval_days'].' days' : '—' }}
                        </td>
                        <td class="small">{{ $p['last_occurrence'] ?? '—' }}</td>
                        <td class="small">
                            @if($p['predicted_next_date'])
                                <span class="{{ $p['days_until_next'] !== null && $p['days_until_next'] <= 7 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    {{ $p['predicted_next_date'] }}
                                </span>
                                @if($p['days_until_next'] !== null)
                                    <br>
                                    @if($p['days_until_next'] <= 0)
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="text-muted">({{ $p['days_until_next'] }}d away)</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $sevColor }} {{ $sev === 'high' ? 'text-dark' : '' }}">
                                {{ ucfirst($sev) }} ({{ $p['severity_score'] }})
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Root Cause Analysis --}}
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-diagram-3 me-2"></i>Root Cause Analysis
                <span class="badge bg-secondary ms-2">IoT-Linked</span>
            </h6>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Each maintenance event is cross-referenced with sensor readings within ±3 days.
                When a sensor was in Warning or Critical state around the time of a maintenance event,
                it is flagged here as a likely root cause contributor.
            </p>
            @if($rootCauses->isEmpty())
                <div class="text-center text-muted py-3">No maintenance records to analyse.</div>
            @else
                @php $linked = $rootCauses->where('has_sensor_link', true); @endphp
                @if($linked->isEmpty())
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>No abnormal sensor readings detected near maintenance events for this bus.
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Maintenance Event</th>
                                <th>Date</th>
                                <th>Cost</th>
                                <th>Sensor Link</th>
                                <th>Abnormal Sensors at Time of Failure</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rootCauses as $rc)
                            <tr class="{{ $rc['has_sensor_link'] ? 'table-warning' : '' }}">
                                <td class="fw-semibold small">{{ $rc['description'] }}</td>
                                <td class="small text-muted">{{ $rc['date'] }}</td>
                                <td class="small">{{ $rc['cost'] ? 'TZS '.number_format($rc['cost']) : '—' }}</td>
                                <td>
                                    @if($rc['has_sensor_link'])
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Linked</span>
                                    @else
                                        <span class="badge bg-success">Clean</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if($rc['has_sensor_link'])
                                        @foreach($rc['sensor_alerts'] as $sa)
                                            <div class="mb-1">
                                                <span class="text-muted">{{ $sa['recorded_at'] }}</span>
                                                @foreach($sa['alerts'] as $alert)
                                                    <span class="badge bg-{{ $alert['severity'] === 'critical' ? 'danger' : 'warning text-dark' }} me-1">
                                                        {{ $alert['sensor'] }}: {{ $alert['value'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No abnormal readings detected</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    const labels   = @json($patterns->pluck('description'));
    const counts   = @json($patterns->pluck('occurrences'));
    const scores   = @json($patterns->pluck('severity_score'));
    const sevLabels = @json($patterns->pluck('severity_label'));

    const colorMap = { critical: 'rgba(220,53,69,0.8)', high: 'rgba(255,193,7,0.8)', medium: 'rgba(13,202,240,0.8)', low: 'rgba(108,117,125,0.6)' };

    // Frequency chart
    new Chart(document.getElementById('patternChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Occurrences',
                data: counts,
                backgroundColor: sevLabels.map(s => colorMap[s] || 'rgba(108,117,125,0.6)'),
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { ticks: { maxRotation: 30 } }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Severity score chart
    new Chart(document.getElementById('severityChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'AI Severity Score (0-100)',
                data: scores,
                backgroundColor: sevLabels.map(s => colorMap[s] || 'rgba(108,117,125,0.6)'),
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { maxRotation: 30 } },
                y: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } }
            }
        }
    });
    </script>
    @endpush

    @endif

</x-app-layout>
