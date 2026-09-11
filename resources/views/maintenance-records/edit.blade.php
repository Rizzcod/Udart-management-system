<x-app-layout title="Edit Maintenance Record">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Edit Maintenance Record</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('maintenance-records.index') }}" style="color:#1c3faa;">Maintenance Records</a></li>
                    <li class="breadcrumb-item active text-muted">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        {{-- Left: summary card --}}
        <div class="col-lg-3">
            <div class="card text-center" style="border-radius:14px;overflow:hidden;">
                <div style="height:5px;background:#16a34a;"></div>
                <div class="card-body py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:64px;height:64px;background:#dcfce7;">
                        <i class="bi bi-clipboard2-check" style="font-size:1.5rem;color:#16a34a;"></i>
                    </div>
                    <div class="fw-bold mb-1" style="font-size:.95rem;color:#111827;">{{ $maintenanceRecord->bus->registration_number }}</div>
                    <div class="text-muted" style="font-size:.8rem;">{{ $maintenanceRecord->maintenance_date->format('d M Y') }}</div>
                </div>
                <div class="card-footer bg-transparent px-3 py-3" style="border-top:1px solid #f0f3fb;">
                    <table class="table table-borderless mb-0" style="font-size:.8rem;">
                        <tr><td class="ps-0 text-muted py-1 text-start">Technician</td><td class="pe-0 py-1 text-end fw-semibold">{{ $maintenanceRecord->technician->name }}</td></tr>
                        <tr style="border-bottom:none;"><td class="ps-0 text-muted py-1 text-start">Recorded</td><td class="pe-0 py-1 text-end">{{ $maintenanceRecord->created_at->format('d M Y') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: edit form --}}
        <div class="col-lg-9">
            <div class="card" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dcfce7;flex-shrink:0;">
                        <i class="bi bi-pencil-square" style="color:#16a34a;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Update Maintenance Record</div>
                        <div class="text-muted" style="font-size:.75rem;">Changes take effect immediately</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('maintenance-records.update', $maintenanceRecord) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Bus <span class="text-danger">*</span></label>
                                <select name="bus_id" class="form-select" style="border-radius:9px;">
                                    @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" {{ old('bus_id', $maintenanceRecord->bus_id)==$bus->id?'selected':'' }}>{{ $bus->registration_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Technician <span class="text-danger">*</span></label>
                                <select name="technician_id" class="form-select" style="border-radius:9px;">
                                    @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ old('technician_id', $maintenanceRecord->technician_id)==$tech->id?'selected':'' }}>{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
                                <input type="date" name="maintenance_date" class="form-control"
                                       value="{{ old('maintenance_date', $maintenanceRecord->maintenance_date->toDateString()) }}" style="border-radius:9px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="3" style="border-radius:9px;">{{ old('description', $maintenanceRecord->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Parts Replaced</label>
                                <textarea name="parts_replaced" class="form-control" rows="2" style="border-radius:9px;">{{ old('parts_replaced', $maintenanceRecord->parts_replaced) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Notes</label>
                                <textarea name="notes" class="form-control" rows="2" style="border-radius:9px;">{{ old('notes', $maintenanceRecord->notes) }}</textarea>
                            </div>
                            <div class="col-12 pt-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                                <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
