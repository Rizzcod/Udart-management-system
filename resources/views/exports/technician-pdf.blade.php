<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #1c3faa; color: #fff; padding: 16px 20px; border-radius: 6px; margin-bottom: 16px; }
    .header h1 { margin: 0 0 4px 0; font-size: 18px; font-weight: 700; }
    .header p  { margin: 0; font-size: 10px; opacity: .85; }
    .kpi-row { display: flex; gap: 12px; margin-bottom: 16px; }
    .kpi { flex: 1; background: #f0f4ff; border-left: 4px solid #1c3faa; padding: 8px 12px; border-radius: 4px; }
    .kpi span { font-size: 20px; font-weight: 700; color: #1c3faa; }
    .kpi small { display: block; color: #6b7280; font-size: 9px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #1c3faa; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #f0f4ff; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: top; }
    .badge-critical { background:#fee2e2;color:#991b1b;padding:1px 5px;border-radius:3px; }
    .badge-high     { background:#ffedd5;color:#c2410c;padding:1px 5px;border-radius:3px; }
    .badge-medium   { background:#dbeafe;color:#1e40af;padding:1px 5px;border-radius:3px; }
    .badge-low      { background:#f3f4f6;color:#374151;padding:1px 5px;border-radius:3px; }
    .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 9px; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <h1>My Work Orders Report</h1>
        <p>Technician: {{ $techName }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="kpi-row">
        <div class="kpi"><span>{{ $totalWO }}</span><small>Total Assigned</small></div>
        <div class="kpi"><span>{{ $completedWO }}</span><small>Completed</small></div>
        <div class="kpi"><span>{{ $inProgressWO }}</span><small>In Progress</small></div>
        <div class="kpi"><span>{{ $avgDays > 0 ? $avgDays . ' days' : '—' }}</span><small>Avg Completion Time</small></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>WO #</th>
                <th>Title</th>
                <th>Bus</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Assigned</th>
                <th>Started</th>
                <th>Completed</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrders as $wo)
            <tr>
                <td>#{{ $wo->id }}</td>
                <td>{{ \Str::limit($wo->title, 35) }}</td>
                <td style="white-space:nowrap;">{{ $wo->bus->registration_number }}</td>
                <td><span class="badge-{{ $wo->priority }}">{{ strtoupper($wo->priority) }}</span></td>
                <td style="white-space:nowrap;">{{ ucwords(str_replace('_',' ',$wo->status)) }}</td>
                <td style="white-space:nowrap;">{{ $wo->created_at->format('d M Y') }}</td>
                <td style="white-space:nowrap;">{{ $wo->start_date?->format('d M Y') ?? '—' }}</td>
                <td style="white-space:nowrap;">{{ $wo->completion_date?->format('d M Y') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:16px;">No work orders assigned.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">UDART IMMS &nbsp;|&nbsp; Technician Work Orders Report &nbsp;|&nbsp; {{ now()->format('Y') }}</div>
</body>
</html>
