<x-app-layout title="Daily Bus Assignments">

    {{-- ── Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">
                <i class="bi bi-calendar2-check me-2 text-primary"></i>Daily Bus Assignments
            </h4>
            <div class="text-muted small mt-1">
                Showing assignments for <strong>{{ $date->format('l, d F Y') }}</strong>
                @if($date->isToday()) <span class="badge ms-1" style="background:#dcfce7;color:#15803d;font-size:.7rem;">Today</span> @endif
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="{{ route('bus-assignments.index') }}" class="d-flex gap-2 align-items-center">
                <input type="date" name="date" class="form-control form-control-sm" style="border-radius:8px;font-size:.85rem;"
                       value="{{ $date->toDateString() }}" onchange="this.form.submit()">
            </form>
            @if(! $date->isToday())
            <a href="{{ route('bus-assignments.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                <i class="bi bi-calendar-day me-1"></i>Today
            </a>
            @endif
            @can('manage bus-assignments')
            <form method="POST" action="{{ route('bus-assignments.auto-assign') }}"
                  onsubmit="return confirm('Run auto-assignment now? This will assign all unassigned drivers (force re-run).')">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success" style="border-radius:8px;">
                    <i class="bi bi-magic me-1"></i>Auto-Assign All
                </button>
            </form>
            <button class="btn btn-sm btn-primary" style="background:#1c3faa;border-color:#1c3faa;border-radius:8px;"
                    data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="bi bi-plus-lg me-1"></i>Assign Manually
            </button>
            @endcan
        </div>
    </div>

    {{-- ── Summary Stats ── --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-person-check-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $assignments->count() }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Drivers Assigned</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;color:#92400e;"><i class="bi bi-person-dash-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $unassignedDrivers->count() }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Unassigned Drivers</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dcfce7;color:#15803d;"><i class="bi bi-bus-front-fill"></i></div>
                    <div>
                        <div style="font-size:1.75rem;font-weight:700;line-height:1;color:#111827;">{{ $availableBuses->count() }}</div>
                        <div class="text-muted mt-1" style="font-size:.82rem;">Buses Still Available</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- ── Assignments Table ── --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-list-check text-primary"></i>
                    <span class="fw-bold" style="font-size:.95rem;">Today's Assignments</span>
                    <span class="ms-auto badge bg-primary rounded-pill" style="font-size:.75rem;">{{ $assignments->count() }}</span>
                </div>
                @if($assignments->isEmpty())
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-calendar-x d-block fs-1 mb-2 opacity-25"></i>
                    <div style="font-size:.9rem;">No assignments for {{ $date->format('d M Y') }}.</div>
                    @can('manage bus-assignments')
                    <button class="btn btn-sm btn-primary mt-3" style="background:#1c3faa;border-color:#1c3faa;border-radius:8px;"
                            data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="bi bi-plus-lg me-1"></i>Assign a Bus
                    </button>
                    @endcan
                </div>
                @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Driver</th>
                                <th>Bus</th>
                                <th>Bus Status</th>
                                <th>Route</th>
                                <th>Assigned By</th>
                                <th>Notes</th>
                                @can('manage bus-assignments')<th></th>@endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $i => $assignment)
                            @php $colors = $assignment->bus->getStatusColors(); @endphp
                            <tr>
                                <td class="ps-4 text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold" style="font-size:.875rem;">{{ $assignment->driver->name }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $assignment->driver->employee_id ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width:32px;height:32px;background:#dbeafe;">
                                            <i class="bi bi-bus-front-fill" style="color:#1d4ed8;font-size:.9rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:.875rem;">{{ $assignment->bus->registration_number }}</div>
                                            <div class="text-muted" style="font-size:.73rem;">{{ $assignment->bus->model }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge d-inline-flex align-items-center gap-1"
                                          style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-size:.73rem;">
                                        @if($assignment->bus->maintenance_locked)<i class="bi bi-shield-x"></i>@endif
                                        {{ $assignment->bus->getStatusLabel() }}
                                    </span>
                                </td>
                                {{-- Route cell: show badge, and inline edit form for supervisors --}}
                                <td style="min-width:180px;">
                                    @if($assignment->route)
                                    <span class="badge mb-1" style="background:#e0e7ff;color:#3730a3;font-size:.72rem;">
                                        <i class="bi bi-signpost-2-fill me-1"></i>{{ $assignment->route }}
                                    </span>
                                    @else
                                    <span class="text-muted" style="font-size:.78rem;">No route</span>
                                    @endif
                                    @can('manage bus-assignments')
                                    <div class="mt-1">
                                        <form method="POST"
                                              action="{{ route('bus-assignments.update-route', $assignment) }}"
                                              class="d-flex gap-1 align-items-center">
                                            @csrf @method('PATCH')
                                            <select name="route" class="form-select form-select-sm"
                                                    style="font-size:.73rem;border-radius:6px;padding:.2rem .4rem;min-width:130px;">
                                                <option value="">— change route —</option>
                                                @foreach($busRoutes as $r)
                                                <option value="{{ $r }}" @selected($assignment->route === $r)>{{ $r }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm"
                                                    style="background:#1c3faa;color:#fff;border-radius:6px;font-size:.7rem;padding:.2rem .5rem;"
                                                    title="Save route change">
                                                <i class="bi bi-check2"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endcan
                                </td>
                                <td style="font-size:.82rem;">
                                    @if($assignment->assigner)
                                        <span class="text-muted">{{ $assignment->assigner->name }}</span>
                                    @else
                                        <span class="badge" style="background:#e0e7ff;color:#3730a3;font-size:.7rem;">
                                            <i class="bi bi-robot me-1"></i>Auto
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted" style="font-size:.8rem;">{{ $assignment->notes ?? '—' }}</td>
                                @can('manage bus-assignments')
                                <td>
                                    <form method="POST" action="{{ route('bus-assignments.destroy', $assignment) }}"
                                          onsubmit="return confirm('Cancel this assignment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:6px;font-size:.75rem;">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Side panel: Unassigned Drivers + Available Buses ── --}}
        <div class="col-lg-4">

            {{-- Unassigned Drivers --}}
            <div class="card mb-3">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-dash text-warning"></i>
                    <span class="fw-bold" style="font-size:.9rem;">Unassigned Drivers</span>
                    @if($unassignedDrivers->isNotEmpty())
                    <span class="ms-auto badge bg-warning text-dark rounded-pill" style="font-size:.7rem;">{{ $unassignedDrivers->count() }}</span>
                    @endif
                </div>
                @if($unassignedDrivers->isEmpty())
                <div class="card-body text-center text-muted py-3" style="font-size:.85rem;">
                    <i class="bi bi-check-circle-fill text-success d-block fs-3 mb-1"></i>
                    All drivers assigned!
                </div>
                @else
                <div class="list-group list-group-flush" style="font-size:.85rem;">
                    @foreach($unassignedDrivers as $driver)
                    <div class="list-group-item d-flex align-items-center gap-2 px-4 py-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:30px;height:30px;background:#fef3c7;color:#92400e;font-weight:700;font-size:.72rem;">
                            {{ strtoupper(substr($driver->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:.82rem;">{{ $driver->name }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $driver->employee_id ?? 'No ID' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Available Buses --}}
            <div class="card">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-bus-front text-success"></i>
                    <span class="fw-bold" style="font-size:.9rem;">Available Buses</span>
                    @if($availableBuses->isNotEmpty())
                    <span class="ms-auto badge bg-success rounded-pill" style="font-size:.7rem;">{{ $availableBuses->count() }}</span>
                    @endif
                </div>
                @if($availableBuses->isEmpty())
                <div class="card-body text-center text-muted py-3" style="font-size:.85rem;">
                    <i class="bi bi-bus-front d-block fs-3 mb-1 opacity-25"></i>
                    No dispatchable buses left.
                </div>
                @else
                <div class="list-group list-group-flush" style="font-size:.85rem;">
                    @foreach($availableBuses as $bus)
                    <div class="list-group-item d-flex align-items-center gap-2 px-4 py-2">
                        <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:30px;height:30px;background:#dcfce7;">
                            <i class="bi bi-bus-front-fill" style="color:#15803d;font-size:.85rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:.82rem;">{{ $bus->registration_number }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $bus->model }} · {{ $bus->year }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Manual Assign Modal ── --}}
    @can('manage bus-assignments')
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;border:none;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-primary"></i>Assign Bus to Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('bus-assignments.store') }}">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Driver</label>
                            <select name="driver_id" class="form-select" required style="border-radius:8px;">
                                <option value="">— Select driver —</option>
                                @foreach(App\Models\User::role('Driver')->orderBy('name')->get() as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} {{ $d->employee_id ? "({$d->employee_id})" : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Bus <span class="text-muted fw-normal">(dispatchable only)</span></label>
                            <select name="bus_id" class="form-select" required style="border-radius:8px;">
                                <option value="">— Select bus —</option>
                                @foreach(App\Models\Bus::where('status','active')->where('maintenance_locked',false)->orderBy('registration_number')->get() as $b)
                                <option value="{{ $b->id }}">{{ $b->registration_number }} — {{ $b->model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Route</label>
                            <select name="route" class="form-select" required style="border-radius:8px;">
                                <option value="">— Select route —</option>
                                @foreach($busRoutes as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Date</label>
                            <input type="date" name="assigned_date" class="form-control" required
                                   value="{{ $date->toDateString() }}" style="border-radius:8px;">
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="notes" class="form-control" rows="2" style="border-radius:8px;"
                                      placeholder="e.g. Temporary replacement…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background:#1c3faa;border-color:#1c3faa;border-radius:8px;">
                            <i class="bi bi-check2 me-1"></i>Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

</x-app-layout>
