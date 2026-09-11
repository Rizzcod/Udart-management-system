<x-app-layout title="Add User">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Add User</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" style="color:#1c3faa;">Users</a></li>
                    <li class="breadcrumb-item active text-muted">Add User</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2"
           style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        {{-- ── Personal Information ── --}}
        <div class="card mb-4" style="border-radius:14px;">
            <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dbeafe;flex-shrink:0;">
                    <i class="bi bi-person" style="color:#1c3faa;font-size:1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:.95rem;color:#111827;">Personal Information</div>
                    <div class="text-muted" style="font-size:.75rem;">Basic contact and identity details</div>
                </div>
            </div>
            <div class="card-body px-4 py-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. John Doe" style="border-radius:9px;" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email"
                                   class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="user@udart.co.tz"
                                   style="border-radius:0 9px 9px 0;" required>
                        </div>
                        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;"><i class="bi bi-telephone text-muted"></i></span>
                            <input type="text" name="phone"
                                   class="form-control border-start-0 ps-1"
                                   value="{{ old('phone') }}" placeholder="+255 7XX XXX XXX"
                                   style="border-radius:0 9px 9px 0;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}"
                               placeholder="e.g. UDART-001" style="border-radius:9px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Role & Department ── --}}
        <div class="card mb-4" style="border-radius:14px;">
            <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#dcfce7;flex-shrink:0;">
                    <i class="bi bi-person-badge" style="color:#16a34a;font-size:1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:.95rem;color:#111827;">Role &amp; Department</div>
                    <div class="text-muted" style="font-size:.75rem;">System access role and organisational placement</div>
                </div>
            </div>
            <div class="card-body px-4 py-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" style="border-radius:9px;" required>
                            <option value="">Select role…</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Department</label>
                        <select name="department" class="form-select" style="border-radius:9px;">
                            <option value="">— Select department —</option>
                            @foreach(['Fleet Maintenance','Operations','Engineering','Administration','Logistics','IT'] as $dept)
                            <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Position / Title</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position') }}"
                               placeholder="e.g. Fleet Supervisor" style="border-radius:9px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Security ── --}}
        <div class="card mb-4" style="border-radius:14px;">
            <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#ffedd5;flex-shrink:0;">
                    <i class="bi bi-lock" style="color:#ea580c;font-size:1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:.95rem;color:#111827;">Security</div>
                    <div class="text-muted" style="font-size:.75rem;">An OTP verification email will be sent to the user</div>
                </div>
            </div>
            <div class="card-body px-4 py-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Minimum 8 characters" style="border-radius:9px;" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Repeat password" style="border-radius:9px;" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                    style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                <i class="bi bi-person-plus"></i> Create User
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
        </div>
    </form>

</x-app-layout>
