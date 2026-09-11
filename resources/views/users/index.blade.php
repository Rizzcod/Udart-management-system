<x-app-layout title="Users Management">

    {{-- ── Page header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Users Management</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Users</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
               style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;font-size:.875rem;">
                <i class="bi bi-plus-lg"></i> Add User
            </a>
        </div>
    </div>

    {{-- ── Filter bar ── --}}
    <div class="card mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label small text-muted fw-semibold mb-1">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name, email, or phone…"
                                   style="border-radius:0 9px 9px 0;">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small text-muted fw-semibold mb-1">Role</label>
                        <select name="role" class="form-select" style="border-radius:9px;">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small text-muted fw-semibold mb-1">Verification</label>
                        <select name="status" class="form-select" style="border-radius:9px;">
                            <option value="">All Status</option>
                            <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Active (Verified)</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending OTP</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>OTP Expired</option>
                        </select>
                    </div>
                    <div class="col-lg-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-size:.875rem;white-space:nowrap;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request('search') || request('role') || request('status'))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1"
                           style="border-radius:9px;font-size:.875rem;white-space:nowrap;">
                            <i class="bi bi-x-lg"></i> Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Flash messages ── --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;border:none;background:#dcfce7;color:#166534;" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mb-3" style="border-radius:10px;border:none;background:#fef9c3;color:#854d0e;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:10px;border:none;background:#fee2e2;color:#991b1b;" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Users list ── --}}
    <div class="card" style="border-radius:14px;">
        <div class="card-body p-0">

            {{-- List header --}}
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                <span class="fw-bold" style="font-size:.95rem;color:#111827;">Users List</span>
                <span class="text-muted" style="font-size:.82rem;">{{ number_format($total) }} total users</span>
            </div>

            {{-- Column headers --}}
            <div class="table-responsive">
                <table class="table mb-0" style="border-collapse:separate;">
                    <thead>
                        <tr style="border-bottom:1px solid #f0f3fb;">
                            <th class="ps-4 py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;width:26%;">User</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;width:20%;">Contact</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;width:16%;">Role &amp; Info</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;width:16%;">Verification</th>
                            <th class="py-3" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;width:11%;">Last Login</th>
                            <th class="py-3 text-end pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;width:11%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        @php
                            $role      = $user->getRoleNames()->first() ?? 'No Role';
                            $roleColor = match($role) {
                                'Admin'       => ['bg'=>'#fee2e2','text'=>'#991b1b','av'=>'#dc2626'],
                                'Supervisor'  => ['bg'=>'#dbeafe','text'=>'#1e40af','av'=>'#1c3faa'],
                                'Technician'  => ['bg'=>'#dcfce7','text'=>'#166534','av'=>'#16a34a'],
                                'Storekeeper' => ['bg'=>'#ffedd5','text'=>'#9a3412','av'=>'#ea580c'],
                                'Driver'      => ['bg'=>'#f3e8ff','text'=>'#6b21a8','av'=>'#7c3aed'],
                                default       => ['bg'=>'#f3f4f6','text'=>'#374151','av'=>'#6b7280'],
                            };
                            $otpStatus = $user->otpStatus();
                            $statusBadge = match($otpStatus) {
                                'active'   => ['bg'=>'#dcfce7','text'=>'#166534','icon'=>'bi-shield-check',   'label'=>'Active'],
                                'pending'  => ['bg'=>'#dbeafe','text'=>'#1e40af','icon'=>'bi-hourglass-split','label'=>'Pending OTP'],
                                'expired'  => ['bg'=>'#fee2e2','text'=>'#991b1b','icon'=>'bi-clock-history',  'label'=>'OTP Expired'],
                                default    => ['bg'=>'#f3f4f6','text'=>'#6b7280','icon'=>'bi-dash-circle',    'label'=>'Inactive'],
                            };
                        @endphp
                        <tr style="border-bottom:1px solid #f9fafb;" class="user-row">
                            {{-- User --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                         style="width:42px;height:42px;background:{{ $roleColor['av'] }};font-size:.95rem;">
                                        {{ strtoupper(substr($user->name,0,1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.9rem;color:#111827;">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            @if($user->employee_id)
                                                ID: {{ $user->employee_id }}
                                            @else
                                                {{ $user->email }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2 mb-1" style="font-size:.83rem;color:#374151;">
                                    <i class="bi bi-envelope" style="color:#9ca3af;"></i>
                                    {{ $user->email }}
                                </div>
                                <div class="d-flex align-items-center gap-2" style="font-size:.83rem;color:#374151;">
                                    <i class="bi bi-telephone" style="color:#9ca3af;"></i>
                                    {{ $user->phone ?: '—' }}
                                </div>
                            </td>

                            {{-- Role & Info --}}
                            <td class="py-3">
                                <span class="badge mb-1" style="background:{{ $roleColor['bg'] }};color:{{ $roleColor['text'] }};font-size:.75rem;font-weight:600;padding:.35em .7em;border-radius:6px;">
                                    {{ $role }}
                                </span>
                                @if($user->department)
                                <div style="font-size:.75rem;color:#6b7280;" class="mt-1">{{ $user->department }}</div>
                                @endif
                                @if($user->position)
                                <div style="font-size:.75rem;color:#9ca3af;">{{ $user->position }}</div>
                                @endif
                            </td>

                            {{-- Verification Status --}}
                            <td class="py-3">
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['text'] }};font-size:.75rem;font-weight:600;padding:.35em .7em;border-radius:6px;">
                                    <i class="bi {{ $statusBadge['icon'] }}"></i>
                                    {{ $statusBadge['label'] }}
                                </span>
                                @if($otpStatus === 'pending' && $user->otp_expires_at)
                                <div style="font-size:.7rem;color:#9ca3af;" class="mt-1">
                                    Expires {{ $user->otp_expires_at->diffForHumans() }}
                                </div>
                                @endif
                                @if($otpStatus === 'expired')
                                <div style="font-size:.7rem;color:#dc2626;" class="mt-1">OTP has expired</div>
                                @endif
                            </td>

                            {{-- Last Login --}}
                            <td class="py-3">
                                @if($user->last_login_at)
                                    <div style="font-size:.82rem;color:#374151;">{{ $user->last_login_at->format('d M Y') }}</div>
                                    <div style="font-size:.73rem;color:#9ca3af;">{{ $user->last_login_at->diffForHumans() }}</div>
                                @else
                                    <span style="font-size:.82rem;color:#9ca3af;">Never</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                                    <a href="{{ route('users.edit', $user) }}"
                                       title="Edit user"
                                       class="action-icon" style="color:#1c3faa;">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('users.permissions', $user) }}"
                                       title="Manage roles &amp; permissions"
                                       class="action-icon" style="color:#7c3aed;">
                                        <i class="bi bi-shield-lock"></i>
                                    </a>

                                    {{-- Resend OTP for unverified users --}}
                                    @if($otpStatus !== 'active')
                                    <form method="POST" action="{{ route('users.resend-otp', $user) }}" class="d-inline"
                                          onsubmit="return confirm('Resend OTP to {{ addslashes($user->email) }}?')">
                                        @csrf
                                        <button type="submit" title="Resend OTP" class="action-icon border-0 bg-transparent p-0" style="color:#d97706;">
                                            <i class="bi bi-envelope-arrow-up"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Delete user --}}
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline delete-user-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete user"
                                                class="action-icon border-0 bg-transparent p-0"
                                                style="color:#dc2626;"
                                                data-name="{{ addslashes($user->name) }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people d-block" style="font-size:2.5rem;opacity:.2;"></i>
                                <div class="mt-2">No users found.</div>
                                @if(request('search') || request('role') || request('status'))
                                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary mt-2" style="border-radius:8px;">Clear filters</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
                <div class="text-muted" style="font-size:.82rem;">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            @endif

        </div>
    </div>

@push('styles')
<style>
    .user-row { transition: background .12s; }
    .user-row:hover { background: #f8faff !important; }
    .user-row:last-child { border-bottom: none !important; }
    .action-icon { font-size:1.05rem; cursor:pointer; transition:opacity .15s; }
    .action-icon:hover { opacity:.7; }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.delete-user-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        var name = this.querySelector('button[data-name]').dataset.name;
        if (!confirm('Delete user "' + name + '"?\n\nThis action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

</x-app-layout>
