<x-app-layout title="Daily Fleet Board">

@push('styles')
<style>
    .fleet-card {
        border-radius: 14px;
        transition: transform .15s, box-shadow .15s;
    }
    .fleet-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(28,63,170,.12);
    }
    .status-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .pulse {
        animation: pulse 1.8s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .5; transform: scale(1.25); }
    }
    .section-divider {
        display: flex; align-items: center; gap: 1rem;
        margin: 1.75rem 0 1rem;
    }
    .section-divider hr { flex: 1; border-color: #e5e7eb; margin: 0; }
    .section-divider span {
        white-space: nowrap;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        padding: .3rem .75rem;
        border-radius: 20px;
    }
    .refresh-indicator {
        display: inline-flex; align-items: center; gap: .4rem;
        font-size: .75rem; color: #6b7280;
    }
    #countdown { font-weight: 600; color: #1c3faa; }
</style>
@endpush

    {{-- ── Header ── --}}
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">
                <i class="bi bi-map me-2 text-primary"></i>Daily Bus Board
            </h4>
            <div class="text-muted small mt-1">
                Live operational status for <strong>{{ $today->format('l, d F Y') }}</strong>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="refresh-indicator">
                <span class="status-dot pulse" style="background:#16a34a;"></span>
                Auto-refreshing in <span id="countdown">60</span>s
            </span>
            <a href="{{ route('bus-assignments.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                <i class="bi bi-sliders me-1"></i>Manage Assignments
            </a>
            <button onclick="window.location.reload()" class="btn btn-sm btn-light" style="border-radius:8px;" title="Refresh now">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    {{-- ── Summary Stats ── --}}
    <div class="row g-3 mb-2">
        <div class="col-6 col-xl-3">
            <div class="card p-3 h-100" style="border-left:4px solid #16a34a;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dcfce7;color:#15803d;">
                        <i class="bi bi-bus-front-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:1.9rem;font-weight:800;line-height:1;color:#111827;">{{ $onRoad->count() }}</div>
                        <div class="mt-1 d-flex align-items-center gap-1" style="font-size:.8rem;color:#15803d;font-weight:600;">
                            <span class="status-dot pulse" style="background:#16a34a;"></span> On the Road
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card p-3 h-100" style="border-left:4px solid #d97706;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef9c3;color:#92400e;">
                        <i class="bi bi-flag-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:1.9rem;font-weight:800;line-height:1;color:#111827;">{{ $atTerminal->count() }}</div>
                        <div class="mt-1 d-flex align-items-center gap-1" style="font-size:.8rem;color:#d97706;font-weight:600;">
                            @if($atTerminal->count())<span class="status-dot pulse" style="background:#d97706;"></span>@endif
                            At Terminal
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card p-3 h-100" style="border-left:4px solid #1c3faa;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div style="font-size:1.9rem;font-weight:800;line-height:1;color:#111827;">{{ $atDepot->count() }}</div>
                        <div class="text-muted mt-1" style="font-size:.8rem;font-weight:600;">Ready to Depart</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card p-3 h-100" style="border-left:4px solid #9333ea;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#f3e8ff;color:#7c3aed;">
                        <i class="bi bi-bus-front"></i>
                    </div>
                    <div>
                        <div style="font-size:1.9rem;font-weight:800;line-height:1;color:#111827;">{{ $unassigned->count() }}</div>
                        <div class="text-muted mt-1" style="font-size:.8rem;font-weight:600;">Unassigned</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card p-3 h-100" style="border-left:4px solid #ea580c;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ffedd5;color:#c2410c;">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <div>
                        <div style="font-size:1.9rem;font-weight:800;line-height:1;color:#111827;">{{ $maintenanceSoon->count() }}</div>
                        <div class="mt-1" style="font-size:.8rem;font-weight:600;color:#c2410c;">Due Within 3 Days</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card p-3 h-100" style="border-left:4px solid #dc2626;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fee2e2;color:#991b1b;">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <div style="font-size:1.9rem;font-weight:800;line-height:1;color:#111827;">{{ $unavailable->count() }}</div>
                        <div class="text-muted mt-1" style="font-size:.8rem;font-weight:600;">Under Maintenance</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 1: ON THE ROAD ── --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-divider">
        <span style="background:#dcfce7;color:#15803d;">
            <span class="status-dot pulse me-1" style="background:#16a34a;"></span>
            On the Road — {{ $onRoad->count() }} bus{{ $onRoad->count() === 1 ? '' : 'es' }}
        </span>
        <hr>
    </div>

    @if($onRoad->isEmpty())
    <div class="card mb-2">
        <div class="card-body text-center text-muted py-4" style="font-size:.875rem;">
            <i class="bi bi-bus-front d-block fs-2 mb-2 opacity-20"></i>
            No buses currently on the road. Drivers start trips from their dashboard.
        </div>
    </div>
    @else
    <div class="row g-3 mb-1">
        @foreach($onRoad as $assignment)
        <div class="col-md-6 col-xl-4">
            <div class="card fleet-card p-0 overflow-hidden" style="border-top:3px solid #16a34a;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;background:#dcfce7;">
                            <i class="bi bi-bus-front-fill" style="font-size:1.15rem;color:#15803d;"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold" style="font-size:1.05rem;color:#111827;">{{ $assignment->bus->registration_number }}</span>
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:#dcfce7;color:#15803d;font-size:.68rem;">
                                    <span class="status-dot pulse" style="background:#16a34a;"></span> On Trip
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $assignment->bus->model }} &middot; {{ $assignment->bus->year }}</div>
                            @if($assignment->route)
                            <div class="mt-1 d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2"
                                 style="background:#dcfce7;font-size:.72rem;font-weight:700;color:#15803d;">
                                <i class="bi bi-signpost-2-fill"></i> {{ $assignment->route }}
                            </div>
                            @endif
                            <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:26px;height:26px;background:#e0e7ff;font-size:.68rem;font-weight:700;color:#3730a3;">
                                    {{ strtoupper(substr($assignment->driver->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-size:.82rem;font-weight:600;color:#374151;">{{ $assignment->driver->name }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">{{ $assignment->driver->employee_id ?? 'Driver' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-3 pb-2" style="font-size:.72rem;color:#9ca3af;">
                    <i class="bi bi-speedometer2 me-1"></i>{{ number_format($assignment->bus->mileage) }} km odometer
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 2: AT TERMINAL (arrived — awaiting next route) ── --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-divider">
        <span style="background:#fef9c3;color:#92400e;">
            <span class="status-dot pulse me-1" style="background:#d97706;"></span>
            At Terminal — Awaiting Next Route &nbsp; {{ $atTerminal->count() }} bus{{ $atTerminal->count() === 1 ? '' : 'es' }}
        </span>
        <hr>
    </div>

    @if($atTerminal->isEmpty())
    <div class="card mb-2">
        <div class="card-body text-center text-muted py-3" style="font-size:.875rem;">
            <i class="bi bi-flag d-block fs-2 mb-2 opacity-20"></i>
            No buses currently at a terminal.
        </div>
    </div>
    @else
    <div class="row g-3 mb-1">
        @foreach($atTerminal as $assignment)
        <div class="col-md-6 col-xl-4">
            <div class="card fleet-card p-0 overflow-hidden" style="border-top:3px solid #d97706;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;background:#fef9c3;">
                            <i class="bi bi-flag-fill" style="font-size:1.15rem;color:#d97706;"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold" style="font-size:1.05rem;color:#111827;">{{ $assignment->bus->registration_number }}</span>
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:#fef9c3;color:#92400e;font-size:.68rem;">
                                    <span class="status-dot pulse" style="background:#d97706;"></span> At Terminal
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $assignment->bus->model }} &middot; {{ $assignment->bus->year }}</div>

                            {{-- Current route --}}
                            @if($assignment->route)
                            <div class="mt-1 d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2"
                                 style="background:#fef9c3;font-size:.72rem;font-weight:700;color:#92400e;">
                                <i class="bi bi-signpost-2-fill"></i> {{ $assignment->route }}
                            </div>
                            @endif

                            {{-- Driver info --}}
                            <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:26px;height:26px;background:#fef9c3;font-size:.68rem;font-weight:700;color:#92400e;">
                                    {{ strtoupper(substr($assignment->driver->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-size:.82rem;font-weight:600;color:#374151;">{{ $assignment->driver->name }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">Waiting for next route</div>
                                </div>
                            </div>

                            {{-- Route reassignment form --}}
                            @can('manage bus-assignments')
                            <form method="POST" action="{{ route('bus-assignments.update-route', $assignment) }}"
                                  class="mt-3 pt-2 border-top d-flex gap-2 align-items-center">
                                @csrf @method('PATCH')
                                <select name="route" class="form-select form-select-sm" style="border-radius:8px;font-size:.78rem;" required>
                                    <option value="" disabled selected>Assign next route…</option>
                                    @foreach($busRoutes as $r)
                                    <option value="{{ $r }}" {{ $assignment->route === $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm fw-semibold flex-shrink-0"
                                        style="background:#d97706;color:#fff;border-radius:8px;white-space:nowrap;font-size:.78rem;">
                                    <i class="bi bi-send me-1"></i>Send
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="px-3 pb-2" style="font-size:.72rem;color:#9ca3af;">
                    <i class="bi bi-speedometer2 me-1"></i>{{ number_format($assignment->bus->mileage) }} km odometer
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 3: AT DEPOT (assigned but not yet on trip) ── --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-divider">
        <span style="background:#dbeafe;color:#1d4ed8;">
            <i class="bi bi-building me-1"></i>
            Ready to Depart — {{ $atDepot->count() }} bus{{ $atDepot->count() === 1 ? '' : 'es' }}
        </span>
        <hr>
    </div>

    @if($atDepot->isEmpty())
    <div class="card mb-2">
        <div class="card-body text-center text-muted py-4" style="font-size:.875rem;">
            <i class="bi bi-building d-block fs-2 mb-2 opacity-20"></i>
            No assigned buses ready to depart.
        </div>
    </div>
    @else
    <div class="row g-3 mb-1">
        @foreach($atDepot as $assignment)
        @php $colors = $assignment->bus->getStatusColors(); @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card fleet-card p-0 overflow-hidden" style="border-top:3px solid #1c3faa;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;background:#dbeafe;">
                            <i class="bi bi-bus-front-fill" style="font-size:1.15rem;color:#1d4ed8;"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold" style="font-size:1.05rem;color:#111827;">{{ $assignment->bus->registration_number }}</span>
                                <span class="badge" style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-size:.68rem;">
                                    {{ $assignment->bus->getStatusLabel() }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $assignment->bus->model }} &middot; {{ $assignment->bus->year }}</div>
                            @if($assignment->route)
                            <div class="mt-1 d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2"
                                 style="background:#e0e7ff;font-size:.72rem;font-weight:700;color:#3730a3;">
                                <i class="bi bi-signpost-2-fill"></i> {{ $assignment->route }}
                            </div>
                            @endif
                            <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:26px;height:26px;background:#e0e7ff;font-size:.68rem;font-weight:700;color:#3730a3;">
                                    {{ strtoupper(substr($assignment->driver->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-size:.82rem;font-weight:600;color:#374151;">{{ $assignment->driver->name }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">{{ $assignment->driver->employee_id ?? 'Driver' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-3 pb-2" style="font-size:.72rem;color:#9ca3af;">
                    <i class="bi bi-speedometer2 me-1"></i>{{ number_format($assignment->bus->mileage) }} km odometer
                    @if($assignment->assigner)
                    &nbsp;&middot;&nbsp;<i class="bi bi-person me-1"></i>Assigned by {{ $assignment->assigner->name ?? 'system' }}
                    @else
                    &nbsp;&middot;&nbsp;<i class="bi bi-robot me-1"></i>Auto-assigned
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 3: UNASSIGNED DISPATCHABLE (no near PM) ── --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($unassigned->isNotEmpty())
    <div class="section-divider">
        <span style="background:#f3e8ff;color:#7c3aed;">
            <i class="bi bi-question-circle me-1"></i>
            No Driver Assigned — {{ $unassigned->count() }} bus{{ $unassigned->count() === 1 ? '' : 'es' }}
        </span>
        <hr>
    </div>
    <div class="row g-3 mb-1">
        @foreach($unassigned as $bus)
        <div class="col-md-6 col-xl-4">
            <div class="card fleet-card p-0 overflow-hidden" style="border-top:3px solid #9333ea;opacity:.85;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;background:#f3e8ff;">
                            <i class="bi bi-bus-front" style="font-size:1.15rem;color:#7c3aed;"></i>
                        </div>
                        <div>
                            <div class="fw-bold mb-1" style="font-size:1.05rem;color:#111827;">{{ $bus->registration_number }}</div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $bus->model }} &middot; {{ $bus->year }}</div>
                            <span class="badge mt-1" style="background:#f3e8ff;color:#7c3aed;font-size:.68rem;">
                                <i class="bi bi-person-dash me-1"></i>No driver today
                            </span>
                        </div>
                    </div>
                </div>
                <div class="px-3 pb-2" style="font-size:.72rem;color:#9ca3af;">
                    <i class="bi bi-speedometer2 me-1"></i>{{ number_format($bus->mileage) }} km
                    &nbsp;&middot;&nbsp;Dispatchable
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 4: MAINTENANCE DUE WITHIN 3 DAYS ── --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($maintenanceSoon->isNotEmpty())
    <div class="section-divider">
        <span style="background:#ffedd5;color:#c2410c;">
            <i class="bi bi-calendar-x me-1"></i>
            Maintenance Due Soon (within 3 days) — {{ $maintenanceSoon->count() }} bus{{ $maintenanceSoon->count() === 1 ? '' : 'es' }}
        </span>
        <hr>
    </div>
    <div class="row g-3 mb-1">
        @foreach($maintenanceSoon as $bus)
        @php $nearestPm = $bus->preventiveMaintenances->first(); @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card fleet-card p-0 overflow-hidden" style="border-top:3px solid #ea580c;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;background:#ffedd5;">
                            <i class="bi bi-bus-front-fill" style="font-size:1.15rem;color:#c2410c;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold" style="font-size:1.05rem;color:#111827;">{{ $bus->registration_number }}</span>
                                <span class="badge" style="background:#ffedd5;color:#c2410c;font-size:.68rem;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>PM Due Soon
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $bus->model }} &middot; {{ $bus->year }}</div>
                            @if($nearestPm)
                            <div class="mt-2 pt-2 border-top">
                                <div style="font-size:.78rem;font-weight:600;color:#c2410c;">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $nearestPm->service_type ?? 'PM Service' }}
                                    — due {{ $nearestPm->next_service_date->format('d M Y') }}
                                    ({{ $nearestPm->next_service_date->diffForHumans() }})
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="px-3 pb-2" style="font-size:.72rem;color:#9ca3af;">
                    <i class="bi bi-speedometer2 me-1"></i>{{ number_format($bus->mileage) }} km &nbsp;&middot;&nbsp; Not assigned today
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 5: UNDER MAINTENANCE / UNAVAILABLE ── --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-divider">
        <span style="background:#fee2e2;color:#991b1b;">
            <i class="bi bi-tools me-1"></i>
            Under Maintenance / Unavailable — {{ $unavailable->count() }} bus{{ $unavailable->count() === 1 ? '' : 'es' }}
        </span>
        <hr>
    </div>

    @if($unavailable->isEmpty())
    <div class="card">
        <div class="card-body text-center text-muted py-4" style="font-size:.875rem;">
            <i class="bi bi-check-circle-fill text-success d-block fs-2 mb-2"></i>
            All buses are currently operational.
        </div>
    </div>
    @else
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Bus</th>
                        <th>Model</th>
                        <th>Status</th>
                        <th>Odometer</th>
                        <th class="pe-4">Maintenance Lock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unavailable as $bus)
                    @php $colors = $bus->getStatusColors(); @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:32px;height:32px;background:#fee2e2;">
                                    <i class="bi bi-bus-front" style="color:#991b1b;font-size:.85rem;"></i>
                                </div>
                                <span class="fw-semibold" style="font-size:.875rem;">{{ $bus->registration_number }}</span>
                            </div>
                        </td>
                        <td style="font-size:.85rem;color:#6b7280;">{{ $bus->model }} &middot; {{ $bus->year }}</td>
                        <td>
                            <span class="badge d-inline-flex align-items-center gap-1"
                                  style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-size:.73rem;">
                                {{ $bus->getStatusLabel() }}
                            </span>
                        </td>
                        <td style="font-size:.83rem;color:#6b7280;">{{ number_format($bus->mileage) }} km</td>
                        <td class="pe-4">
                            @if($bus->maintenance_locked)
                            <span class="badge" style="background:#ffe4e6;color:#9f1239;font-size:.7rem;">
                                <i class="bi bi-shield-x me-1"></i>Locked
                            </span>
                            @else
                            <span class="text-muted" style="font-size:.8rem;">—</span>
                            @endif
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
    // Auto-refresh countdown
    let seconds = 60;
    const el = document.getElementById('countdown');
    setInterval(() => {
        seconds--;
        if (seconds <= 0) {
            window.location.reload();
        } else {
            el.textContent = seconds;
        }
    }, 1000);
</script>
@endpush

</x-app-layout>
