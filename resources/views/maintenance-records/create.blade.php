<x-app-layout title="Add Maintenance Record">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Add Maintenance Record</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('maintenance-records.index') }}" style="color:#1c3faa;">Maintenance Records</a></li>
                    <li class="breadcrumb-item active text-muted">Add Record</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="border-radius:14px;">
            <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dcfce7;flex-shrink:0;">
                    <i class="bi bi-clipboard2-plus" style="color:#16a34a;font-size:1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:.95rem;color:#111827;">Maintenance Record Details</div>
                    <div class="text-muted" style="font-size:.75rem;">Log a completed maintenance activity</div>
                </div>
            </div>
            <div class="card-body px-4 py-4">
                <form method="POST" action="{{ route('maintenance-records.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Bus <span class="text-danger">*</span></label>
                            <select name="bus_id" class="form-select @error('bus_id') is-invalid @enderror" style="border-radius:9px;">
                                <option value="">Select bus…</option>
                                @foreach($buses as $bus)
                                <option value="{{ $bus->id }}" {{ old('bus_id', $selectedBus?->id)==$bus->id?'selected':'' }}>{{ $bus->registration_number }}</option>
                                @endforeach
                            </select>
                            @error('bus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Technician <span class="text-danger">*</span></label>
                            <select name="technician_id" class="form-select @error('technician_id') is-invalid @enderror" style="border-radius:9px;">
                                <option value="">Select technician…</option>
                                @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ old('technician_id')==$tech->id?'selected':'' }}>{{ $tech->name }}</option>
                                @endforeach
                            </select>
                            @error('technician_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
                            <input type="date" name="maintenance_date" class="form-control @error('maintenance_date') is-invalid @enderror"
                                   value="{{ old('maintenance_date', today()->toDateString()) }}" style="border-radius:9px;">
                            @error('maintenance_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" style="border-radius:9px;">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Parts Replaced</label>
                            <textarea name="parts_replaced" class="form-control" rows="2"
                                      placeholder="List parts replaced during this maintenance" style="border-radius:9px;">{{ old('parts_replaced') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" style="border-radius:9px;">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12 pt-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                    style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                <i class="bi bi-check-lg"></i> Save Record
                            </button>
                            <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
    </div>
</x-app-layout>
