<x-app-layout title="Maintenance Records">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Maintenance Records</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Maintenance Records</li>
                </ol>
            </nav>
        </div>
        @can('create maintenance-records')
        <a href="{{ route('maintenance-records.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
           style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;">
            <i class="bi bi-plus-lg"></i> Add Record
        </a>
        @endcan
    </div>

    <div class="card mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label small text-muted fw-semibold mb-1">Bus</label>
                        <select name="bus_id" class="form-select" style="border-radius:9px;">
                            <option value="">All Buses</option>
                            @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" {{ request('bus_id')==$bus->id?'selected':'' }}>{{ $bus->registration_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-size:.875rem;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request('bus_id'))
                        <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1" style="border-radius:9px;font-size:.875rem;">
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
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">Records</span>
            <span class="text-muted" style="font-size:.82rem;">{{ $records->total() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Description</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Technician</th>
                    <th class="py-3 pe-4 text-end" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($records as $rec)
                    <tr class="rec-row" style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-4 py-3" style="font-size:.875rem;white-space:nowrap;">{{ $rec->maintenance_date->format('d M Y') }}</td>
                        <td class="py-3">
                            <a href="{{ route('buses.show', $rec->bus) }}" style="color:#1c3faa;font-size:.875rem;font-weight:600;">{{ $rec->bus->registration_number }}</a>
                        </td>
                        <td class="py-3" style="font-size:.875rem;max-width:280px;">{{ Str::limit($rec->description, 55) }}</td>
                        <td class="py-3" style="font-size:.875rem;">{{ $rec->technician->name }}</td>
                        <td class="py-3 pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                @can('edit maintenance-records')
                                <a href="{{ route('maintenance-records.edit', $rec) }}" title="Edit" class="action-icon" style="color:#6b7280;font-size:1.05rem;"><i class="bi bi-pencil-square"></i></a>
                                @endcan
                                @can('delete maintenance-records')
                                <form method="POST" action="{{ route('maintenance-records.destroy', $rec) }}" class="d-inline" onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-icon border-0 bg-transparent p-0" style="color:#dc2626;font-size:1.05rem;" title="Delete"><i class="bi bi-trash3"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard2-check d-block" style="font-size:2.5rem;opacity:.2;"></i>
                        <div class="mt-2">No maintenance records yet.</div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $records->firstItem() }}–{{ $records->lastItem() }} of {{ $records->total() }}</div>
            {{ $records->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

@push('styles')
<style>
.rec-row { transition:background .12s; }
.rec-row:hover { background:#f8faff !important; }
.rec-row:last-child { border-bottom:none !important; }
.action-icon { cursor:pointer; transition:opacity .15s; text-decoration:none; }
.action-icon:hover { opacity:.65; }
</style>
@endpush
</x-app-layout>
