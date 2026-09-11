<x-app-layout title="AI Predictions">

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-danger">{{ $riskSummary['high'] ?? 0 }}</div>
                    <div class="text-muted">High Risk Buses</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-warning">{{ $riskSummary['medium'] ?? 0 }}</div>
                    <div class="text-muted">Medium Risk Buses</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-success">{{ $riskSummary['low'] ?? 0 }}</div>
                    <div class="text-muted">Low Risk Buses</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cpu me-2"></i>AI Failure Predictions</h5>
            <form method="POST" action="{{ route('predictions.run-all') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-play-circle me-1"></i>Run All Predictions
                </button>
            </form>
        </div>

        {{-- Filters --}}
        <div class="card-body border-bottom pb-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Bus</label>
                    <select name="bus_id" class="form-select form-select-sm">
                        <option value="">All buses</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" {{ request('bus_id') == $bus->id ? 'selected' : '' }}>
                                {{ $bus->registration_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Risk Level</label>
                    <select name="risk_level" class="form-select form-select-sm">
                        <option value="">All levels</option>
                        <option value="high"   {{ request('risk_level') === 'high'   ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('risk_level') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low"    {{ request('risk_level') === 'low'    ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bus</th>
                            <th>Risk Level</th>
                            <th>Predicted Failure</th>
                            <th>Confidence</th>
                            <th>Recommended Action</th>
                            <th>Predicted At</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictions as $p)
                        <tr>
                            <td>
                                <a href="{{ route('buses.show', $p->bus) }}" class="fw-semibold text-decoration-none">
                                    {{ $p->bus->registration_number }}
                                </a>
                                <div class="small text-muted">{{ $p->bus->model }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $p->getRiskBadgeClass() }} text-uppercase">
                                    {{ $p->risk_level }}
                                </span>
                            </td>
                            <td>{{ $p->predicted_failure_type ?? '—' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;min-width:60px">
                                        <div class="progress-bar bg-{{ $p->getRiskBadgeClass() }}"
                                             style="width:{{ $p->confidence_score }}%"></div>
                                    </div>
                                    <span class="small">{{ $p->confidence_score }}%</span>
                                </div>
                            </td>
                            <td class="small">{{ Str::limit($p->recommended_action, 60) }}</td>
                            <td class="small text-muted">{{ $p->predicted_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('predictions.show', $p) }}" class="btn btn-outline-primary btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No predictions yet. Click "Run All Predictions" to start.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($predictions->hasPages())
        <div class="card-footer">
            {{ $predictions->links() }}
        </div>
        @endif
    </div>

</x-app-layout>
