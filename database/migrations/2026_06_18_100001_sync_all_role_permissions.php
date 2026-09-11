<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private array $all = [
        'view buses', 'create buses', 'edit buses', 'delete buses',
        'view work-orders', 'create work-orders', 'edit work-orders', 'delete work-orders', 'update work-order status',
        'view own-reports',
        'view maintenance-records', 'create maintenance-records', 'edit maintenance-records', 'delete maintenance-records',
        'view preventive-maintenance', 'create preventive-maintenance', 'edit preventive-maintenance', 'delete preventive-maintenance',
        'view spare-parts', 'create spare-parts', 'edit spare-parts', 'delete spare-parts',
        'view bus-assignments', 'manage bus-assignments',
        'view iot', 'view predictions', 'run predictions', 'view failure-patterns',
        'view reports',
        'view announcements', 'create announcements', 'delete announcements',
        'manage users',
        'view users', 'create users', 'edit users', 'delete users', 'manage user-permissions',
    ];

    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->all as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $map = [
            'Admin' => $this->all,

            'Supervisor' => array_values(array_diff($this->all, [
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
            // On a fresh install roles don't exist yet (RoleSeeder creates them),
            // so look up without throwing — findByName() throws RoleDoesNotExist.
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->syncPermissions($perms);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Permissions are shared data — not safe to drop on rollback
    }
};
