<x-app-layout title="PM Schedule Detail">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Preventive Maintenance Detail</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('preventive-maintenance.edit', $preventiveMaintenance) }}"
                           class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    @php
                        $statusClass = match($preventiveMaintenance->status) {
                            'overdue'   => 'danger',
                            'upcoming'  => 'warning',
                            'completed' => 'success',
                            default     => 'secondary',
                        };
                    @endphp

                    <div class="alert alert-{{ $statusClass }} mb-4">
                        <strong class="text-uppercase">{{ $preventiveMaintenance->status }}</strong>
                        @if($preventiveMaintenance->status === 'overdue')
                            — This service is overdue. Schedule immediately.
                        @elseif($preventiveMaintenance->status === 'upcoming')
                            — Upcoming service. Prepare accordingly.
                        @else
                            — Service completed.
                        @endif
                    </div>

                    <dl class="row mb-0">
                        <dt class="col-sm-4">Bus</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('buses.show', $preventiveMaintenance->bus) }}">
                                {{ $preventiveMaintenance->bus->registration_number }}
                            </a>
                            — {{ $preventiveMaintenance->bus->model }}
                        </dd>

                        <dt class="col-sm-4">Service Type</dt>
                        <dd class="col-sm-8">{{ $preventiveMaintenance->service_type }}</dd>

                        <dt class="col-sm-4">Interval (days)</dt>
                        <dd class="col-sm-8">{{ $preventiveMaintenance->interval_days ?? '—' }} days</dd>

                        <dt class="col-sm-4">Interval (km)</dt>
                        <dd class="col-sm-8">{{ $preventiveMaintenance->interval_km ? number_format($preventiveMaintenance->interval_km).' km' : '—' }}</dd>

                        <dt class="col-sm-4">Last Service Date</dt>
                        <dd class="col-sm-8">{{ $preventiveMaintenance->last_service_date?->format('d M Y') ?? '—' }}</dd>

                        <dt class="col-sm-4">Last Service Mileage</dt>
                        <dd class="col-sm-8">{{ $preventiveMaintenance->last_service_km ? number_format($preventiveMaintenance->last_service_km).' km' : '—' }}</dd>

                        <dt class="col-sm-4">Next Service Date</dt>
                        <dd class="col-sm-8">
                            <span class="text-{{ $statusClass }} fw-semibold">
                                {{ $preventiveMaintenance->next_service_date?->format('d M Y') ?? '—' }}
                            </span>
                            @if($preventiveMaintenance->next_service_date)
                                <span class="text-muted small ms-2">({{ $preventiveMaintenance->next_service_date->diffForHumans() }})</span>
                            @endif
                        </dd>

                        @if($preventiveMaintenance->notes)
                        <dt class="col-sm-4">Notes</dt>
                        <dd class="col-sm-8">{{ $preventiveMaintenance->notes }}</dd>
                        @endif
                    </dl>
                </div>
                <div class="card-footer d-flex gap-2">
                    <form method="POST" action="{{ route('preventive-maintenance.destroy', $preventiveMaintenance) }}"
                          onsubmit="return confirm('Delete this PM schedule?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
