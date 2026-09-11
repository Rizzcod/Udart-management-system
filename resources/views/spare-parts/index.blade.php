<x-app-layout title="Spare Parts">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Spare Parts Inventory</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Spare Parts</li>
                </ol>
            </nav>
        </div>
        @can('create spare-parts')
        <a href="{{ route('spare-parts.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
           style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;">
            <i class="bi bi-plus-lg"></i> Add Part
        </a>
        @endcan
    </div>

    <div class="card mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label small text-muted fw-semibold mb-1">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" value="{{ request('search') }}"
                                   placeholder="Part name or number…" style="border-radius:0 9px 9px 0;">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check d-flex align-items-center gap-2 ms-1 mt-1">
                            <input class="form-check-input" type="checkbox" name="low_stock" id="lowStock" value="1" {{ request('low_stock')?'checked':'' }}>
                            <label class="form-check-label small fw-semibold" for="lowStock" style="color:#374151;">Low Stock Only</label>
                        </div>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-size:.875rem;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request('search') || request('low_stock'))
                        <a href="{{ route('spare-parts.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1" style="border-radius:9px;font-size:.875rem;">
                            <i class="bi bi-x-lg"></i> Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="border-radius:14px;">
        <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #f0f3fb;">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">Parts List</span>
            <span class="text-muted" style="font-size:.82rem;">{{ $parts->total() }} parts</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Part</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Part Number</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Stock</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Min Stock</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Unit Price (TZS)</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Supplier</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Used</th>
                    <th class="py-3 pe-4 text-end" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($parts as $part)
                    <tr class="part-row {{ $part->isLowStock()?'low-stock-row':'' }}" style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:38px;height:38px;background:{{ $part->isLowStock()?'#fee2e2':'#f0f3fb' }};">
                                    <i class="bi bi-gear" style="font-size:.9rem;color:{{ $part->isLowStock()?'#991b1b':'#6b7280' }};"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.875rem;color:#111827;">{{ $part->part_name }}</div>
                                    @if($part->isLowStock())
                                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.7rem;padding:.25em .55em;border-radius:5px;">Low Stock</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-muted" style="font-size:.82rem;">{{ $part->part_number }}</td>
                        <td class="py-3">
                            <span class="fw-bold" style="font-size:.9rem;color:{{ $part->isLowStock()?'#dc2626':'#16a34a' }};">{{ $part->quantity }}</span>
                        </td>
                        <td class="py-3 text-muted" style="font-size:.875rem;">{{ $part->minimum_stock }}</td>
                        <td class="py-3 fw-semibold" style="font-size:.875rem;">{{ number_format($part->unit_price) }}</td>
                        <td class="py-3 text-muted" style="font-size:.875rem;">{{ $part->supplier ?? '—' }}</td>
                        <td class="py-3">
                            <span class="badge" style="background:#f0f3fb;color:#374151;font-size:.78rem;padding:.3em .65em;border-radius:6px;">{{ $part->usages_count }}</span>
                        </td>
                        <td class="py-3 pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <a href="{{ route('spare-parts.show', $part) }}" title="View" class="action-icon" style="color:#1c3faa;font-size:1.05rem;"><i class="bi bi-eye"></i></a>
                                @can('edit spare-parts')
                                <a href="{{ route('spare-parts.edit', $part) }}" title="Edit" class="action-icon" style="color:#6b7280;font-size:1.05rem;"><i class="bi bi-pencil-square"></i></a>
                                @endcan
                                @can('delete spare-parts')
                                <form method="POST" action="{{ route('spare-parts.destroy', $part) }}" class="d-inline" onsubmit="return confirm('Delete this part?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-icon border-0 bg-transparent p-0" style="color:#dc2626;font-size:1.05rem;" title="Delete"><i class="bi bi-trash3"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam d-block" style="font-size:2.5rem;opacity:.2;"></i>
                        <div class="mt-2">No spare parts found.</div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($parts->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $parts->firstItem() }}–{{ $parts->lastItem() }} of {{ $parts->total() }}</div>
            {{ $parts->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

@push('styles')
<style>
.part-row { transition:background .12s; }
.part-row:hover { background:#f8faff !important; }
.part-row:last-child { border-bottom:none !important; }
.low-stock-row { background:#fff5f5 !important; }
.low-stock-row:hover { background:#fff0f0 !important; }
.action-icon { cursor:pointer; transition:opacity .15s; text-decoration:none; }
.action-icon:hover { opacity:.65; }
</style>
@endpush
</x-app-layout>
