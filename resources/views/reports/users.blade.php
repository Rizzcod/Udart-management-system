<x-app-layout title="Users Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Users Report</h5>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        style="background:#7c3aed;border-color:#7c3aed;color:#fff;border-radius:8px;">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('reports.users.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.users.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.users.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
                </ul>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#f5f3ff;">
                        <i class="bi bi-people" style="font-size:1.3rem;color:#7c3aed;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#7c3aed;line-height:1;">{{ $totalUsers }}</div>
                        <div class="text-muted small">Total Users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#f0fdf4;">
                        <i class="bi bi-shield-check" style="font-size:1.3rem;color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#16a34a;line-height:1;">{{ $verifiedUsers }}</div>
                        <div class="text-muted small">Verified (Active)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#eff6ff;">
                        <i class="bi bi-hourglass-split" style="font-size:1.3rem;color:#1c3faa;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#1c3faa;line-height:1;">{{ $pendingUsers }}</div>
                        <div class="text-muted small">Pending OTP</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#fef2f2;">
                        <i class="bi bi-clock-history" style="font-size:1.3rem;color:#dc2626;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#dc2626;line-height:1;">{{ $expiredUsers }}</div>
                        <div class="text-muted small">OTP Expired</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-4">
        {{-- Users by Role chart --}}
        <div class="col-lg-6">
            <div class="card p-4" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">Users by Role</h6>
                <div style="position:relative;min-height:220px;">
                    <canvas id="roleChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
        </div>

        {{-- Verification Status chart --}}
        <div class="col-lg-3">
            <div class="card p-4" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">Verification Status</h6>
                <div style="position:relative;min-height:220px;">
                    <canvas id="verifyChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
        </div>

        {{-- Login Activity --}}
        <div class="col-lg-3">
            <div class="card p-4 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">Login Activity</h6>
                <div class="d-flex flex-column gap-3 mt-2">
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Today</span>
                            <span class="small fw-bold">{{ $lastLoginStats['today'] }}</span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px;">
                            <div class="progress-bar bg-success" style="width:{{ $totalUsers > 0 ? round($lastLoginStats['today']/$totalUsers*100) : 0 }}%;border-radius:4px;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Last 7 days</span>
                            <span class="small fw-bold">{{ $lastLoginStats['week'] }}</span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px;">
                            <div class="progress-bar bg-primary" style="width:{{ $totalUsers > 0 ? round($lastLoginStats['week']/$totalUsers*100) : 0 }}%;border-radius:4px;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Last 30 days</span>
                            <span class="small fw-bold">{{ $lastLoginStats['month'] }}</span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px;">
                            <div class="progress-bar bg-info" style="width:{{ $totalUsers > 0 ? round($lastLoginStats['month']/$totalUsers*100) : 0 }}%;border-radius:4px;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Never logged in</span>
                            <span class="small fw-bold">{{ $lastLoginStats['never'] }}</span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px;">
                            <div class="progress-bar bg-secondary" style="width:{{ $totalUsers > 0 ? round($lastLoginStats['never']/$totalUsers*100) : 0 }}%;border-radius:4px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles breakdown table --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="card-body p-0">
                    <div class="px-4 py-3 border-bottom">
                        <h6 class="fw-bold mb-0">Users per Role</h6>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr style="border-bottom:1px solid #f0f3fb;">
                                <th class="ps-4 py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Role</th>
                                <th class="py-2 text-end pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Count</th>
                                <th class="py-2 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;width:40%;">Distribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            @php
                                $pct = $totalUsers > 0 ? round($role->users_count / $totalUsers * 100) : 0;
                                $roleColor = match($role->name) {
                                    'Admin'       => '#dc2626',
                                    'Supervisor'  => '#1c3faa',
                                    'Technician'  => '#16a34a',
                                    'Storekeeper' => '#ea580c',
                                    'Driver'      => '#7c3aed',
                                    default       => '#6b7280',
                                };
                            @endphp
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td class="ps-4 py-3">
                                    <span class="d-inline-flex align-items-center gap-2">
                                        <span class="rounded-circle" style="width:10px;height:10px;background:{{ $roleColor }};display:inline-block;"></span>
                                        <span style="font-size:.875rem;font-weight:500;">{{ $role->name }}</span>
                                    </span>
                                </td>
                                <td class="py-3 text-end pe-4 fw-bold" style="font-size:.875rem;">{{ $role->users_count }}</td>
                                <td class="py-3 pe-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px;border-radius:4px;">
                                            <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $roleColor }};border-radius:4px;"></div>
                                        </div>
                                        <span class="text-muted" style="font-size:.75rem;width:30px;">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recently added users --}}
        <div class="col-lg-7">
            <div class="card" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="card-body p-0">
                    <div class="px-4 py-3 border-bottom">
                        <h6 class="fw-bold mb-0">Recently Added Users</h6>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr style="border-bottom:1px solid #f0f3fb;">
                                <th class="ps-4 py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">User</th>
                                <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Role</th>
                                <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                                <th class="py-2 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $u)
                            @php
                                $uRole = $u->getRoleNames()->first() ?? '—';
                                $otpSt = $u->otpStatus();
                                $stBadge = match($otpSt) {
                                    'active'  => ['bg'=>'#dcfce7','text'=>'#166534','label'=>'Active'],
                                    'pending' => ['bg'=>'#dbeafe','text'=>'#1e40af','label'=>'Pending'],
                                    'expired' => ['bg'=>'#fee2e2','text'=>'#991b1b','label'=>'Expired'],
                                    default   => ['bg'=>'#f3f4f6','text'=>'#6b7280','label'=>'Inactive'],
                                };
                            @endphp
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td class="ps-4 py-3">
                                    <div class="fw-semibold" style="font-size:.875rem;color:#111827;">{{ $u->name }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $u->email }}</div>
                                </td>
                                <td class="py-3" style="font-size:.875rem;">{{ $uRole }}</td>
                                <td class="py-3">
                                    <span class="badge" style="background:{{ $stBadge['bg'] }};color:{{ $stBadge['text'] }};font-size:.72rem;font-weight:600;border-radius:6px;">
                                        {{ $stBadge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 pe-4" style="font-size:.82rem;color:#6b7280;">{{ $u->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function () {
    var roleData  = @json($usersByRole);
    var roleColors = ['#dc2626','#1c3faa','#16a34a','#ea580c','#7c3aed','#6b7280','#ca8a04','#0891b2'];

    new Chart(document.getElementById('roleChart'), {
        type: 'bar',
        data: {
            labels: roleData.map(r => r.label),
            datasets: [{
                label: 'Users',
                data: roleData.map(r => r.count),
                backgroundColor: roleData.map((_, i) => roleColors[i % roleColors.length] + '33'),
                borderColor:     roleData.map((_, i) => roleColors[i % roleColors.length]),
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    new Chart(document.getElementById('verifyChart'), {
        type: 'doughnut',
        data: {
            labels: ['Verified', 'Pending', 'Expired'],
            datasets: [{
                data: [{{ $verifiedUsers }}, {{ $pendingUsers }}, {{ $expiredUsers }}],
                backgroundColor: ['#16a34a','#1c3faa','#dc2626'],
                hoverOffset: 6,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
    });
}());
</script>
@endpush

</x-app-layout>
