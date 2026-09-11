<x-app-layout title="Edit Role – {{ $role->name }}">

    @php
        $meta = match($role->name) {
            'Admin'       => ['bg'=>'#fee2e2','text'=>'#991b1b','av'=>'#dc2626','icon'=>'bi-shield-fill-check'],
            'Supervisor'  => ['bg'=>'#dbeafe','text'=>'#1e40af','av'=>'#1c3faa','icon'=>'bi-person-fill-gear'],
            'Technician'  => ['bg'=>'#dcfce7','text'=>'#166534','av'=>'#16a34a','icon'=>'bi-tools'],
            'Storekeeper' => ['bg'=>'#ffedd5','text'=>'#9a3412','av'=>'#ea580c','icon'=>'bi-box-seam-fill'],
            'Driver'      => ['bg'=>'#f3e8ff','text'=>'#6b21a8','av'=>'#7c3aed','icon'=>'bi-truck'],
            default       => ['bg'=>'#f3f4f6','text'=>'#374151','av'=>'#6b7280','icon'=>'bi-person-fill'],
        };

        $totalAll = 0;
        foreach($groups as $perms) { $totalAll += count($perms); }
        $assigned = count($rolePerms);
    @endphp

    {{-- Page header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">
                <span class="me-2" style="color:{{ $meta['av'] }};"><i class="bi {{ $meta['icon'] }}"></i></span>
                {{ $role->name }} — Permissions
            </h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}" style="color:#1c3faa;">Roles &amp; Permissions</a></li>
                    <li class="breadcrumb-item active text-muted">{{ $role->name }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2"
           style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">

        {{-- ── Left: role info card ── --}}
        <div class="col-lg-3">
            <div class="card text-center" style="border-radius:14px;overflow:hidden;position:sticky;top:1rem;">
                <div style="height:5px;background:{{ $meta['av'] }};"></div>
                <div class="card-body py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:64px;height:64px;background:{{ $meta['bg'] }};">
                        <i class="bi {{ $meta['icon'] }}" style="color:{{ $meta['av'] }};font-size:1.8rem;"></i>
                    </div>
                    <div class="fw-bold mb-1" style="font-size:1rem;color:#111827;">{{ $role->name }}</div>
                    <span class="badge mb-3" style="background:{{ $meta['bg'] }};color:{{ $meta['text'] }};font-size:.75rem;padding:.35em .8em;border-radius:6px;">
                        System Role
                    </span>

                    {{-- Progress ring via simple progress bar --}}
                    @php $pct = $totalAll ? round($assigned / $totalAll * 100) : 0; @endphp
                    <div class="mb-1" style="font-size:.75rem;color:#6b7280;">Permission coverage</div>
                    <div class="progress mb-1" style="height:8px;border-radius:6px;background:#f1f5f9;">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $meta['av'] }};border-radius:6px;"></div>
                    </div>
                    <div class="fw-bold mb-3" data-perm-count style="font-size:.85rem;color:{{ $meta['av'] }};">{{ $assigned }} / {{ $totalAll }} permissions</div>

                    <div class="d-grid gap-2">
                        <button type="button" id="selectAllBtn"
                                class="btn btn-sm"
                                style="background:{{ $meta['bg'] }};color:{{ $meta['text'] }};border:none;border-radius:8px;font-size:.8rem;font-weight:600;"
                                onclick="toggleAll(true)">
                            <i class="bi bi-check2-all me-1"></i> Select All
                        </button>
                        <button type="button"
                                class="btn btn-sm"
                                style="background:#f3f4f6;color:#374151;border:none;border-radius:8px;font-size:.8rem;font-weight:600;"
                                onclick="toggleAll(false)">
                            <i class="bi bi-x-lg me-1"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-transparent px-3 py-3" style="border-top:1px solid #f0f3fb;">
                    <div class="text-muted mb-1" style="font-size:.72rem;">Users with this role</div>
                    <div class="fw-bold" style="font-size:1.2rem;color:#111827;">{{ $role->users()->count() }}</div>
                </div>
            </div>
        </div>

        {{-- ── Right: permission matrix ── --}}
        <div class="col-lg-9">

            @if($errors->any())
            <div class="alert d-flex align-items-start gap-2 alert-dismissible fade show mb-3"
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

            <form method="POST" action="{{ route('roles.update', $role) }}" id="permForm">
                @csrf

                @foreach($groups as $category => $permissions)
                @php
                    $catCount   = count($permissions);
                    $catAssigned = count(array_intersect($rolePerms, $permissions));
                    $catIcons = [
                        'Buses'                  => 'bi-bus-front',
                        'Work Orders'            => 'bi-clipboard-check',
                        'Maintenance Records'    => 'bi-wrench-adjustable',
                        'Preventive Maintenance' => 'bi-calendar-check',
                        'Spare Parts'            => 'bi-box-seam',
                        'Intelligence'           => 'bi-cpu',
                        'Reports'                => 'bi-bar-chart-line',
                        'User Management'        => 'bi-people',
                        'Administration'         => 'bi-gear',
                    ];
                    $catIcon = $catIcons[$category] ?? 'bi-circle';
                    $allInCat = $catAssigned === $catCount;
                @endphp
                <div class="card mb-3" style="border-radius:14px;overflow:hidden;">
                    {{-- Category header --}}
                    <div class="card-header d-flex align-items-center justify-content-between py-3 px-4"
                         style="background:#f8faff;border-bottom:1px solid #eef1f8;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:36px;height:36px;background:#dbeafe;flex-shrink:0;">
                                <i class="bi {{ $catIcon }}" style="color:#1c3faa;font-size:.95rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold" style="font-size:.93rem;color:#111827;">{{ $category }}</div>
                                <div class="text-muted" style="font-size:.73rem;">{{ $catAssigned }} of {{ $catCount }} permissions assigned</div>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0" title="Toggle all in {{ $category }}">
                            <input class="form-check-input cat-toggle" type="checkbox"
                                   data-cat="{{ $loop->index }}"
                                   style="width:2.4em;height:1.2em;cursor:pointer;"
                                   {{ $allInCat ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- Permission checkboxes --}}
                    <div class="card-body px-4 py-3">
                        <div class="row g-2">
                            @foreach($permissions as $perm)
                            @php
                                $checked = in_array($perm, $rolePerms);
                                // Extract verb from permission name for the badge
                                $parts = explode(' ', $perm, 2);
                                $verb  = ucfirst($parts[0]);
                                $obj   = ucwords(str_replace(['-', 'work-order'], [' ', 'Work Order'], $parts[1] ?? ''));
                                $verbColor = match($verb) {
                                    'View'   => ['bg'=>'#dbeafe','text'=>'#1e40af'],
                                    'Create' => ['bg'=>'#dcfce7','text'=>'#166534'],
                                    'Edit'   => ['bg'=>'#ffedd5','text'=>'#9a3412'],
                                    'Delete' => ['bg'=>'#fee2e2','text'=>'#991b1b'],
                                    'Update' => ['bg'=>'#fef9c3','text'=>'#854d0e'],
                                    'Run'    => ['bg'=>'#f3e8ff','text'=>'#6b21a8'],
                                    'Manage' => ['bg'=>'#fee2e2','text'=>'#991b1b'],
                                    default  => ['bg'=>'#f3f4f6','text'=>'#374151'],
                                };
                            @endphp
                            <div class="col-sm-6 col-xl-4">
                                <label class="perm-item d-flex align-items-center gap-2 p-2 rounded-3 cursor-pointer"
                                       style="border:1.5px solid {{ $checked ? '#c7d2fe' : '#f0f3fb' }};background:{{ $checked ? '#f0f4ff' : '#fff' }};transition:all .12s;cursor:pointer;"
                                       data-cat="{{ $loop->parent->index }}">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $perm }}"
                                           class="perm-checkbox form-check-input flex-shrink-0 mb-0"
                                           style="width:1.1em;height:1.1em;cursor:pointer;"
                                           {{ $checked ? 'checked' : '' }}>
                                    <div class="min-w-0">
                                        <span class="badge me-1"
                                              style="background:{{ $verbColor['bg'] }};color:{{ $verbColor['text'] }};font-size:.65rem;font-weight:700;padding:.25em .55em;border-radius:4px;vertical-align:middle;">
                                            {{ $verb }}
                                        </span>
                                        <span style="font-size:.82rem;color:#374151;vertical-align:middle;">{{ $obj }}</span>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Save button --}}
                <div class="d-flex gap-2 pt-1">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                            style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                        <i class="bi bi-check-lg"></i> Save Permissions
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

