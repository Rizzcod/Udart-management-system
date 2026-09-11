<x-app-layout title="Work Orders">
    @php
        $pColors = ['critical'=>['bg'=>'#fee2e2','text'=>'#991b1b'],'high'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'medium'=>['bg'=>'#dbeafe','text'=>'#1e40af'],'low'=>['bg'=>'#f3f4f6','text'=>'#374151']];
        $sColors = ['pending'=>['bg'=>'#f3f4f6','text'=>'#374151'],'assigned'=>['bg'=>'#dbeafe','text'=>'#1e40af'],'in_progress'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'completed'=>['bg'=>'#dcfce7','text'=>'#15803d'],'cancelled'=>['bg'=>'#fee2e2','text'=>'#991b1b']];
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Work Orders</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Work Orders</li>
                </ol>
            </nav>
        </div>
        @can('create work-orders')
        <a href="{{ route('work-orders.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
           style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;">
            <i class="bi bi-plus-lg"></i> New Work Order
        </a>
        @endcan
    </div>

    <div class="card mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                        <select name="status" class="form-select" style="border-radius:9px;">
                            <option value="">All Statuses</option>
                            @foreach(['pending','assigned','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small text-muted fw-semibold mb-1">Priority</label>
                        <select name="priority" class="form-select" style="border-radius:9px;">
                            <option value="">All</option>
                            @foreach(['critical','high','medium','low'] as $p)
                            <option value="{{ $p }}" {{ request('priority')===$p?'selected':'' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
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
                        @if(request()->hasAny(['status','priority','bus_id']))
                        <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1" style="border-radius:9px;font-size:.875rem;">
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
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">Work Order List</span>
            <span class="text-muted" style="font-size:.82rem;">{{ $workOrders->total() }} orders</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">ID</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Title</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Priority</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Assigned To</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Reported By</th>
                    <th class="py-3 text-end pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Date</th>
                    <th class="py-3 pe-4"></th>
                </tr></thead>
                <tbody>
                    @forelse($workOrders as $wo)
                    @php $pc=$pColors[$wo->priority]??$pColors['low']; $sc=$sColors[$wo->status]??$sColors['pending']; @endphp
                    <tr class="wo-row" style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-4 py-3 text-muted" style="font-size:.82rem;">#{{ $wo->id }}</td>
                        <td class="py-3">
                            <a href="{{ route('buses.show', $wo->bus) }}" style="color:#1c3faa;font-size:.875rem;font-weight:600;">{{ $wo->bus->registration_number }}</a>
                        </td>
                        <td class="py-3" style="font-size:.875rem;max-width:220px;">{{ Str::limit($wo->title, 45) }}</td>
                        <td class="py-3">
                            <span class="badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};font-size:.75rem;font-weight:600;padding:.35em .7em;border-radius:6px;">
                                {{ ucfirst($wo->priority) }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};font-size:.75rem;font-weight:600;padding:.35em .7em;border-radius:6px;">
                                {{ ucwords(str_replace('_',' ',$wo->status)) }}
                            </span>
                        </td>
                        <td class="py-3" style="font-size:.875rem;">{!! $wo->assignee?->name ?? '<span class="text-muted">—</span>' !!}</td>
                        <td class="py-3" style="font-size:.875rem;">{{ $wo->reporter->name }}</td>
                        <td class="py-3 pe-4 text-end text-muted" style="font-size:.82rem;">{{ $wo->created_at->format('d M Y') }}</td>
                        <td class="py-3 pe-4">
                            <a href="{{ route('work-orders.show', $wo) }}" title="View" class="action-icon" style="color:#1c3faa;font-size:1.05rem;"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-wrench-adjustable d-block" style="font-size:2.5rem;opacity:.2;"></i>
                        <div class="mt-2">No work orders found.</div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workOrders->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $workOrders->firstItem() }}–{{ $workOrders->lastItem() }} of {{ $workOrders->total() }}</div>
            {{ $workOrders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

@push('styles')
<style>
.wo-row { transition:background .12s; }
.wo-row:hover { background:#f8faff !important; }
.wo-row:last-child { border-bottom:none !important; }
.action-icon { cursor:pointer; transition:opacity .15s; text-decoration:none; }
.action-icon:hover { opacity:.65; }
</style>
@endpush
</x-app-layout>
