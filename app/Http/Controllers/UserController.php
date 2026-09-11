<?php

namespace App\Http\Controllers;

use App\Mail\UserOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($q2) => $q2->where('name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('employee_id', 'like', "%$s%")
                    ->orWhere('phone', 'like', "%$s%"))
            )
            ->when($request->role, fn($q, $r) => $q->role($r))
            ->when($request->status, function ($q, $status) {
                match ($status) {
                    'active'  => $q->whereNotNull('email_verified_at'),
                    'pending' => $q->whereNull('email_verified_at')->whereNotNull('otp')->where('otp_expires_at', '>', now()),
                    'expired' => $q->whereNull('email_verified_at')->where(fn($q2) => $q2->whereNull('otp')->orWhere('otp_expires_at', '<=', now())),
                    default   => null,
                };
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();
        $total = User::count();

        return view('users.index', compact('users', 'roles', 'total'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8|confirmed',
            'role'        => 'required|exists:roles,name',
            'phone'       => 'nullable|string|max:30',
            'employee_id' => 'nullable|string|max:50',
            'department'  => 'nullable|string|max:100',
            'position'    => 'nullable|string|max:100',
        ]);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'phone'           => $data['phone'] ?? null,
            'employee_id'     => $data['employee_id'] ?? null,
            'department'      => $data['department'] ?? null,
            'position'        => $data['position'] ?? null,
            'otp'             => Hash::make($otp),
            'otp_expires_at'  => now()->addHours(24),
            'email_verified_at' => null,
        ]);

        $user->assignRole($data['role']);

        try {
            Mail::to($user->email)->send(new UserOtpMail($user, $otp));
            return redirect()->route('users.index')
                ->with('success', "User {$user->name} created. A 6-digit OTP has been sent to {$user->email} for email verification.");
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('warning', "User {$user->name} created but the OTP email could not be sent. Please check your mail configuration and use Resend OTP.");
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'role'        => 'required|exists:roles,name',
            'password'    => 'nullable|min:8|confirmed',
            'phone'       => 'nullable|string|max:30',
            'employee_id' => 'nullable|string|max:50',
            'department'  => 'nullable|string|max:100',
            'position'    => 'nullable|string|max:100',
        ]);

        $user->update([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'department'  => $data['department'] ?? null,
            'position'    => $data['position'] ?? null,
            'password'    => $data['password'] ? Hash::make($data['password']) : $user->password,
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', "User {$user->name} updated successfully.");
    }

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

    public function permissions(User $user)
    {
        $roles       = Role::orderBy('name')->get();
        $groups      = $this->permissionGroups();
        $rolePerms   = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        $directPerms = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('users.permissions', compact('user', 'roles', 'groups', 'rolePerms', 'directPerms'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'role'        => 'required|exists:roles,name',
            'permissions' => 'nullable|array',
        ]);

        // Only keep permission names that are actually defined in the known groups
        $validNames  = collect($this->permissionGroups())->flatten()->all();
        $toSync      = array_intersect($request->input('permissions', []), $validNames);

        // Change role first (changes inherited permissions)
        $user->syncRoles([$request->role]);

        // Sync direct (extra) permissions on top of the new role
        $user->syncPermissions($toSync);

        // Force-flush Spatie's permission cache so changes are visible immediately
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('users.permissions', $user)
            ->with('success', "Role and permissions updated for {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "User {$name} has been deleted.");
    }

}
