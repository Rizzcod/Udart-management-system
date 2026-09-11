<x-app-layout title="Roles & Permissions">

    {{-- Page header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Roles &amp; Permissions</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Roles &amp; Permissions</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert d-flex align-items-center gap-2 alert-dismissible fade show mb-4"
         style="background:#dcfce7;border:none;border-radius:10px;color:#15803d;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Stat cards ── --}}
    @php
        $stats = [
            ['label' => 'Total Roles',           'value' => $roles->count(),   'icon' => 'bi-shield-lock',  'bg' => '#dbeafe', 'color' => '#1c3faa'],
            ['label' => 'Total Permissions',      'value' => $totalPermissions, 'icon' => 'bi-key',          'bg' => '#dcfce7', 'color' => '#16a34a'],
            ['label' => 'Users with Roles',       'value' => $usersWithRoles,   'icon' => 'bi-people',       'bg' => '#ffedd5', 'color' => '#ea580c'],
            ['label' => 'Avg. Permissions / Role','value' => $avgPerms,         'icon' => 'bi-bar-chart',    'bg' => '#f3e8ff', 'color' => '#7c3aed'],
        ];
    @endphp
    <div class="row g-3 mb-4">
        @foreach($stats as $s)
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100" style="border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:{{ $s['bg'] }};">
                        <i class="bi {{ $s['icon'] }}" style="color:{{ $s['color'] }};font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:1.4rem;color:#111827;line-height:1.1;">{{ $s['value'] }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $s['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Roles grid ── --}}
    <div class="row g-4">
        @foreach($roles as $role)
        @php
            $meta = match($role->name) {
                'Admin'       => ['bg'=>'#fee2e2','text'=>'#991b1b','av'=>'#dc2626','icon'=>'bi-shield-fill-check'],
                'Supervisor'  => ['bg'=>'#dbeafe','text'=>'#1e40af','av'=>'#1c3faa','icon'=>'bi-person-fill-gear'],
                'Technician'  => ['bg'=>'#dcfce7','text'=>'#166534','av'=>'#16a34a','icon'=>'bi-tools'],
                'Storekeeper' => ['bg'=>'#ffedd5','text'=>'#9a3412','av'=>'#ea580c','icon'=>'bi-box-seam-fill'],
                'Driver'      => ['bg'=>'#f3e8ff','text'=>'#6b21a8','av'=>'#7c3aed','icon'=>'bi-truck'],
                default       => ['bg'=>'#f3f4f6','text'=>'#374151','av'=>'#6b7280','icon'=>'bi-person-fill'],
            };
            $rolePerms = $role->permissions->pluck('name')->toArray();
            // Gather tags: pick permission group names that have ≥1 permission assigned
            $activeCats = [];
            foreach($groups as $cat => $perms) {
                if (count(array_intersect($rolePerms, $perms)) > 0) {
                    $activeCats[] = $cat;
                }
            }
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 role-card" style="border-radius:14px;overflow:hidden;border:2px solid transparent;transition:border .15s;">
                {{-- Colour accent top bar --}}
                <div style="height:5px;background:{{ $meta['av'] }};"></div>

                <div class="card-body p-4">
                    {{-- Header --}}
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:50px;height:50px;background:{{ $meta['bg'] }};">
                            <i class="bi {{ $meta['icon'] }}" style="color:{{ $meta['av'] }};font-size:1.4rem;"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold mb-1" style="font-size:1.05rem;color:#111827;">{{ $role->name }}</div>
                            <div class="d-flex gap-3" style="font-size:.78rem;color:#6b7280;">
                                <span><i class="bi bi-people me-1"></i>{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</span>
                                <span><i class="bi bi-key me-1"></i>{{ $role->permissions_count }} {{ Str::plural('permission', $role->permissions_count) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Permission category tags --}}
                    <div class="d-flex flex-wrap gap-1 mb-4">
                        @if(count($activeCats))
                            @foreach($activeCats as $cat)
                            <span class="badge" style="background:{{ $meta['bg'] }};color:{{ $meta['text'] }};font-size:.72rem;font-weight:600;padding:.3em .7em;border-radius:6px;">
                                {{ $cat }}
                            </span>
                            @endforeach
                        @else
                            <span class="text-muted" style="font-size:.78rem;">No permissions assigned</span>
                        @endif
                    </div>

                    {{-- Permission count bar --}}
                    @php $pct = $totalPermissions ? round($role->permissions_count / $totalPermissions * 100) : 0; @endphp
                    <div class="mb-1 d-flex justify-content-between align-items-center" style="font-size:.72rem;color:#9ca3af;">
                        <span>Permission coverage</span>
                        <span class="fw-semibold" style="color:{{ $meta['av'] }};">{{ $pct }}%</span>
                    </div>
                    <div class="progress mb-4" style="height:5px;border-radius:4px;background:#f1f5f9;">
                        <div class="progress-bar" role="progressbar" style="width:{{ $pct }}%;background:{{ $meta['av'] }};border-radius:4px;"></div>
                    </div>

                    {{-- Actions --}}
                    <a href="{{ route('roles.show', $role) }}"
                       class="btn w-100 d-flex align-items-center justify-content-center gap-2"
                       style="background:{{ $meta['bg'] }};color:{{ $meta['text'] }};border:none;border-radius:9px;font-size:.875rem;font-weight:600;">
                        <i class="bi bi-pencil-square"></i> Manage Permissions
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

@push('styles')
<style>
    .role-card:hover { border-color: #c7d2fe !important; box-shadow: 0 4px 16px rgba(28,63,170,.08); }
</style>
@endpush

</x-app-layout>
