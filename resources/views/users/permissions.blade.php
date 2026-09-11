<x-app-layout title="Roles & Permissions — {{ $user->name }}">

    @php
        $currentRole  = $user->getRoleNames()->first() ?? 'No Role';
        $roleColor = match($currentRole) {
            'Admin'       => ['bg'=>'#fee2e2','text'=>'#991b1b','av'=>'#dc2626'],
            'Supervisor'  => ['bg'=>'#dbeafe','text'=>'#1e40af','av'=>'#1c3faa'],
            'Technician'  => ['bg'=>'#dcfce7','text'=>'#166634','av'=>'#16a34a'],
            'Storekeeper' => ['bg'=>'#ffedd5','text'=>'#9a3412','av'=>'#ea580c'],
            'Driver'      => ['bg'=>'#f3e8ff','text'=>'#6b21a8','av'=>'#7c3aed'],
            default       => ['bg'=>'#f3f4f6','text'=>'#374151','av'=>'#6b7280'],
        };

        $groupIcons = [
            'Buses'                  => 'bus-front',
            'Work Orders'            => 'wrench-adjustable',
            'Maintenance Records'    => 'clipboard2-check',
            'Preventive Maintenance' => 'calendar2-check',
            'Spare Parts'            => 'box-seam',
            'Intelligence'           => 'broadcast',
            'Reports'                => 'file-earmark-bar-graph',
            'User Management'        => 'people',
            'Administration'         => 'shield-lock',
        ];

        $actionLabels = [
            'view'   => ['bg'=>'#dbeafe','text'=>'#1e40af','icon'=>'eye'],
            'create' => ['bg'=>'#dcfce7','text'=>'#15803d','icon'=>'plus-circle'],
            'edit'   => ['bg'=>'#ffedd5','text'=>'#c2410c','icon'=>'pencil'],
            'delete' => ['bg'=>'#fee2e2','text'=>'#991b1b','icon'=>'trash3'],
            'update' => ['bg'=>'#f3e8ff','text'=>'#7c3aed','icon'=>'arrow-repeat'],
            'run'    => ['bg'=>'#cffafe','text'=>'#0e7490','icon'=>'play-fill'],
            'manage' => ['bg'=>'#fee2e2','text'=>'#991b1b','icon'=>'person-gear'],
        ];
    @endphp

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Roles &amp; Permissions</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" style="color:#1c3faa;">Users</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.edit', $user) }}" style="color:#1c3faa;">{{ $user->name }}</a></li>
                    <li class="breadcrumb-item active text-muted">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-pencil"></i> Edit Profile
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert d-flex align-items-start gap-2 alert-dismissible fade show mb-4"
         style="background:#fee2e2;border:none;border-radius:10px;color:#991b1b;">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
        <div>
            <strong>Could not save permissions:</strong>
            <ul class="mb-0 mt-1 ps-3" style="font-size:.875rem;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('users.permissions.update', $user) }}">
        @csrf

    <div class="row g-4">

        {{-- ── Left: User card + Role selector ── --}}
        <div class="col-lg-3">

            {{-- User identity --}}
            <div class="card mb-3" style="border-radius:14px;overflow:hidden;">
                <div style="height:5px;background:{{ $roleColor['av'] }};"></div>
                <div class="card-body text-center py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle fw-bold text-white"
                         style="width:64px;height:64px;background:{{ $roleColor['av'] }};font-size:1.3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="fw-bold mb-1" style="font-size:.95rem;color:#111827;">{{ $user->name }}</div>
                    <span class="badge mb-2" style="background:{{ $roleColor['bg'] }};color:{{ $roleColor['text'] }};font-size:.75rem;padding:.35em .8em;border-radius:6px;">
                        {{ $currentRole }}
                    </span>
                    <div class="text-muted" style="font-size:.75rem;">{{ $user->email }}</div>
                    @if($user->department)
                    <div class="text-muted mt-1" style="font-size:.73rem;">{{ $user->department }}</div>
                    @endif
                </div>
            </div>

            {{-- Role assignment --}}
            <div class="card mb-3" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-person-badge" style="color:#1c3faa;"></i>
                    <span class="fw-bold" style="font-size:.88rem;color:#111827;">Assign Role</span>
                </div>
                <div class="card-body px-4 py-3">
                    <p class="text-muted mb-3" style="font-size:.78rem;line-height:1.5;">
                        Changing the role updates the user's base permissions. Extra grants below are preserved.
                    </p>
                    @foreach($roles as $role)
                    @php
                        $rc = match($role->name) {
                            'Admin'       => ['bg'=>'#fee2e2','text'=>'#991b1b','av'=>'#dc2626'],
                            'Supervisor'  => ['bg'=>'#dbeafe','text'=>'#1e40af','av'=>'#1c3faa'],
                            'Technician'  => ['bg'=>'#dcfce7','text'=>'#166634','av'=>'#16a34a'],
                            'Storekeeper' => ['bg'=>'#ffedd5','text'=>'#9a3412','av'=>'#ea580c'],
                            'Driver'      => ['bg'=>'#f3e8ff','text'=>'#6b21a8','av'=>'#7c3aed'],
                            default       => ['bg'=>'#f3f4f6','text'=>'#374151','av'=>'#6b7280'],
                        };
                        $isSelected = $user->hasRole($role->name);
                    @endphp
                    <label class="d-flex align-items-center gap-3 p-2 rounded-3 mb-1 role-option {{ $isSelected ? 'role-selected' : '' }}"
                           style="cursor:pointer;border:2px solid {{ $isSelected ? $rc['av'] : '#f0f3fb' }};background:{{ $isSelected ? $rc['bg'] : '#fafafa' }};transition:all .15s;">
                        <input type="radio" name="role" value="{{ $role->name }}"
                               {{ $isSelected ? 'checked' : '' }}
                               class="role-radio" style="display:none;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:30px;height:30px;background:{{ $rc['av'] }};">
                            <i class="bi bi-person-fill" style="color:#fff;font-size:.75rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:.82rem;color:#111827;">{{ $role->name }}</div>
                            <div style="font-size:.7rem;color:#6b7280;">{{ $role->permissions->count() }} base permissions</div>
                        </div>
                        @if($isSelected)
                        <i class="bi bi-check-circle-fill ms-auto" style="color:{{ $rc['av'] }};"></i>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Legend --}}
            <div class="card" style="border-radius:14px;">
                <div class="card-body px-4 py-3">
                    <div class="fw-semibold mb-2" style="font-size:.8rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Legend</div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:#dcfce7;color:#15803d;font-size:.72rem;">Via Role</span>
                        <span style="font-size:.78rem;color:#6b7280;">Inherited from role</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:.72rem;">Extra</span>
                        <span style="font-size:.78rem;color:#6b7280;">Directly granted</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" style="background:#f3f4f6;color:#6b7280;font-size:.72rem;">—</span>
                        <span style="font-size:.78rem;color:#6b7280;">Not granted</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right: Permission matrix ── --}}
        <div class="col-lg-9">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-0" style="color:#111827;">Permission Matrix</h6>
                    <div class="text-muted" style="font-size:.78rem;">
                        Check boxes below grant <strong>extra permissions</strong> on top of the assigned role.
                        Role-inherited permissions are shown but cannot be revoked here — change the role instead.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 flex-shrink-0"
                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;">
                    <i class="bi bi-shield-check"></i> Save Changes
                </button>
            </div>

            @foreach($groups as $groupName => $perms)
            @php $icon = $groupIcons[$groupName] ?? 'key'; @endphp
            <div class="card mb-3" style="border-radius:14px;overflow:hidden;">
                <div class="px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;background:#fafbff;">
                    <i class="bi bi-{{ $icon }}" style="color:#1c3faa;font-size:1rem;"></i>
                    <span class="fw-bold" style="font-size:.9rem;color:#111827;">{{ $groupName }}</span>
                    @php
                        $grantedViaRole = collect($perms)->filter(fn($p) => in_array($p, $rolePerms))->count();
                        $grantedDirect  = collect($perms)->filter(fn($p) => in_array($p, $directPerms) && !in_array($p, $rolePerms))->count();
                    @endphp
                    @if($grantedViaRole > 0)
                    <span class="badge ms-1" style="background:#dcfce7;color:#15803d;font-size:.7rem;">{{ $grantedViaRole }} via role</span>
                    @endif
                    @if($grantedDirect > 0)
                    <span class="badge ms-1" style="background:#dbeafe;color:#1e40af;font-size:.7rem;">{{ $grantedDirect }} extra</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0" style="font-size:.875rem;">
                        <thead>
                            <tr style="border-bottom:1px solid #f0f3fb;">
                                <th class="ps-4 py-2" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;width:45%;">Permission</th>
                                <th class="py-2 text-center" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;width:20%;">Via Role</th>
                                <th class="py-2 text-center" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;width:20%;">Extra Grant</th>
                                <th class="py-2 pe-4 text-end" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;width:15%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perms as $perm)
                            @php
                                $fromRole   = in_array($perm, $rolePerms);
                                $isDirect   = in_array($perm, $directPerms);
                                $hasAccess  = $fromRole || $isDirect;
                                $verb       = explode(' ', $perm)[0];
                                $al         = $actionLabels[$verb] ?? ['bg'=>'#f3f4f6','text'=>'#374151','icon'=>'key'];
                                // Human-readable label
                                $label = ucwords(str_replace(['-', '_'], ' ', $perm));
                            @endphp
                            <tr style="border-bottom:1px solid #f9fafb;" class="perm-row {{ $hasAccess ? 'perm-granted' : '' }}">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge d-inline-flex align-items-center gap-1"
                                              style="background:{{ $al['bg'] }};color:{{ $al['text'] }};font-size:.7rem;padding:.25em .55em;border-radius:5px;">
                                            <i class="bi bi-{{ $al['icon'] }}"></i> {{ ucfirst($verb) }}
                                        </span>
                                        <span style="color:#374151;">{{ $label }}</span>
                                    </div>
                                </td>

                                {{-- Via Role column --}}
                                <td class="py-3 text-center">
                                    @if($fromRole)
                                    <i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:1.1rem;" title="Granted via role"></i>
                                    @else
                                    <span style="color:#d1d5db;font-size:1rem;">—</span>
                                    @endif
                                </td>

                                {{-- Extra Grant column --}}
                                <td class="py-3 text-center">
                                    @if($fromRole)
                                    {{-- Already granted via role; extra checkbox would be redundant --}}
                                    <div title="Already granted via role — no extra grant needed" style="cursor:not-allowed;opacity:.4;">
                                        <i class="bi bi-dash-circle" style="font-size:1.05rem;color:#9ca3af;"></i>
                                    </div>
                                    @else
                                    <div class="form-check d-flex justify-content-center m-0">
                                        <input class="form-check-input perm-checkbox" type="checkbox"
                                               name="permissions[]" value="{{ $perm }}"
                                               id="perm_{{ Str::slug($perm) }}"
                                               {{ $isDirect ? 'checked' : '' }}
                                               style="width:1.15rem;height:1.15rem;cursor:pointer;border-color:#9ca3af;">
                                    </div>
                                    @endif
                                </td>

                                {{-- Status badge --}}
                                <td class="py-3 pe-4 text-end">
                                    @if($fromRole)
                                    <span class="badge" style="background:#dcfce7;color:#15803d;font-size:.72rem;padding:.3em .65em;border-radius:6px;">Via Role</span>
                                    @elseif($isDirect)
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:.72rem;padding:.3em .65em;border-radius:6px;">Extra</span>
                                    @else
                                    <span style="color:#d1d5db;font-size:.78rem;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-end mt-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                    <i class="bi bi-shield-check"></i> Save Role &amp; Permissions
                </button>
            </div>
        </div>

    </div>
    </form>

@push('styles')
<style>
.perm-row { transition: background .1s; }
.perm-row:hover { background: #f8faff !important; }
.perm-row:last-child { border-bottom: none !important; }
.perm-granted { background: #fafffe !important; }
.perm-granted:hover { background: #f0fffe !important; }

.role-option:hover { border-color: #9ca3af !important; background: #f9fafb !important; }

.form-check-input:checked { background-color: #1c3faa; border-color: #1c3faa; }
</style>
@endpush

@push('scripts')
<script>
// Role radio visual update
document.querySelectorAll('.role-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.role-option').forEach(opt => {
            opt.style.borderColor = '#f0f3fb';
            opt.style.background  = '#fafafa';
            opt.querySelector('.bi-check-circle-fill')?.remove();
        });
        const label = radio.closest('.role-option');
        label.style.borderColor = '#1c3faa';
        label.style.background  = '#eff6ff';
    });
});
</script>
@endpush

</x-app-layout>
