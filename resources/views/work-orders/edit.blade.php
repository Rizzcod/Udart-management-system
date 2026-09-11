<x-app-layout title="Edit Work Order #{{ $workOrder->id }}">
    @php
        $pColors = ['critical'=>['bg'=>'#fee2e2','text'=>'#991b1b','dot'=>'#dc2626'],'high'=>['bg'=>'#ffedd5','text'=>'#c2410c','dot'=>'#ea580c'],'medium'=>['bg'=>'#dbeafe','text'=>'#1e40af','dot'=>'#1c3faa'],'low'=>['bg'=>'#f3f4f6','text'=>'#374151','dot'=>'#9ca3af']];
        $sColors = ['pending'=>['bg'=>'#f3f4f6','text'=>'#374151'],'assigned'=>['bg'=>'#dbeafe','text'=>'#1e40af'],'in_progress'=>['bg'=>'#ffedd5','text'=>'#c2410c'],'completed'=>['bg'=>'#dcfce7','text'=>'#15803d'],'cancelled'=>['bg'=>'#fee2e2','text'=>'#991b1b']];
        $pc = $pColors[$workOrder->priority] ?? $pColors['low'];
        $sc = $sColors[$workOrder->status] ?? $sColors['pending'];
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Edit Work Order</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}" style="color:#1c3faa;">Work Orders</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('work-orders.show', $workOrder) }}" style="color:#1c3faa;">#{{ $workOrder->id }}</a></li>
                    <li class="breadcrumb-item active text-muted">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        {{-- Left: summary card --}}
        <div class="col-lg-3">
            <div class="card text-center" style="border-radius:14px;overflow:hidden;">
                <div style="height:5px;background:{{ $pc['dot'] }};"></div>
                <div class="card-body py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle fw-bold text-white"
                         style="width:64px;height:64px;background:#1c3faa;font-size:.9rem;">
                        #{{ $workOrder->id }}
                    </div>
                    <div class="fw-bold mb-2" style="font-size:.88rem;color:#111827;line-height:1.3;">{{ Str::limit($workOrder->title, 50) }}</div>
                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                        <span class="badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};font-size:.72rem;padding:.3em .65em;border-radius:5px;">{{ ucfirst($workOrder->priority) }}</span>
                        <span class="badge" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};font-size:.72rem;padding:.3em .65em;border-radius:5px;">{{ ucwords(str_replace('_',' ',$workOrder->status)) }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent px-3 py-3" style="border-top:1px solid #f0f3fb;">
                    <table class="table table-borderless mb-0" style="font-size:.8rem;">
                        <tr><td class="ps-0 text-muted py-1 text-start">Bus</td><td class="pe-0 py-1 text-end fw-semibold">{{ $workOrder->bus->registration_number }}</td></tr>
                        <tr><td class="ps-0 text-muted py-1 text-start">Assigned To</td><td class="pe-0 py-1 text-end">{{ $workOrder->assignedTo?->name ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-1 text-start">Created</td><td class="pe-0 py-1 text-end">{{ $workOrder->created_at->format('d M Y') }}</td></tr>
                        <tr style="border-bottom:none;"><td class="ps-0 text-muted py-1 text-start">Start Date</td><td class="pe-0 py-1 text-end">{{ $workOrder->start_date?->format('d M Y') ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: edit form --}}
        <div class="col-lg-9">
            <div class="card" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dbeafe;flex-shrink:0;">
                        <i class="bi bi-pencil-square" style="color:#1c3faa;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Update Work Order</div>
                        <div class="text-muted" style="font-size:.75rem;">Changes take effect immediately</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('work-orders.update', $workOrder) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Bus <span class="text-danger">*</span></label>
                                <select name="bus_id" class="form-select @error('bus_id') is-invalid @enderror" style="border-radius:9px;">
                                    @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" {{ old('bus_id',$workOrder->bus_id)==$bus->id?'selected':'' }}>{{ $bus->registration_number }} — {{ $bus->model }}</option>
                                    @endforeach
                                </select>
                                @error('bus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" style="border-radius:9px;">
                                    @foreach(['critical','high','medium','low'] as $p)
                                    <option value="{{ $p }}" {{ old('priority',$workOrder->priority)===$p?'selected':'' }}>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $workOrder->title) }}" style="border-radius:9px;">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                          rows="4" style="border-radius:9px;">{{ old('description', $workOrder->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Notes</label>
                                <textarea name="notes" class="form-control" rows="2" style="border-radius:9px;">{{ old('notes', $workOrder->notes) }}</textarea>
                            </div>
                            <div class="col-12 pt-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                                <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
