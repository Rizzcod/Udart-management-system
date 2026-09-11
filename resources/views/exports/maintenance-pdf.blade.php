<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #1c3faa; color: #fff; padding: 16px 20px; border-radius: 6px; margin-bottom: 20px; }
    .header h1 { margin: 0 0 4px 0; font-size: 18px; font-weight: 700; }
    .header p  { margin: 0; font-size: 10px; opacity: .8; }
    .meta { display: flex; gap: 24px; margin-bottom: 16px; }
    .kpi { background: #f0f4ff; border-left: 4px solid #1c3faa; padding: 8px 12px; border-radius: 4px; margin-bottom: 16px; }
    .kpi span { font-size: 20px; font-weight: 700; color: #1c3faa; }
    .kpi small { display: block; color: #6b7280; font-size: 9px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    thead th { background: #1c3faa; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #f8faff; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: top; }
    .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 9px; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <h1>Maintenance Report</h1>
        <p>UDART Fleet Management System &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
        @if($filters['bus'] || $filters['from'] || $filters['to'])
        <p style="margin-top:6px;">
            Filter:
            @if($filters['bus']) Bus: {{ $filters['bus'] }} @endif
            @if($filters['from']) &nbsp;From: {{ $filters['from'] }} @endif
            @if($filters['to']) &nbsp;To: {{ $filters['to'] }} @endif
        </p>
        @endif
    </div>

    <div class="kpi">
        <span>{{ $totalRecords }}</span>
        <small>Total Maintenance Records</small>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Bus</th>
                <th>Description</th>
                <th>Technician</th>
                <th>Parts Replaced</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
            <tr>
                <td style="white-space:nowrap;">{{ $rec->maintenance_date->format('d M Y') }}</td>
                <td style="white-space:nowrap;">{{ $rec->bus->registration_number }}</td>
                <td>{{ Str::limit($rec->description, 80) }}</td>
                <td style="white-space:nowrap;">{{ $rec->technician->name }}</td>
                <td>{{ $rec->parts_replaced ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:16px;">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">UDART IMMS &nbsp;|&nbsp; Maintenance Report &nbsp;|&nbsp; {{ now()->format('Y') }}</div>
</body>
</html>
