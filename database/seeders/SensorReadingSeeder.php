<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        $buses = Bus::pluck('id', 'registration_number');

        // Realistic sensor profiles per bus
        // temperature: engine temp in °C (normal 80-100, warning >105, critical >115)
        // vibration: g-force (normal 0.1-0.5, warning >0.8, critical >1.2)
        // oil_pressure: PSI (normal 25-65, warning <20 or >80)
        // battery_voltage: V (normal 13.5-14.5 running, 12.4-12.7 at rest)

        $profiles = [
            'UDART-001' => ['temp' => [88, 91, 89, 93, 87],  'vib' => [0.22, 0.19, 0.25, 0.21, 0.23], 'oil' => [48, 50, 47, 52, 49], 'batt' => [14.1, 14.2, 14.0, 14.3, 14.1]],
            'UDART-002' => ['temp' => [92, 95, 90, 94, 96],  'vib' => [0.30, 0.28, 0.33, 0.31, 0.29], 'oil' => [45, 46, 44, 47, 43], 'batt' => [14.0, 13.9, 14.1, 14.2, 14.0]],
            'UDART-003' => ['temp' => [95, 98, 100, 97, 99], 'vib' => [0.55, 0.60, 0.58, 0.62, 0.57], 'oil' => [38, 36, 40, 37, 39], 'batt' => [13.8, 13.7, 13.9, 13.8, 13.6]],
            'UDART-004' => ['temp' => [108,112,115,110,113], 'vib' => [0.85, 0.90, 0.88, 0.92, 0.87], 'oil' => [22, 20, 24, 21, 23], 'batt' => [13.5, 13.4, 13.6, 13.5, 13.3]],
            'UDART-005' => ['temp' => [86, 88, 85, 87, 89],  'vib' => [0.18, 0.20, 0.17, 0.19, 0.21], 'oil' => [52, 54, 51, 53, 55], 'batt' => [14.3, 14.2, 14.4, 14.3, 14.5]],
            'UDART-006' => ['temp' => [90, 92, 91, 93, 94],  'vib' => [0.25, 0.27, 0.24, 0.26, 0.28], 'oil' => [47, 48, 46, 49, 47], 'batt' => [14.0, 14.1, 13.9, 14.0, 14.2]],
            'UDART-007' => ['temp' => [101,104,107,103,106], 'vib' => [0.72, 0.78, 0.75, 0.80, 0.77], 'oil' => [30, 28, 32, 29, 31], 'batt' => [13.6, 13.5, 13.7, 13.4, 13.6]],
            'UDART-008' => ['temp' => [84, 86, 85, 87, 83],  'vib' => [0.15, 0.17, 0.14, 0.16, 0.18], 'oil' => [55, 57, 54, 56, 58], 'batt' => [14.4, 14.3, 14.5, 14.4, 14.3]],
        ];

        // Generate 5 readings over the last 3 days (every ~14 hours)
        $baseTime = now()->subDays(3);

        foreach ($profiles as $regNum => $data) {
            $busId = $buses[$regNum] ?? null;
            if (!$busId) continue;

            for ($i = 0; $i < 5; $i++) {
                $recordedAt = $baseTime->copy()->addHours($i * 14);

                DB::table('sensor_readings')->insert([
                    'bus_id'          => $busId,
                    'temperature'     => $data['temp'][$i],
                    'oil_temperature' => $data['temp'][$i] + 9,
                    'vibration'       => $data['vib'][$i],
                    'oil_pressure'    => $data['oil'][$i],
                    'battery_voltage' => $data['batt'][$i],
                    'recorded_at'     => $recordedAt,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
