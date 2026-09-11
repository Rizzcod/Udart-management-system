<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,                  // roles + all users
            PermissionSeeder::class,            // granular permissions per role
            BusSeeder::class,                   // buses + driver assignments
            SparePartSeeder::class,             // inventory
            MaintenanceRecordSeeder::class,     // 21 historical maintenance records
            WorkOrderSeeder::class,             // 15 work orders (various states)
            SparePartUsageSeeder::class,        // parts consumed in work orders
            PreventiveMaintenanceSeeder::class, // PM schedules (upcoming/overdue/completed)
            SensorReadingSeeder::class,         // IoT sensor readings (last 3 days)
            FailurePredictionSeeder::class,     // AI failure predictions
        ]);
    }
}
