<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FailurePredictionSeeder extends Seeder
{
    public function run(): void
    {
        $buses = Bus::pluck('id', 'registration_number');

        $predictions = [
            [
                'bus'                   => 'UDART-004',
                'risk_level'            => 'high',
                'predicted_failure_type'=> 'Cooling System Failure',
                'confidence_score'      => 91.50,
                'recommended_action'    => 'Immediate inspection of radiator, coolant hoses, and water pump. Bus currently showing sustained temperatures above 108°C. Take out of service until resolved.',
                'predicted_at'          => now()->subHours(6),
            ],
            [
                'bus'                   => 'UDART-007',
                'risk_level'            => 'high',
                'predicted_failure_type'=> 'Engine Wear / Oil System Degradation',
                'confidence_score'      => 87.30,
                'recommended_action'    => 'Engine oil pressure consistently at 28-32 PSI, dropping below safe threshold. High mileage (115,000 km) with increased vibration (0.72-0.80g). Schedule comprehensive engine inspection within 72 hours.',
                'predicted_at'          => now()->subHours(12),
            ],
            [
                'bus'                   => 'UDART-003',
                'risk_level'            => 'medium',
                'predicted_failure_type'=> 'Transmission or Drivetrain Issue',
                'confidence_score'      => 74.80,
                'recommended_action'    => 'Elevated vibration readings (0.55-0.62g) combined with overdue transmission service. Inspect transmission mounts, driveshaft, and fluid level. Schedule service within 1 week.',
                'predicted_at'          => now()->subHours(18),
            ],
            [
                'bus'                   => 'UDART-008',
                'risk_level'            => 'medium',
                'predicted_failure_type'=> 'Gearbox Internal Component Failure',
                'confidence_score'      => 82.10,
                'recommended_action'    => 'Bus currently under repair for transmission slipping. Sensor data consistent with internal clutch pack wear. Complete gearbox overhaul recommended rather than partial repair.',
                'predicted_at'          => now()->subHours(24),
            ],
            [
                'bus'                   => 'UDART-002',
                'risk_level'            => 'low',
                'predicted_failure_type'=> 'Air Intake Restriction',
                'confidence_score'      => 63.40,
                'recommended_action'    => 'Air filter overdue for replacement (last changed at 88,000 km, now at 92,000 km). Slightly elevated engine temperatures suggest reduced airflow. Replace air filter within 2 weeks.',
                'predicted_at'          => now()->subDay(),
            ],
            [
                'bus'                   => 'UDART-006',
                'risk_level'            => 'low',
                'predicted_failure_type'=> 'Lubrication System Strain',
                'confidence_score'      => 58.20,
                'recommended_action'    => 'Engine oil change is overdue (as of 2026-06-13). Minor increase in oil temperature detected. Change oil and filter immediately to prevent accelerated wear.',
                'predicted_at'          => now()->subHours(8),
            ],
            [
                'bus'                   => 'UDART-001',
                'risk_level'            => 'low',
                'predicted_failure_type'=> 'Brake System Degradation',
                'confidence_score'      => 51.70,
                'recommended_action'    => 'Brake inspection due in approximately 2 months. No immediate concern but monitor for any brake-related driver reports. Normal operating conditions detected.',
                'predicted_at'          => now()->subHours(36),
            ],
            [
                'bus'                   => 'UDART-009',
                'risk_level'            => 'medium',
                'predicted_failure_type'=> 'Battery / Electrical System Failure',
                'confidence_score'      => 78.90,
                'recommended_action'    => 'Bus previously had dead battery incident. Currently inactive. Before returning to service, perform full electrical system check including alternator output, battery capacity test, and wiring harness inspection.',
                'predicted_at'          => now()->subDays(2),
            ],
        ];

        foreach ($predictions as $prediction) {
            $busId = $buses[$prediction['bus']] ?? null;
            if (!$busId) continue;

            DB::table('failure_predictions')->insert([
                'bus_id'                 => $busId,
                'risk_level'             => $prediction['risk_level'],
                'predicted_failure_type' => $prediction['predicted_failure_type'],
                'confidence_score'       => $prediction['confidence_score'],
                'recommended_action'     => $prediction['recommended_action'],
                'predicted_at'           => $prediction['predicted_at'],
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }
}
