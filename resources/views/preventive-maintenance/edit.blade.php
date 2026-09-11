<x-app-layout title="Edit PM Schedule">
    @php
        $pmColors = [
            'upcoming'  => ['bg'=>'#dbeafe','text'=>'#1e40af','border'=>'#1c3faa'],
            'overdue'   => ['bg'=>'#fee2e2','text'=>'#991b1b','border'=>'#dc2626'],
            'completed' => ['bg'=>'#dcfce7','text'=>'#15803d','border'=>'#16a34a'],
        ];
        $pc = $pmColors[$preventiveMaintenance->status] ?? $pmColors['upcoming'];
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Edit PM Schedule</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('preventive-maintenance.index') }}" style="color:#1c3faa;">Preventive Maintenance</a></li>
                    <li class="breadcrumb-item active text-muted">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        {{-- Left: schedule summary (read-only) --}}
        <div class="col-lg-4">
            <div class="card" style="border-radius:14px;overflow:hidden;">
                <div style="height:5px;background:{{ $pc['border'] }};"></div>
                <div class="card-body py-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:64px;height:64px;background:#fef9c3;">
                        <i class="bi bi-calendar2-check" style="font-size:1.5rem;color:#854d0e;"></i>
                    </div>
                    <div class="fw-bold mb-1" style="font-size:.95rem;color:#111827;">{{ $preventiveMaintenance->service_type }}</div>
                    <div class="text-muted mb-2" style="font-size:.8rem;">{{ $preventiveMaintenance->bus->registration_number }}</div>
                    <span class="badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};font-size:.75rem;padding:.35em .8em;border-radius:6px;">
                        {{ ucfirst($preventiveMaintenance->status) }}
                    </span>
                </div>
                <div class="card-footer bg-transparent px-3 py-3" style="border-top:1px solid #f0f3fb;">
                    <table class="table table-borderless mb-0" style="font-size:.8rem;">
                        @if($preventiveMaintenance->interval_days)
                        <tr>
                            <td class="ps-0 text-muted py-1 text-start">Interval</td>
                            <td class="pe-0 py-1 text-end fw-semibold">{{ $preventiveMaintenance->interval_days }} days</td>
                        </tr>
                        @endif
                        @if($preventiveMaintenance->interval_km)
                        <tr>
                            <td class="ps-0 text-muted py-1 text-start">Interval (km)</td>
                            <td class="pe-0 py-1 text-end fw-semibold">{{ number_format($preventiveMaintenance->interval_km) }} km</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="ps-0 text-muted py-1 text-start">Last Service</td>
                            <td class="pe-0 py-1 text-end">{{ $preventiveMaintenance->last_service_date?->format('d M Y') ?? '—' }}</td>
                        </tr>
                        <tr style="border-bottom:none;">
                            <td class="ps-0 text-muted py-1 text-start">Next Due</td>
                            <td class="pe-0 py-1 text-end fw-semibold" style="color:{{ $pc['text'] }};">
                                {{ $preventiveMaintenance->next_service_date?->format('d M Y') ?? '—' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Auto-schedule notice --}}
            <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-2" style="background:#f0f4ff;border:1px solid #c7d4f8;font-size:.8rem;">
                <i class="bi bi-info-circle-fill mt-1" style="color:#1c3faa;flex-shrink:0;"></i>
                <div style="color:#374151;">
                    Intervals and dates are managed automatically by the scheduler. Marking this service <strong>Completed</strong> will instantly schedule the next cycle.
                </div>
            </div>
        </div>

        {{-- Right: edit form --}}
        <div class="col-lg-8">
            <div class="card" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#fef9c3;flex-shrink:0;">
                        <i class="bi bi-pencil-square" style="color:#854d0e;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Update Schedule</div>
                        <div class="text-muted" style="font-size:.75rem;">Mark service complete or update notes — the system handles the rest</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('preventive-maintenance.update', $preventiveMaintenance) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">

                            {{-- Read-only: bus --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Bus</label>
                                <div class="form-control" style="border-radius:9px;background:#f9fafb;color:#374151;">
                                    {{ $preventiveMaintenance->bus->registration_number }}
                                </div>
                            </div>

                            {{-- Read-only: service type --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Service Type</label>
                                <div class="form-control" style="border-radius:9px;background:#f9fafb;color:#374151;">
                                    {{ $preventiveMaintenance->service_type }}
                                </div>
                            </div>

                            {{-- Status selector --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" style="border-radius:9px;">
                                    @foreach(['upcoming', 'overdue', 'completed'] as $s)
                                    <option value="{{ $s }}" {{ old('status', $preventiveMaintenance->status) === $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text" style="font-size:.75rem;">
                                    Setting to <strong>Completed</strong> auto-schedules the next cycle immediately.
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Notes <span class="text-muted fw-normal">(optional)</span></label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Any remarks about this service…" style="border-radius:9px;">{{ old('notes', $preventiveMaintenance->notes) }}</textarea>
                            </div>

                            <div class="col-12 pt-2 d-flex gap-2 flex-wrap">
                                {{-- Quick-complete shortcut --}}
                                @if($preventiveMaintenance->status !== 'completed')
                                <button type="submit" name="status" value="completed"
                                        class="btn d-flex align-items-center gap-2"
                                        style="background:#15803d;border-color:#15803d;color:#fff;border-radius:9px;font-weight:600;"
                                        onclick="document.querySelector('select[name=status]').value='completed'">
                                    <i class="bi bi-check2-circle"></i> Mark Complete &amp; Schedule Next
                                </button>
                                @endif
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                                <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
