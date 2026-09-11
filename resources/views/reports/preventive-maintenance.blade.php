<x-app-layout title="Preventive Maintenance Report">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-calendar2-check me-2"></i>Preventive Maintenance Report</h5>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        style="background:#16a34a;border-color:#16a34a;border-radius:8px;">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export As</h6></li>
                    <li><a class="dropdown-item" href="{{ route('reports.preventive-maintenance.export', ['format'=>'xlsx']) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.preventive-maintenance.export', ['format'=>'csv']) }}"><i class="bi bi-filetype-csv me-2 text-primary"></i>CSV (.csv)</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.preventive-maintenance.export', ['format'=>'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)</a></li>
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
                         style="width:46px;height:46px;background:#f0fdf4;">
                        <i class="bi bi-calendar2-check" style="font-size:1.3rem;color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#16a34a;line-height:1;">{{ $totalSchedules }}</div>
                        <div class="text-muted small">Total Schedules</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#fef2f2;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:1.3rem;color:#dc2626;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#dc2626;line-height:1;">{{ $overdueCount }}</div>
                        <div class="text-muted small">Overdue</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#fef9c3;">
                        <i class="bi bi-alarm" style="font-size:1.3rem;color:#ca8a04;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#ca8a04;line-height:1;">{{ $upcomingCount }}</div>
                        <div class="text-muted small">Upcoming</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100" style="border-radius:12px;border:1px solid #e5e7eb;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:46px;height:46px;background:#eff6ff;">
                        <i class="bi bi-check2-all" style="font-size:1.3rem;color:#1c3faa;"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold" style="color:#1c3faa;line-height:1;">{{ $completedCount }}</div>
                        <div class="text-muted small">Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card p-4" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">Status Distribution</h6>
                <canvas id="pmStatusChart" height="240"></canvas>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card p-4" style="border-radius:12px;border:1px solid #e5e7eb;">
                <h6 class="fw-bold mb-3">Schedules by Service Type</h6>
                <canvas id="pmServiceTypeChart" height="240"></canvas>
            </div>
        </div>
    </div>
    <script>
    (function () {
        var byStatus      = @json($byStatus);
        var byServiceType = @json($byServiceType);
        new Chart(document.getElementById('pmStatusChart'), {
            type: 'doughnut',
            data: {
                labels: byStatus.map(function(s){ return s.label; }),
                datasets: [{ data: byStatus.map(function(s){ return s.total; }), backgroundColor: byStatus.map(function(s){ return s.color; }), hoverOffset: 6 }]
            },
            options: { cutout: '60%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
        });
        new Chart(document.getElementById('pmServiceTypeChart'), {
            type: 'bar',
            data: {
                labels: byServiceType.map(function(s){ return s.label; }),
                datasets: [{ label: 'Schedules', data: byServiceType.map(function(s){ return s.total; }), backgroundColor: '#16a34a33', borderColor: '#16a34a', borderWidth: 2, borderRadius: 6 }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }());
    </script>

    {{-- Overdue Alerts --}}
    @if($overdueCount > 0)
    <div class="card mb-4" style="border-radius:12px;border:1px solid #fca5a5;background:#fef2f2;">
        <div class="card-body p-0">
            <div class="px-4 py-3 border-bottom" style="border-color:#fca5a5 !important;">
                <h6 class="fw-bold mb-0" style="color:#dc2626;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Overdue Schedules ({{ $overdueCount }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr style="border-bottom:1px solid #fca5a5;">
                            <th class="ps-4 py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Service Type</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Next Service Date</th>
                            <th class="py-2 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdue as $pm)
                        <tr style="border-bottom:1px solid #fee2e2;">
                            <td class="ps-4 py-3 fw-semibold" style="font-size:.875rem;">{{ $pm->bus->registration_number ?? '—' }}</td>
                            <td class="py-3" style="font-size:.875rem;">{{ $pm->service_type }}</td>
                            <td class="py-3" style="font-size:.875rem;color:#dc2626;">{{ $pm->next_service_date?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 pe-4 fw-bold" style="font-size:.875rem;color:#dc2626;">
                                {{ $pm->next_service_date ? abs((int) round(now()->diffInDays($pm->next_service_date))) . ' days' : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Upcoming schedules --}}
    @if($upcomingCount > 0)
    <div class="card mb-4" style="border-radius:12px;border:1px solid #fde68a;background:#fefce8;">
        <div class="card-body p-0">
            <div class="px-4 py-3 border-bottom" style="border-color:#fde68a !important;">
                <h6 class="fw-bold mb-0" style="color:#ca8a04;">
                    <i class="bi bi-alarm me-2"></i>Upcoming Schedules (next 10)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr style="border-bottom:1px solid #fde68a;">
                            <th class="ps-4 py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Service Type</th>
                            <th class="py-2 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcoming as $pm)
                        <tr style="border-bottom:1px solid #fef08a;">
                            <td class="ps-4 py-3 fw-semibold" style="font-size:.875rem;">{{ $pm->bus->registration_number ?? '—' }}</td>
                            <td class="py-3" style="font-size:.875rem;">{{ $pm->service_type }}</td>
                            <td class="py-3 pe-4" style="font-size:.875rem;color:#ca8a04;">
                                {{ $pm->next_service_date?->format('d M Y') ?? '—' }}
                                @if($pm->next_service_date)
                                <span class="text-muted ms-1" style="font-size:.75rem;">({{ $pm->next_service_date->diffForHumans() }})</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- All Schedules Table --}}
    <div class="card" style="border-radius:12px;border:1px solid #e5e7eb;">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                <span class="fw-bold" style="font-size:.95rem;">All PM Schedules</span>
                <span class="text-muted small">{{ $totalSchedules }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr style="border-bottom:1px solid #f0f3fb;">
                            <th class="ps-4 py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Bus</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Service Type</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Last Service</th>
                            <th class="py-2" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Next Service</th>
                            <th class="py-2 pe-4" style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $pm)
                        @php
                            $stColor = match($pm->status) {
                                'overdue'   => ['bg'=>'#fef2f2','text'=>'#dc2626'],
                                'upcoming'  => ['bg'=>'#fef9c3','text'=>'#ca8a04'],
                                'completed' => ['bg'=>'#dcfce7','text'=>'#16a34a'],
                                default     => ['bg'=>'#f3f4f6','text'=>'#6b7280'],
                            };
                        @endphp
                        <tr style="border-bottom:1px solid #f9fafb;">
                            <td class="ps-4 py-3 fw-semibold" style="font-size:.875rem;color:#111827;">{{ $pm->bus->registration_number ?? '—' }}</td>
                            <td class="py-3" style="font-size:.875rem;">{{ $pm->service_type }}</td>
                            <td class="py-3" style="font-size:.82rem;color:#6b7280;">{{ $pm->last_service_date?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3" style="font-size:.82rem;color:#374151;">{{ $pm->next_service_date?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 pe-4">
                                <span class="badge" style="background:{{ $stColor['bg'] }};color:{{ $stColor['text'] }};font-size:.72rem;font-weight:600;border-radius:6px;">
                                    {{ ucfirst($pm->status ?? 'scheduled') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar2-x d-block" style="font-size:2.5rem;opacity:.2;"></i>
                                <div class="mt-2">No PM schedules found.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</x-app-layout>
