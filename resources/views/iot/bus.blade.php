<x-app-layout title="IoT — {{ $bus->registration_number }}">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-broadcast me-2"></i>Sensor History — {{ $bus->registration_number }}</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small"><i class="bi bi-arrow-repeat"></i> Live — updates every 1s</span>
            <a href="{{ route('iot.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    <div id="healthBanner">
    @if($latestReading)
    @php $health = $latestReading->getHealthStatus(); $healthColor = ['normal'=>'success','warning'=>'warning','critical'=>'danger'][$health]; @endphp
    <div class="alert alert-{{ $healthColor }} d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-{{ $health === 'normal' ? 'check-circle' : 'exclamation-triangle' }}-fill fs-5"></i>
        <div>Current status: <strong>{{ strtoupper($health) }}</strong> — last reading {{ $latestReading->recorded_at->diffForHumans() }}</div>
    </div>
    @endif
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-12">
            <div class="card p-3">
                <div class="fw-semibold mb-2">Coolant Temperature (°C) — Last 50 readings</div>
                <canvas id="tempChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card p-3">
                <div class="fw-semibold mb-2">Oil Temperature (°C) — Last 50 readings</div>
                <canvas id="oilTempChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3">
                <div class="fw-semibold mb-2">Vibration (g) — Last 50 readings</div>
                <canvas id="vibChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3">
                <div class="fw-semibold mb-2">Oil Pressure (PSI)</div>
                <canvas id="pressureChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3">
                <div class="fw-semibold mb-2">Battery Voltage (V)</div>
                <canvas id="battChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="fw-semibold p-3 border-bottom">Raw Reading Log (latest 100)</div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light"><tr><th>Time</th><th>Coolant (°C)</th><th>Oil Temp (°C)</th><th>Vibration (g)</th><th>Oil (PSI)</th><th>Battery (V)</th><th>Status</th></tr></thead>
                <tbody id="readingsTableBody">
                    @forelse($readings as $r)
                    @php $h = $r->getHealthStatus(); $hc = ['normal'=>'success','warning'=>'warning','critical'=>'danger'][$h]; @endphp
                    <tr class="{{ $h === 'critical' ? 'table-danger' : ($h === 'warning' ? 'table-warning' : '') }}">
                        <td class="text-muted small">{{ $r->recorded_at->format('d M H:i:s') }}</td>
                        <td>{{ $r->temperature }}</td>
                        <td>{{ $r->oil_temperature }}</td>
                        <td>{{ $r->vibration }}</td>
                        <td>{{ $r->oil_pressure }}</td>
                        <td>{{ $r->battery_voltage }}</td>
                        <td><span class="badge bg-{{ $hc }}">{{ $h }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">No readings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    const dataUrl = @json(route('iot.bus.data', $bus));
    const healthColors = { normal: 'success', warning: 'warning', critical: 'danger' };
    const charts = {};

    function makeChart(id, label, key, color) {
        const ctx = document.getElementById(id);
        if (!ctx) return null;
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label,
                    data: [],
                    borderColor: color,
                    backgroundColor: color + '22',
                    borderWidth: 1.5,
                    pointRadius: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                animation: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: false } }
            }
        });
    }

    charts.temp     = makeChart('tempChart',    'Coolant Temp','temperature',     '#dc3545');
    charts.oilTemp  = makeChart('oilTempChart', 'Oil Temp',    'oil_temperature', '#6f42c1');
    charts.vib      = makeChart('vibChart',     'Vibration',   'vibration',       '#fd7e14');
    charts.pressure = makeChart('pressureChart','Oil Pressure','oil_pressure',    '#0d6efd');
    charts.batt     = makeChart('battChart',    'Battery',     'battery_voltage', '#198754');

    function updateChart(chart, readings, key) {
        if (!chart) return;
        chart.data.labels = readings.map(r => r.recorded_at.substring(11, 19));
        chart.data.datasets[0].data = readings.map(r => r[key]);
        chart.update('none');
    }

    function updateBanner(health, lastReadingAt) {
        const banner = document.getElementById('healthBanner');
        if (!health) { banner.innerHTML = ''; return; }
        const color = healthColors[health] ?? 'secondary';
        const icon = health === 'normal' ? 'check-circle' : 'exclamation-triangle';
        banner.innerHTML = `
            <div class="alert alert-${color} d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-${icon}-fill fs-5"></i>
                <div>Current status: <strong>${health.toUpperCase()}</strong> — last reading ${lastReadingAt}</div>
            </div>`;
    }

    function updateTable(rows) {
        const tbody = document.getElementById('readingsTableBody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No readings yet.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.slice().reverse().map(r => {
            const hc = healthColors[r.health] ?? 'secondary';
            const rowClass = r.health === 'critical' ? 'table-danger' : (r.health === 'warning' ? 'table-warning' : '');
            return `
                <tr class="${rowClass}">
                    <td class="text-muted small">${r.time}</td>
                    <td>${r.temperature}</td>
                    <td>${r.oil_temperature}</td>
                    <td>${r.vibration}</td>
                    <td>${r.oil_pressure}</td>
                    <td>${r.battery_voltage}</td>
                    <td><span class="badge bg-${hc}">${r.health}</span></td>
                </tr>`;
        }).join('');
    }

    async function refreshData() {
        try {
            const res = await fetch(dataUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();

            updateChart(charts.temp,     data.readings, 'temperature');
            updateChart(charts.oilTemp,  data.readings, 'oil_temperature');
            updateChart(charts.vib,      data.readings, 'vibration');
            updateChart(charts.pressure, data.readings, 'oil_pressure');
            updateChart(charts.batt,     data.readings, 'battery_voltage');

            updateBanner(data.health, data.last_reading_at);
            updateTable(data.table_rows);
        } catch (e) {
            console.error('IoT refresh failed', e);
        }
    }

    refreshData();
    setInterval(refreshData, 1000);
    </script>
    @endpush
</x-app-layout>
