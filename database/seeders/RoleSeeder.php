<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'Supervisor', 'Technician', 'Storekeeper', 'Driver'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── Permissions ─────────────────────────────────────────────────────
        // Note: PermissionSeeder (runs after this) is the canonical source for role→permission mapping.
        // This list only ensures Permission records exist if this seeder runs in isolation.
        $allPermissions = [
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
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $permMap = [
            'Admin' => $allPermissions,
            'Supervisor' => array_values(array_diff($allPermissions, [
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

        foreach ($permMap as $roleName => $perms) {
            $role = Role::findByName($roleName, 'web');
            $role->syncPermissions($perms);
        }

        $verified = now();

        $admin = User::firstOrCreate(
            ['email' => 'admin@udart.co.tz'],
            ['name' => 'Admin User', 'password' => Hash::make('password'), 'email_verified_at' => $verified]
        );
        $admin->update(['email_verified_at' => $verified]);
        $admin->assignRole('Admin');

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@udart.co.tz'],
            ['name' => 'John Supervisor', 'password' => Hash::make('password'), 'email_verified_at' => $verified]
        );
        $supervisor->update(['email_verified_at' => $verified]);
        $supervisor->assignRole('Supervisor');

        $technician = User::firstOrCreate(
            ['email' => 'tech@udart.co.tz'],
            ['name' => 'James Technician', 'password' => Hash::make('password'), 'email_verified_at' => $verified]
        );
        $technician->update(['email_verified_at' => $verified]);
        $technician->assignRole('Technician');

        $storekeeper = User::firstOrCreate(
            ['email' => 'store@udart.co.tz'],
            ['name' => 'Mary Storekeeper', 'password' => Hash::make('password'), 'email_verified_at' => $verified]
        );
        $storekeeper->update(['email_verified_at' => $verified]);
        $storekeeper->assignRole('Storekeeper');

        $driver = User::firstOrCreate(
            ['email' => 'driver@udart.co.tz'],
            ['name' => 'Ali Driver', 'password' => Hash::make('password'), 'email_verified_at' => $verified]
        );
        $driver->update(['email_verified_at' => $verified]);
        $driver->assignRole('Driver');

        $driver2 = User::firstOrCreate(
            ['email' => 'driver2@udart.co.tz'],
            ['name' => 'Grace Driver', 'password' => Hash::make('password'), 'email_verified_at' => $verified]
        );
        $driver2->update(['email_verified_at' => $verified]);
        $driver2->assignRole('Driver');

        // Additional users with full profile fields
        $users = [
            [
                'email' => 'supervisor2@udart.co.tz',
                'name' => 'Jane Mwangi',
                'role' => 'Supervisor',
                'phone' => '+255712345678',
                'employee_id' => 'EMP-S002',
                'department' => 'Operations',
                'position' => 'Fleet Supervisor',
            ],
            [
                'email' => 'tech2@udart.co.tz',
                'name' => 'Sarah Kimani',
                'role' => 'Technician',
                'phone' => '+255722345679',
                'employee_id' => 'EMP-T002',
                'department' => 'Maintenance',
                'position' => 'Senior Technician',
            ],
            [
                'email' => 'tech3@udart.co.tz',
                'name' => 'Moses Ouma',
                'role' => 'Technician',
                'phone' => '+255732345680',
                'employee_id' => 'EMP-T003',
                'department' => 'Maintenance',
                'position' => 'Junior Technician',
            ],
            [
                'email' => 'driver3@udart.co.tz',
                'name' => 'Hassan Juma',
                'role' => 'Driver',
                'phone' => '+255742345681',
                'employee_id' => 'EMP-D003',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
            [
                'email' => 'driver4@udart.co.tz',
                'name' => 'Fatuma Said',
                'role' => 'Driver',
                'phone' => '+255752345682',
                'employee_id' => 'EMP-D004',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
            [
                'email' => 'driver5@udart.co.tz',
                'name' => 'Peter Njoroge',
                'role' => 'Driver',
                'phone' => '+255762345683',
                'employee_id' => 'EMP-D005',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
            [
                'email' => 'driver6@udart.co.tz',
                'name' => 'Amina Rashid',
                'role' => 'Driver',
                'phone' => '+255772345684',
                'employee_id' => 'EMP-D006',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
            [
                'email' => 'driver7@udart.co.tz',
                'name' => 'Joseph Msangi',
                'role' => 'Driver',
                'phone' => '+255782345685',
                'employee_id' => 'EMP-D007',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
            [
                'email' => 'driver8@udart.co.tz',
                'name' => 'Rehema Mkwawa',
                'role' => 'Driver',
                'phone' => '+255792345686',
                'employee_id' => 'EMP-D008',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
            [
                'email' => 'driver9@udart.co.tz',
                'name' => 'Salim Baraka',
                'role' => 'Driver',
                'phone' => '+255712345687',
                'employee_id' => 'EMP-D009',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
            [
                'email' => 'driver10@udart.co.tz',
                'name' => 'Zainab Hamisi',
                'role' => 'Driver',
                'phone' => '+255722345688',
                'employee_id' => 'EMP-D010',
                'department' => 'Transport',
                'position' => 'Bus Driver',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password'), 'email_verified_at' => $verified])
            );
            $user->update(array_merge($data, ['email_verified_at' => $verified]));
            $user->assignRole($role);
        }

        // Update existing users with profile fields
        $profileUpdates = [
            'admin@udart.co.tz'      => ['phone' => '+255700000001', 'employee_id' => 'EMP-A001', 'department' => 'Administration', 'position' => 'System Administrator'],
            'supervisor@udart.co.tz' => ['phone' => '+255700000002', 'employee_id' => 'EMP-S001', 'department' => 'Operations', 'position' => 'Operations Manager'],
            'tech@udart.co.tz'       => ['phone' => '+255700000003', 'employee_id' => 'EMP-T001', 'department' => 'Maintenance', 'position' => 'Lead Technician'],
            'store@udart.co.tz'      => ['phone' => '+255700000004', 'employee_id' => 'EMP-K001', 'department' => 'Stores', 'position' => 'Storekeeper'],
            'driver@udart.co.tz'     => ['phone' => '+255700000005', 'employee_id' => 'EMP-D001', 'department' => 'Transport', 'position' => 'Bus Driver'],
            'driver2@udart.co.tz'    => ['phone' => '+255700000006', 'employee_id' => 'EMP-D002', 'department' => 'Transport', 'position' => 'Bus Driver'],
        ];

        foreach ($profileUpdates as $email => $fields) {
            User::where('email', $email)->update($fields);
        }
    }
}
