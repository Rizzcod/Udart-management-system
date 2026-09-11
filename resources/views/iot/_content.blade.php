{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center border-danger border-2 h-100">
            <div class="fs-2 fw-bold text-danger">{{ $criticalCount }}</div>
            <div class="small text-muted">Critical</div>
            <div class="small text-danger">Immediate action required</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center border-warning border-2 h-100">
            <div class="fs-2 fw-bold text-warning">{{ $warningCount }}</div>
            <div class="small text-muted">Warning</div>
            <div class="small text-warning">Monitor closely</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center border-success border-2 h-100">
            <div class="fs-2 fw-bold text-success">{{ $normalCount }}</div>
            <div class="small text-muted">Normal</div>
            <div class="small text-success">Operating normally</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center border-info border-2 h-100">
            <div class="fs-2 fw-bold text-info">{{ $anomalyCount }}</div>
            <div class="small text-muted">Anomalies Detected</div>
            <div class="small text-info">Sudden spike vs 24h avg</div>
        </div>
    </div>
</div>

{{-- Live Alert Panel (only shown when critical/warning buses exist) --}}
@php $alertBuses = $sortedReadings->filter(fn($lr) => in_array($lr['health'], ['critical', 'warning'])); @endphp
@if($alertBuses->isNotEmpty())
<div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <h6 class="mb-0">Live Sensor Alerts — {{ $alertBuses->count() }} Bus(es) Require Attention</h6>
    </div>
    <div class="list-group list-group-flush">
        @foreach($alertBuses as $lr)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="fw-bold">{{ $lr['bus']->registration_number }}</span>
                    <span class="text-muted small ms-2">{{ $lr['bus']->model }}</span>
                </div>
                <span class="badge bg-{{ $lr['health'] === 'critical' ? 'danger' : 'warning text-dark' }}">
                    {{ strtoupper($lr['health']) }}
                </span>
            </div>
            <div class="mt-1 d-flex flex-wrap gap-2">
                @foreach($lr['sensor_alerts'] as $alert)
                <span class="badge bg-{{ $alert['severity'] === 'critical' ? 'danger' : 'warning text-dark' }} small">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $alert['sensor'] }}: {{ $alert['value'] }}
                </span>
                @endforeach
                @if(count($lr['anomalies']) > 0)
                    @foreach($lr['anomalies'] as $a)
                    <span class="badge bg-info text-dark small">
                        <i class="bi bi-graph-up-arrow me-1"></i>
                        {{ $a['sensor'] }} spike: {{ $a['current'] }} (avg {{ $a['avg'] }})
                    </span>
                    @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Bus Sensor Cards --}}
