<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $buses = [
            ['registration_number' => 'UDART-001', 'model' => 'Yutong ZK6120HGS', 'manufacturer' => 'Yutong', 'year' => 2019, 'status' => 'active', 'mileage' => 85000],
            ['registration_number' => 'UDART-002', 'model' => 'Yutong ZK6120HGS', 'manufacturer' => 'Yutong', 'year' => 2019, 'status' => 'active', 'mileage' => 92000],
            ['registration_number' => 'UDART-003', 'model' => 'Higer KLQ6125', 'manufacturer' => 'Higer', 'year' => 2020, 'status' => 'active', 'mileage' => 67000],
            ['registration_number' => 'UDART-004', 'model' => 'Higer KLQ6125', 'manufacturer' => 'Higer', 'year' => 2020, 'status' => 'under_repair', 'mileage' => 71000],
            ['registration_number' => 'UDART-005', 'model' => 'King Long XMQ6127', 'manufacturer' => 'King Long', 'year' => 2021, 'status' => 'active', 'mileage' => 45000],
            ['registration_number' => 'UDART-006', 'model' => 'King Long XMQ6127', 'manufacturer' => 'King Long', 'year' => 2021, 'status' => 'active', 'mileage' => 48000],
            ['registration_number' => 'UDART-007', 'model' => 'Yutong ZK6120HGS', 'manufacturer' => 'Yutong', 'year' => 2018, 'status' => 'active', 'mileage' => 115000],
            ['registration_number' => 'UDART-008', 'model' => 'Higer KLQ6125', 'manufacturer' => 'Higer', 'year' => 2022, 'status' => 'active', 'mileage' => 28000],
            ['registration_number' => 'UDART-009', 'model' => 'Yutong ZK6120HGS', 'manufacturer' => 'Yutong', 'year' => 2022, 'status' => 'inactive', 'mileage' => 31000],
            ['registration_number' => 'UDART-010', 'model' => 'King Long XMQ6127', 'manufacturer' => 'King Long', 'year' => 2023, 'status' => 'active', 'mileage' => 12000],
        ];

        foreach ($buses as $bus) {
            DB::table('buses')->insertOrIgnore(array_merge($bus, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $driverAssignments = [
            'UDART-001' => 'driver@udart.co.tz',
            'UDART-002' => 'driver2@udart.co.tz',
            'UDART-003' => 'driver3@udart.co.tz',
            'UDART-004' => 'driver4@udart.co.tz',
            'UDART-005' => 'driver5@udart.co.tz',
            'UDART-006' => 'driver6@udart.co.tz',
            'UDART-007' => 'driver7@udart.co.tz',
            'UDART-008' => 'driver8@udart.co.tz',
            'UDART-009' => 'driver9@udart.co.tz',
            'UDART-010' => 'driver10@udart.co.tz',
        ];

        foreach ($driverAssignments as $regNum => $email) {
            $driver = User::where('email', $email)->first();
            if ($driver) {
                Bus::where('registration_number', $regNum)->update(['driver_id' => $driver->id]);
            }
        }
    }
}
