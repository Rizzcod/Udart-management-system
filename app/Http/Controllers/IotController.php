<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\SensorReading;

class IotController extends Controller
{
    public function dashboard()
    {
        return view('iot.dashboard', $this->buildDashboardData());
    }

    public function dashboardData()
    {
        return view('iot._content', $this->buildDashboardData())->render();
    }

    private function buildDashboardData(): array
    {
        $buses = Bus::where('status', '!=', 'inactive')
            ->orderBy('registration_number')
            ->get();

        $latestReadings = $buses->map(function ($bus) {
            $reading = $bus->sensorReadings()->latest('recorded_at')->first();

            // 24-hour average for anomaly detection
            $avg24h = $bus->sensorReadings()
                ->where('recorded_at', '>=', now()->subHours(24))
                ->selectRaw('AVG(temperature) as avg_temp, AVG(oil_temperature) as avg_oil_temp, AVG(vibration) as avg_vib, AVG(oil_pressure) as avg_pres, AVG(battery_voltage) as avg_bat')
                ->first();

            $anomalies = [];
            $trends    = ['temperature' => null, 'oil_temperature' => null, 'vibration' => null, 'oil_pressure' => null, 'battery_voltage' => null];

            if ($reading && $avg24h && $avg24h->avg_temp) {
                // Anomaly: current value deviates > 15% from 24h average
                if ($avg24h->avg_temp > 0 && abs($reading->temperature - $avg24h->avg_temp) / $avg24h->avg_temp > 0.15) {
                    $anomalies[] = ['sensor' => 'Coolant Temperature', 'current' => $reading->temperature.'°C', 'avg' => round($avg24h->avg_temp, 1).'°C'];
                }
                if ($avg24h->avg_oil_temp > 0 && abs($reading->oil_temperature - $avg24h->avg_oil_temp) / $avg24h->avg_oil_temp > 0.15) {
                    $anomalies[] = ['sensor' => 'Oil Temperature', 'current' => $reading->oil_temperature.'°C', 'avg' => round($avg24h->avg_oil_temp, 1).'°C'];
                }
                if ($avg24h->avg_vib > 0 && abs($reading->vibration - $avg24h->avg_vib) / $avg24h->avg_vib > 0.20) {
                    $anomalies[] = ['sensor' => 'Vibration', 'current' => $reading->vibration.'g', 'avg' => round($avg24h->avg_vib, 2).'g'];
                }
                if ($avg24h->avg_pres > 0 && abs($reading->oil_pressure - $avg24h->avg_pres) / $avg24h->avg_pres > 0.15) {
                    $anomalies[] = ['sensor' => 'Oil Pressure', 'current' => $reading->oil_pressure.' PSI', 'avg' => round($avg24h->avg_pres, 1).' PSI'];
                }
                if ($avg24h->avg_bat > 0 && abs($reading->battery_voltage - $avg24h->avg_bat) / $avg24h->avg_bat > 0.10) {
                    $anomalies[] = ['sensor' => 'Battery Voltage', 'current' => $reading->battery_voltage.'V', 'avg' => round($avg24h->avg_bat, 1).'V'];
                }

                // Trend arrows (up / down / stable relative to 24h avg)
                $trends['temperature']     = $this->trend($reading->temperature, $avg24h->avg_temp, 2);
                $trends['oil_temperature'] = $this->trend($reading->oil_temperature, $avg24h->avg_oil_temp, 2);
                $trends['vibration']       = $this->trend($reading->vibration, $avg24h->avg_vib, 0.1);
                $trends['oil_pressure']    = $this->trend($reading->oil_pressure, $avg24h->avg_pres, 3);
                $trends['battery_voltage'] = $this->trend($reading->battery_voltage, $avg24h->avg_bat, 0.3);
            }

            $sensorAlerts = $reading ? $reading->getSensorAlerts() : [];

            return [
                'bus'          => $bus,
                'reading'      => $reading,
                'health'       => $reading ? $reading->getHealthStatus() : 'no_data',
                'anomalies'    => $anomalies,
                'trends'       => $trends,
                'sensor_alerts' => $sensorAlerts,
            ];
        });

        $criticalCount = $latestReadings->where('health', 'critical')->count();
        $warningCount  = $latestReadings->where('health', 'warning')->count();
        $normalCount   = $latestReadings->where('health', 'normal')->count();
        $anomalyCount  = $latestReadings->filter(fn($lr) => count($lr['anomalies']) > 0)->count();

        // Buses with active alerts (critical or warning) sorted first
        $sortedReadings = $latestReadings->sortBy(fn($lr) => match($lr['health']) {
            'critical' => 0, 'warning' => 1, 'no_data' => 3, default => 2,
        })->values();

        return compact(
            'sortedReadings', 'criticalCount', 'warningCount', 'normalCount', 'anomalyCount'
        );
    }

    public function busReadings(Bus $bus)
    {
        $readings = $bus->sensorReadings()
            ->latest('recorded_at')
            ->take(100)
            ->get();

        $latestReading = $readings->first();

        return view('iot.bus', compact('bus', 'readings', 'latestReading'));
    }

    public function busReadingsData(Bus $bus)
    {
        $readings = $bus->sensorReadings()
            ->latest('recorded_at')
            ->take(100)
            ->get();

        $latestReading = $readings->first();

        return response()->json([
            'readings' => $readings->reverse()->values(),
            'health'   => $latestReading?->getHealthStatus(),
            'last_reading_at' => $latestReading?->recorded_at?->diffForHumans(),
            'table_rows' => $readings->map(function ($r) {
                $h = $r->getHealthStatus();
                return [
                    'time'   => $r->recorded_at->format('d M H:i:s'),
                    'temperature' => $r->temperature,
                    'oil_temperature' => $r->oil_temperature,
                    'vibration' => $r->vibration,
                    'oil_pressure' => $r->oil_pressure,
                    'battery_voltage' => $r->battery_voltage,
                    'health' => $h,
                ];
            }),
        ]);
    }

    // -----------------------------------------------------------------------
    private function trend(?float $current, ?float $avg, float $threshold): string
    {
        if ($current === null || $avg === null || $avg == 0) return 'stable';
        $diff = $current - $avg;
        if (abs($diff) < $threshold) return 'stable';
        return $diff > 0 ? 'up' : 'down';
    }
}
