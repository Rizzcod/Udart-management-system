<x-app-layout title="Report #{{ $workOrder->id }}">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-file-text me-2"></i>Breakdown Report #{{ $workOrder->id }}</h5>
        <a href="{{ route('driver.my-reports') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> My Reports</a>
    </div>

    <div class="row g-4">
        {{-- Main details --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">{{ $workOrder->title }}</span>
                    <div class="d-flex gap-2">
                        <span class="badge bg-{{ $workOrder->getPriorityBadgeClass() }}">{{ ucfirst($workOrder->priority) }}</span>
                        <span class="badge bg-{{ $workOrder->getStatusBadgeClass() }}">{{ $workOrder->getStatusLabel() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small fw-semibold mb-1">Description</div>
                        <p class="mb-0">{{ $workOrder->description }}</p>
                    </div>
                    @if($workOrder->location)
                    <div class="mb-3">
                        <div class="text-muted small fw-semibold mb-1">Location at time of breakdown</div>
                        <p class="mb-0"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $workOrder->location }}</p>
                    </div>
                    @endif
                    @if($workOrder->notes)
                    <div>
                        <div class="text-muted small fw-semibold mb-1">Maintenance Notes</div>
                        <p class="mb-0 fst-italic text-muted">{{ $workOrder->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 10-stage progress timeline --}}
            <div class="card">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-diagram-3 me-2 text-primary"></i>Repair Progress Timeline
                </div>
                <div class="card-body">
                    @if($workOrder->status === 'cancelled')
                        <div class="alert alert-secondary py-2 mb-0">
                            <i class="bi bi-x-circle me-1"></i> This work order was cancelled.
                        </div>
                    @else
                        @php
                            $stages    = \App\Models\WorkOrder::STAGES;
                            $stageKeys = array_keys($stages);
                            // If status is 'pending' (legacy), treat as 'reported' for display
                            $current   = $workOrder->status === 'pending' ? 'reported' : $workOrder->status;
                            $currentIdx = array_search($current, $stageKeys);
                        @endphp

                        {{-- Mobile-friendly vertical timeline --}}
                        <div class="d-flex flex-column gap-0">
                            @foreach($stages as $key => $step)
                            @php
                                $idx  = array_search($key, $stageKeys);
                                $done = $currentIdx !== false && $idx <= $currentIdx;
                                $active = $key === $current;
                            @endphp
                            <div class="d-flex align-items-start gap-3 pb-3 {{ $loop->last ? '' : 'border-start ms-3' }}" style="{{ $loop->last ? '' : 'border-left:2px solid ' . ($done && !$active ? 'var(--bs-success)' : '#dee2e6') . ' !important;padding-left:1.5rem;margin-left:0.75rem;' }}">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $loop->last ? '' : '' }}"
                                     style="width:36px;height:36px;min-width:36px;
                                            background:{{ $done ? 'var(--bs-' . $step['color'] . ')' : '#f3f4f6' }};
                                            color:{{ $done ? '#fff' : '#9ca3af' }};
                                            border:2px solid {{ $done ? 'var(--bs-' . $step['color'] . ')' : '#dee2e6' }};
                                            {{ $loop->last ? '' : 'margin-left:-1.25rem;' }}">
                                    <i class="bi {{ $step['icon'] }}" style="font-size:.8rem;"></i>
                                </div>
                                <div class="pt-1 pb-2" style="min-width:0;">
                                    <div class="fw-{{ $active ? 'bold' : ($done ? 'semibold' : 'normal') }}"
                                         style="font-size:.875rem;color:{{ $done ? '#111827' : '#9ca3af' }};">
                                        {{ $step['label'] }}
                                        @if($active)
                                        <span class="badge ms-1" style="background:var(--bs-{{ $step['color'] }});font-size:.7rem;">Current</span>
                                        @endif
                                    </div>
                                    @if($active && $workOrder->assignee && in_array($key, ['assigned','in_progress','testing']))
                                    <div class="text-muted" style="font-size:.78rem;">
                                        <i class="bi bi-person me-1"></i>{{ $workOrder->assignee->name }}
                                    </div>
                                    @endif
                                    @if($key === 'in_progress' && $workOrder->start_date)
                                    <div class="text-muted" style="font-size:.78rem;">
                                        <i class="bi bi-calendar me-1"></i>Started {{ $workOrder->start_date->format('d M Y') }}
                                    </div>
                                    @endif
                                    @if(in_array($key, ['completed','returned_to_service']) && $workOrder->completion_date && $done)
                                    <div class="text-muted" style="font-size:.78rem;">
                                        <i class="bi bi-calendar-check me-1"></i>{{ $workOrder->completion_date->format('d M Y') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Side info --}}
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-bus-front me-2 text-primary"></i>Bus Details</div>
                <div class="card-body">
                    @php $colors = $workOrder->bus->getStatusColors(); @endphp
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted small">Registration</td><td class="fw-semibold small">{{ $workOrder->bus->registration_number }}</td></tr>
                        <tr><td class="text-muted small">Model</td><td class="small">{{ $workOrder->bus->model }}</td></tr>
                        <tr><td class="text-muted small">Status</td><td>
                            <span class="badge" style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-size:.75rem;">
                                {{ $workOrder->bus->getStatusLabel() }}
                            </span>
                        </td></tr>
                        <tr><td class="text-muted small">Mileage</td><td class="small">{{ number_format($workOrder->bus->mileage) }} km</td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-info-circle me-2 text-primary"></i>Report Info</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted small">Reported</td><td class="small">{{ $workOrder->created_at->format('d M Y, H:i') }}</td></tr>
                        @if($workOrder->assignee)
                        <tr><td class="text-muted small">Technician</td><td class="small fw-semibold">{{ $workOrder->assignee->name }}</td></tr>
                        @else
                        <tr><td class="text-muted small">Technician</td><td class="small text-muted">Not assigned yet</td></tr>
                        @endif
                        @if($workOrder->start_date)
                        <tr><td class="text-muted small">Started</td><td class="small">{{ $workOrder->start_date->format('d M Y') }}</td></tr>
                        @endif
                        @if($workOrder->completion_date)
                        <tr><td class="text-muted small">Completed</td><td class="small">{{ $workOrder->completion_date->format('d M Y') }}</td></tr>
                        @endif
                    </table>

                    @php $resolved = $workOrder->isResolved(); @endphp

                    @if($workOrder->status === 'returned_to_service')
                    <div class="alert alert-success py-2 small mt-3 mb-0">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Your bus has been fully repaired and returned to service.
                    </div>
                    @elseif($workOrder->status === 'completed')
                    <div class="alert alert-success py-2 small mt-3 mb-0">
                        <i class="bi bi-check-circle me-1"></i>
                        Repair completed. Awaiting formal return to service.
                    </div>
                    @elseif($workOrder->status === 'cancelled')
                    <div class="alert alert-secondary py-2 small mt-3 mb-0">
                        <i class="bi bi-x-circle me-1"></i>
                        This work order was cancelled.
                    </div>
                    @elseif($workOrder->status === 'awaiting_parts')
                    <div class="alert alert-warning py-2 small mt-3 mb-0">
                        <i class="bi bi-box-seam me-1"></i>
                        Repair paused — waiting for spare parts to arrive.
                    </div>
                    @elseif(in_array($workOrder->status, ['testing']))
                    <div class="alert alert-info py-2 small mt-3 mb-0">
                        <i class="bi bi-speedometer2 me-1"></i>
                        Repair done. Your bus is being tested before return.
                    </div>
                    @elseif(! $resolved)
                    <div class="alert alert-info py-2 small mt-3 mb-0">
                        <i class="bi bi-clock me-1"></i>
                        The maintenance team is handling your report.
                        @if($workOrder->assignee) {{ $workOrder->assignee->name }} is assigned. @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