@push('styles')
<style>
    .perm-item:hover { border-color: #c7d2fe !important; background: #f0f4ff !important; }
    .cursor-pointer { cursor: pointer; }
</style>
@endpush

@push('scripts')
<script>
    // Toggle individual checkbox styling
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', () => updateItemStyle(cb));
    });

    function updateItemStyle(cb) {
        const label = cb.closest('.perm-item');
        if (cb.checked) {
            label.style.borderColor = '#c7d2fe';
            label.style.background  = '#f0f4ff';
        } else {
            label.style.borderColor = '#f0f3fb';
            label.style.background  = '#fff';
        }
        // Sync category toggle
        const cat = label.getAttribute('data-cat');
        syncCatToggle(cat);
        // Sync left-panel count
        updateCount();
    }

    // Category row toggle
    document.querySelectorAll('.cat-toggle').forEach(toggle => {
        toggle.addEventListener('change', () => {
            const cat = toggle.getAttribute('data-cat');
            document.querySelectorAll(`.perm-item[data-cat="${cat}"] .perm-checkbox`).forEach(cb => {
                cb.checked = toggle.checked;
                updateItemStyle(cb);
            });
            updateCount();
        });
    });

    function syncCatToggle(cat) {
        const boxes   = [...document.querySelectorAll(`.perm-item[data-cat="${cat}"] .perm-checkbox`)];
        const toggle  = document.querySelector(`.cat-toggle[data-cat="${cat}"]`);
        if (!toggle) return;
        const all     = boxes.every(cb => cb.checked);
        const some    = boxes.some(cb => cb.checked);
        toggle.checked       = all;
        toggle.indeterminate = some && !all;
    }

    function toggleAll(state) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = state;
            updateItemStyle(cb);
        });
        document.querySelectorAll('.cat-toggle').forEach(t => {
            t.checked = state;
            t.indeterminate = false;
        });
        updateCount();
    }

    function updateCount() {
        const total    = document.querySelectorAll('.perm-checkbox').length;
        const assigned = document.querySelectorAll('.perm-checkbox:checked').length;
        const pct      = total ? Math.round(assigned / total * 100) : 0;
        const bar      = document.querySelector('.progress-bar');
        if (bar) bar.style.width = pct + '%';
        // Update text nodes inside .card-body.text-center — find the fw-bold showing count
        const countEl = document.querySelector('[data-perm-count]');
        if (countEl) countEl.textContent = `${assigned} / ${total} permissions`;
    }

    // Initialize indeterminate states on load
    document.querySelectorAll('.cat-toggle').forEach(t => syncCatToggle(t.getAttribute('data-cat')));
</script>
@endpush

</x-app-layout>
