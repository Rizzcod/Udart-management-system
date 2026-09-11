<x-app-layout title="My Today's Assignment">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">
                <i class="bi bi-bus-front-fill me-2 text-primary"></i>Today's Assignment
            </h4>
            <div class="text-muted small mt-1">{{ now()->format('l, d F Y') }}</div>
        </div>
        <a href="{{ route('driver.dashboard') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    @if($assignment)
        @php
            $bus    = $assignment->bus;
            $colors = $bus->getStatusColors();
            $onTrip = $bus->status === 'on_trip';
            $route  = $assignment->route;
        @endphp

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card" style="border-radius:18px;overflow:hidden;">

                    {{-- Gradient header with bus + route --}}
                    <div style="background:linear-gradient(135deg,#1c3faa 0%,#2563eb 100%);padding:2rem 2rem 1.75rem;">
                        <div class="text-center">
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3"
                                 style="width:72px;height:72px;background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.35);">
                                <i class="bi bi-bus-front-fill" style="font-size:2rem;color:#fff;"></i>
                            </div>
                            <div style="font-size:1.9rem;font-weight:800;color:#fff;letter-spacing:.04em;line-height:1;">
                                {{ $bus->registration_number }}
                            </div>
                            <div style="color:rgba(255,255,255,.72);font-size:.88rem;margin-top:.3rem;">
                                {{ $bus->model }} &middot; {{ $bus->manufacturer }} &middot; {{ $bus->year }}
                            </div>

                            {{-- Route pill --}}
                            @if($route)
                            <div class="mt-3 d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill"
                                 style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.35);">
                                <i class="bi bi-signpost-2-fill" style="color:#fde68a;font-size:1rem;"></i>
                                <span style="color:#fff;font-weight:700;font-size:.95rem;letter-spacing:.02em;">{{ $route }}</span>
                            </div>
                            @else
                            <div class="mt-3 d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill"
                                 style="background:rgba(255,255,255,.12);border:1px dashed rgba(255,255,255,.4);color:rgba(255,255,255,.6);font-size:.83rem;">
                                <i class="bi bi-exclamation-circle me-1"></i>No route assigned — contact supervisor
                            </div>
                            @endif

                            <div class="mt-2">
                                <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;font-size:.75rem;padding:.3rem .7rem;border-radius:20px;">
                                    <i class="bi bi-calendar-check me-1"></i>{{ $assignment->assigned_date->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-4 py-4">

                        {{-- Live status badge --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-3"
                             style="background:{{ $onTrip ? '#dcfce7' : $colors['bg'].'20' }};border:1px solid {{ $onTrip ? '#bbf7d0' : $colors['bg'] }};">
                            <span style="font-size:.85rem;font-weight:600;color:#374151;">Bus Status</span>
                            @if($onTrip)
                            <span class="badge d-inline-flex align-items-center gap-1"
                                  style="background:#dcfce7;color:#15803d;font-size:.8rem;padding:.35rem .75rem;">
                                <span style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;
                                             animation:pulse-dot 1.8s ease-in-out infinite;"></span>
                                On the Road
                            </span>
                            @else
                            <span class="badge d-inline-flex align-items-center gap-1"
                                  style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-size:.8rem;padding:.35rem .75rem;">
                                @if($bus->maintenance_locked)<i class="bi bi-shield-x"></i>@endif
                                {{ $bus->getStatusLabel() }}
                            </span>
                            @endif
                        </div>

                        @if(! $bus->isDispatchable() && ! $onTrip)
                        <div class="alert py-2 mb-3 d-flex align-items-center gap-2"
                             style="background:#ffe4e6;border:1px solid #fecdd3;color:#9f1239;font-size:.82rem;border-radius:10px;">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <span>This bus is currently <strong>not dispatchable</strong>. Contact your supervisor before driving.</span>
                        </div>
                        @endif

                        {{-- Details table --}}
                        <table class="table table-borderless mb-4" style="font-size:.875rem;">
                            <tr>
                                <td class="ps-0 text-muted py-2">Odometer</td>
                                <td class="text-end pe-0 fw-semibold py-2">{{ number_format($bus->mileage) }} km</td>
                            </tr>
                            @if($route)
                            <tr>
                                <td class="ps-0 text-muted py-2">Route</td>
                                <td class="text-end pe-0 py-2">
                                    <span class="fw-semibold" style="color:#1c3faa;">{{ $route }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="ps-0 text-muted py-2">Assigned By</td>
                                <td class="text-end pe-0 py-2">
                                    @if($assignment->assigner)
                                        {{ $assignment->assigner->name }}
                                    @else
                                        <span class="badge" style="background:#e0e7ff;color:#3730a3;font-size:.72rem;">
                                            <i class="bi bi-robot me-1"></i>Auto-assigned
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0 text-muted py-2">Assigned At</td>
                                <td class="text-end pe-0 py-2 text-muted" style="font-size:.82rem;">{{ $assignment->created_at->format('H:i') }}</td>
                            </tr>
                            @if($assignment->notes)
                            <tr>
                                <td class="ps-0 text-muted py-2">Notes</td>
                                <td class="text-end pe-0 py-2" style="font-size:.82rem;">{{ $assignment->notes }}</td>
                            </tr>
                            @endif
                        </table>

                        {{-- Primary actions: Start / End trip --}}
                        <div class="d-grid gap-2 mb-2">
                            @if($onTrip)
                            <form method="POST" action="{{ route('driver.trip.end') }}">
                                @csrf
                                <button type="submit" class="btn w-100 py-2 fw-bold"
                                        style="background:#dc2626;color:#fff;border-radius:10px;font-size:.95rem;">
                                    <i class="bi bi-stop-circle-fill me-2"></i>End Trip
                                </button>
                            </form>
                            @elseif($bus->isDispatchable() && $route)
                            <form method="POST" action="{{ route('driver.trip.start') }}">
                                @csrf
                                <button type="submit" class="btn w-100 py-2 fw-bold"
                                        style="background:#16a34a;color:#fff;border-radius:10px;font-size:.95rem;">
                                    <i class="bi bi-play-circle-fill me-2"></i>Start Route
                                </button>
                            </form>
                            @elseif($bus->isDispatchable() && ! $route)
                            <div class="alert py-2 mb-0 d-flex align-items-center gap-2"
                                 style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;font-size:.83rem;border-radius:10px;">
                                <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                                You need a route assignment before you can start. Ask your supervisor.
                            </div>
                            @endif
                        </div>

                        {{-- Secondary actions --}}
                        <div class="d-grid gap-2">
                            <a href="{{ route('driver.report-breakdown') }}" class="btn py-2"
                               style="background:#ffe4e6;color:#9f1239;border:1px solid #fecdd3;border-radius:10px;font-weight:600;font-size:.9rem;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Report a Breakdown
                            </a>
                            <a href="{{ route('driver.dashboard') }}" class="btn btn-light py-2"
                               style="border-radius:10px;font-weight:600;font-size:.9rem;">
                                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- No assignment --}}
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3"
                             style="width:80px;height:80px;background:#fef3c7;">
                            <i class="bi bi-bus-front" style="font-size:2rem;color:#92400e;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">No Assignment Yet</h5>
                        <p class="text-muted mb-4" style="font-size:.9rem;">
                            You haven't been assigned a bus for today, {{ now()->format('d F Y') }}.
                            Assignments are made automatically each morning.
                        </p>
                        <div class="alert py-2 d-inline-flex align-items-center gap-2"
                             style="background:#dbeafe;border:none;color:#1d4ed8;font-size:.83rem;border-radius:10px;">
                            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                            If it's already past 06:30 and you still have no assignment, contact your supervisor.
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('messages.create') }}" class="btn btn-sm btn-primary me-2"
                               style="background:#1c3faa;border-color:#1c3faa;border-radius:8px;">
                                <i class="bi bi-chat-dots me-1"></i>Message Supervisor
                            </a>
                            <a href="{{ route('driver.dashboard') }}" class="btn btn-sm btn-light" style="border-radius:8px;">
                                <i class="bi bi-arrow-left me-1"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@push('styles')
<style>
@keyframes pulse-dot {
    0%,100%{opacity:1;transform:scale(1);}
    50%{opacity:.5;transform:scale(1.3);}
}
</style>
@endpush

</x-app-layout>
