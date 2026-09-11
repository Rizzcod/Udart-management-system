<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #7c3aed; color: #fff; padding: 16px 20px; border-radius: 6px; margin-bottom: 16px; }
    .header h1 { margin: 0 0 4px 0; font-size: 18px; font-weight: 700; }
    .header p  { margin: 0; font-size: 10px; opacity: .85; }
    .kpi-row { display: flex; gap: 12px; margin-bottom: 16px; }
    .kpi { flex: 1; background: #f5f3ff; border-left: 4px solid #7c3aed; padding: 8px 12px; border-radius: 4px; }
    .kpi span { font-size: 20px; font-weight: 700; color: #7c3aed; }
    .kpi small { display: block; color: #6b7280; font-size: 9px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #7c3aed; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #f5f3ff; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: top; }
    .verified   { background:#dcfce7;color:#15803d;padding:1px 5px;border-radius:3px; }
    .unverified { background:#fee2e2;color:#991b1b;padding:1px 5px;border-radius:3px; }
    .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 9px; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <h1>Users Report</h1>
        <p>UDART Fleet Management System &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="kpi-row">
        <div class="kpi"><span>{{ $totalUsers }}</span><small>Total Users</small></div>
        <div class="kpi"><span>{{ $verifiedUsers }}</span><small>Verified</small></div>
        <div class="kpi"><span>{{ $pendingUsers }}</span><small>Pending Verification</small></div>
        <div class="kpi"><span>{{ $lastLoginStats['today'] }}</span><small>Active Today</small></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Position</th>
                <th>Verified</th>
                <th>Last Login</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentUsers as $user)
            <tr>
                <td style="white-space:nowrap;">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td style="white-space:nowrap;">{{ $user->getRoleNames()->first() ?? '—' }}</td>
                <td>{{ $user->department ?? '—' }}</td>
                <td>{{ $user->position ?? '—' }}</td>
                <td><span class="{{ $user->email_verified_at ? 'verified' : 'unverified' }}">{{ $user->email_verified_at ? 'Yes' : 'No' }}</span></td>
                <td style="white-space:nowrap;">{{ $user->last_login_at?->format('d M Y') ?? 'Never' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:16px;">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">UDART IMMS &nbsp;|&nbsp; Users Report &nbsp;|&nbsp; {{ now()->format('Y') }}</div>
</body>
</html>
