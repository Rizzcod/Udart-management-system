<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $all = [
            // Buses
            'view buses', 'create buses', 'edit buses', 'delete buses',
            // Work Orders
            'view work-orders', 'create work-orders', 'edit work-orders',
            'delete work-orders', 'update work-order status',
            // Own Reports (driver / technician personal history)
            'view own-reports',
            // Maintenance Records
            'view maintenance-records', 'create maintenance-records',
            'edit maintenance-records', 'delete maintenance-records',
            // Preventive Maintenance
            'view preventive-maintenance', 'create preventive-maintenance',
            'edit preventive-maintenance', 'delete preventive-maintenance',
            // Spare Parts
            'view spare-parts', 'create spare-parts', 'edit spare-parts', 'delete spare-parts',
            // Bus Assignments & Fleet Board
            'view bus-assignments', 'manage bus-assignments',
            // Intelligence
            'view iot', 'view predictions', 'run predictions', 'view failure-patterns',
            // Reports
            'view reports',
            // Announcements
            'view announcements', 'create announcements', 'delete announcements',
            // Admin
            'manage users',
            'view users', 'create users', 'edit users', 'delete users', 'manage user-permissions',
        ];

        foreach ($all as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $map = [
            'Admin' => $all,

            'Supervisor' => array_values(array_diff($all, [
                'manage users', 'create users', 'edit users', 'delete users', 'manage user-permissions',
            ])),

            'Technician' => [
                'view buses',
                'view work-orders', 'create work-orders', 'update work-order status',
                'view own-reports',
                'view maintenance-records', 'create maintenance-records', 'edit maintenance-records',
                'view preventive-maintenance',
                'view spare-parts',
                'view bus-assignments',
                'view iot', 'view predictions', 'view failure-patterns',
                'view announcements',
            ],

            'Storekeeper' => [
                'view buses',
                'view work-orders',
                'view maintenance-records',
                'view preventive-maintenance',
                'view spare-parts', 'create spare-parts', 'edit spare-parts', 'delete spare-parts',
                'view reports',
                'view announcements',
            ],

            'Driver' => [
                'view own-reports',
                'view announcements',
            ],
        ];

        foreach ($map as $roleName => $perms) {
            $role = Role::findByName($roleName, 'web');
            $role->syncPermissions($perms);
        }
    }
}
