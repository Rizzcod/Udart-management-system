<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SparePartSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            ['part_name' => 'Engine Oil Filter', 'part_number' => 'SP-001', 'quantity' => 25, 'minimum_stock' => 10, 'unit_price' => 15000, 'supplier' => 'AutoParts TZ'],
            ['part_name' => 'Air Filter', 'part_number' => 'SP-002', 'quantity' => 18, 'minimum_stock' => 8, 'unit_price' => 22000, 'supplier' => 'AutoParts TZ'],
            ['part_name' => 'Brake Pads (Front)', 'part_number' => 'SP-003', 'quantity' => 12, 'minimum_stock' => 6, 'unit_price' => 85000, 'supplier' => 'Brake Masters'],
            ['part_name' => 'Brake Pads (Rear)', 'part_number' => 'SP-004', 'quantity' => 10, 'minimum_stock' => 6, 'unit_price' => 78000, 'supplier' => 'Brake Masters'],
            ['part_name' => 'Clutch Plate', 'part_number' => 'SP-005', 'quantity' => 4, 'minimum_stock' => 3, 'unit_price' => 320000, 'supplier' => 'Trans Tech'],
            ['part_name' => 'Timing Belt', 'part_number' => 'SP-006', 'quantity' => 6, 'minimum_stock' => 4, 'unit_price' => 195000, 'supplier' => 'AutoParts TZ'],
            ['part_name' => 'Battery 12V 150Ah', 'part_number' => 'SP-007', 'quantity' => 8, 'minimum_stock' => 4, 'unit_price' => 450000, 'supplier' => 'Power Solutions'],
            ['part_name' => 'Coolant (5L)', 'part_number' => 'SP-008', 'quantity' => 30, 'minimum_stock' => 10, 'unit_price' => 18000, 'supplier' => 'AutoParts TZ'],
            ['part_name' => 'Power Steering Fluid', 'part_number' => 'SP-009', 'quantity' => 15, 'minimum_stock' => 5, 'unit_price' => 12000, 'supplier' => 'AutoParts TZ'],
            ['part_name' => 'Fuel Injector', 'part_number' => 'SP-010', 'quantity' => 2, 'minimum_stock' => 4, 'unit_price' => 580000, 'supplier' => 'Diesel Tech'],
            ['part_name' => 'Alternator', 'part_number' => 'SP-011', 'quantity' => 3, 'minimum_stock' => 2, 'unit_price' => 750000, 'supplier' => 'Electrical Plus'],
            ['part_name' => 'Shock Absorber (Front)', 'part_number' => 'SP-012', 'quantity' => 6, 'minimum_stock' => 4, 'unit_price' => 280000, 'supplier' => 'Suspension TZ'],
        ];

        foreach ($parts as $part) {
            DB::table('spare_parts')->insertOrIgnore(array_merge($part, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
