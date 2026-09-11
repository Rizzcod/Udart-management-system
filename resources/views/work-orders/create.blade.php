<x-app-layout title="New Work Order">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">New Work Order</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}" style="color:#1c3faa;">Work Orders</a></li>
                    <li class="breadcrumb-item active text-muted">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="border-radius:14px;">
        <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dbeafe;flex-shrink:0;">
                <i class="bi bi-wrench-adjustable" style="color:#1c3faa;font-size:1rem;"></i>
            </div>
            <div>
                <div class="fw-bold" style="font-size:.95rem;color:#111827;">Work Order Details</div>
                <div class="text-muted" style="font-size:.75rem;">Fill in the details to create a new work order</div>
            </div>
        </div>
        <div class="card-body px-4 py-4">
            <form method="POST" action="{{ route('work-orders.store') }}">
                @csrf
                <div class="row g-3">

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Bus <span class="text-danger">*</span></label>
                        <select name="bus_id" id="bus_id_select"
                                class="form-select @error('bus_id') is-invalid @enderror" style="border-radius:9px;">
                            <option value="">Select bus…</option>
                            @foreach($buses as $bus)
                            <option value="{{ $bus->id }}"
                                    data-locked="{{ $bus->maintenance_locked ? 'true' : 'false' }}"
                                    data-reg="{{ $bus->registration_number }}"
                                    {{ old('bus_id', $selectedBus?->id) == $bus->id ? 'selected' : '' }}>
                                {{ $bus->registration_number }} — {{ $bus->model }}
                                @if($bus->maintenance_locked) [OUT OF SERVICE] @endif
                            </option>
                            @endforeach
                        </select>
                        @error('bus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Restriction alert — shown by JS when a locked bus is selected --}}
                    <div class="col-12" id="bus_restriction_alert" style="display:none;">
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:#fff7ed;border:1.5px solid #fdba74;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#ea580c;font-size:1.2rem;flex-shrink:0;margin-top:.1rem;"></i>
                            <div style="font-size:.85rem;">
                                <div class="fw-bold" style="color:#9a3412;">Vehicle Out of Service — Maintenance Required</div>
                                <div style="color:#7c2d12;margin-top:.2rem;">
                                    <span id="bus_restriction_reg"></span> is currently <strong>Out of Service</strong> due to overdue preventive maintenance.
                                    This vehicle is not allowed to operate until the required maintenance has been completed and approved.
                                    You may still create this maintenance work order to track and action the required work.
                                </div>
                                <a href="{{ route('preventive-maintenance.index', ['status' => 'overdue']) }}"
                                   class="btn btn-sm mt-2" style="background:#ea580c;color:#fff;border-radius:7px;font-size:.78rem;font-weight:600;border:none;">
                                    <i class="bi bi-calendar2-check me-1"></i>View Overdue PM Tasks
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" style="border-radius:9px;">
                            @foreach(['critical','high','medium','low'] as $p)
                            <option value="{{ $p }}" {{ old('priority','medium')===$p?'selected':'' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="e.g. Engine overheating investigation" style="border-radius:9px;">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4" placeholder="Describe the issue in detail…" style="border-radius:9px;">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Assign Technician</label>
                        <select name="assigned_to" class="form-select" style="border-radius:9px;">
                            <option value="">Assign later</option>
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('assigned_to')==$tech->id?'selected':'' }}>{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" style="border-radius:9px;">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" style="border-radius:9px;">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 pt-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                            <i class="bi bi-check-lg"></i> Create Work Order
                        </button>
                        <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
(function () {
    const sel   = document.getElementById('bus_id_select');
    const alert = document.getElementById('bus_restriction_alert');
    const reg   = document.getElementById('bus_restriction_reg');

    function checkRestriction() {
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.locked === 'true') {
            reg.textContent = opt.dataset.reg;
            alert.style.display = '';
        } else {
            alert.style.display = 'none';
        }
    }

    sel.addEventListener('change', checkRestriction);
    checkRestriction(); // run on page load (handles old() pre-selection)
})();
</script>
@endpush
</x-app-layout>