<div class="row g-3">
    @forelse($sortedReadings as $lr)
    @php
        $health   = $lr['health'];
        $colorMap = ['normal' => 'success', 'warning' => 'warning', 'critical' => 'danger', 'no_data' => 'secondary'];
        $color    = $colorMap[$health];
        $reading  = $lr['reading'];
        $trends   = $lr['trends'];

        $trendIcon = fn($t, $higher_is_bad = true) => match($t) {
            'up'   => $higher_is_bad ? '<i class="bi bi-arrow-up text-danger" title="Rising"></i>' : '<i class="bi bi-arrow-up text-success" title="Rising"></i>',
            'down' => $higher_is_bad ? '<i class="bi bi-arrow-down text-success" title="Falling"></i>' : '<i class="bi bi-arrow-down text-danger" title="Falling"></i>',
            default => '<i class="bi bi-dash text-muted" title="Stable"></i>',
        };
    @endphp
    <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
        <div class="card border-{{ $color }} border-2 h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <div>
                    <span class="fw-bold">{{ $lr['bus']->registration_number }}</span>
                    <span class="text-muted small ms-1">· {{ $lr['bus']->model }}</span>
                </div>
                <span class="badge bg-{{ $color }} {{ $color === 'warning' ? 'text-dark' : '' }}">
                    {{ strtoupper(str_replace('_', ' ', $health)) }}
                </span>
            </div>
            <div class="card-body p-3">
                @if($reading)
                <div class="row g-2 text-center mb-2">
                    {{-- Temperature --}}
                    <div class="col-6">
                        <div class="bg-light rounded p-2 position-relative">
                            <i class="bi bi-thermometer-half text-danger"></i>
                            <div class="fw-semibold small">
                                {{ $reading->temperature }}°C
                                {!! $trendIcon($trends['temperature'], true) !!}
                            </div>
                            <div style="font-size:.65rem;" class="text-muted">Heat / Temp</div>
                            @if($reading->temperature > 85)
                            <span style="font-size:.6rem;" class="badge bg-{{ $reading->temperature > 95 ? 'danger' : 'warning text-dark' }} position-absolute top-0 end-0 m-1">!</span>
                            @endif
                        </div>
                    </div>
                    {{-- Oil Temperature --}}
                    <div class="col-6">
                        <div class="bg-light rounded p-2 position-relative">
                            <i class="bi bi-thermometer-half text-primary"></i>
                            <div class="fw-semibold small">
                                {{ $reading->oil_temperature }}°C
                                {!! $trendIcon($trends['oil_temperature'], true) !!}
                            </div>
                            <div style="font-size:.65rem;" class="text-muted">Oil Temp</div>
                            @if($reading->oil_temperature > 115)
                            <span style="font-size:.6rem;" class="badge bg-{{ $reading->oil_temperature > 130 ? 'danger' : 'warning text-dark' }} position-absolute top-0 end-0 m-1">!</span>
                            @endif
                        </div>
                    </div>
                    {{-- Vibration --}}
                    <div class="col-6">
                        <div class="bg-light rounded p-2 position-relative">
                            <i class="bi bi-activity text-warning"></i>
                            <div class="fw-semibold small">
                                {{ $reading->vibration }}g
                                {!! $trendIcon($trends['vibration'], true) !!}
                            </div>
                            <div style="font-size:.65rem;" class="text-muted">Vibration</div>
                            @if($reading->vibration > 1.5)
                            <span style="font-size:.6rem;" class="badge bg-{{ $reading->vibration > 2.5 ? 'danger' : 'warning text-dark' }} position-absolute top-0 end-0 m-1">!</span>
                            @endif
                        </div>
                    </div>
                    {{-- Oil Pressure --}}
                    <div class="col-6">
                        <div class="bg-light rounded p-2 position-relative">
                            <i class="bi bi-droplet-fill text-primary"></i>
                            <div class="fw-semibold small">
                                {{ $reading->oil_pressure }} PSI
                                {!! $trendIcon($trends['oil_pressure'], false) !!}
                            </div>
                            <div style="font-size:.65rem;" class="text-muted">Oil Pressure</div>
                            @if($reading->oil_pressure < 30)
                            <span style="font-size:.6rem;" class="badge bg-{{ $reading->oil_pressure < 20 ? 'danger' : 'warning text-dark' }} position-absolute top-0 end-0 m-1">!</span>
                            @endif
                        </div>
                    </div>
                    {{-- Battery --}}
                    <div class="col-6">
                        <div class="bg-light rounded p-2 position-relative">
                            <i class="bi bi-battery-charging text-success"></i>
                            <div class="fw-semibold small">
                                {{ $reading->battery_voltage }}V
                                {!! $trendIcon($trends['battery_voltage'], false) !!}
                            </div>
                            <div style="font-size:.65rem;" class="text-muted">Battery</div>
                            @if($reading->battery_voltage < 12)
                            <span style="font-size:.6rem;" class="badge bg-{{ $reading->battery_voltage < 11 ? 'danger' : 'warning text-dark' }} position-absolute top-0 end-0 m-1">!</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Anomaly flags --}}
                @if(count($lr['anomalies']) > 0)
                <div class="alert alert-info py-1 px-2 mb-2 small">
                    <i class="bi bi-graph-up-arrow me-1"></i><strong>Anomaly:</strong>
                    @foreach($lr['anomalies'] as $a)
                        {{ $a['sensor'] }} spike ({{ $a['current'] }} vs avg {{ $a['avg'] }})@if(!$loop->last),@endif
                    @endforeach
                </div>
                @endif

                <div class="text-muted mt-1" style="font-size:.7rem;">
                    <i class="bi bi-clock me-1"></i>Last reading: {{ $reading->recorded_at->diffForHumans() }}
                </div>
                @else
                <div class="text-muted small py-3 text-center">
                    <i class="bi bi-wifi-off fs-4 d-block mb-1"></i>
                    No sensor data received.<br>Connect the ESP32 device.
                </div>
                @endif
            </div>
            <div class="card-footer p-2">
                <a href="{{ route('iot.bus', $lr['bus']) }}" class="btn btn-sm btn-outline-{{ $color }} w-100">
                    <i class="bi bi-graph-up me-1"></i>View Sensor History
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-bus-front fs-1 d-block mb-2"></i>No buses found.
    </div>
    @endforelse
</div>
