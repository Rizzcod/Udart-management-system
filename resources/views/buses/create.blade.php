<x-app-layout title="Register Bus">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Register New Bus</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buses.index') }}" style="color:#1c3faa;">Buses</a></li>
                    <li class="breadcrumb-item active text-muted">Register</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('buses.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="border-radius:14px;">
            <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dbeafe;flex-shrink:0;">
                    <i class="bi bi-bus-front" style="color:#1c3faa;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:.95rem;color:#111827;">Bus Information</div>
                    <div class="text-muted" style="font-size:.75rem;">Enter the details of the new bus to add it to the fleet</div>
                </div>
            </div>
            <div class="card-body px-4 py-4">
                <form method="POST" action="{{ route('buses.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Registration Number <span class="text-danger">*</span></label>
                            <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror"
                                   value="{{ old('registration_number') }}" placeholder="UDART-011" style="border-radius:9px;">
                            @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" style="border-radius:9px;">
                                <option value="active" {{ old('status','active')==='active' ? 'selected':'' }}>Active</option>
                                <option value="inactive" {{ old('status')==='inactive' ? 'selected':'' }}>Inactive</option>
                                <option value="under_repair" {{ old('status')==='under_repair' ? 'selected':'' }}>Under Repair</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Model <span class="text-danger">*</span></label>
                            <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
                                   value="{{ old('model') }}" placeholder="Yutong ZK6120HGS" style="border-radius:9px;">
                            @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Manufacturer <span class="text-danger">*</span></label>
                            <input type="text" name="manufacturer" class="form-control @error('manufacturer') is-invalid @enderror"
                                   value="{{ old('manufacturer') }}" placeholder="Yutong" style="border-radius:9px;">
                            @error('manufacturer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                                   value="{{ old('year', date('Y')) }}" min="2000" max="{{ date('Y')+1 }}" style="border-radius:9px;">
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Mileage (km) <span class="text-danger">*</span></label>
                            <input type="number" name="mileage" class="form-control @error('mileage') is-invalid @enderror"
                                   value="{{ old('mileage', 0) }}" min="0" style="border-radius:9px;">
                            @error('mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Assign Driver</label>
                            <select name="driver_id" class="form-select" style="border-radius:9px;">
                                <option value="">No driver assigned</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected':'' }}>{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" style="border-radius:9px;">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12 pt-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                    style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                <i class="bi bi-check-lg"></i> Register Bus
                            </button>
                            <a href="{{ route('buses.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
    </div>
</x-app-layout>
