<x-app-layout title="My Profile">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">My Profile</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
        $roleColors = ['Admin'=>'#dc2626','Supervisor'=>'#1c3faa','Technician'=>'#16a34a','Storekeeper'=>'#ea580c','Driver'=>'#7c3aed'];
        $roleBgs    = ['Admin'=>'#fee2e2','Supervisor'=>'#dbeafe','Technician'=>'#dcfce7','Storekeeper'=>'#ffedd5','Driver'=>'#ede9fe'];
        $userRole   = $user->roles->first()?->name ?? 'User';
        $rc = $roleColors[$userRole] ?? '#1c3faa';
        $rb = $roleBgs[$userRole]    ?? '#dbeafe';
    @endphp

    <div class="row g-4">
        {{-- Left: identity card + stats --}}
        <div class="col-lg-4">
            <div class="card mb-4 text-center" style="border-radius:14px;overflow:hidden;">
                <div style="height:6px;background:#1c3faa;"></div>
                <div class="card-body py-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle fw-bold text-white"
                         style="width:80px;height:80px;background:#1c3faa;font-size:1.4rem;letter-spacing:1px;">
                        {{ $user->initials() }}
                    </div>
                    <h5 class="fw-bold mb-1" style="color:#111827;">{{ $user->name }}</h5>
                    <span class="badge mb-2" style="background:{{ $rb }};color:{{ $rc }};font-size:.8rem;padding:.4em .9em;border-radius:7px;">{{ $userRole }}</span>
                    <div class="text-muted" style="font-size:.82rem;">{{ $user->email }}</div>
                    @if($user->position)
                    <div class="text-muted mt-1" style="font-size:.8rem;">{{ $user->position }}</div>
                    @endif
                    @if($user->department)
                    <div class="text-muted" style="font-size:.8rem;">{{ $user->department }}</div>
                    @endif
                </div>
                <div class="card-footer bg-transparent" style="border-top:1px solid #f0f3fb;">
                    <div class="row g-0 text-center">
                        <div class="col" style="border-right:1px solid #f0f3fb;">
                            <div class="fw-bold" style="font-size:1.1rem;color:#111827;">{{ $workOrderCount }}</div>
                            <div class="text-muted" style="font-size:.72rem;">Work Orders</div>
                        </div>
                        <div class="col" style="border-right:1px solid #f0f3fb;">
                            <div class="fw-bold" style="font-size:1.1rem;color:#16a34a;">{{ $completedCount }}</div>
                            <div class="text-muted" style="font-size:.72rem;">Completed</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size:1.1rem;color:#111827;">{{ $maintenanceCount }}</div>
                            <div class="text-muted" style="font-size:.72rem;">Maintenance</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="border-radius:14px;">
                <div class="px-4 py-3 d-flex align-items-center gap-2" style="border-bottom:1px solid #f0f3fb;">
                    <i class="bi bi-person-badge text-muted"></i>
                    <span class="fw-bold" style="font-size:.9rem;color:#111827;">Account Info</span>
                </div>
                <div class="card-body px-4">
                    <table class="table table-borderless mb-0" style="font-size:.82rem;">
                        <tr><td class="ps-0 text-muted py-2">Employee ID</td><td class="pe-0 py-2 text-end fw-semibold">{{ $user->employee_id ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Phone</td><td class="pe-0 py-2 text-end fw-semibold">{{ $user->phone ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Department</td><td class="pe-0 py-2 text-end">{{ $user->department ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Position</td><td class="pe-0 py-2 text-end">{{ $user->position ?? '—' }}</td></tr>
                        <tr><td class="ps-0 text-muted py-2">Member Since</td><td class="pe-0 py-2 text-end">{{ $user->created_at->format('d M Y') }}</td></tr>
                        <tr style="border-bottom:none;"><td class="ps-0 text-muted py-2">Last Login</td><td class="pe-0 py-2 text-end">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: edit forms --}}
        <div class="col-lg-8">
            @if(session('status') === 'profile-updated')
            <div class="alert d-flex align-items-center gap-2 alert-dismissible fade show mb-4"
                 style="background:#dcfce7;border:none;border-radius:10px;color:#15803d;">
                <i class="bi bi-check-circle-fill"></i>
                Profile updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('status') === 'password-updated')
            <div class="alert d-flex align-items-center gap-2 alert-dismissible fade show mb-4"
                 style="background:#dcfce7;border:none;border-radius:10px;color:#15803d;">
                <i class="bi bi-check-circle-fill"></i>
                Password changed successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Personal Information --}}
            <div class="card mb-4" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dbeafe;flex-shrink:0;">
                        <i class="bi bi-person" style="color:#1c3faa;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Personal Information</div>
                        <div class="text-muted" style="font-size:.75rem;">Update your name, email, and contact details</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required style="border-radius:9px;">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required style="border-radius:9px;">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}" placeholder="+255 7XX XXX XXX" style="border-radius:9px;">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Employee ID</label>
                                <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                                       value="{{ old('employee_id', $user->employee_id) }}" placeholder="e.g. UDART-001" style="border-radius:9px;">
                                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Department</label>
                                <select name="department" class="form-select @error('department') is-invalid @enderror" style="border-radius:9px;">
                                    <option value="">— Select department —</option>
                                    @foreach(['Fleet Maintenance','Operations','Engineering','Administration','Logistics','IT'] as $dept)
                                    <option value="{{ $dept }}" {{ old('department', $user->department)===$dept?'selected':'' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                                @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Position / Job Title</label>
                                <input type="text" name="position" class="form-control @error('position') is-invalid @enderror"
                                       value="{{ old('position', $user->position) }}" placeholder="e.g. Senior Technician" style="border-radius:9px;">
                                @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card mb-4" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#ffedd5;flex-shrink:0;">
                        <i class="bi bi-lock" style="color:#ea580c;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Change Password</div>
                        <div class="text-muted" style="font-size:.75rem;">Use a strong password you don't use elsewhere</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf @method('PATCH')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Current Password</label>
                                <input type="password" name="current_password" autocomplete="current-password"
                                       class="form-control @error('current_password') is-invalid @enderror" style="border-radius:9px;">
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">New Password</label>
                                <input type="password" name="password" autocomplete="new-password"
                                       class="form-control @error('password') is-invalid @enderror" style="border-radius:9px;">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Confirm New Password</label>
                                <input type="password" name="password_confirmation" autocomplete="new-password"
                                       class="form-control" style="border-radius:9px;">
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn d-flex align-items-center gap-2"
                                        style="background:#ea580c;border-color:#ea580c;color:#fff;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-key"></i> Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
