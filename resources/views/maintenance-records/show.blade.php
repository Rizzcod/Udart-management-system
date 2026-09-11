<x-app-layout title="Maintenance Record">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Maintenance Record</h5>
        <a href="{{ route('maintenance-records.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card p-4" style="max-width:600px;">
        <dl class="row">
            <dt class="col-4 text-muted">Bus</dt><dd class="col-8"><a href="{{ route('buses.show', $maintenanceRecord->bus) }}">{{ $maintenanceRecord->bus->registration_number }}</a></dd>
            <dt class="col-4 text-muted">Date</dt><dd class="col-8">{{ $maintenanceRecord->maintenance_date->format('d M Y') }}</dd>
            <dt class="col-4 text-muted">Technician</dt><dd class="col-8">{{ $maintenanceRecord->technician->name }}</dd>
<dt class="col-4 text-muted">Description</dt><dd class="col-8">{{ $maintenanceRecord->description }}</dd>
            @if($maintenanceRecord->parts_replaced)
            <dt class="col-4 text-muted">Parts Replaced</dt><dd class="col-8">{{ $maintenanceRecord->parts_replaced }}</dd>
            @endif
            @if($maintenanceRecord->notes)
            <dt class="col-4 text-muted">Notes</dt><dd class="col-8">{{ $maintenanceRecord->notes }}</dd>
            @endif
        </dl>
        <div class="d-flex gap-2 mt-2">
            <a href="{{ route('maintenance-records.edit', $maintenanceRecord) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
        </div>
    </div>
</x-app-layout>
