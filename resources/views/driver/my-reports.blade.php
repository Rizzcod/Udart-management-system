<x-app-layout title="My Reports">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-card-list me-2"></i>My Breakdown Reports</h5>
        <a href="{{ route('driver.report-breakdown') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Report Breakdown
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="card p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label small fw-semibold mb-1">Filter by Status</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['pending','assigned','in_progress','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            @if(request('status'))
            <div class="col-auto">
                <a href="{{ route('driver.my-reports') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
            @endif
        </div>
    </form>

    <div class="card">
        @if($reports->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                No reports found.
                <div class="mt-2">
                    <a href="{{ route('driver.report-breakdown') }}" class="btn btn-sm btn-danger">Report a Breakdown</a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Title</th>
                            <th>Bus</th>
                            <th>Location</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Reported</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td class="ps-3 text-muted small">{{ $report->id }}</td>
                            <td>
                                <div class="fw-semibold small">{{ Str::limit($report->title, 45) }}</div>
                            </td>
                            <td class="small">{{ $report->bus->registration_number }}</td>
                            <td class="small text-muted">{{ $report->location ? Str::limit($report->location, 30) : '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $report->getPriorityBadgeClass() }}">{{ ucfirst($report->priority) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $report->getStatusBadgeClass() }}">
                                    {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                </span>
                            </td>
                            <td class="small">{{ $report->assignee?->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $report->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('driver.show-report', $report) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
