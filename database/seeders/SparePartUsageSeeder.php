<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SparePartUsageSeeder extends Seeder
{
    public function run(): void
    {
        // Map work order titles to the spare parts used in them
        $usages = [
            'Routine Oil Change & Service'      => [['part_number' => 'SP-001', 'qty' => 1]],
            'Air Conditioning Not Working'       => [['part_number' => 'SP-008', 'qty' => 1]],
            'Battery Dead - Bus Will Not Start'  => [['part_number' => 'SP-007', 'qty' => 1]],
            'Alternator Failure'                 => [['part_number' => 'SP-011', 'qty' => 1]],
            'Clutch Pedal Soft - Worn Clutch'   => [['part_number' => 'SP-005', 'qty' => 1]],
            'Brake System Abnormality'           => [
                ['part_number' => 'SP-003', 'qty' => 1],
                ['part_number' => 'SP-009', 'qty' => 1],
            ],
        ];

        $partIds = DB::table('spare_parts')->pluck('id', 'part_number');

        foreach ($usages as $workOrderTitle => $parts) {
            $workOrder = DB::table('work_orders')->where('title', $workOrderTitle)->first();
            if (!$workOrder) continue;

            foreach ($parts as $part) {
                $partId = $partIds[$part['part_number']] ?? null;
                if (!$partId) continue;

                $exists = DB::table('spare_part_usages')
                    ->where('work_order_id', $workOrder->id)
                    ->where('spare_part_id', $partId)
                    ->exists();

                if (!$exists) {
                    DB::table('spare_part_usages')->insert([
                        'work_order_id' => $workOrder->id,
                        'spare_part_id' => $partId,
                        'quantity_used'  => $part['qty'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    // Deduct stock
                    DB::table('spare_parts')
                        ->where('id', $partId)
                        ->decrement('quantity', $part['qty']);
                }
            }
        }
    }
}
