<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    private array $permissions = [
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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Permissions are shared data — not safe to delete on rollback
    }
};
