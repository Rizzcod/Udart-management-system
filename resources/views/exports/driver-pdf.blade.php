<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #0d9488; color: #fff; padding: 16px 20px; border-radius: 6px; margin-bottom: 16px; }
    .header h1 { margin: 0 0 4px 0; font-size: 18px; font-weight: 700; }
    .header p  { margin: 0; font-size: 10px; opacity: .85; }
    .kpi-row { display: flex; gap: 12px; margin-bottom: 16px; }
    .kpi { flex: 1; background: #f0fdfa; border-left: 4px solid #0d9488; padding: 8px 12px; border-radius: 4px; }
    .kpi span { font-size: 20px; font-weight: 700; color: #0d9488; }
    .kpi small { display: block; color: #6b7280; font-size: 9px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #0d9488; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #f0fdfa; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: top; }
    .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 9px; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <h1>Driver Activity Report</h1>
        <p>Driver: {{ $driverName }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
        @if($fromDate || $toDate)
        <p style="margin-top:4px;">Period: {{ $fromDate ?? 'All time' }} — {{ $toDate ?? 'Present' }}</p>
        @endif
    </div>

    <div class="kpi-row">
        <div class="kpi"><span>{{ $totalLogs }}</span><small>Total Trips Logged</small></div>
        <div class="kpi"><span>{{ number_format($totalKm) }} km</span><small>Total KM Traveled</small></div>
        <div class="kpi"><span>{{ number_format($thisMonthKm) }} km</span><small>This Month KM</small></div>
        <div class="kpi"><span>{{ $breakdownCount }}</span><small>Breakdowns Reported</small></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Bus</th>
                <th>KM Traveled</th>
                <th>Odometer (km)</th>
                <th>Route</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td style="white-space:nowrap;">{{ $log->trip_date->format('d M Y') }}</td>
                <td style="white-space:nowrap;">{{ $log->bus->registration_number }}</td>
                <td>{{ number_format($log->km_traveled) }}</td>
                <td>{{ number_format($log->odometer_reading) }}</td>
                <td>{{ $log->route ?? '—' }}</td>
                <td>{{ \Str::limit($log->notes ?? '', 40) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:16px;">No trip logs found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">UDART IMMS &nbsp;|&nbsp; Driver Activity Report &nbsp;|&nbsp; {{ now()->format('Y') }}</div>
</body>
</html>
