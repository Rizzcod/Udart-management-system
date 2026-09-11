<x-app-layout title="Driver Dashboard">

    {{-- ── Welcome Banner ── --}}
    <div class="welcome-card mb-4 d-flex align-items-center justify-content-between">
        <div style="position:relative;z-index:1;">
            @php
                $hour = now()->hour;
                $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                $user = auth()->user();
            @endphp
            <div style="font-size:1.55rem;font-weight:700;line-height:1.2;" class="mb-1">
                {{ $greeting }}, {{ explode(' ', $user->name)[0] }}!
            </div>
            <div style="color:rgba(255,255,255,.78);font-size:.9rem;" class="mb-2">
                UDART Fleet Management &nbsp;•&nbsp;
                <span class="badge" style="background:rgba(255,255,255,.2);font-weight:500;font-size:.78rem;">Driver</span>
                @if($todayAssignment)
                &nbsp;•&nbsp; {{ $todayAssignment->bus->registration_number }}
                @elseif($staticBusAssignment)
                &nbsp;•&nbsp; {{ $staticBusAssignment->registration_number }}
                @endif
            </div>
            <div style="color:rgba(255,255,255,.58);font-size:.78rem;">
                <i class="bi bi-clock me-1"></i>{{ now()->format('l, d F Y') }}
                @if($user->last_login_at)
                &nbsp;•&nbsp; Last login: {{ $user->last_login_at->diffForHumans() }}
                @endif
            </div>
        </div>
        <div class="welcome-avatar d-none d-md-flex">
            <i class="bi bi-person-fill" style="font-size:2rem;"></i>
        </div>
    </div>

    {{-- ── Today's Assignment Banner ── --}}
    @if($todayAssignment)
    @php
        $assignedBus  = $todayAssignment->bus;
        $aColors      = $assignedBus->getStatusColors();
        $busStatus    = $assignedBus->status;
        $onTrip       = $busStatus === 'on_trip';
        $arrived      = $busStatus === 'arrived';
        $todayRoute   = $todayAssignment->route;
        $mapsUrl      = $todayRoute ? 'https://www.google.com/maps/search/' . urlencode($todayRoute) : null;
        $bannerBorder = $onTrip ? '#16a34a' : ($arrived ? '#d97706' : '#1c3faa');
        $bannerBg     = $onTrip ? '#f0fdf4' : ($arrived ? '#fefce8' : '#f0f4ff');
    @endphp
    <div class="card mb-4" style="border-left:4px solid {{ $bannerBorder }};background:{{ $bannerBg }};">
        <div class="card-body px-4 py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                <div class="d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px;height:52px;background:{{ $onTrip ? '#dcfce7' : ($arrived ? '#fef9c3' : '#dbeafe') }};">
                        <i class="bi bi-{{ $arrived ? 'flag-fill' : 'bus-front-fill' }}"
                           style="font-size:1.5rem;color:{{ $onTrip ? '#15803d' : ($arrived ? '#d97706' : '#1d4ed8') }};"></i>
                    </div>
                    <div>
                        <div style="font-size:.72rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Today's Assignment</div>
                        <div style="font-size:1.35rem;font-weight:800;color:#111827;line-height:1.1;">{{ $assignedBus->registration_number }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $assignedBus->model }} &middot; {{ $assignedBus->year }}</div>
                        @if($todayRoute)
                        <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                            <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2"
                                 style="background:{{ $onTrip ? '#dcfce7' : ($arrived ? '#fef9c3' : '#e0e7ff') }};font-size:.78rem;font-weight:700;color:{{ $onTrip ? '#15803d' : ($arrived ? '#92400e' : '#3730a3') }};">
                                <i class="bi bi-signpost-2-fill"></i> {{ $todayRoute }}
                            </div>
                            @if($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                               class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2 text-decoration-none"
                               style="background:#e0f2fe;font-size:.72rem;font-weight:600;color:#0369a1;">
                                <i class="bi bi-map"></i> Maps
                            </a>
                            @endif
                        </div>
                        @else
                        <div class="text-muted mt-1" style="font-size:.75rem;"><i class="bi bi-exclamation-circle me-1"></i>No route assigned yet</div>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Status badge --}}
                    @if($onTrip)
                    <span class="badge d-inline-flex align-items-center gap-1"
                          style="background:#dcfce7;color:#15803d;font-size:.8rem;padding:.4rem .85rem;">
                        <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;animation:pulse-dot 1.8s ease-in-out infinite;"></span>
                        On the Road
                    </span>
                    @elseif($arrived)
                    <span class="badge d-inline-flex align-items-center gap-1"
                          style="background:#fef9c3;color:#92400e;font-size:.8rem;padding:.4rem .85rem;">
                        <span style="width:8px;height:8px;border-radius:50%;background:#d97706;display:inline-block;animation:pulse-dot 1.8s ease-in-out infinite;"></span>
                        At Terminal
                    </span>
                    @else
                    <span class="badge d-inline-flex align-items-center gap-1"
                          style="background:{{ $aColors['bg'] }};color:{{ $aColors['text'] }};font-size:.8rem;padding:.4rem .85rem;">
                        @if($assignedBus->maintenance_locked)<i class="bi bi-shield-x"></i>@endif
                        {{ $assignedBus->getStatusLabel() }}
                    </span>
                    @endif

                    {{-- Trip actions --}}
                    @if($onTrip)
                    <form method="POST" action="{{ route('driver.trip.arrived') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm fw-semibold"
                                style="background:#d97706;color:#fff;border-radius:8px;font-size:.82rem;">
                            <i class="bi bi-flag-fill me-1"></i>Arrived at Terminal
                        </button>
                    </form>
                    @elseif($arrived)
                    <form method="POST" action="{{ route('driver.trip.start') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm fw-semibold"
                                style="background:#1c3faa;color:#fff;border-radius:8px;font-size:.82rem;">
                            <i class="bi bi-arrow-repeat me-1"></i>Start Return Trip
                        </button>
                    </form>
                    <form method="POST" action="{{ route('driver.trip.end') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm fw-semibold"
                                style="background:#6b7280;color:#fff;border-radius:8px;font-size:.82rem;">
                            <i class="bi bi-stop-circle me-1"></i>End Shift
                        </button>
                    </form>
                    @elseif($assignedBus->isDispatchable() && $todayRoute)
                    <form method="POST" action="{{ route('driver.trip.start') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm fw-semibold"
                                style="background:#16a34a;color:#fff;border-radius:8px;font-size:.82rem;">
                            <i class="bi bi-play-circle me-1"></i>Start Route
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('driver.my-assignment') }}" class="btn btn-sm btn-outline-secondary"
                       style="border-radius:8px;font-size:.8rem;">
                        <i class="bi bi-eye me-1"></i>Full Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    @push('styles')
    <style>
    @keyframes pulse-dot {
        0%,100%{opacity:1;transform:scale(1);}
        50%{opacity:.5;transform:scale(1.3);}
    }
    </style>
    @endpush
    @elseif($staticBusAssignment)
    {{-- Static bus.driver_id assignment — admin set this via Edit Bus, not the daily scheduler --}}
    <div class="card mb-4" style="border-left:4px solid #1c3faa;background:#f0f4ff;">
        <div class="card-body px-4 py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px;height:52px;background:#dbeafe;">
                        <i class="bi bi-bus-front-fill" style="font-size:1.5rem;color:#1d4ed8;"></i>
                    </div>
                    <div>
                        <div style="font-size:.72rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Assigned Bus</div>
                        <div style="font-size:1.35rem;font-weight:800;color:#111827;line-height:1.1;">{{ $staticBusAssignment->registration_number }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $staticBusAssignment->model }} &middot; {{ $staticBusAssignment->year }}</div>
                        <div class="mt-1 text-warning" style="font-size:.75rem;">
                            <i class="bi bi-exclamation-circle me-1"></i>No route scheduled for today — contact your supervisor for daily dispatch.
                        </div>
                    </div>
                </div>
                <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:.8rem;padding:.4rem .85rem;">
                    {{ $staticBusAssignment->getStatusLabel() }}
                </span>
            </div>
        </div>
    </div>
    @else
    <div class="alert mb-4 d-flex align-items-center gap-2"
         style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;border-radius:12px;">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <span style="font-size:.875rem;">
            <strong>No bus assigned yet</strong> for today, {{ now()->format('d M Y') }}.
            Assignments are generated automatically each morning. Contact your supervisor if needed.
        </span>
        <a href="{{ route('driver.my-assignment') }}" class="btn btn-sm ms-auto"
           style="background:#92400e;color:#fff;border-radius:8px;font-size:.78rem;white-space:nowrap;">
            Check Status
        </a>
    </div>
    @endif

    {{-- ── Stat Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-card-list"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['total'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Total Reports</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;color:#92400e;"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['pending'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Awaiting Action</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ffedd5;color:#c2410c;"><i class="bi bi-wrench-adjustable"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['in_progress'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">In Progress</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dcfce7;color:#15803d;"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $stats['completed'] }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- ── Today's Bus Details + Mileage Log Form ── --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-bus-front text-primary"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Today's Bus Details</span>
                    @if($todayAssignment)
                    <span class="ms-auto badge" style="background:#dbeafe;color:#1d4ed8;font-size:.72rem;">
                        {{ now()->format('d M Y') }}
                    </span>
                    @endif
                </div>
                <div class="card-body px-4">
                    @if($todayAssignment)
                        @php
                            $bus    = $todayAssignment->bus;
                            $colors = $bus->getStatusColors();
                        @endphp
                        <div class="text-center mb-4">
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-2"
                                 style="width:64px;height:64px;background:#dbeafe;">
                                <i class="bi bi-bus-front-fill" style="font-size:1.8rem;color:#1d4ed8;"></i>
                            </div>
                            <div class="fw-bold" style="font-size:1.2rem;">{{ $bus->registration_number }}</div>
                            <div class="text-muted small">{{ $bus->model }} &middot; {{ $bus->manufacturer }}</div>
                            @if($todayAssignment->route)
                            <div class="mt-2 d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2"
                                 style="background:#e0e7ff;font-size:.75rem;font-weight:700;color:#3730a3;">
                                <i class="bi bi-signpost-2-fill"></i> {{ $todayAssignment->route }}
                            </div>
                            @endif
                        </div>

                        <table class="table table-borderless mb-4" style="font-size:.875rem;">
                            <tr><td class="ps-0 text-muted">Year</td><td class="text-end pe-0">{{ $bus->year }}</td></tr>
                            <tr><td class="ps-0 text-muted">Status</td><td class="text-end pe-0">
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-size:.75rem;">
                                    @if($bus->maintenance_locked)<i class="bi bi-shield-x"></i>@endif
                                    {{ $bus->getStatusLabel() }}
                                </span>
                            </td></tr>
                            <tr><td class="ps-0 text-muted">Odometer</td><td class="text-end pe-0 fw-semibold">{{ number_format($bus->mileage) }} km</td></tr>
                            <tr><td class="ps-0 text-muted">Assigned By</td><td class="text-end pe-0" style="font-size:.82rem;">
                                @if($todayAssignment->assigner)
                                    {{ $todayAssignment->assigner->name }}
                                @else
                                    <span class="badge" style="background:#e0e7ff;color:#3730a3;font-size:.7rem;">
                                        <i class="bi bi-robot me-1"></i>Auto
                                    </span>
                                @endif
                            </td></tr>
                        </table>

                        @if(! $bus->isDispatchable())
                        <div class="alert py-2 mb-3 d-flex align-items-center gap-2"
                             style="background:#ffe4e6;border:1px solid #fecdd3;color:#9f1239;font-size:.82rem;border-radius:10px;">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <span>Bus <strong>restricted from dispatch</strong> ({{ $bus->getStatusLabel() }}). Contact your supervisor.</span>
                        </div>
                        @endif

                        {{-- Mileage log form --}}
                        <div class="border-top pt-3">
                            <div class="fw-semibold mb-2" style="font-size:.85rem;color:#374151;">
                                <i class="bi bi-speedometer2 me-1 text-primary"></i>Log Today's Trip
                            </div>
                            @if(session('success'))
                            <div class="alert alert-success py-2 small mb-2">{{ session('success') }}</div>
                            @endif
                            <form method="POST" action="{{ route('driver.mileage-log.store') }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Trip Date</label>
                                    <input type="date" name="trip_date" class="form-control form-control-sm @error('trip_date') is-invalid @enderror"
                                           value="{{ old('trip_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                                    @error('trip_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-1">KM Traveled</label>
                                        <input type="number" name="km_traveled" class="form-control form-control-sm @error('km_traveled') is-invalid @enderror"
                                               value="{{ old('km_traveled') }}" placeholder="e.g. 120" min="1">
                                        @error('km_traveled')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-1">Odometer (end)</label>
                                        <input type="number" name="odometer_reading" class="form-control form-control-sm @error('odometer_reading') is-invalid @enderror"
                                               value="{{ old('odometer_reading', $bus->mileage) }}" min="{{ $bus->mileage }}">
                                        @error('odometer_reading')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Notes <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="text" name="notes" class="form-control form-control-sm"
                                           value="{{ old('notes') }}" placeholder="e.g. heavy traffic on Morogoro Rd">
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100" style="background:#1c3faa;border-color:#1c3faa;">
                                    <i class="bi bi-check2 me-1"></i>Submit Mileage Log
                                </button>
                            </form>
                        </div>
                    @elseif($staticBusAssignment)
                        @php
                            $bus    = $staticBusAssignment;
                            $colors = $bus->getStatusColors();
                        @endphp
                        <div class="alert py-2 mb-3 d-flex align-items-center gap-2"
                             style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;font-size:.82rem;border-radius:10px;">
                            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                            <span>Bus assigned via vehicle records. Ask your supervisor to schedule today's route on the Bus Assignments page to enable trip actions.</span>
                        </div>
                        <div class="text-center mb-4">
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-2"
                                 style="width:64px;height:64px;background:#dbeafe;">
                                <i class="bi bi-bus-front-fill" style="font-size:1.8rem;color:#1d4ed8;"></i>
                            </div>
                            <div class="fw-bold" style="font-size:1.2rem;">{{ $bus->registration_number }}</div>
                            <div class="text-muted small">{{ $bus->model }} &middot; {{ $bus->manufacturer }}</div>
                        </div>
                        <table class="table table-borderless mb-0" style="font-size:.875rem;">
                            <tr><td class="ps-0 text-muted py-2">Year</td><td class="text-end pe-0">{{ $bus->year }}</td></tr>
                            <tr><td class="ps-0 text-muted py-2">Status</td><td class="text-end pe-0">
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-size:.75rem;">
                                    {{ $bus->getStatusLabel() }}
                                </span>
                            </td></tr>
                            <tr><td class="ps-0 text-muted py-2">Odometer</td><td class="text-end pe-0 fw-semibold">{{ number_format($bus->mileage) }} km</td></tr>
                        </table>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3"
                                 style="width:64px;height:64px;background:#fef3c7;">
                                <i class="bi bi-bus-front" style="font-size:1.8rem;color:#92400e;"></i>
                            </div>
                            <div class="fw-semibold mb-1" style="color:#374151;">No Bus Assigned Today</div>
                            <div style="font-size:.83rem;">The system assigns buses automatically each morning at 04:30. Contact your supervisor if you expected an assignment.</div>
                        </div>
                    @endif
                </div>
                @if($todayAssignment || $staticBusAssignment)
                <div class="card-footer bg-transparent px-4 py-3">
                    <a href="{{ route('driver.report-breakdown') }}" class="quick-btn" style="background:#dc2626;color:#fff;">
                        <i class="bi bi-exclamation-triangle-fill"></i> Report Breakdown
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Right column: Quick Actions + Mileage Log History + Recent Reports ── --}}
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Quick Actions</span>
                </div>
                <div class="card-body px-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('driver.report-breakdown') }}" class="quick-btn" style="background:#dc2626;color:#fff;">
                                <i class="bi bi-exclamation-triangle"></i> Report Breakdown
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('driver.my-reports') }}" class="quick-btn quick-btn-blue">
                                <i class="bi bi-card-list"></i> My Reports
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('messages.index') }}" class="quick-btn quick-btn-purple">
                                <i class="bi bi-chat-dots"></i> Messages
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('announcements.index') }}" class="quick-btn" style="background:#d97706;color:#fff;">
                                <i class="bi bi-megaphone"></i> Announcements
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent mileage logs (by driver, across all buses) --}}
            <div class="card mb-3">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-speedometer2 text-success"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Recent Mileage Logs</span>
                    <a href="{{ route('driver.my-activity') }}" class="ms-auto btn btn-sm btn-outline-secondary"
                       style="border-radius:8px;font-size:.75rem;">View All</a>
                </div>
                @if($recentMileageLogs->isEmpty())
                <div class="card-body text-center text-muted py-3">
                    <i class="bi bi-speedometer d-block fs-2 mb-1 opacity-25"></i>
                    <span style="font-size:.875rem;">No mileage logs yet. Log your first trip after completing a route.</span>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size:.825rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Bus</th>
                                <th>KM</th>
                                <th class="pe-4">Odometer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMileageLogs as $log)
                            <tr>
                                <td class="ps-4">{{ $log->trip_date->format('d M Y') }}</td>
                                <td class="text-muted" style="font-size:.78rem;">{{ $log->bus->registration_number ?? '—' }}</td>
                                <td><span class="fw-semibold text-success">+{{ number_format($log->km_traveled) }} km</span></td>
                                <td class="pe-4 text-muted">{{ number_format($log->odometer_reading) }} km</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Recent breakdown reports --}}
            <div class="card">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary"></i>
                        <span class="fw-bold" style="font-size:.95rem;">Recent Reports</span>
                    </div>
                    <a href="{{ route('driver.my-reports') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.8rem;">View All</a>
                </div>
                @if($recentReports->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        <i class="bi bi-inbox d-block fs-2 mb-1 opacity-25"></i>
                        <span style="font-size:.875rem;">No breakdown reports yet.</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Title</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentReports as $report)
                                <tr>
                                    <td class="ps-4" style="font-size:.875rem;">{{ Str::limit($report->title, 38) }}</td>
                                    <td><span class="badge bg-{{ $report->getPriorityBadgeClass() }}">{{ ucfirst($report->priority) }}</span></td>
                                    <td><span class="badge bg-{{ $report->getStatusBadgeClass() }}">{{ $report->getStatusLabel() }}</span></td>
                                    <td style="color:#9ca3af;font-size:.78rem;">{{ $report->created_at->format('d M Y') }}</td>
                                    <td><a href="{{ route('driver.show-report', $report) }}" class="btn btn-sm btn-outline-primary" style="border-radius:6px;font-size:.75rem;">View</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
