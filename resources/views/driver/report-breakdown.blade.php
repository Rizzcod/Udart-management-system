<x-app-layout title="Report Breakdown">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Report a Breakdown</h5>
        <a href="{{ route('driver.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <div class="card p-4" style="max-width:680px;">
        <form method="POST" action="{{ route('driver.store-breakdown') }}">
            @csrf
            <div class="row g-3">

                {{-- Bus --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Bus <span class="text-danger">*</span></label>
                    @if($bus)
                        <input type="hidden" name="bus_id" value="{{ $bus->id }}">
                        <div class="form-control bg-light d-flex align-items-center gap-2">
                            <i class="bi bi-bus-front text-primary"></i>
                            <span class="fw-semibold">{{ $bus->registration_number }}</span>
                            <span class="text-muted small">— {{ $bus->model }}</span>
                            <span class="badge ms-auto bg-{{ $bus->status === 'active' ? 'success' : ($bus->status === 'under_repair' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $bus->status)) }}
                            </span>
                        </div>
                    @else
                        <select name="bus_id" class="form-select @error('bus_id') is-invalid @enderror">
                            <option value="">Select bus…</option>
                            @foreach($buses as $b)
                            <option value="{{ $b->id }}" {{ old('bus_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->registration_number }} — {{ $b->model }}
                            </option>
                            @endforeach
                        </select>
                        @error('bus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>

                {{-- Priority --}}
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                        <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical — Bus cannot move</option>
                        <option value="high" {{ old('priority', '') === 'high' ? 'selected' : '' }}>High — Serious issue</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium — Can complete route</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low — Minor issue</option>
                    </select>
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Current mileage --}}
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Current Odometer (km)</label>
                    <input type="number" name="mileage" class="form-control @error('mileage') is-invalid @enderror"
                           value="{{ old('mileage', $bus?->mileage) }}" min="0" placeholder="e.g. 85500">
                    <div class="form-text">Updates the bus odometer record.</div>
                    @error('mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Title --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Issue Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="e.g. Engine overheating, flat tyre, brake failure">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Location --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Current Location</label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                           value="{{ old('location') }}" placeholder="e.g. Kariakoo junction, along Morogoro Road km 12">
                    <div class="form-text">Where is the bus right now? This helps dispatch a technician faster.</div>
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Description --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Describe the Problem <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="4" placeholder="Describe what happened, any warning lights, noises, or symptoms…">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="alert alert-warning py-2 small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Your report will immediately notify the maintenance team. For critical breakdowns, stay with the bus and ensure passenger safety first.
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-send me-1"></i> Submit Report
                    </button>
                    <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>

            </div>
        </form>
    </div>
</x-app-layout>
