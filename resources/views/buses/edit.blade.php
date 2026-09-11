<x-app-layout title="Edit Bus">
    @php
        $sc = [
            'active'       => ['bg'=>'#dcfce7','text'=>'#15803d','border'=>'#16a34a'],
            'inactive'     => ['bg'=>'#f3f4f6','text'=>'#374151','border'=>'#9ca3af'],
            'under_repair' => ['bg'=>'#ffedd5','text'=>'#c2410c','border'=>'#ea580c'],
            'out_of_service' => ['bg'=>'#fee2e2','text'=>'#991b1b','border'=>'#dc2626'],
        ];
        $c = $sc[$bus->status] ?? $sc['inactive'];
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Edit Bus</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buses.index') }}" style="color:#1c3faa;">Buses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buses.show', $bus) }}" style="color:#1c3faa;">{{ $bus->registration_number }}</a></li>
                    <li class="breadcrumb-item active text-muted">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('buses.show', $bus) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    {{-- Maintenance restriction warning --}}
    @if($bus->maintenance_locked)
    <div class="d-flex align-items-start gap-3 mb-4 p-3 rounded-3" style="background:#fee2e2;border:1.5px solid #fca5a5;">
        <i class="bi bi-shield-x" style="color:#dc2626;font-size:1.3rem;flex-shrink:0;margin-top:.1rem;"></i>
        <div style="font-size:.85rem;">
            <div class="fw-bold" style="color:#991b1b;">Vehicle Maintenance Lock Active</div>
            <div style="color:#7f1d1d;margin-top:.2rem;">
                This vehicle is restricted from operations due to overdue preventive maintenance.
                Status cannot be set to <strong>Active</strong> and no driver can be assigned until all overdue PM tasks are completed.
            </div>
            <a href="{{ route('preventive-maintenance.index', ['bus_id' => $bus->id, 'status' => 'overdue']) }}"
               class="btn btn-sm mt-2" style="background:#dc2626;color:#fff;border-radius:7px;font-size:.78rem;font-weight:600;border:none;">
                <i class="bi bi-exclamation-triangle me-1"></i>View Overdue PM Tasks
            </a>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- Left: summary card --}}
        <div class="col-lg-3">
            <div class="card text-center" style="border-radius:14px;overflow:hidden;">
                <div style="height:5px;background:{{ $c['border'] }};"></div>
                <div class="card-body py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle fw-bold text-white"
                         style="width:64px;height:64px;background:#1c3faa;font-size:.9rem;letter-spacing:1px;">
                        {{ strtoupper(substr($bus->registration_number,-3)) }}
                    </div>
                    <div class="fw-bold mb-1" style="font-size:.95rem;color:#111827;">{{ $bus->registration_number }}</div>
                    <span class="badge mb-2 d-inline-flex align-items-center gap-1" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};font-size:.75rem;padding:.35em .8em;border-radius:6px;">
                        @if($bus->maintenance_locked)<i class="bi bi-shield-x"></i>@endif
                        {{ ucwords(str_replace('_',' ',$bus->status)) }}
                    </span>
                    <div class="text-muted" style="font-size:.8rem;">{{ $bus->model }}</div>
                </div>
                <div class="card-footer bg-transparent px-3 py-3" style="border-top:1px solid #f0f3fb;">
                    <table class="table table-borderless mb-0" style="font-size:.8rem;">
                        <tr><td class="ps-0 text-muted py-1 text-start">Manufacturer</td><td class="pe-0 py-1 text-end fw-semibold">{{ $bus->manufacturer }}</td></tr>
                        <tr><td class="ps-0 text-muted py-1 text-start">Year</td><td class="pe-0 py-1 text-end">{{ $bus->year }}</td></tr>
                        <tr><td class="ps-0 text-muted py-1 text-start">Mileage</td><td class="pe-0 py-1 text-end">{{ number_format($bus->mileage) }} km</td></tr>
                        <tr style="border-bottom:none;"><td class="ps-0 text-muted py-1 text-start">Driver</td><td class="pe-0 py-1 text-end">{{ $bus->driver?->name ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: edit form --}}
        <div class="col-lg-9">
            <div class="card" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dbeafe;flex-shrink:0;">
                        <i class="bi bi-pencil-square" style="color:#1c3faa;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Update Bus Details</div>
                        <div class="text-muted" style="font-size:.75rem;">Changes take effect immediately after saving</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('buses.update', $bus) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Registration Number <span class="text-danger">*</span></label>
                                <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror"
                                       value="{{ old('registration_number', $bus->registration_number) }}" style="border-radius:9px;">
                                @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                                @if($bus->maintenance_locked)
                                    {{-- Show current status read-only; hidden input carries the value --}}
                                    <input type="hidden" name="status" value="{{ $bus->status }}">
                                    <div class="form-control d-flex align-items-center gap-2"
                                         style="border-radius:9px;background:#fef2f2;color:#991b1b;border-color:#fca5a5;cursor:not-allowed;">
                                        <i class="bi bi-shield-x"></i>
                                        {{ ucwords(str_replace('_',' ',$bus->status)) }}
                                        <span class="ms-auto" style="font-size:.72rem;">(locked by PM system)</span>
                                    </div>
                                    <div class="form-text text-danger" style="font-size:.75rem;">
                                        Complete all overdue PM tasks to unlock this vehicle.
                                    </div>
                                @else
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" style="border-radius:9px;">
                                        @foreach(['active','inactive','under_repair'] as $s)
                                        <option value="{{ $s }}" {{ old('status',$bus->status)===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Model <span class="text-danger">*</span></label>
                                <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
                                       value="{{ old('model', $bus->model) }}" style="border-radius:9px;">
                                @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Manufacturer <span class="text-danger">*</span></label>
                                <input type="text" name="manufacturer" class="form-control @error('manufacturer') is-invalid @enderror"
                                       value="{{ old('manufacturer', $bus->manufacturer) }}" style="border-radius:9px;">
                                @error('manufacturer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Year <span class="text-danger">*</span></label>
                                <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                                       value="{{ old('year', $bus->year) }}" min="2000" max="{{ date('Y')+1 }}" style="border-radius:9px;">
                                @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Mileage (km) <span class="text-danger">*</span></label>
                                <input type="number" name="mileage" class="form-control @error('mileage') is-invalid @enderror"
                                       value="{{ old('mileage', $bus->mileage) }}" min="0" style="border-radius:9px;">
                                @error('mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Assign Driver</label>
                                @if($bus->maintenance_locked)
                                    <input type="hidden" name="driver_id" value="">
                                    <div class="form-control" style="border-radius:9px;background:#fef2f2;color:#991b1b;border-color:#fca5a5;cursor:not-allowed;font-size:.875rem;">
                                        <i class="bi bi-lock me-1"></i> Blocked — overdue maintenance
                                    </div>
                                @elseif(in_array($bus->status, ['out_of_service','inactive']))
                                    <input type="hidden" name="driver_id" value="">
                                    <div class="form-control" style="border-radius:9px;background:#f9fafb;color:#6b7280;border-color:#d1d5db;cursor:not-allowed;font-size:.875rem;">
                                        <i class="bi bi-person-slash me-1"></i> N/A — bus is {{ str_replace('_', ' ', $bus->status) }}
                                    </div>
                                @else
                                    <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror" style="border-radius:9px;">
                                        <option value="">No driver</option>
                                        @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" {{ old('driver_id',$bus->driver_id)==$driver->id?'selected':'' }}>{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('driver_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" style="border-radius:9px;">{{ old('notes', $bus->notes) }}</textarea>
                            </div>
                            <div class="col-12 pt-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                                <a href="{{ route('buses.show', $bus) }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
