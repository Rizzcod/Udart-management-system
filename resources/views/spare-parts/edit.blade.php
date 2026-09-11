<x-app-layout title="Edit Spare Part">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Edit Spare Part</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('spare-parts.index') }}" style="color:#1c3faa;">Spare Parts</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('spare-parts.show', $sparePart) }}" style="color:#1c3faa;">{{ $sparePart->part_name }}</a></li>
                    <li class="breadcrumb-item active text-muted">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('spare-parts.show', $sparePart) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @php
        $lowStock = $sparePart->isLowStock();
        $border   = $lowStock ? '#dc2626' : '#1c3faa';
        $iconBg   = $lowStock ? '#fee2e2' : '#f0f3fb';
        $iconColor = $lowStock ? '#991b1b' : '#374151';
    @endphp

    <div class="row g-4">
        {{-- Left: summary card --}}
        <div class="col-lg-3">
            <div class="card text-center" style="border-radius:14px;overflow:hidden;">
                <div style="height:5px;background:{{ $border }};"></div>
                <div class="card-body py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:64px;height:64px;background:{{ $iconBg }};">
                        <i class="bi bi-gear" style="font-size:1.5rem;color:{{ $iconColor }};"></i>
                    </div>
                    <div class="fw-bold mb-1" style="font-size:.95rem;color:#111827;">{{ $sparePart->part_name }}</div>
                    <div class="text-muted mb-2" style="font-size:.8rem;">{{ $sparePart->part_number }}</div>
                    @if($lowStock)
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.75rem;padding:.35em .8em;border-radius:6px;">Low Stock</span>
                    @else
                    <span class="badge" style="background:#dcfce7;color:#15803d;font-size:.75rem;padding:.35em .8em;border-radius:6px;">In Stock</span>
                    @endif
                </div>
                <div class="card-footer bg-transparent px-3 py-3" style="border-top:1px solid #f0f3fb;">
                    <table class="table table-borderless mb-0" style="font-size:.8rem;">
                        <tr><td class="ps-0 text-muted py-1 text-start">Quantity</td><td class="pe-0 py-1 text-end fw-semibold {{ $lowStock ? 'text-danger' : '' }}">{{ $sparePart->quantity }}</td></tr>
                        <tr><td class="ps-0 text-muted py-1 text-start">Min. Stock</td><td class="pe-0 py-1 text-end">{{ $sparePart->minimum_stock }}</td></tr>
                        <tr><td class="ps-0 text-muted py-1 text-start">Unit Price</td><td class="pe-0 py-1 text-end">TZS {{ number_format($sparePart->unit_price) }}</td></tr>
                        <tr style="border-bottom:none;"><td class="ps-0 text-muted py-1 text-start">Supplier</td><td class="pe-0 py-1 text-end">{{ $sparePart->supplier ?? '—' }}</td></tr>
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
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Update Part Details</div>
                        <div class="text-muted" style="font-size:.75rem;">Changes take effect immediately</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('spare-parts.update', $sparePart) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Part Name <span class="text-danger">*</span></label>
                                <input type="text" name="part_name" class="form-control @error('part_name') is-invalid @enderror"
                                       value="{{ old('part_name', $sparePart->part_name) }}" style="border-radius:9px;">
                                @error('part_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Part Number <span class="text-danger">*</span></label>
                                <input type="text" name="part_number" class="form-control @error('part_number') is-invalid @enderror"
                                       value="{{ old('part_number', $sparePart->part_number) }}" style="border-radius:9px;">
                                @error('part_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control"
                                       value="{{ old('quantity', $sparePart->quantity) }}" min="0" style="border-radius:9px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Min. Stock <span class="text-danger">*</span></label>
                                <input type="number" name="minimum_stock" class="form-control"
                                       value="{{ old('minimum_stock', $sparePart->minimum_stock) }}" min="0" style="border-radius:9px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Unit Price (TZS) <span class="text-danger">*</span></label>
                                <input type="number" name="unit_price" class="form-control"
                                       value="{{ old('unit_price', $sparePart->unit_price) }}" min="0" step="0.01" style="border-radius:9px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Supplier</label>
                                <input type="text" name="supplier" class="form-control"
                                       value="{{ old('supplier', $sparePart->supplier) }}" style="border-radius:9px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" style="border-radius:9px;">{{ old('notes', $sparePart->notes) }}</textarea>
                            </div>
                            <div class="col-12 pt-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                                <a href="{{ route('spare-parts.show', $sparePart) }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
