<x-app-layout title="{{ $bus->registration_number }}">
    @php $sc=['active'=>['bg'=>'#dcfce7','text'=>'#15803d'],'inactive'=>['bg'=>'#f3f4f6','text'=>'#374151'],'under_repair'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'out_of_service'=>['bg'=>'#fee2e2','text'=>'#991b1b']]; $c=$sc[$bus->status]??$sc['inactive']; @endphp

    {{-- Vehicle restriction banner --}}
    @if($bus->maintenance_locked)
    <div class="d-flex align-items-start gap-3 mb-4 p-4 rounded-3" style="background:#fee2e2;border:2px solid #fca5a5;">
        <i class="bi bi-shield-x" style="color:#dc2626;font-size:1.75rem;flex-shrink:0;margin-top:.1rem;"></i>
        <div class="flex-fill">
            <div class="fw-bold mb-1" style="color:#991b1b;font-size:1rem;">
                Vehicle Out of Service — Operations Restricted
            </div>
            <div style="color:#7f1d1d;font-size:.875rem;line-height:1.55;">
                This vehicle is <strong>not allowed to operate</strong> until the required maintenance has been completed and approved.
                It cannot be dispatched or assigned to any service until all overdue preventive maintenance tasks are cleared.
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="{{ route('preventive-maintenance.index', ['bus_id' => $bus->id, 'status' => 'overdue']) }}"
                   class="btn btn-sm fw-semibold d-flex align-items-center gap-1"
                   style="background:#dc2626;color:#fff;border-radius:8px;font-size:.8rem;border:none;">
                    <i class="bi bi-exclamation-triangle"></i> View Overdue PM Tasks
                </a>
                @can('edit preventive-maintenance')
                <a href="{{ route('preventive-maintenance.index', ['bus_id' => $bus->id]) }}"
                   class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                   style="border-radius:8px;font-size:.8rem;">
                    <i class="bi bi-calendar2-check"></i> Manage PM Schedules
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">{{ $bus->registration_number }}</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buses.index') }}" style="color:#1c3faa;">Buses</a></li>
                    <li class="breadcrumb-item active text-muted">{{ $bus->registration_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @can('edit buses')
            <a href="{{ route('buses.edit', $bus) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-pencil"></i> Edit
            </a>
            @endcan
            <a href="{{ route('buses.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Top info cards --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card h-100" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-bus-front text-primary"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Bus Details</span>
                    <span class="badge ms-auto" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};font-size:.75rem;padding:.35em .7em;border-radius:6px;">
                        {{ ucfirst(str_replace('_',' ',$bus->status)) }}
                    </span>
                </div>
                <div class="card-body px-4">
                    <table class="table table-borderless mb-0" style="font-size:.875rem;">
                        <tr><td class="ps-0 text-muted py-2">Model</td><td class="pe-0 py-2 fw-semibold text-end">{{ $bus->model }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Manufacturer</td><td class="pe-0 py-2 text-end">{{ $bus->manufacturer }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Year</td><td class="pe-0 py-2 text-end">{{ $bus->year }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Mileage</td><td class="pe-0 py-2 fw-semibold text-end">{{ number_format($bus->mileage) }} km</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Driver</td><td class="pe-0 py-2 text-end">{{ $bus->driver?->name ?? '—' }}</td></tr>
                        @if($bus->notes)
                        <tr><td class="ps-0 text-muted py-2">Notes</td><td class="pe-0 py-2 text-end" style="font-size:.82rem;">{{ $bus->notes }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-broadcast text-primary"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Latest Sensor Reading</span>
                </div>
                <div class="card-body px-4">
                    @if($latestReading)
                    @php $health=$latestReading->getHealthStatus(); $hc=['normal'=>['bg'=>'#dcfce7','text'=>'#15803d'],'warning'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'critical'=>['bg'=>'#fee2e2','text'=>'#991b1b']][$health]; @endphp
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge" style="background:{{ $hc['bg'] }};color:{{ $hc['text'] }};font-size:.8rem;padding:.4em .8em;border-radius:7px;">{{ strtoupper($health) }}</span>
                        <span class="text-muted" style="font-size:.75rem;">{{ $latestReading->recorded_at->diffForHumans() }}</span>
                    </div>
                    <table class="table table-borderless mb-3" style="font-size:.875rem;">
                        <tr><td class="ps-0 text-muted py-2">Coolant Temperature</td><td class="pe-0 py-2 fw-semibold text-end">{{ $latestReading->temperature }} °C</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Oil Temperature</td><td class="pe-0 py-2 fw-semibold text-end">{{ $latestReading->oil_temperature }} °C</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Vibration</td><td class="pe-0 py-2 text-end">{{ $latestReading->vibration }} g</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Oil Pressure</td><td class="pe-0 py-2 text-end">{{ $latestReading->oil_pressure }} PSI</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Battery Voltage</td><td class="pe-0 py-2 text-end">{{ $latestReading->battery_voltage }} V</td></tr>
                    </table>
                    <a href="{{ route('iot.bus', $bus) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">View Full History</a>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-broadcast d-block" style="font-size:2rem;opacity:.2;"></i>
                        <div class="mt-2" style="font-size:.875rem;">No sensor data yet.</div>
                        <small>Connect the ESP32 device.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-graph-up-arrow text-warning"></i>
                    <span class="fw-bold" style="font-size:.95rem;">AI Risk Prediction</span>
                </div>
                <div class="card-body px-4">
                    @if($latestPrediction)
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-{{ $latestPrediction->getRiskBadgeClass() }}" style="font-size:.8rem;padding:.4em .8em;border-radius:7px;">
                            {{ strtoupper($latestPrediction->risk_level) }} RISK
                        </span>
                        <span class="text-muted" style="font-size:.75rem;">{{ $latestPrediction->predicted_at->diffForHumans() }}</span>
                    </div>
                    <table class="table table-borderless mb-3" style="font-size:.875rem;">
                        <tr><td class="ps-0 text-muted py-2">Failure Type</td><td class="pe-0 py-2 text-end">{{ $latestPrediction->predicted_failure_type ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Confidence</td><td class="pe-0 py-2 fw-semibold text-end">{{ $latestPrediction->confidence_score }}%</td></tr>
                    </table>
                    @if($latestPrediction->recommended_action)
                    <div class="alert py-2 small" style="background:#ffedd5;border:none;border-radius:9px;color:#9a3412;">
                        <i class="bi bi-lightbulb me-1"></i>{{ $latestPrediction->recommended_action }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-graph-up-arrow d-block" style="font-size:2rem;opacity:.2;"></i>
                        <div class="mt-2 mb-3" style="font-size:.875rem;">No prediction yet.</div>
                        <form method="POST" action="{{ route('predictions.run', $bus) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1" style="border-radius:8px;">
                                <i class="bi bi-play-fill"></i> Run Prediction
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card" style="border-radius:14px;">
        <div class="px-4 pt-3" style="border-bottom:1px solid #f0f3fb;">
            <ul class="nav nav-tabs border-0" id="busTabs">
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#maintenance" style="font-size:.875rem;">
                        <i class="bi bi-clipboard2-check me-1"></i>Maintenance
                        <span class="badge ms-1" style="background:#f0f3fb;color:#374151;font-size:.7rem;">{{ $bus->maintenanceRecords->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#workorders" style="font-size:.875rem;">
                        <i class="bi bi-wrench-adjustable me-1"></i>Work Orders
                        <span class="badge ms-1" style="background:#f0f3fb;color:#374151;font-size:.7rem;">{{ $bus->workOrders->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#pm" style="font-size:.875rem;">
                        <i class="bi bi-calendar2-check me-1"></i>Preventive Maintenance
                        <span class="badge ms-1" style="background:#f0f3fb;color:#374151;font-size:.7rem;">{{ $bus->preventiveMaintenances->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="maintenance">
                <div class="d-flex justify-content-end px-4 pt-3">
                    <a href="{{ route('maintenance-records.create', ['bus_id'=>$bus->id]) }}" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" style="border-radius:8px;">
                        <i class="bi bi-plus-lg"></i> Add Record
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Description</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Technician</th>
                        </tr></thead>
                        <tbody>
                            @forelse($bus->maintenanceRecords->sortByDesc('maintenance_date') as $rec)
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td class="ps-4 py-3" style="font-size:.875rem;">{{ $rec->maintenance_date->format('d M Y') }}</td>
                                <td class="py-3" style="font-size:.875rem;">{{ Str::limit($rec->description,55) }}</td>
                                <td class="py-3 pe-4" style="font-size:.875rem;">{{ $rec->technician->name }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted"><i class="bi bi-clipboard2 d-block opacity-25 fs-2"></i>No maintenance records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="workorders">
                <div class="d-flex justify-content-end px-4 pt-3">
                    <a href="{{ route('work-orders.create', ['bus_id'=>$bus->id]) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" style="border-radius:8px;">
                        <i class="bi bi-plus-lg"></i> Create Work Order
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Title</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Priority</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Assigned To</th>
                            <th class="py-3 text-end pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                        </tr></thead>
                        <tbody>
                            @forelse($bus->workOrders->sortByDesc('created_at') as $wo)
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td class="ps-4 py-3"><a href="{{ route('work-orders.show',$wo) }}" style="color:#1c3faa;font-size:.875rem;">{{ Str::limit($wo->title,45) }}</a></td>
                                <td class="py-3"><span class="badge bg-{{ $wo->getPriorityBadgeClass() }}">{{ ucfirst($wo->priority) }}</span></td>
                                <td class="py-3"><span class="badge bg-{{ $wo->getStatusBadgeClass() }}">{{ ucfirst(str_replace('_',' ',$wo->status)) }}</span></td>
                                <td class="py-3" style="font-size:.875rem;">{{ $wo->assignee?->name ?? '—' }}</td>
                                <td class="py-3 pe-4 text-end text-muted" style="font-size:.82rem;">{{ $wo->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-wrench d-block opacity-25 fs-2"></i>No work orders.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="pm">
                <div class="d-flex justify-content-end px-4 pt-3">
                    <a href="{{ route('preventive-maintenance.create', ['bus_id'=>$bus->id]) }}" class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1" style="border-radius:8px;">
                        <i class="bi bi-plus-lg"></i> Add PM Schedule
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Service Type</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Interval</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Next Due</th>
                            <th class="py-3 text-end pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                        </tr></thead>
                        <tbody>
                            @forelse($bus->preventiveMaintenances as $pm)
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td class="ps-4 py-3" style="font-size:.875rem;font-weight:600;">{{ $pm->service_type }}</td>
                                <td class="py-3 text-muted" style="font-size:.82rem;">{{ $pm->interval_days ? $pm->interval_days.' days' : ($pm->interval_km ? number_format($pm->interval_km).' km' : '—') }}</td>
                                <td class="py-3 {{ $pm->status==='overdue'?'text-danger fw-semibold':'' }}" style="font-size:.875rem;">{{ $pm->next_service_date?->format('d M Y') ?? '—' }}</td>
                                <td class="py-3 pe-4 text-end"><span class="badge bg-{{ $pm->getStatusBadgeClass() }}">{{ ucfirst($pm->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted"><i class="bi bi-calendar2 d-block opacity-25 fs-2"></i>No PM schedules.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
