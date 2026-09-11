<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #198754; color: #fff; padding: 16px 20px; border-radius: 6px; margin-bottom: 20px; }
    .header h1 { margin: 0 0 4px 0; font-size: 18px; font-weight: 700; }
    .header p  { margin: 0; font-size: 10px; opacity: .85; }
    .kpis { margin-bottom: 16px; }
    .kpi-row { display: flex; gap: 12px; }
    .kpi { background: #f0fdf4; border-left: 4px solid #198754; padding: 7px 12px; border-radius: 4px; flex: 1; }
    .kpi span { font-size: 17px; font-weight: 700; color: #15803d; }
    .kpi small { display: block; color: #6b7280; font-size: 8px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #198754; color: #fff; padding: 6px 7px; text-align: left; font-size: 9px; }
    tbody tr:nth-child(even) { background: #f0fdf4; }
    tbody td { padding: 5px 7px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
    .footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 8px; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <h1>Fleet Performance Report</h1>
        <p>UDART Fleet Management System &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="kpis">
        <div class="kpi-row">
            <div class="kpi">
                <span>{{ $buses->count() }}</span>
                <small>Total Buses</small>
            </div>
            <div class="kpi">
                <span>{{ $buses->where('status', 'active')->count() }}</span>
                <small>Active</small>
            </div>
            <div class="kpi">
                <span>{{ $buses->whereIn('status', ['under_repair','breakdown_reported','awaiting_spare_parts'])->count() }}</span>
                <small>Under Repair / Breakdown</small>
            </div>
            <div class="kpi">
                <span>{{ $buses->whereIn('status', ['maintenance_due','under_preventive_maintenance'])->count() }}</span>
                <small>Maintenance Due</small>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Registration</th>
                <th>Model</th>
                <th>Year</th>
                <th>Status</th>
                <th>Mileage</th>
                <th>Work Orders</th>
                <th>Completed</th>
                <th>Maint. Records</th>
                <th>MTTR (days)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fleetStats as $stat)
            <tr>
                <td style="font-weight:600;">{{ $stat['bus']->registration_number }}</td>
                <td>{{ $stat['bus']->model }}</td>
                <td>{{ $stat['bus']->year }}</td>
                <td>{{ $stat['bus']->getStatusLabel() }}</td>
                <td style="text-align:right;">{{ number_format($stat['bus']->mileage) }}</td>
                <td style="text-align:center;">{{ $stat['total_wo'] }}</td>
                <td style="text-align:center;">{{ $stat['completed_wo'] }}</td>
                <td style="text-align:center;">{{ $stat['total_records'] }}</td>
                <td style="text-align:center;">{{ $stat['mttr'] ?: '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:14px;">No buses found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">UDART IMMS &nbsp;|&nbsp; Fleet Performance Report &nbsp;|&nbsp; {{ now()->format('Y') }}</div>
</body>
</html>
