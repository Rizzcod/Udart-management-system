<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 16px; }

    /* Header */
    .header { background: #16a34a; color: #fff; padding: 12px 16px; margin-bottom: 12px; }
    .header h1 { margin: 0 0 2px; font-size: 16px; font-weight: 700; }
    .header p  { margin: 0; font-size: 9px; opacity: .85; }

    /* KPI table */
    .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .kpi-table td { width: 20%; padding: 7px 9px; }
    .kpi-num { font-size: 18px; font-weight: 700; line-height: 1.1; }
    .kpi-lbl { font-size: 8px; color: #6b7280; }

    /* Charts outer table */
    .charts-outer { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .charts-outer td { vertical-align: top; padding: 0; }
    .chart-box { border: 1px solid #e5e7eb; padding: 9px 10px; }
    .chart-title { font-size: 10px; font-weight: 700; color: #111827; margin-bottom: 7px; }

    /* Bar chart row */
    .bar-label { font-size: 8.5px; color: #374151; margin-bottom: 2px; }
    .bar-table  { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
    .bar-filled { height: 11px; }
    .bar-empty  { height: 11px; background: #f3f4f6; }

    /* Section headings */
    .sec-head { font-size: 10px; font-weight: 700; padding: 5px 8px; margin-bottom: 6px; }
    .sec-overdue  { background: #fef2f2; color: #dc2626; border-left: 3px solid #dc2626; }
    .sec-upcoming { background: #fefce8; color: #92400e; border-left: 3px solid #ca8a04; }
    .sec-all      { background: #f0fdf4; color: #15803d; border-left: 3px solid #16a34a; }

    /* Data tables */
    table.dt { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.dt thead th { padding: 5px 6px; text-align: left; font-size: 8.5px; color: #fff; }
    table.dt tbody td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8.5px; vertical-align: top; }
    table.dt tbody tr:nth-child(even) td { background: #f9fafb; }
    .th-green  { background: #16a34a; }
    .th-red    { background: #dc2626; }
    .th-amber  { background: #ca8a04; }

    .badge { padding: 1px 4px; font-size: 7.5px; }
    .b-ov { background:#fee2e2; color:#991b1b; }
    .b-up { background:#fef3c7; color:#92400e; }
    .b-co { background:#dcfce7; color:#15803d; }
    .b-sc { background:#dbeafe; color:#1e40af; }

    .footer { margin-top: 10px; padding-top: 8px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 8px; text-align: center; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>Preventive Maintenance Report</h1>
    <p>UDART Fleet Management System &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }} &nbsp;|&nbsp; {{ $totalSchedules }} total schedules</p>
</div>

{{-- KPI Cards --}}
<table class="kpi-table">
    <tr>
        <td style="background:#f0fdf4;border-left:4px solid #16a34a;">
            <div class="kpi-num" style="color:#16a34a;">{{ $totalSchedules }}</div>
            <div class="kpi-lbl">Total Schedules</div>
        </td>
        <td style="background:#fef2f2;border-left:4px solid #dc2626;">
            <div class="kpi-num" style="color:#dc2626;">{{ $overdueCount }}</div>
            <div class="kpi-lbl">Overdue</div>
        </td>
        <td style="background:#fefce8;border-left:4px solid #ca8a04;">
            <div class="kpi-num" style="color:#ca8a04;">{{ $upcomingCount }}</div>
            <div class="kpi-lbl">Upcoming</div>
        </td>
        <td style="background:#dcfce7;border-left:4px solid #16a34a;">
            <div class="kpi-num" style="color:#15803d;">{{ $completedCount }}</div>
            <div class="kpi-lbl">Completed</div>
        </td>
        <td style="background:#eff6ff;border-left:4px solid #1c3faa;">
            <div class="kpi-num" style="color:#1c3faa;">{{ $scheduledCount }}</div>
            <div class="kpi-lbl">Scheduled</div>
        </td>
    </tr>
</table>

{{-- Charts --}}
@php
    $statusData = [
        ['label'=>'Overdue',   'count'=>$overdueCount,   'color'=>'#dc2626'],
        ['label'=>'Upcoming',  'count'=>$upcomingCount,  'color'=>'#ca8a04'],
        ['label'=>'Completed', 'count'=>$completedCount, 'color'=>'#16a34a'],
        ['label'=>'Scheduled', 'count'=>$scheduledCount, 'color'=>'#1c3faa'],
    ];
    $maxSvc = $byServiceType->max('total') ?: 1;
@endphp

<table class="charts-outer">
    <tr>
        {{-- Status Distribution --}}
        <td style="width:40%;padding-right:8px;">
            <div class="chart-box">
                <div class="chart-title">Status Distribution</div>
                @foreach($statusData as $s)
                @php
                    $pct  = $totalSchedules > 0 ? round($s['count'] / $totalSchedules * 100) : 0;
                    $rest = 100 - $pct;
                @endphp
                <div class="bar-label">{{ $s['label'] }} — <strong>{{ $s['count'] }}</strong> ({{ $pct }}%)</div>
                <table class="bar-table">
                    <tr>
                        @if($pct > 0)
                        <td class="bar-filled" style="width:{{ $pct }}%;background:{{ $s['color'] }};"></td>
                        @endif
                        @if($rest > 0)
                        <td class="bar-empty" style="width:{{ $rest }}%;"></td>
                        @endif
                    </tr>
                </table>
                @endforeach
            </div>
        </td>

        {{-- Schedules by Service Type --}}
        <td style="width:60%;padding-left:0;">
            <div class="chart-box">
                <div class="chart-title">Schedules by Service Type</div>
                @foreach($byServiceType->take(8) as $st)
                @php
                    $sp   = round($st['total'] / $maxSvc * 100);
                    $srem = 100 - $sp;
                @endphp
                <div class="bar-label">{{ $st['label'] }} — <strong>{{ $st['total'] }}</strong></div>
                <table class="bar-table">
                    <tr>
                        @if($sp > 0)
                        <td class="bar-filled" style="width:{{ $sp }}%;background:#16a34a;"></td>
                        @endif
                        @if($srem > 0)
                        <td class="bar-empty" style="width:{{ $srem }}%;"></td>
                        @endif
                    </tr>
                </table>
                @endforeach
            </div>
        </td>
    </tr>
</table>

{{-- Overdue Schedules --}}
@if($overdueCount > 0)
<div class="sec-head sec-overdue">⚠ Overdue Schedules ({{ $overdueCount }})</div>
<table class="dt">
    <thead>
        <tr>
            <th class="th-red">Bus</th>
            <th class="th-red">Service Type</th>
            <th class="th-red">Next Service Date</th>
            <th class="th-red">Days Overdue</th>
            <th class="th-red">Last Service</th>
            <th class="th-red">Last KM</th>
            <th class="th-red">Notes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($overdue as $pm)
        <tr>
            <td style="font-weight:700;white-space:nowrap;">{{ $pm->bus->registration_number ?? '—' }}</td>
            <td>{{ $pm->service_type }}</td>
            <td style="white-space:nowrap;color:#dc2626;">{{ $pm->next_service_date?->format('d M Y') ?? '—' }}</td>
            <td style="font-weight:700;color:#dc2626;">{{ $pm->next_service_date ? abs((int) round(now()->diffInDays($pm->next_service_date))) . ' days' : '—' }}</td>
            <td style="white-space:nowrap;">{{ $pm->last_service_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $pm->last_service_km ? number_format($pm->last_service_km) : '—' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($pm->notes ?? '', 55) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Upcoming Schedules --}}
@if($upcomingCount > 0)
<div class="sec-head sec-upcoming">🕐 Upcoming Schedules ({{ $upcoming->count() }} shown)</div>
<table class="dt">
    <thead>
        <tr>
            <th class="th-amber">Bus</th>
            <th class="th-amber">Service Type</th>
            <th class="th-amber">Due Date</th>
            <th class="th-amber">Last Service</th>
            <th class="th-amber">Interval (KM)</th>
            <th class="th-amber">Notes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($upcoming as $pm)
        <tr>
            <td style="font-weight:700;white-space:nowrap;">{{ $pm->bus->registration_number ?? '—' }}</td>
            <td>{{ $pm->service_type }}</td>
            <td style="white-space:nowrap;color:#ca8a04;">{{ $pm->next_service_date?->format('d M Y') ?? '—' }}</td>
            <td style="white-space:nowrap;">{{ $pm->last_service_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $pm->interval_km ? number_format($pm->interval_km) : '—' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($pm->notes ?? '', 60) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- All Schedules --}}
<div class="sec-head sec-all">All PM Schedules — {{ $totalSchedules }} records</div>
<table class="dt">
    <thead>
        <tr>
            <th class="th-green">Bus</th>
            <th class="th-green">Service Type</th>
            <th class="th-green">Status</th>
            <th class="th-green">Last Service</th>
            <th class="th-green">Next Service</th>
            <th class="th-green">Last KM</th>
            <th class="th-green">Interval KM</th>
            <th class="th-green">Notes</th>
        </tr>
    </thead>
    <tbody>
        @forelse($schedules as $pm)
        <tr>
            <td style="font-weight:700;white-space:nowrap;">{{ $pm->bus->registration_number ?? '—' }}</td>
            <td>{{ $pm->service_type }}</td>
            <td>
                @php $st = $pm->status ?? 'scheduled'; @endphp
                <span class="badge b-{{ substr($st,0,2) }}">{{ ucfirst($st) }}</span>
            </td>
            <td style="white-space:nowrap;">{{ $pm->last_service_date?->format('d M Y') ?? '—' }}</td>
            <td style="white-space:nowrap;">{{ $pm->next_service_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $pm->last_service_km ? number_format($pm->last_service_km) : '—' }}</td>
            <td>{{ $pm->interval_km ? number_format($pm->interval_km) : '—' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($pm->notes ?? '', 45) }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:12px;">No PM schedules found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">UDART IMMS &nbsp;|&nbsp; Preventive Maintenance Report &nbsp;|&nbsp; {{ now()->format('d M Y') }}</div>
</body>
</html>
