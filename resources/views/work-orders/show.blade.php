<x-app-layout title="Work Order #{{ $workOrder->id }}">
    @php
        $pColors = ['critical'=>['bg'=>'#fee2e2','text'=>'#991b1b'],'high'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'medium'=>['bg'=>'#dbeafe','text'=>'#1e40af'],'low'=>['bg'=>'#f3f4f6','text'=>'#374151']];
        $sColors = ['reported'=>['bg'=>'#fce7f3','text'=>'#be185d'],'pending'=>['bg'=>'#f3f4f6','text'=>'#374151'],'assessment'=>['bg'=>'#fef3c7','text'=>'#92400e'],'pending_approval'=>['bg'=>'#ede9fe','text'=>'#5b21b6'],'assigned'=>['bg'=>'#dbeafe','text'=>'#1e40af'],'awaiting_parts'=>['bg'=>'#ede9fe','text'=>'#6d28d9'],'in_progress'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'testing'=>['bg'=>'#cffafe','text'=>'#155e75'],'completed'=>['bg'=>'#dcfce7','text'=>'#15803d'],'returned_to_service'=>['bg'=>'#dcfce7','text'=>'#166534'],'cancelled'=>['bg'=>'#fee2e2','text'=>'#991b1b']];
        $pc = $pColors[$workOrder->priority] ?? $pColors['low'];
        $sc = $sColors[$workOrder->status] ?? $sColors['pending'];
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Work Order <span class="text-muted">#{{ $workOrder->id }}</span></h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}" style="color:#1c3faa;">Work Orders</a></li>
                    <li class="breadcrumb-item active text-muted">#{{ $workOrder->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @can('edit work-orders')
            <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-pencil"></i> Edit
            </a>
            @endcan
            @role('Technician')
            <a href="{{ route('work-orders.my-assignments') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-arrow-left"></i> My Assignments
            </a>
            @else
            <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            @endrole
        </div>
    </div>

    <div class="row g-3">
        {{-- Main content --}}
        <div class="col-lg-8">
            {{-- Title / status card --}}
            <div class="card mb-3" style="border-radius:14px;">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <h5 class="fw-bold mb-0" style="color:#111827;line-height:1.4;">{{ $workOrder->title }}</h5>
                        <div class="d-flex gap-2 flex-shrink-0 ms-3">
                            <span class="badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};font-size:.78rem;padding:.4em .8em;border-radius:7px;">{{ strtoupper($workOrder->priority) }}</span>
                            <span class="badge" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};font-size:.78rem;padding:.4em .8em;border-radius:7px;">{{ strtoupper(str_replace('_',' ',$workOrder->status)) }}</span>
                        </div>
                    </div>
                    <div class="mb-1 text-muted fw-semibold" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;">Description</div>
                    <p class="mb-0" style="font-size:.9rem;line-height:1.7;color:#374151;">{{ $workOrder->description }}</p>
                    @if($workOrder->notes)
                    <hr class="my-3">
                    <div class="mb-1 text-muted fw-semibold" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;">Notes</div>
                    <p class="mb-0" style="font-size:.875rem;color:#6b7280;">{{ $workOrder->notes }}</p>
                    @endif
                </div>
            </div>

            {{-- Parts used --}}
            @if($workOrder->sparePartUsages->count())
            <div class="card mb-3" style="border-radius:14px;">
                <div class="px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-boxes text-warning"></i>
                    <span class="fw-bold" style="font-size:.9rem;">Parts Used</span>
                    <span class="badge ms-1" style="background:#f0f3fb;color:#374151;font-size:.7rem;">{{ $workOrder->sparePartUsages->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Part</th>
                            <th class="py-3 text-end pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Qty Used</th>
                        </tr></thead>
                        <tbody>
                            @foreach($workOrder->sparePartUsages as $usage)
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td class="ps-4 py-2" style="font-size:.875rem;">{{ $usage->sparePart->part_name }}</td>
                                <td class="py-2 pe-4 text-end fw-semibold" style="font-size:.875rem;">{{ $usage->quantity_used }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Status Actions --}}
            @can('update work-order status')
            @php
            $allTransitions = [
                'reported'            => ['assessment', 'cancelled'],
                'pending'             => ['assessment', 'assigned', 'cancelled'],
                'assessment'          => ['pending_approval', 'assigned', 'cancelled'],
                'pending_approval'    => ['assigned', 'cancelled'],
                'assigned'            => ['awaiting_parts', 'in_progress', 'cancelled'],
                'awaiting_parts'      => ['in_progress', 'cancelled'],
                'in_progress'         => ['testing', 'completed', 'cancelled'],
                'testing'             => ['completed', 'in_progress'],
                'completed'           => ['returned_to_service'],
                'returned_to_service' => [],
                'cancelled'           => [],
            ];
            $nextStatuses = $allTransitions[$workOrder->status] ?? [];
            $btnConfigs = [
                'assessment'          => ['style'=>'background:#0284c7;border-color:#0284c7;', 'icon'=>'clipboard2-pulse',  'label'=>'Send for Assessment'],
                'pending_approval'    => ['style'=>'background:#7c3aed;border-color:#7c3aed;', 'icon'=>'hourglass-split',   'label'=>'Mark Pending Approval'],
                'assigned'            => ['style'=>'background:#1c3faa;border-color:#1c3faa;', 'icon'=>'person-check',      'label'=>'Assign Technician'],
                'awaiting_parts'      => ['style'=>'background:#6d28d9;border-color:#6d28d9;', 'icon'=>'box-seam',          'label'=>'Mark Awaiting Parts'],
                'in_progress'         => ['style'=>'background:#ea580c;border-color:#ea580c;', 'icon'=>'wrench-adjustable', 'label'=>'Start Repair'],
                'testing'             => ['style'=>'background:#0891b2;border-color:#0891b2;', 'icon'=>'speedometer2',      'label'=>'Send for Testing'],
                'completed'           => ['style'=>'background:#16a34a;border-color:#16a34a;', 'icon'=>'check-circle',      'label'=>'Mark Completed'],
                'returned_to_service' => ['style'=>'background:#15803d;border-color:#15803d;', 'icon'=>'bus-front',         'label'=>'Return to Service'],
                'cancelled'           => ['style'=>'background:#dc2626;border-color:#dc2626;', 'icon'=>'x-circle',          'label'=>'Cancel Work Order'],
            ];
            @endphp
            @if($nextStatuses)
            <div class="card" style="border-radius:14px;">
                <div class="px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-arrow-right-circle text-primary"></i>
                    <span class="fw-bold" style="font-size:.9rem;">Update Status</span>
                </div>
                <div class="card-body px-4 py-4">
                    @foreach($nextStatuses as $nextStatus)
                    @php $cfg = $btnConfigs[$nextStatus] ?? ['style'=>'background:#6b7280;border-color:#6b7280;','icon'=>'arrow-right','label'=>ucwords(str_replace('_',' ',$nextStatus))]; @endphp
                    <form method="POST" action="{{ route('work-orders.status', $workOrder) }}" class="mb-3 p-3" style="border:1px solid #f0f3fb;border-radius:10px;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $nextStatus }}">

                        @if($nextStatus === 'assigned')
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Assign Technician <span class="text-danger">*</span></label>
                            <select name="assigned_to" class="form-select" required style="border-radius:9px;">
                                <option value="">Select technician…</option>
                                @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $workOrder->assigned_to == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if(in_array($nextStatus, ['completed', 'returned_to_service']))
                        <div class="mb-3">
                            <div class="fw-semibold small mb-2">Parts Used <span class="text-muted">(optional)</span></div>
                            <div id="parts-container-{{ $nextStatus }}">
                                <div class="row g-2 mb-2 parts-row">
                                    <div class="col-7">
                                        <select name="spare_part_id[]" class="form-select form-select-sm" style="border-radius:8px;">
                                            <option value="">No part</option>
                                            @foreach($spareParts as $part)
                                            <option value="{{ $part->id }}">{{ $part->part_name }} (stock: {{ $part->quantity }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3"><input type="number" name="quantity_used[]" class="form-control form-control-sm" placeholder="Qty" min="1" value="1" style="border-radius:8px;"></div>
                                    <div class="col-2"><button type="button" class="btn btn-sm btn-outline-secondary add-part" data-target="parts-container-{{ $nextStatus }}" style="border-radius:8px;">+</button></div>
                                </div>
                            </div>
                            <div class="mt-2" style="max-width:200px;">
                                <label class="form-label small fw-semibold">Final Mileage (km)</label>
                                <input type="number" name="final_mileage" class="form-control form-control-sm" value="{{ $workOrder->bus->mileage }}" min="0" style="border-radius:8px;">
                            </div>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-sm text-white d-inline-flex align-items-center gap-2" style="{{ $cfg['style'] }}border-radius:8px;">
                            <i class="bi bi-{{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
            @endif
            @endcan
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card mb-3" style="border-radius:14px;">
                <div class="px-4 py-3 fw-bold" style="border-bottom:1px solid #f0f3fb;font-size:.9rem;">Details</div>
                <div class="card-body px-4">
                    <table class="table table-borderless mb-0" style="font-size:.875rem;">
                        <tr><td class="ps-0 text-muted py-2">Bus</td><td class="pe-0 py-2 text-end"><a href="{{ route('buses.show', $workOrder->bus) }}" style="color:#1c3faa;">{{ $workOrder->bus->registration_number }}</a></td></tr>
                        <tr><td class="ps-0 text-muted py-2">Reported By</td><td class="pe-0 py-2 text-end">{{ $workOrder->reporter->name }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Assigned To</td><td class="pe-0 py-2 text-end">{{ $workOrder->assignee?->name ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Start Date</td><td class="pe-0 py-2 text-end">{{ $workOrder->start_date?->format('d M Y') ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Completed</td><td class="pe-0 py-2 text-end">{{ $workOrder->completion_date?->format('d M Y') ?? '—' }}</td></tr>
                        <tr style="border-bottom:none;"><td class="ps-0 text-muted py-2">Created</td><td class="pe-0 py-2 text-end" style="font-size:.8rem;">{{ $workOrder->created_at->format('d M Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>

            {{-- Workflow Timeline --}}
            <div class="card" style="border-radius:14px;">
                <div class="px-4 py-3 fw-bold d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;font-size:.9rem;">
                    <i class="bi bi-diagram-3 text-primary"></i> Workflow
                </div>
                <div class="card-body px-4 py-3">
                    @php
                    $stages = \App\Models\WorkOrder::STAGES;
                    $stageKeys = array_keys($stages);
                    $isCancelled = $workOrder->status === 'cancelled';
                    $currentStageIdx = array_search($workOrder->status, $stageKeys);
                    @endphp
                    <div class="d-flex flex-column" style="gap:.55rem;">
                    @foreach($stages as $stageKey => $stage)
                    @php
                        $stageIdx = array_search($stageKey, $stageKeys);
                        if ($isCancelled) {
                            $state = 'future';
                        } elseif ($stageKey === $workOrder->status) {
                            $state = 'current';
                        } elseif ($currentStageIdx !== false && $stageIdx < $currentStageIdx) {
                            $state = 'done';
                        } else {
                            $state = 'future';
                        }
                    @endphp
                    <div class="d-flex align-items-center gap-2" style="font-size:.82rem;">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                             style="width:26px;height:26px;font-size:.7rem;
                             @if($state==='current') background:#1c3faa;color:#fff;
                             @elseif($state==='done') background:#dcfce7;color:#15803d;
                             @else background:#f3f4f6;color:#9ca3af; @endif">
                            <i class="bi bi-{{ $state === 'done' ? 'check2' : $stage['icon'] }}"></i>
                        </div>
                        <span style="@if($state==='current')color:#111827;font-weight:600;@elseif($state==='done')color:#374151;@else color:#9ca3af;@endif">
                            {{ $stage['label'] }}
                        </span>
                        @if($state === 'current')<span class="badge ms-auto" style="background:#dbeafe;color:#1e40af;font-size:.65rem;padding:.2em .5em;">Now</span>@endif
                    </div>
                    @endforeach
                    @if($isCancelled)
                    <div class="d-flex align-items-center gap-2 mt-1" style="font-size:.82rem;">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                             style="width:26px;height:26px;font-size:.7rem;background:#fee2e2;color:#991b1b;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <span style="color:#991b1b;font-weight:600;">Cancelled</span>
                        <span class="badge ms-auto" style="background:#fee2e2;color:#991b1b;font-size:.65rem;padding:.2em .5em;">Now</span>
                    </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.querySelectorAll('.add-part').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const container = document.getElementById(this.dataset.target);
        const row = this.closest('.parts-row').cloneNode(true);
        row.querySelectorAll('select,input').forEach(el => el.value = el.tagName === 'INPUT' ? 1 : '');
        container.appendChild(row);
        row.querySelector('.add-part').addEventListener('click', arguments.callee);
    });
});
</script>
@endpush
</x-app-layout>
