<x-app-layout title="Prediction Detail">

    <div class="row g-4">

        {{-- Left column: main prediction card --}}
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cpu me-2"></i>AI Failure Prediction</h5>
                    <a href="{{ route('predictions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>All Predictions
                    </a>
                </div>
                <div class="card-body">

                    {{-- Risk banner --}}
                    <div class="alert alert-{{ $prediction->getRiskBadgeClass() }} d-flex align-items-center gap-3 mb-4">
                        <i class="bi bi-{{ $prediction->risk_level === 'high' ? 'exclamation-triangle-fill' : ($prediction->risk_level === 'medium' ? 'exclamation-circle-fill' : 'check-circle-fill') }} fs-2"></i>
                        <div>
                            <div class="fw-bold fs-5 text-uppercase">{{ $prediction->risk_level }} Risk</div>
                            <div>{{ $prediction->recommended_action }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">Bus</div>
                                <div class="fw-semibold">{{ $prediction->bus->registration_number }}</div>
                                <div class="text-muted small">{{ $prediction->bus->model }} ({{ $prediction->bus->year }})</div>
                                <a href="{{ route('buses.show', $prediction->bus) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    View Bus
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">Predicted Failure Type</div>
                                <div class="fw-semibold fs-5">{{ $prediction->predicted_failure_type ?? 'None Predicted' }}</div>
                                <div class="small text-muted mt-1">Predicted: {{ $prediction->predicted_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Confidence --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Model Confidence</span>
                            <span class="fw-bold">{{ $prediction->confidence_score }}%</span>
                        </div>
                        <div class="progress" style="height:14px">
                            <div class="progress-bar bg-{{ $prediction->getRiskBadgeClass() }}"
                                 style="width:{{ $prediction->confidence_score }}%"></div>
                        </div>
                        <div class="small text-muted mt-1">
                            Random Forest Classifier — analysed 8 vehicle health indicators
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2 flex-wrap">
                        <form method="POST" action="{{ route('predictions.run', $prediction->bus) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Re-run Prediction
                            </button>
                        </form>
                        <a href="{{ route('work-orders.create', ['bus_id' => $prediction->bus_id]) }}" class="btn btn-warning">
                            <i class="bi bi-tools me-1"></i>Create Work Order
                        </a>
                        <a href="{{ route('failure-patterns.show', $prediction->bus) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-diagram-3 me-1"></i>View Failure Patterns
                        </a>
                    </div>
                </div>
            </div>

            {{-- Feature contributions table --}}
            @if($prediction->feature_contributions)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Input Feature Values for This Prediction</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Feature</th>
                                <th class="text-end">Value</th>
                                <th class="text-end">Model Weight</th>
                                <th style="min-width:120px">Contribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prediction->feature_contributions as $fc)
                            <tr>
                                <td class="small fw-semibold">{{ $fc['feature'] }}</td>
                                <td class="text-end small text-muted">{{ $fc['value'] }}</td>
                                <td class="text-end small">{{ $fc['importance'] }}%</td>
                                <td>
                                    <div class="progress" style="height:8px">
                                        <div class="progress-bar bg-{{ $prediction->getRiskBadgeClass() }}"
                                             style="width:{{ $fc['importance'] }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- Right column: feature importance chart + model info --}}
        <div class="col-lg-5">

            {{-- Feature Importance Chart --}}
            @if($prediction->feature_contributions)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart-horizontal me-2"></i>Feature Importance (Model)</h6>
                </div>
                <div class="card-body">
                    <canvas id="featureChart" height="260"></canvas>
                </div>
                <div class="card-footer text-muted small">
                    Shows how much each feature influenced the model's decision, independent of this specific bus.
                </div>
            </div>
            @endif

            {{-- AI Model Info panel --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>AI Model Information</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Algorithm</span>
                        <span class="fw-semibold">Random Forest Classifier</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Number of Trees</span>
                        <span class="fw-semibold">150</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Input Features</span>
                        <span class="fw-semibold">8</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Risk Classes</span>
                        <span class="fw-semibold">Low · Medium · High</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Failure Types Detected</span>
                        <span class="fw-semibold">5 types</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Data Sources</span>
                        <span class="fw-semibold">IoT + Maintenance + Mileage</span>
                    </li>
                </ul>
                <div class="card-footer">
                    <div class="small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        The model is trained on sensor averages, mileage, service history, and failure frequency.
                        Higher confidence scores indicate stronger agreement across all decision trees.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($prediction->feature_contributions)
    @push('scripts')
    <script>
    const features = @json(collect($prediction->feature_contributions)->pluck('feature'));
    const weights  = @json(collect($prediction->feature_contributions)->pluck('importance'));
    const riskColor = {
        danger:  'rgba(220,53,69,0.8)',
        warning: 'rgba(255,193,7,0.8)',
        success: 'rgba(25,135,84,0.8)',
    };
    const barColor = riskColor['{{ $prediction->getRiskBadgeClass() }}'] || 'rgba(13,110,253,0.75)';

    new Chart(document.getElementById('featureChart'), {
        type: 'bar',
        data: {
            labels: features,
            datasets: [{
                label: 'Importance (%)',
                data: weights,
                backgroundColor: weights.map((_, i) => i === 0 ? barColor : barColor.replace('0.8', '0.5')),
                borderWidth: 1,
                borderColor: 'rgba(0,0,0,0.1)',
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, max: 50, ticks: { callback: v => v + '%' } },
                y: { ticks: { font: { size: 11 } } }
            }
        }
    });
    </script>
    @endpush
    @endif

</x-app-layout>
