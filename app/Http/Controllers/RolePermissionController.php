<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    private function permissionGroups(): array
    {
        return [
            'Buses' => [
                'view buses', 'create buses', 'edit buses', 'delete buses',
            ],
            'Work Orders' => [
                'view work-orders', 'create work-orders', 'edit work-orders',
                'delete work-orders', 'update work-order status',
            ],
            'Maintenance Records' => [
                'view maintenance-records', 'create maintenance-records',
                'edit maintenance-records', 'delete maintenance-records',
            ],
            'Preventive Maintenance' => [
                'view preventive-maintenance', 'create preventive-maintenance',
                'edit preventive-maintenance', 'delete preventive-maintenance',
            ],
            'Spare Parts' => [
                'view spare-parts', 'create spare-parts', 'edit spare-parts', 'delete spare-parts',
            ],
            'Intelligence' => [
                'view iot', 'view predictions', 'run predictions', 'view failure-patterns',
            ],
            'Reports' => [
                'view reports',
            ],
            'User Management' => [
                'view users', 'create users', 'edit users', 'delete users', 'manage user-permissions',
            ],
            'Administration' => [
                'manage users',
            ],
        ];
    }

    public function index()
    {
        $roles           = Role::withCount(['permissions', 'users'])->orderBy('name')->get();
        $totalPermissions = Permission::count();
        $usersWithRoles  = User::whereHas('roles')->count();
        $avgPerms        = $roles->count() ? round($roles->avg('permissions_count'), 1) : 0;
        $groups          = $this->permissionGroups();

        return view('roles-permissions.index', compact('roles', 'totalPermissions', 'usersWithRoles', 'avgPerms', 'groups'));
    }

    public function show(Role $role)
    {
        $groups   = $this->permissionGroups();
        $rolePerms = $role->permissions->pluck('name')->toArray();

        return view('roles-permissions.show', compact('role', 'groups', 'rolePerms'));
    }

    public function update(Request $request, Role $role)
    {
        // Collect all valid permission names from the defined groups (no DB round-trip needed)
        $validNames = collect($this->permissionGroups())->flatten()->all();

        // Only keep submitted values that are actually in our defined groups
        $submitted = array_intersect($request->input('permissions', []), $validNames);

        $role->syncPermissions($submitted);

        // Flush Spatie's permission cache so all users with this role see updated permissions immediately
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.show', $role)
            ->with('success', "Permissions for the \"{$role->name}\" role have been updated successfully.");
    }
}
