<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreventiveMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $buses = Bus::pluck('id', 'registration_number');

        $schedules = [
            // UDART-001 (mileage 85,000, year 2019) - active
            ['bus' => 'UDART-001', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-05-10', 'last_service_km' => 85000, 'next_service_date' => '2026-08-07', 'status' => 'upcoming',  'notes' => 'Use 15W-40 diesel engine oil'],
            ['bus' => 'UDART-001', 'service_type' => 'Brake Inspection',         'interval_days' => 180, 'interval_km' => 10000, 'last_service_date' => '2026-02-20', 'last_service_km' => 80000, 'next_service_date' => '2026-08-19', 'status' => 'upcoming',  'notes' => null],
            ['bus' => 'UDART-001', 'service_type' => 'Tire Rotation',            'interval_days' => 120, 'interval_km' => 8000,  'last_service_date' => '2026-03-01', 'last_service_km' => 82000, 'next_service_date' => '2026-06-29', 'status' => 'upcoming',  'notes' => 'Check tire pressure as well'],

            // UDART-002 (mileage 92,000, year 2019) - active
            ['bus' => 'UDART-002', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-04-15', 'last_service_km' => 90000, 'next_service_date' => '2026-07-14', 'status' => 'upcoming',  'notes' => null],
            ['bus' => 'UDART-002', 'service_type' => 'Timing Belt Replacement', 'interval_days' => 730, 'interval_km' => 60000, 'last_service_date' => '2026-05-02', 'last_service_km' => 92000, 'next_service_date' => '2028-05-02', 'status' => 'completed', 'notes' => 'Just replaced, next due in 2 years or 60,000 km'],
            ['bus' => 'UDART-002', 'service_type' => 'Air Filter Replacement',  'interval_days' => 180, 'interval_km' => 15000, 'last_service_date' => '2026-01-22', 'last_service_km' => 88000, 'next_service_date' => '2026-06-22', 'status' => 'overdue',   'notes' => 'High dust environment, may need earlier replacement'],

            // UDART-003 (mileage 67,000, year 2020) - active
            ['bus' => 'UDART-003', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-04-20', 'last_service_km' => 65000, 'next_service_date' => '2026-07-19', 'status' => 'upcoming',  'notes' => null],
            ['bus' => 'UDART-003', 'service_type' => 'Transmission Service',    'interval_days' => 365, 'interval_km' => 40000, 'last_service_date' => '2025-06-10', 'last_service_km' => 48000, 'next_service_date' => '2026-06-10', 'status' => 'overdue',   'notes' => 'Overdue - schedule immediately'],
            ['bus' => 'UDART-003', 'service_type' => 'Coolant Flush',           'interval_days' => 365, 'interval_km' => 40000, 'last_service_date' => '2025-07-01', 'last_service_km' => 45000, 'next_service_date' => '2026-07-01', 'status' => 'upcoming',  'notes' => null],

            // UDART-004 (mileage 71,000, year 2020) - under_repair
            ['bus' => 'UDART-004', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-05-20', 'last_service_km' => 71000, 'next_service_date' => '2026-08-18', 'status' => 'upcoming',  'notes' => 'On hold while bus is under repair'],
            ['bus' => 'UDART-004', 'service_type' => 'Clutch Inspection',       'interval_days' => 365, 'interval_km' => 30000, 'last_service_date' => '2026-03-10', 'last_service_km' => 70000, 'next_service_date' => '2027-03-10', 'status' => 'completed', 'notes' => 'Full clutch kit replaced'],

            // UDART-005 (mileage 45,000, year 2021) - active
            ['bus' => 'UDART-005', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-04-01', 'last_service_km' => 43000, 'next_service_date' => '2026-06-30', 'status' => 'upcoming',  'notes' => null],
            ['bus' => 'UDART-005', 'service_type' => 'Battery Check',           'interval_days' => 180, 'interval_km' => null,  'last_service_date' => '2026-01-30', 'last_service_km' => null,  'next_service_date' => '2026-07-30', 'status' => 'upcoming',  'notes' => 'Battery replaced 2026-01-30'],
            ['bus' => 'UDART-005', 'service_type' => 'Brake Fluid Flush',       'interval_days' => 730, 'interval_km' => null,  'last_service_date' => '2026-06-01', 'last_service_km' => null,  'next_service_date' => '2028-06-01', 'status' => 'completed', 'notes' => null],

            // UDART-006 (mileage 48,000, year 2021) - active
            ['bus' => 'UDART-006', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-03-15', 'last_service_km' => 46000, 'next_service_date' => '2026-06-13', 'status' => 'overdue',   'notes' => 'Overdue by 1 day - schedule today'],
            ['bus' => 'UDART-006', 'service_type' => 'Wheel Alignment',         'interval_days' => 180, 'interval_km' => 20000, 'last_service_date' => '2026-06-05', 'last_service_km' => 48000, 'next_service_date' => '2026-12-05', 'status' => 'upcoming',  'notes' => 'Alignment corrected after new tires'],
            ['bus' => 'UDART-006', 'service_type' => 'Fuel Injector Service',   'interval_days' => 365, 'interval_km' => 30000, 'last_service_date' => '2026-02-28', 'last_service_km' => 46000, 'next_service_date' => '2027-02-28', 'status' => 'completed', 'notes' => null],

            // UDART-007 (mileage 115,000, year 2018) - active, oldest bus
            ['bus' => 'UDART-007', 'service_type' => 'Engine Oil Change',       'interval_days' => 60,  'interval_km' => 3000,  'last_service_date' => '2026-05-01', 'last_service_km' => 114000,'next_service_date' => '2026-06-10', 'status' => 'overdue',   'notes' => 'High mileage bus - more frequent oil changes required'],
            ['bus' => 'UDART-007', 'service_type' => 'Transmission Service',    'interval_days' => 365, 'interval_km' => 40000, 'last_service_date' => '2026-03-15', 'last_service_km' => 113000,'next_service_date' => '2027-03-15', 'status' => 'completed', 'notes' => null],
            ['bus' => 'UDART-007', 'service_type' => 'Suspension Inspection',   'interval_days' => 180, 'interval_km' => 20000, 'last_service_date' => '2025-12-01', 'last_service_km' => 108000,'next_service_date' => '2026-06-01', 'status' => 'overdue',   'notes' => 'High mileage bus requires close monitoring'],

            // UDART-008 (mileage 28,000, year 2022) - active, newer bus
            ['bus' => 'UDART-008', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-05-01', 'last_service_km' => 26000, 'next_service_date' => '2026-07-30', 'status' => 'upcoming',  'notes' => null],
            ['bus' => 'UDART-008', 'service_type' => 'Brake Inspection',         'interval_days' => 180, 'interval_km' => 10000, 'last_service_date' => '2026-04-01', 'last_service_km' => 25000, 'next_service_date' => '2026-09-28', 'status' => 'upcoming',  'notes' => 'Rear drums replaced during last inspection'],

            // UDART-009 (mileage 31,000, year 2022) - inactive
            ['bus' => 'UDART-009', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-02-10', 'last_service_km' => 29000, 'next_service_date' => '2026-05-11', 'status' => 'overdue',   'notes' => 'Bus inactive - service when returned to active fleet'],
            ['bus' => 'UDART-009', 'service_type' => 'Coolant Flush',           'interval_days' => 365, 'interval_km' => 40000, 'last_service_date' => '2026-03-22', 'last_service_km' => 30000, 'next_service_date' => '2027-03-22', 'status' => 'completed', 'notes' => 'Thermostat replaced during flush'],

            // UDART-010 (mileage 12,000, year 2023) - active, newest bus
            ['bus' => 'UDART-010', 'service_type' => 'Engine Oil Change',       'interval_days' => 90,  'interval_km' => 5000,  'last_service_date' => '2026-04-10', 'last_service_km' => 12000, 'next_service_date' => '2026-07-09', 'status' => 'upcoming',  'notes' => 'First service completed. Synthetic oil used.'],
            ['bus' => 'UDART-010', 'service_type' => 'Air Filter Replacement',  'interval_days' => 180, 'interval_km' => 15000, 'last_service_date' => null,          'last_service_km' => null,  'next_service_date' => '2026-10-01', 'status' => 'upcoming',  'notes' => 'Factory filter still in place'],
        ];

        foreach ($schedules as $schedule) {
            $busId = $buses[$schedule['bus']] ?? null;
            if (!$busId) continue;

            DB::table('preventive_maintenance')->insertOrIgnore([
                'bus_id'           => $busId,
                'service_type'     => $schedule['service_type'],
                'interval_days'    => $schedule['interval_days'],
                'interval_km'      => $schedule['interval_km'],
                'last_service_date'=> SeedDate::shift($schedule['last_service_date']),
                'last_service_km'  => $schedule['last_service_km'],
                'next_service_date'=> SeedDate::shift($schedule['next_service_date']),
                'status'           => $schedule['status'],
                'notes'            => $schedule['notes'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
}
