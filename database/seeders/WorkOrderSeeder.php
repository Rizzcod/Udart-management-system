<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $supervisor  = User::where('email', 'supervisor@udart.co.tz')->first();
        $supervisor2 = User::where('email', 'supervisor2@udart.co.tz')->first();
        $tech1       = User::where('email', 'tech@udart.co.tz')->first();
        $tech2       = User::where('email', 'tech2@udart.co.tz')->first();
        $tech3       = User::where('email', 'tech3@udart.co.tz')->first();
        $driver1     = User::where('email', 'driver@udart.co.tz')->first();
        $driver2     = User::where('email', 'driver2@udart.co.tz')->first();
        $driver3     = User::where('email', 'driver3@udart.co.tz')->first();
        $driver4     = User::where('email', 'driver4@udart.co.tz')->first();
        $driver5     = User::where('email', 'driver5@udart.co.tz')->first();

        if (!$supervisor || !$tech1) return;

        $sup2  = $supervisor2 ?? $supervisor;
        $t2    = $tech2 ?? $tech1;
        $t3    = $tech3 ?? $tech1;
        $d3    = $driver3 ?? $driver1;
        $d4    = $driver4 ?? $driver1;
        $d5    = $driver5 ?? $driver1;

        $buses = Bus::pluck('id', 'registration_number');

        $orders = [
            [
                'bus' => 'UDART-004', 'reported_by' => $d4->id, 'assigned_to' => $tech1->id,
                'title' => 'Engine Overheating', 'location' => 'Morogoro Road, Dar es Salaam',
                'description' => 'Bus engine temperature gauge in red zone. Driver reported steam coming from bonnet. Bus pulled off road immediately.',
                'priority' => 'critical', 'status' => 'in_progress',
                'start_date' => '2026-06-10', 'completion_date' => null,
                'notes' => 'Coolant leak suspected. Radiator hose check in progress.',
            ],
            [
                'bus' => 'UDART-007', 'reported_by' => $supervisor->id, 'assigned_to' => $t2->id,
                'title' => 'Brake System Abnormality', 'location' => 'Workshop Bay 2',
                'description' => 'Routine inspection revealed uneven brake pad wear on front axle. Left pad worn to metal, right pad still at 40%.',
                'priority' => 'high', 'status' => 'assigned',
                'start_date' => '2026-06-13', 'completion_date' => null,
                'notes' => 'Parts on order. Expected delivery 2026-06-15.',
            ],
            [
                'bus' => 'UDART-001', 'reported_by' => $supervisor->id, 'assigned_to' => $tech1->id,
                'title' => 'Routine Oil Change & Service', 'location' => 'Workshop Bay 1',
                'description' => 'Scheduled 5,000 km service. Oil change, filter replacement, fluid top-ups and visual inspection.',
                'priority' => 'low', 'status' => 'completed',
                'start_date' => '2026-05-10', 'completion_date' => '2026-05-10',
                'notes' => 'All fluids topped up. Next service due at 90,000 km.',
            ],
            [
                'bus' => 'UDART-002', 'reported_by' => $driver2->id, 'assigned_to' => $t2->id,
                'title' => 'Air Conditioning Not Working', 'location' => 'Terminal 3, Ubungo',
                'description' => 'AC blowing warm air. Driver and passengers complained of excessive heat. Unit was not cooling at all.',
                'priority' => 'medium', 'status' => 'completed',
                'start_date' => '2026-05-20', 'completion_date' => '2026-05-21',
                'notes' => 'Low refrigerant. System recharged, no leaks found.',
            ],
            [
                'bus' => 'UDART-003', 'reported_by' => $d3->id, 'assigned_to' => $t3->id,
                'title' => 'Tire Puncture Repair', 'location' => 'Tegeta Stage, Dar es Salaam',
                'description' => 'Rear right tire went flat mid-route. Spare tyre mounted by driver. Damaged tire brought in for repair.',
                'priority' => 'low', 'status' => 'completed',
                'start_date' => '2026-06-02', 'completion_date' => '2026-06-02',
                'notes' => 'Nail found in tread. Tire patched and reinstalled.',
            ],
            [
                'bus' => 'UDART-005', 'reported_by' => $d5->id, 'assigned_to' => null,
                'title' => 'Check Engine Light On', 'location' => 'Kariakoo Bus Stop',
                'description' => 'Check engine warning light illuminated on dashboard. No noticeable performance issues but driver reported the vehicle feels sluggish at higher RPM.',
                'priority' => 'medium', 'status' => 'pending',
                'start_date' => null, 'completion_date' => null,
                'notes' => null,
            ],
            [
                'bus' => 'UDART-006', 'reported_by' => $driver1->id, 'assigned_to' => null,
                'title' => 'Front Door Mechanism Faulty', 'location' => 'Mwenge Terminal',
                'description' => 'Front passenger door not closing fully. Mechanism appears jammed. Passengers have been using rear door only.',
                'priority' => 'low', 'status' => 'pending',
                'start_date' => null, 'completion_date' => null,
                'notes' => null,
            ],
            [
                'bus' => 'UDART-008', 'reported_by' => $sup2->id, 'assigned_to' => $t2->id,
                'title' => 'Transmission Slipping Under Load', 'location' => 'Workshop Bay 3',
                'description' => 'Gear slipping noticed at 3rd to 4th shift under heavy load. RPM spikes without corresponding speed increase. Transmission fluid low and dark.',
                'priority' => 'high', 'status' => 'in_progress',
                'start_date' => '2026-06-12', 'completion_date' => null,
                'notes' => 'Gearbox opened. Internal clutch pack worn. Awaiting replacement parts.',
            ],
            [
                'bus' => 'UDART-009', 'reported_by' => $driver1->id, 'assigned_to' => $tech1->id,
                'title' => 'Battery Dead - Bus Will Not Start', 'location' => 'Depot, Mbezi',
                'description' => 'Bus could not start in the morning. Jump-start attempted and failed. Battery voltage reading at 8.2V, severely discharged.',
                'priority' => 'medium', 'status' => 'completed',
                'start_date' => '2026-06-03', 'completion_date' => '2026-06-03',
                'notes' => 'Battery replaced. Alternator output tested and confirmed normal at 14.2V.',
            ],
            [
                'bus' => 'UDART-010', 'reported_by' => $d5->id, 'assigned_to' => $tech1->id,
                'title' => 'Fuel Leak Detected Under Engine', 'location' => 'Kimara Stage',
                'description' => 'Fuel smell reported in cabin. Puddle of fuel found under engine bay. Bus taken out of service immediately for safety.',
                'priority' => 'critical', 'status' => 'assigned',
                'start_date' => '2026-06-14', 'completion_date' => null,
                'notes' => 'Fuel line cracked near injector rail. Parts being sourced.',
            ],
            [
                'bus' => 'UDART-001', 'reported_by' => $driver1->id, 'assigned_to' => null,
                'title' => 'Windshield Crack - Passenger Safety', 'location' => 'Posta Bus Stop',
                'description' => 'Crack on windshield starting from lower corner spreading horizontally. Crack appeared after driving on rough section of road.',
                'priority' => 'low', 'status' => 'pending',
                'start_date' => null, 'completion_date' => null,
                'notes' => 'Driver noted crack is on passenger side, not obscuring driver vision.',
            ],
            [
                'bus' => 'UDART-002', 'reported_by' => $supervisor->id, 'assigned_to' => $t3->id,
                'title' => 'Alternator Failure', 'location' => 'Workshop Bay 1',
                'description' => 'Battery warning light came on during operation. Alternator output measured at 9.8V, below the required 13.5-14.5V range.',
                'priority' => 'high', 'status' => 'completed',
                'start_date' => '2026-05-14', 'completion_date' => '2026-05-15',
                'notes' => 'Alternator replaced. New unit tested at 14.3V output.',
            ],
            [
                'bus' => 'UDART-003', 'reported_by' => $d3->id, 'assigned_to' => $t2->id,
                'title' => 'Power Steering Loss', 'location' => 'Workshop Bay 2',
                'description' => 'Steering became very heavy and unresponsive. Power steering fluid reservoir found empty. Possible pump failure or line leak.',
                'priority' => 'high', 'status' => 'in_progress',
                'start_date' => '2026-06-11', 'completion_date' => null,
                'notes' => 'Pump shaft seal failed. Power Steering Fluid topped up temporarily. Seal replacement in progress.',
            ],
            [
                'bus' => 'UDART-004', 'reported_by' => $d4->id, 'assigned_to' => $tech1->id,
                'title' => 'Clutch Pedal Soft - Worn Clutch', 'location' => 'Workshop Bay 1',
                'description' => 'Clutch pedal going to the floor with no resistance. Bus hard to engage gears. Clutch plate worn and slipping.',
                'priority' => 'medium', 'status' => 'completed',
                'start_date' => '2026-03-08', 'completion_date' => '2026-03-11',
                'notes' => 'Full clutch kit replaced. Flywheel surface inspected and found acceptable.',
            ],
            [
                'bus' => 'UDART-007', 'reported_by' => $driver2->id, 'assigned_to' => null,
                'title' => 'Excessive Exhaust Smoke (Black)', 'location' => 'Tabata Stage',
                'description' => 'Black smoke from exhaust on acceleration. Passengers and bystanders complained. Could indicate over-fuelling or blocked air filter.',
                'priority' => 'medium', 'status' => 'pending',
                'start_date' => null, 'completion_date' => null,
                'notes' => null,
            ],
        ];

        foreach ($orders as $order) {
            $busId = $buses[$order['bus']] ?? null;
            if (!$busId) continue;

            DB::table('work_orders')->insertOrIgnore([
                'bus_id'          => $busId,
                'reported_by'     => $order['reported_by'],
                'assigned_to'     => $order['assigned_to'],
                'title'           => $order['title'],
                'location'        => $order['location'],
                'description'     => $order['description'],
                'priority'        => $order['priority'],
                'status'          => $order['status'],
                'start_date'      => SeedDate::shift($order['start_date']),
                'completion_date' => SeedDate::shift($order['completion_date']),
                'notes'           => $order['notes'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
