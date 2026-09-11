<x-app-layout title="Add Spare Part">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Add Spare Part</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('spare-parts.index') }}" style="color:#1c3faa;">Spare Parts</a></li>
                    <li class="breadcrumb-item active text-muted">Add Part</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('spare-parts.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="border-radius:14px;">
            <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#f0f3fb;flex-shrink:0;">
                    <i class="bi bi-box-seam" style="color:#374151;font-size:1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:.95rem;color:#111827;">Part Details</div>
                    <div class="text-muted" style="font-size:.75rem;">Add a new spare part to the inventory</div>
                </div>
            </div>
            <div class="card-body px-4 py-4">
                <form method="POST" action="{{ route('spare-parts.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-8">
                            <label class="form-label fw-semibold small">Part Name <span class="text-danger">*</span></label>
                            <input type="text" name="part_name" class="form-control @error('part_name') is-invalid @enderror"
                                   value="{{ old('part_name') }}" placeholder="e.g. Air Filter" style="border-radius:9px;">
                            @error('part_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Part Number <span class="text-danger">*</span></label>
                            <input type="text" name="part_number" class="form-control @error('part_number') is-invalid @enderror"
                                   value="{{ old('part_number') }}" placeholder="SP-013" style="border-radius:9px;">
                            @error('part_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 0) }}" min="0" style="border-radius:9px;">
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Min. Stock <span class="text-danger">*</span></label>
                            <input type="number" name="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror"
                                   value="{{ old('minimum_stock', 5) }}" min="0" style="border-radius:9px;">
                            @error('minimum_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Unit Price (TZS) <span class="text-danger">*</span></label>
                            <input type="number" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror"
                                   value="{{ old('unit_price', 0) }}" min="0" step="0.01" style="border-radius:9px;">
                            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Supplier</label>
                            <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}" style="border-radius:9px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" style="border-radius:9px;">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12 pt-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                    style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                <i class="bi bi-check-lg"></i> Add Part
                            </button>
                            <a href="{{ route('spare-parts.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
    </div>
</x-app-layout>
