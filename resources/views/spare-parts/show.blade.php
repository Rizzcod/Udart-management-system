<x-app-layout title="{{ $sparePart->part_name }}">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">{{ $sparePart->part_name }}</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('spare-parts.index') }}" style="color:#1c3faa;">Spare Parts</a></li>
                    <li class="breadcrumb-item active text-muted">{{ $sparePart->part_name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @can('edit spare-parts')
            <a href="{{ route('spare-parts.edit', $sparePart) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-pencil"></i> Edit
            </a>
            @endcan
            <a href="{{ route('spare-parts.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- Part details --}}
        <div class="col-lg-4">
            <div class="card" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-box-seam" style="color:#374151;"></i>
                    <span class="fw-bold" style="font-size:.9rem;">Part Details</span>
                    @if($sparePart->isLowStock())
                    <span class="badge ms-auto" style="background:#fee2e2;color:#991b1b;font-size:.72rem;padding:.3em .65em;border-radius:6px;">Low Stock</span>
                    @else
                    <span class="badge ms-auto" style="background:#dcfce7;color:#15803d;font-size:.72rem;padding:.3em .65em;border-radius:6px;">In Stock</span>
                    @endif
                </div>
                <div class="card-body px-4">
                    <table class="table table-borderless mb-0" style="font-size:.875rem;">
                        <tr><td class="ps-0 text-muted py-2">Part Number</td><td class="pe-0 py-2 text-end fw-semibold">{{ $sparePart->part_number }}</td></tr>
                        <tr>
                            <td class="ps-0 text-muted py-2">Quantity</td>
                            <td class="pe-0 py-2 text-end fw-bold" style="font-size:1rem;color:{{ $sparePart->isLowStock()?'#dc2626':'#16a34a' }};">{{ $sparePart->quantity }}</td>
                        </tr>
                        <tr><td class="ps-0 text-muted py-2">Min. Stock</td><td class="pe-0 py-2 text-end">{{ $sparePart->minimum_stock }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Unit Price</td><td class="pe-0 py-2 text-end fw-semibold">TZS {{ number_format($sparePart->unit_price) }}</td></tr>
                        <tr style="border-bottom:none;"><td class="ps-0 text-muted py-2">Supplier</td><td class="pe-0 py-2 text-end">{{ $sparePart->supplier ?? '—' }}</td></tr>
                    </table>
                    @if($sparePart->isLowStock())
                    <div class="mt-3 p-3 d-flex align-items-start gap-2" style="background:#fee2e2;border-radius:9px;">
                        <i class="bi bi-exclamation-triangle text-danger mt-1 flex-shrink-0"></i>
                        <span style="font-size:.82rem;color:#991b1b;">Stock is below minimum. A reorder is needed.</span>
                    </div>
                    @endif
                    @if($sparePart->notes)
                    <div class="mt-3">
                        <div class="text-muted fw-semibold mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Notes</div>
                        <p class="mb-0" style="font-size:.875rem;color:#374151;">{{ $sparePart->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Usage history --}}
        <div class="col-lg-8">
            <div class="card" style="border-radius:14px;">
                <div class="px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-clock-history text-muted"></i>
                    <span class="fw-bold" style="font-size:.9rem;">Usage History</span>
                    <span class="badge ms-1" style="background:#f0f3fb;color:#374151;font-size:.7rem;">{{ $sparePart->usages->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Work Order</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Qty Used</th>
                            <th class="py-3 pe-4 text-end" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                        </tr></thead>
                        <tbody>
                            @forelse($sparePart->usages as $usage)
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td class="ps-4 py-3">
                                    <a href="{{ route('work-orders.show', $usage->workOrder) }}" style="color:#1c3faa;font-size:.875rem;">
                                        <span class="text-muted me-1" style="font-size:.8rem;">#{{ $usage->work_order_id }}</span>{{ Str::limit($usage->workOrder->title, 35) }}
                                    </a>
                                </td>
                                <td class="py-3" style="font-size:.875rem;">{{ $usage->workOrder->bus->registration_number }}</td>
                                <td class="py-3 fw-semibold" style="font-size:.875rem;">{{ $usage->quantity_used }}</td>
                                <td class="py-3 pe-4 text-end text-muted" style="font-size:.82rem;">{{ $usage->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history d-block" style="font-size:2rem;opacity:.2;"></i>
                                <div class="mt-2">Not used in any work order yet.</div>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
