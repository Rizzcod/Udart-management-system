<x-app-layout title="Edit User">

    {{-- Page header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Edit User</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" style="color:#1c3faa;">Users</a></li>
                    <li class="breadcrumb-item active text-muted">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.permissions', $user) }}" class="btn d-flex align-items-center gap-2"
               style="background:#7c3aed;color:#fff;border-color:#7c3aed;border-radius:9px;font-size:.875rem;font-weight:600;">
                <i class="bi bi-shield-lock"></i> Roles &amp; Permissions
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2"
               style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    @php
        $role = $user->getRoleNames()->first() ?? 'No Role';
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
            'active'  => ['bg'=>'#dcfce7','text'=>'#166534','icon'=>'bi-shield-check',    'label'=>'Active'],
            'pending' => ['bg'=>'#dbeafe','text'=>'#1e40af','icon'=>'bi-hourglass-split', 'label'=>'Pending OTP'],
            'expired' => ['bg'=>'#fee2e2','text'=>'#991b1b','icon'=>'bi-clock-history',   'label'=>'OTP Expired'],
            default   => ['bg'=>'#f3f4f6','text'=>'#6b7280','icon'=>'bi-dash-circle',     'label'=>'Inactive'],
        };
    @endphp

    <div class="row g-4">

        {{-- Left: user identity card --}}
        <div class="col-lg-3">
            <div class="card text-center" style="border-radius:14px;overflow:hidden;">
                <div style="height:5px;background:{{ $roleColor['av'] }};"></div>
                <div class="card-body py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle fw-bold text-white"
                         style="width:68px;height:68px;background:{{ $roleColor['av'] }};font-size:1.3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="fw-bold mb-1" style="font-size:.95rem;color:#111827;">{{ $user->name }}</div>
                    <span class="badge mb-2" style="background:{{ $roleColor['bg'] }};color:{{ $roleColor['text'] }};font-size:.75rem;padding:.35em .8em;border-radius:6px;">
                        {{ $role }}
                    </span>
                    <div class="text-muted" style="font-size:.78rem;">{{ $user->email }}</div>
                </div>
                <div class="card-footer bg-transparent px-3 py-3" style="border-top:1px solid #f0f3fb;">
                    <table class="table table-borderless mb-0" style="font-size:.78rem;">
                        <tr>
                            <td class="ps-0 text-muted py-1 text-start">Status</td>
                            <td class="pe-0 py-1 text-end">
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['text'] }};font-size:.72rem;padding:.3em .6em;border-radius:5px;">
                                    <i class="bi {{ $statusBadge['icon'] }}"></i> {{ $statusBadge['label'] }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted py-1 text-start">Employee ID</td>
                            <td class="pe-0 py-1 text-end fw-semibold" style="color:#111827;">{{ $user->employee_id ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted py-1 text-start">Department</td>
                            <td class="pe-0 py-1 text-end">{{ $user->department ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted py-1 text-start">Last Login</td>
                            <td class="pe-0 py-1 text-end">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                        </tr>
                        <tr style="border-bottom:none;">
                            <td class="ps-0 text-muted py-1 text-start">Member Since</td>
                            <td class="pe-0 py-1 text-end">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: edit form --}}
        <div class="col-lg-9">

            @if(session('success'))
            <div class="alert d-flex align-items-center gap-2 alert-dismissible fade show mb-4"
                 style="background:#dcfce7;border:none;border-radius:10px;color:#15803d;">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf @method('PUT')

                {{-- ── Basic info ── --}}
                <div class="card mb-4" style="border-radius:14px;">
                    <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#dbeafe;flex-shrink:0;">
                            <i class="bi bi-person" style="color:#1c3faa;"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.93rem;color:#111827;">Personal Information</div>
                            <div class="text-muted" style="font-size:.74rem;">Name, email, phone and identity details</div>
                        </div>
                    </div>
                    <div class="card-body px-4 py-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required style="border-radius:9px;">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="email"
                                           class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}" required style="border-radius:0 9px 9px 0;">
                                </div>
                                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;"><i class="bi bi-telephone text-muted"></i></span>
                                    <input type="text" name="phone"
                                           class="form-control border-start-0 ps-1 @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $user->phone) }}"
                                           placeholder="+255 7XX XXX XXX" style="border-radius:0 9px 9px 0;">
                                </div>
                                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Employee ID</label>
                                <input type="text" name="employee_id"
                                       class="form-control @error('employee_id') is-invalid @enderror"
                                       value="{{ old('employee_id', $user->employee_id) }}"
                                       placeholder="e.g. UDART-001" style="border-radius:9px;">
                                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Role & department ── --}}
                <div class="card mb-4" style="border-radius:14px;">
                    <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#dcfce7;flex-shrink:0;">
                            <i class="bi bi-person-badge" style="color:#16a34a;"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.93rem;color:#111827;">Role &amp; Department</div>
                            <div class="text-muted" style="font-size:.74rem;">System access role and organisational placement</div>
                        </div>
                    </div>
                    <div class="card-body px-4 py-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" style="border-radius:9px;" required>
                                    @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ $user->hasRole($r->name) ? 'selected' : '' }}>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Department</label>
                                <select name="department" class="form-select @error('department') is-invalid @enderror" style="border-radius:9px;">
                                    <option value="">— Select department —</option>
                                    @foreach(['Fleet Maintenance','Operations','Engineering','Administration','Logistics','IT'] as $dept)
                                    <option value="{{ $dept }}" {{ old('department', $user->department) === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                                @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Position / Job Title</label>
                                <input type="text" name="position"
                                       class="form-control @error('position') is-invalid @enderror"
                                       value="{{ old('position', $user->position) }}"
                                       placeholder="e.g. Senior Technician" style="border-radius:9px;">
                                @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Password ── --}}
                <div class="card mb-4" style="border-radius:14px;">
                    <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#ffedd5;flex-shrink:0;">
                            <i class="bi bi-lock" style="color:#ea580c;"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.93rem;color:#111827;">Change Password</div>
                            <div class="text-muted" style="font-size:.74rem;">Leave blank to keep the current password</div>
                        </div>
                    </div>
                    <div class="card-body px-4 py-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">New Password</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Leave blank to keep current" style="border-radius:9px;">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Repeat new password" style="border-radius:9px;">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Actions ── --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                            style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                        <i class="bi bi-check-lg"></i> Save Changes
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
