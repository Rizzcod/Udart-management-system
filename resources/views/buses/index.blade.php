<x-app-layout title="Buses">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Buses</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Buses</li>
                </ol>
            </nav>
        </div>
        @can('create buses')
        <a href="{{ route('buses.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
           style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;">
            <i class="bi bi-plus-lg"></i> Register Bus
        </a>
        @endcan
    </div>

    {{-- Filter bar --}}
    <div class="card mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label small text-muted fw-semibold mb-1">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" value="{{ request('search') }}"
                                   placeholder="Registration number or model…" style="border-radius:0 9px 9px 0;">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                        <select name="status" class="form-select" style="border-radius:9px;">
                            <option value="">All Statuses</option>
                            @foreach(\App\Models\Bus::STATUSES as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-size:.875rem;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request('search') || request('status'))
                        <a href="{{ route('buses.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1"
                           style="border-radius:9px;font-size:.875rem;">
                            <i class="bi bi-x-lg"></i> Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table card --}}
    <div class="card" style="border-radius:14px;">
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">Buses List</span>
            <span class="text-muted" style="font-size:.82rem;">{{ $buses->total() }} buses</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr style="border-bottom:1px solid #f0f3fb;">
                        <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Bus</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Model & Year</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Mileage</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Driver</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Status</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Work Orders</th>
                        <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Maintenance</th>
                        <th class="py-3 text-end pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $bus)
                    @php $c = $bus->getStatusColors(); @endphp
                    <tr style="border-bottom:1px solid #f9fafb;" class="bus-row">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                     style="width:42px;height:42px;background:#1c3faa;font-size:.8rem;">
                                    {{ strtoupper(substr($bus->registration_number, -3)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.9rem;color:#111827;">{{ $bus->registration_number }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $bus->manufacturer }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div style="font-size:.875rem;color:#374151;">{{ $bus->model }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $bus->year }}</div>
                        </td>
                        <td class="py-3" style="font-size:.875rem;color:#374151;">{{ number_format($bus->mileage) }} km</td>
                        <td class="py-3" style="font-size:.875rem;color:#374151;">
                            @if(in_array($bus->status, ['out_of_service','inactive']))
                                <span class="text-muted" style="font-style:italic;">—</span>
                            @else
                                {{ $bus->driver?->name ?? '—' }}
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="badge d-inline-flex align-items-center gap-1" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};font-size:.75rem;font-weight:600;padding:.35em .7em;border-radius:6px;">
                                @if($bus->maintenance_locked)<i class="bi bi-shield-x"></i>@endif
                                {{ $bus->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background:#f0f3fb;color:#374151;font-size:.78rem;padding:.3em .65em;border-radius:6px;">
                                {{ $bus->work_orders_count }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background:#f0f3fb;color:#374151;font-size:.78rem;padding:.3em .65em;border-radius:6px;">
                                {{ $bus->maintenance_records_count }}
                            </span>
                        </td>
                        <td class="py-3 pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <a href="{{ route('buses.show', $bus) }}" title="View" class="action-icon" style="color:#1c3faa;font-size:1.05rem;"><i class="bi bi-eye"></i></a>
                                @can('edit buses')
                                <a href="{{ route('buses.edit', $bus) }}" title="Edit" class="action-icon" style="color:#6b7280;font-size:1.05rem;"><i class="bi bi-pencil-square"></i></a>
                                @endcan
                                @can('delete buses')
                                <form method="POST" action="{{ route('buses.destroy', $bus) }}" class="d-inline" onsubmit="return confirm('Remove this bus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-icon border-0 bg-transparent p-0" style="color:#dc2626;font-size:1.05rem;" title="Delete"><i class="bi bi-trash3"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-bus-front d-block" style="font-size:2.5rem;opacity:.2;"></i>
                        <div class="mt-2">No buses found.</div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($buses->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $buses->firstItem() }}–{{ $buses->lastItem() }} of {{ $buses->total() }}</div>
            {{ $buses->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

@push('styles')
<style>
.bus-row { transition:background .12s; }
.bus-row:hover { background:#f8faff !important; }
.bus-row:last-child { border-bottom:none !important; }
.action-icon { cursor:pointer; transition:opacity .15s; text-decoration:none; }
.action-icon:hover { opacity:.65; }
</style>
@endpush
</x-app-layout>
