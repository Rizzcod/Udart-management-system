<x-app-layout title="Preventive Maintenance">
    @php
        $pmColors       = ['upcoming'=>['bg'=>'#dbeafe','text'=>'#1e40af'],'overdue'=>['bg'=>'#fee2e2','text'=>'#991b1b'],'completed'=>['bg'=>'#dcfce7','text'=>'#15803d']];
        $priorityColors = ['critical'=>['bg'=>'#fee2e2','text'=>'#991b1b'],'high'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'medium'=>['bg'=>'#fef3c7','text'=>'#92400e'],'low'=>['bg'=>'#dcfce7','text'=>'#15803d']];
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Preventive Maintenance</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Preventive Maintenance</li>
                </ol>
            </nav>
        </div>
        @can('create preventive-maintenance')
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('preventive-maintenance.generate-all') }}">
                @csrf
                <button type="submit" class="btn btn-outline-primary d-flex align-items-center gap-2"
                        style="border-color:#1c3faa;color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;"
                        onclick="return confirm('Auto-generate PM schedules for all buses now?')">
                    <i class="bi bi-arrow-repeat"></i> Generate All Schedules
                </button>
            </form>
            <a href="{{ route('preventive-maintenance.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
               style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;">
                <i class="bi bi-plus-lg"></i> Add Schedule
            </a>
        </div>
        @endcan
    </div>

    {{-- Filter card --}}
    <div class="card mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-2">
                        <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                        <select name="status" class="form-select" style="border-radius:9px;">
                            <option value="">All Statuses</option>
                            @foreach(['overdue','upcoming','completed'] as $s)
                            <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
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
                    <div class="col-lg-2">
                        <label class="form-label small text-muted fw-semibold mb-1">AI Priority</label>
                        <select name="priority" class="form-select" style="border-radius:9px;">
                            <option value="">All Priorities</option>
                            <option value="critical" {{ request('priority')==='critical'?'selected':'' }}>Critical</option>
                            <option value="high"     {{ request('priority')==='high'?'selected':'' }}>High</option>
                            <option value="medium"   {{ request('priority')==='medium'?'selected':'' }}>Medium</option>
                            <option value="low"      {{ request('priority')==='low'?'selected':'' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-size:.875rem;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->hasAny(['status','bus_id','priority']))
                        <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1" style="border-radius:9px;font-size:.875rem;">
                            <i class="bi bi-x-lg"></i> Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- AI Smart Recommendations Panel --}}
    @if($highPriorityBuses->isNotEmpty())
    <div class="card mb-4" style="border-radius:14px;border:1.5px solid #fde68a;">
        <button class="btn w-100 d-flex align-items-center justify-content-between px-4 py-3 border-0 bg-transparent text-start ai-toggle-btn"
                type="button" data-bs-toggle="collapse" data-bs-target="#aiPanel" aria-expanded="true" aria-controls="aiPanel">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <i class="bi bi-robot" style="color:#92400e;font-size:1.15rem;"></i>
                <span class="fw-bold" style="color:#92400e;font-size:.95rem;">AI Maintenance Intelligence</span>
                <span class="badge" style="background:#fde68a;color:#78350f;font-size:.72rem;font-weight:700;">
                    {{ $highPriorityBuses->count() }} bus{{ $highPriorityBuses->count() > 1 ? 'es' : '' }} flagged
                </span>
            </div>
            <i class="bi bi-chevron-up ai-chevron text-muted" style="font-size:.85rem;transition:transform .2s;flex-shrink:0;"></i>
        </button>
        <div class="collapse show" id="aiPanel">
            <div style="border-top:1.5px solid #fde68a;background:#fffbeb;border-radius:0 0 12px 12px;">
                <div class="px-4 py-2 border-bottom" style="border-color:#fde68a !important;font-size:.78rem;color:#92400e;">
                    <i class="bi bi-info-circle me-1"></i>
                    These vehicles are flagged based on age, breakdown history, and mileage. Maintenance intervals are automatically shortened to keep them roadworthy.
                </div>
                <div class="row g-0 p-3">
                    @foreach($highPriorityBuses as $entry)
                    @php $pc = $priorityColors[$entry['level']] ?? $priorityColors['medium']; @endphp
                    <div class="col-sm-6 col-xl-4 p-2">
                        <div class="rounded-3 p-3 h-100" style="background:#fff;border:1px solid #fde68a;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};font-size:.68rem;font-weight:700;letter-spacing:.02em;">
                                    {{ strtoupper($entry['level']) }}
                                </span>
                                <a href="{{ route('buses.show', $entry['bus']) }}" class="fw-bold" style="color:#1c3faa;font-size:.875rem;text-decoration:none;">
                                    {{ $entry['bus']->registration_number }}
                                </a>
                                <span class="ms-auto text-muted" style="font-size:.7rem;">{{ $entry['score'] }}/120</span>
                            </div>
                            <div class="mb-1" style="font-size:.75rem;color:#78350f;">
                                Intervals at <strong>{{ (int) round($entry['multiplier'] * 100) }}%</strong> of standard
                                &bull; {{ (int) round((1 - $entry['multiplier']) * 100) }}% shorter
                            </div>
                            @foreach(array_slice($entry['insights'], 0, 2) as $insight)
                            <div style="font-size:.72rem;color:#92400e;line-height:1.4;">&bull; {{ $insight }}</div>
                            @endforeach
                            <div class="mt-2">
                                <a href="{{ route('preventive-maintenance.index', ['bus_id' => $entry['bus']->id]) }}#pm-table"
                                   style="font-size:.72rem;color:#1c3faa;text-decoration:none;font-weight:600;">
                                    View schedules &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Active bus filter banner --}}
    @if(request('bus_id'))
    @php $filteredBus = $buses->firstWhere('id', request('bus_id')); @endphp
    @if($filteredBus)
    <div class="d-flex align-items-center justify-content-between mb-3 px-4 py-3 rounded-3"
         style="background:#dbeafe;border:1px solid #93c5fd;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:38px;height:38px;background:#1d4ed8;">
                <i class="bi bi-bus-front-fill" style="color:#fff;font-size:1rem;"></i>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:600;color:#1e40af;text-transform:uppercase;letter-spacing:.05em;">Viewing PM Schedules For</div>
                <div style="font-size:1.05rem;font-weight:800;color:#1e3a8a;">{{ $filteredBus->registration_number }}</div>
                <div style="font-size:.75rem;color:#3b82f6;">{{ $filteredBus->model }} &middot; {{ $filteredBus->manufacturer }} &middot; {{ $filteredBus->year }}</div>
            </div>
        </div>
        <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-sm"
           style="background:#1d4ed8;color:#fff;border-radius:8px;font-size:.8rem;">
            <i class="bi bi-x-lg me-1"></i>Clear Filter
        </a>
    </div>
    @endif
    @endif

    {{-- Bulk action bar (hidden until rows are selected) --}}
    @can('edit preventive-maintenance')
    <form id="bulkForm" method="POST" action="{{ route('preventive-maintenance.bulk-status') }}">
        @csrf
        <div id="bulkBar" class="d-none align-items-center gap-3 px-4 py-3 mb-3 rounded-3"
             style="background:#1c3faa;border-radius:12px !important;">
            <span id="bulkCount" class="text-white fw-semibold" style="font-size:.875rem;white-space:nowrap;"></span>
            <span class="text-white opacity-50" style="font-size:.8rem;">|</span>
            <span class="text-white opacity-75" style="font-size:.82rem;">Change status to:</span>
            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" name="status" value="completed"
                        class="btn btn-sm fw-semibold"
                        style="background:#dcfce7;color:#15803d;border:none;border-radius:7px;font-size:.8rem;"
                        onclick="return confirm('Mark selected schedules as Completed?')">
                    <i class="bi bi-check-circle me-1"></i>Mark Completed
                </button>
                <button type="submit" name="status" value="upcoming"
                        class="btn btn-sm fw-semibold"
                        style="background:#dbeafe;color:#1e40af;border:none;border-radius:7px;font-size:.8rem;"
                        onclick="return confirm('Mark selected schedules as Upcoming?')">
                    <i class="bi bi-clock me-1"></i>Mark Upcoming
                </button>
                <button type="submit" name="status" value="overdue"
                        class="btn btn-sm fw-semibold"
                        style="background:#fee2e2;color:#991b1b;border:none;border-radius:7px;font-size:.8rem;"
                        onclick="return confirm('Mark selected schedules as Overdue?')">
                    <i class="bi bi-exclamation-circle me-1"></i>Mark Overdue
                </button>
            </div>
            <button type="button" id="bulkClear" class="btn btn-sm ms-auto"
                    style="background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:7px;font-size:.8rem;">
                <i class="bi bi-x-lg me-1"></i>Clear
            </button>
        </div>

    {{-- Table card --}}
    <div class="card" id="pm-table" style="border-radius:14px;">
        <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #f0f3fb;">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">PM Schedules</span>
            <span class="text-muted" style="font-size:.82rem;">{{ $schedules->total() }} schedules</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th class="ps-4 py-3" style="width:42px;">
                        <input type="checkbox" id="selectAll" class="form-check-input" style="cursor:pointer;width:16px;height:16px;">
                    </th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Priority</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Service Type</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Interval</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Last Service</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Next Due</th>
                    <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                    <th class="py-3 pe-4 text-end" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($schedules as $pm)
                    @php
                        $pc    = $pmColors[$pm->status] ?? $pmColors['upcoming'];
                        $pData = $priorityMap[$pm->bus->id] ?? ['level' => 'low', 'score' => 0];
                        $ppCol = $priorityColors[$pData['level']] ?? $priorityColors['low'];
                    @endphp
                    <tr class="pm-row" style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-4 py-3">
                            <input type="checkbox" name="ids[]" value="{{ $pm->id }}" class="form-check-input pm-checkbox"
                                   style="cursor:pointer;width:16px;height:16px;">
                        </td>
                        <td class="py-3">
                            <a href="{{ route('buses.show', $pm->bus) }}" style="color:#1c3faa;font-size:.875rem;font-weight:600;">
                                {{ $pm->bus->registration_number }}
                            </a>
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background:{{ $ppCol['bg'] }};color:{{ $ppCol['text'] }};font-size:.7rem;font-weight:600;padding:.3em .65em;border-radius:6px;">
                                {{ ucfirst($pData['level']) }}
                            </span>
                            <div class="text-muted" style="font-size:.67rem;margin-top:2px;">{{ $pData['score'] }}/120</div>
                        </td>
                        <td class="py-3 fw-semibold" style="font-size:.875rem;">{{ $pm->service_type }}</td>
                        <td class="py-3 text-muted" style="font-size:.82rem;">
                            {{ $pm->interval_days ? $pm->interval_days.' days' : ($pm->interval_km ? number_format($pm->interval_km).' km' : '—') }}
                        </td>
                        <td class="py-3 text-muted" style="font-size:.82rem;">{{ $pm->last_service_date?->format('d M Y') ?? '—' }}</td>
                        <td class="py-3 {{ $pm->status==='overdue'?'fw-semibold':'text-muted' }}"
                            style="font-size:.875rem;{{ $pm->status==='overdue'?'color:#991b1b;':'' }}">
                            {{ $pm->next_service_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};font-size:.75rem;font-weight:600;padding:.35em .7em;border-radius:6px;">
                                {{ ucfirst($pm->status) }}
                            </span>
                        </td>
                        <td class="py-3 pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                @can('edit preventive-maintenance')
                                <a href="{{ route('preventive-maintenance.edit', $pm) }}" title="Edit" class="action-icon" style="color:#6b7280;font-size:1.05rem;"><i class="bi bi-pencil-square"></i></a>
                                @endcan
                                @can('delete preventive-maintenance')
                                <button type="button" class="action-icon border-0 bg-transparent p-0 btn-delete-pm"
                                        data-url="{{ route('preventive-maintenance.destroy', $pm) }}"
                                        style="color:#dc2626;font-size:1.05rem;" title="Delete">
                                    <i class="bi bi-trash3"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-5">
                        <div class="text-center">
                            <i class="bi bi-calendar2-x d-block text-muted" style="font-size:2.8rem;opacity:.25;"></i>
                            <div class="mt-2 fw-semibold" style="color:#374151;">
                                @if(request('bus_id'))
                                    No PM schedules found for this bus.
                                @else
                                    No PM schedules yet.
                                @endif
                            </div>
                            <div class="text-muted mt-1 mb-3" style="font-size:.82rem;">
                                @if(request('bus_id'))
                                    Schedules are auto-generated each morning. You can also generate them now.
                                @else
                                    Use "Generate All Schedules" to automatically create PM schedules for every bus in the fleet.
                                @endif
                            </div>
                            @can('create preventive-maintenance')
                            <div class="d-flex justify-content-center gap-2">
                                <form method="POST" action="{{ route('preventive-maintenance.generate-all') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary"
                                            style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-size:.875rem;">
                                        <i class="bi bi-arrow-repeat me-1"></i>Generate Schedules Now
                                    </button>
                                </form>
                                @if(request('bus_id'))
                                <a href="{{ route('preventive-maintenance.create', ['bus_id' => request('bus_id')]) }}"
                                   class="btn btn-outline-primary" style="border-color:#1c3faa;color:#1c3faa;border-radius:9px;font-size:.875rem;">
                                    <i class="bi bi-plus-lg me-1"></i>Add Manually
                                </a>
                                @endif
                            </div>
                            @endcan
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($schedules->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $schedules->firstItem() }}–{{ $schedules->lastItem() }} of {{ $schedules->total() }}</div>
            {{ $schedules->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
    </form>
    @endcan

{{-- Standalone delete form — _method injected by JS only when triggered --}}
@can('delete preventive-maintenance')
<form id="pmDeleteForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" id="pmDeleteMethod" name="_method" value="">
</form>
@endcan

@push('styles')
<style>
.pm-row { transition:background .12s; }
.pm-row:hover { background:#f8faff !important; }
.pm-row.pm-selected { background:#eff6ff !important; }
.pm-row:last-child { border-bottom:none !important; }
.action-icon { cursor:pointer; transition:opacity .15s; text-decoration:none; }
.action-icon:hover { opacity:.65; }
.ai-toggle-btn.collapsed .ai-chevron { transform:rotate(180deg); }
html { scroll-behavior: smooth; }
#bulkBar { transition: opacity .15s; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(request('bus_id'))
    var pmTable = document.getElementById('pm-table');
    if (pmTable) { pmTable.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    @endif

    var selectAll  = document.getElementById('selectAll');
    var bulkBar    = document.getElementById('bulkBar');
    var bulkCount  = document.getElementById('bulkCount');
    var bulkClear  = document.getElementById('bulkClear');

    if (!selectAll) return;

    function getChecked() {
        return document.querySelectorAll('.pm-checkbox:checked');
    }

    function updateBar() {
        var checked = getChecked();
        var n = checked.length;
        if (n > 0) {
            bulkCount.textContent = n + ' schedule' + (n > 1 ? 's' : '') + ' selected';
            bulkBar.classList.remove('d-none');
            bulkBar.classList.add('d-flex');
        } else {
            bulkBar.classList.add('d-none');
            bulkBar.classList.remove('d-flex');
        }
        // highlight rows
        document.querySelectorAll('.pm-checkbox').forEach(function (cb) {
            cb.closest('tr').classList.toggle('pm-selected', cb.checked);
        });
        // update select-all state
        var all = document.querySelectorAll('.pm-checkbox');
        selectAll.indeterminate = n > 0 && n < all.length;
        selectAll.checked = all.length > 0 && n === all.length;
    }

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.pm-checkbox').forEach(function (cb) {
            cb.checked = selectAll.checked;
        });
        updateBar();
    });

    document.querySelectorAll('.pm-checkbox').forEach(function (cb) {
        cb.addEventListener('change', updateBar);
    });

    bulkClear.addEventListener('click', function () {
        document.querySelectorAll('.pm-checkbox').forEach(function (cb) { cb.checked = false; });
        selectAll.checked = false;
        selectAll.indeterminate = false;
        updateBar();
    });

    // Delete buttons — standalone form, _method set dynamically so it never leaks into bulkForm
    var deleteForm   = document.getElementById('pmDeleteForm');
    var deleteMethod = document.getElementById('pmDeleteMethod');
    if (deleteForm && deleteMethod) {
        document.querySelectorAll('.btn-delete-pm').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!confirm('Delete this PM schedule?')) return;
                deleteForm.action  = btn.dataset.url;
                deleteMethod.value = 'DELETE';
                deleteForm.submit();
            });
        });
    }
});
</script>
@endpush
</x-app-layout>
