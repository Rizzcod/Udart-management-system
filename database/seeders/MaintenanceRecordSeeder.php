<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $tech1 = User::where('email', 'tech@udart.co.tz')->first();
        $tech2 = User::where('email', 'tech2@udart.co.tz')->first();
        $tech3 = User::where('email', 'tech3@udart.co.tz')->first();

        if (!$tech1) return;

        $t2 = $tech2 ?? $tech1;
        $t3 = $tech3 ?? $tech1;

        $buses = Bus::pluck('id', 'registration_number');

        $records = [
            ['bus' => 'UDART-001', 'tech' => $tech1->id, 'description' => 'Routine engine oil change and filter replacement', 'date' => '2026-01-15', 'cost' => 45000, 'parts_replaced' => 'Engine Oil Filter, Engine Oil (10L)', 'notes' => 'Oil was very dark, changed on schedule'],
            ['bus' => 'UDART-001', 'tech' => $t2->id,   'description' => 'Front brake pad inspection and replacement', 'date' => '2026-02-20', 'cost' => 85000, 'parts_replaced' => 'Brake Pads (Front)', 'notes' => 'Pads worn below 3mm, replaced both sides'],
            ['bus' => 'UDART-001', 'tech' => $tech1->id, 'description' => 'Shock absorber replacement - front axle', 'date' => '2026-04-18', 'cost' => 560000, 'parts_replaced' => 'Shock Absorber (Front) x2', 'notes' => 'Driver reported excessive bouncing on rough roads'],
            ['bus' => 'UDART-002', 'tech' => $t2->id,   'description' => 'Air filter replacement and engine bay cleaning', 'date' => '2026-01-22', 'cost' => 22000, 'parts_replaced' => 'Air Filter', 'notes' => 'Filter heavily clogged with dust'],
            ['bus' => 'UDART-002', 'tech' => $tech1->id, 'description' => 'Timing belt replacement and tensioner check', 'date' => '2026-05-02', 'cost' => 195000, 'parts_replaced' => 'Timing Belt', 'notes' => 'Replaced at 90,000 km as per manufacturer schedule'],
            ['bus' => 'UDART-002', 'tech' => $t3->id,   'description' => 'Alternator failure - replacement', 'date' => '2026-05-15', 'cost' => 750000, 'parts_replaced' => 'Alternator', 'notes' => 'Charging system completely failed, battery drained'],
            ['bus' => 'UDART-003', 'tech' => $tech1->id, 'description' => 'Tire rotation and wheel balancing', 'date' => '2026-02-05', 'cost' => 15000, 'parts_replaced' => null, 'notes' => 'All four tires rotated, balance corrected'],
            ['bus' => 'UDART-003', 'tech' => $t2->id,   'description' => 'Alternator replacement after charging failure', 'date' => '2026-05-15', 'cost' => 750000, 'parts_replaced' => 'Alternator', 'notes' => 'Bus stopped mid-route, towed to workshop'],
            ['bus' => 'UDART-004', 'tech' => $tech1->id, 'description' => 'Clutch plate and pressure plate replacement', 'date' => '2026-03-10', 'cost' => 320000, 'parts_replaced' => 'Clutch Plate', 'notes' => 'Clutch slipping under load, replaced full clutch kit'],
            ['bus' => 'UDART-004', 'tech' => $t2->id,   'description' => 'Engine oil change after clutch repair', 'date' => '2026-05-20', 'cost' => 45000, 'parts_replaced' => 'Engine Oil Filter', 'notes' => 'Routine maintenance post major repair'],
            ['bus' => 'UDART-005', 'tech' => $t3->id,   'description' => 'Battery replacement - 12V 150Ah', 'date' => '2026-01-30', 'cost' => 450000, 'parts_replaced' => 'Battery 12V 150Ah', 'notes' => 'Battery failed to hold charge, replaced under warranty'],
            ['bus' => 'UDART-005', 'tech' => $tech1->id, 'description' => 'Brake fluid flush and bleeding', 'date' => '2026-06-01', 'cost' => 30000, 'parts_replaced' => null, 'notes' => 'Fluid discoloured and contaminated with moisture'],
            ['bus' => 'UDART-006', 'tech' => $t2->id,   'description' => 'Fuel injector cleaning and calibration', 'date' => '2026-02-28', 'cost' => 80000, 'parts_replaced' => null, 'notes' => 'Rough idling resolved after cleaning injectors'],
            ['bus' => 'UDART-006', 'tech' => $tech1->id, 'description' => 'Wheel alignment and steering adjustment', 'date' => '2026-06-05', 'cost' => 45000, 'parts_replaced' => null, 'notes' => 'Bus was pulling left, alignment corrected'],
            ['bus' => 'UDART-007', 'tech' => $t3->id,   'description' => 'Transmission fluid change and filter replacement', 'date' => '2026-03-15', 'cost' => 250000, 'parts_replaced' => null, 'notes' => 'Transmission fluid dark and burnt, flushed and refilled'],
            ['bus' => 'UDART-007', 'tech' => $tech1->id, 'description' => 'AC compressor service and refrigerant recharge', 'date' => '2026-06-08', 'cost' => 180000, 'parts_replaced' => null, 'notes' => 'AC not cooling, compressor serviced and gas recharged'],
            ['bus' => 'UDART-008', 'tech' => $t2->id,   'description' => 'Rear brake pad and drum replacement', 'date' => '2026-04-01', 'cost' => 170000, 'parts_replaced' => 'Brake Pads (Rear)', 'notes' => 'Squealing noise reported by driver, replaced pads and drums'],
            ['bus' => 'UDART-008', 'tech' => $tech1->id, 'description' => 'Fuel filter replacement', 'date' => '2026-06-10', 'cost' => 28000, 'parts_replaced' => null, 'notes' => 'Scheduled fuel filter change at 25,000 km'],
            ['bus' => 'UDART-009', 'tech' => $t3->id,   'description' => 'Coolant flush and thermostat replacement', 'date' => '2026-03-22', 'cost' => 54000, 'parts_replaced' => 'Coolant (5L)', 'notes' => 'Engine running hot, thermostat stuck closed'],
            ['bus' => 'UDART-009', 'tech' => $t2->id,   'description' => 'Exhaust system repair - muffler and pipe', 'date' => '2026-05-25', 'cost' => 120000, 'parts_replaced' => null, 'notes' => 'Loud exhaust noise, muffler corroded and replaced'],
            ['bus' => 'UDART-010', 'tech' => $tech1->id, 'description' => 'First scheduled oil change at 10,000 km', 'date' => '2026-04-10', 'cost' => 45000, 'parts_replaced' => 'Engine Oil Filter', 'notes' => 'New bus first service completed successfully'],
        ];

        foreach ($records as $record) {
            $busId = $buses[$record['bus']] ?? null;
            if (!$busId) continue;

            DB::table('maintenance_records')->insertOrIgnore([
                'bus_id'         => $busId,
                'technician_id'  => $record['tech'],
                'description'    => $record['description'],
                'maintenance_date' => SeedDate::shift($record['date']),
                'cost'           => $record['cost'],
                'parts_replaced' => $record['parts_replaced'],
                'notes'          => $record['notes'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
