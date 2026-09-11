@props(['title' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}UDART MMS</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/udart-logo.jpg') }}">

    <link href="{{ asset('libs/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/css/flag-icons.min.css') }}" rel="stylesheet">
    <script src="{{ asset('libs/js/chart.umd.min.js') }}"></script>

    <style>
        * { box-sizing: border-box; }
        body { background: #eef1f8; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* ── Sidebar ── */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #1c3faa;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width .2s ease;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 4px; }

        /* ── Collapsed sidebar ── */
        .sidebar.collapsed { width: 74px; }
        .sidebar.collapsed .brand { padding: 1.25rem .95rem; gap: 0; }
        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .brand-sub,
        .sidebar.collapsed .section-label,
        .sidebar.collapsed .user-section .flex-grow-1 { display: none; }
        .sidebar.collapsed .nav-link { justify-content: center; margin: .1rem .5rem; padding: .6rem; font-size: 0; }
        .sidebar.collapsed .nav-link i { margin: 0; font-size: 1rem; }
        .sidebar.collapsed .nav-link .badge { display: none; }
        .sidebar.collapsed .user-section .d-flex { justify-content: center; }
        body.sidebar-collapsed .main-wrapper { margin-left: 74px; width: calc(100vw - 74px); }

        .sidebar-toggle-btn {
            border: none;
            background: rgba(28,63,170,.08);
            color: #1c3faa;
            width: 34px; height: 34px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background .15s;
        }
        .sidebar-toggle-btn:hover { background: rgba(28,63,170,.16); }

        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1.25rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,.12);
            text-decoration: none;
            white-space: nowrap;
        }
        .sidebar .brand-icon {
            width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            background: #fff;
            border-radius: 10px;
            padding: 4px;
        }
        .sidebar .brand-icon img {
            width: 36px; height: 36px; object-fit: contain;
        }
        .sidebar .brand-text { color: #fff; font-weight: 700; font-size: 1rem; line-height: 1.2; }
        .sidebar .brand-sub  { color: rgba(255,255,255,.55); font-size: .7rem; font-weight: 400; }

        .sidebar nav { flex-grow: 1; padding: .5rem 0; }

        .sidebar .section-label {
            color: rgba(255,255,255,.38);
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 1rem 1.4rem .35rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.72);
            padding: .55rem 1.2rem;
            border-radius: .45rem;
            margin: .1rem .6rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .88rem;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .sidebar .nav-link i { font-size: 1rem; width: 1.3rem; text-align: center; flex-shrink: 0; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.14); color: #fff; }
        .sidebar .nav-link.active { background: rgba(255,255,255,.2); color: #fff; font-weight: 600; }

        .sidebar .user-section {
            border-top: 1px solid rgba(255,255,255,.12);
            padding: 1rem 1.2rem;
        }
        .sidebar .user-avatar {
            width: 38px; height: 38px;
            background: rgba(255,255,255,.9);
            color: #1c3faa;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .82rem; flex-shrink: 0;
        }

        /* ── Main ── */
        .main-wrapper {
            margin-left: 250px;
            width: calc(100vw - 250px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .2s ease, width .2s ease;
        }

        /* ── Topbar ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #dde3f0;
            padding: .7rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
        }
        .topbar .breadcrumb { margin: 0; font-size: .82rem; }
        .topbar .breadcrumb-item a { color: #1c3faa; text-decoration: none; }
        .topbar .breadcrumb-item.active { color: #6b7280; }
        .topbar .breadcrumb-item+.breadcrumb-item::before { color: #9ca3af; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(28,63,170,.07);
            background: #fff;
        }
        .card-header { border-radius: 14px 14px 0 0 !important; border-bottom: 1px solid #f0f3fb; }

        /* ── Stat cards ── */
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        /* ── Welcome banner ── */
        .welcome-card {
            background: linear-gradient(135deg, #1c3faa 0%, #2563eb 100%);
            border-radius: 14px;
            color: #fff;
            padding: 1.5rem 2rem;
            position: relative;
            overflow: hidden;
        }
        .welcome-card::before {
            content: '';
            position: absolute;
            right: -60px; top: -60px;
            width: 220px; height: 220px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
        }
        .welcome-card::after {
            content: '';
            position: absolute;
            right: 40px; bottom: -80px;
            width: 180px; height: 180px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
        }
        .welcome-avatar {
            width: 64px; height: 64px;
            background: rgba(255,255,255,.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #fff;
            border: 2px solid rgba(255,255,255,.35);
            flex-shrink: 0;
            position: relative; z-index: 1;
        }

        /* ── Quick action buttons ── */
        .quick-btn {
            border: none;
            border-radius: 10px;
            padding: .7rem 1rem;
            font-size: .85rem;
            font-weight: 600;
            display: flex; align-items: center; gap: .5rem;
            width: 100%;
            text-decoration: none;
            transition: opacity .15s, transform .1s;
        }
        .quick-btn:hover { opacity: .88; transform: translateY(-1px); color: #fff; }
        .quick-btn-blue   { background: #1c3faa; color: #fff; }
        .quick-btn-green  { background: #16a34a; color: #fff; }
        .quick-btn-purple { background: #7c3aed; color: #fff; }
        .quick-btn-orange { background: #ea580c; color: #fff; }
        .quick-btn-teal   { background: #0d9488; color: #fff; }

        /* ── Table ── */
        .table th { font-size: .78rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; }
        .table td { font-size: .875rem; vertical-align: middle; }
        .table tbody tr:hover { background: #f8faff; }

        /* ── Badges ── */
        .badge { font-weight: 600; letter-spacing: .02em; }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <div class="brand-icon">
                <img src="{{ asset('images/udart-logo.jpg') }}" alt="UDART">
            </div>
            <div>
                <div class="brand-text">{{ __('UDART MMS') }}</div>
                <div class="brand-sub">{{ __('Maintenance System') }}</div>
            </div>
        </a>

        <nav>
            @role('Driver')
            {{-- ── Driver nav ── --}}
            <div class="section-label">{{ __('Main') }}</div>
            <a href="{{ route('driver.dashboard') }}" class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
            </a>

            <div class="section-label">{{ __('My Bus') }}</div>
            <a href="{{ route('driver.my-assignment') }}" class="nav-link {{ request()->routeIs('driver.my-assignment') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i> {{ __("Today's Assignment") }}
            </a>
            <a href="{{ route('driver.report-breakdown') }}" class="nav-link {{ request()->routeIs('driver.report-breakdown') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> {{ __('Report Breakdown') }}
            </a>
            <a href="{{ route('driver.my-reports') }}" class="nav-link {{ request()->routeIs('driver.my-reports') ? 'active' : '' }}">
                <i class="bi bi-card-list"></i> {{ __('My Reports') }}
            </a>
            <a href="{{ route('driver.my-activity') }}" class="nav-link {{ request()->routeIs('driver.my-activity*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i> {{ __('My Activity Report') }}
            </a>

            <div class="section-label">{{ __('Communication') }}</div>
            @php
                $unreadMsg = \DB::table('messages')
                    ->join('message_thread_participants as mtp', fn($j) => $j->on('mtp.thread_id', '=', 'messages.thread_id')->where('mtp.user_id', auth()->id()))
                    ->where('messages.sender_id', '!=', auth()->id())
                    ->where(fn($q) => $q->whereNull('mtp.last_read_at')->orWhereColumn('messages.created_at', '>', 'mtp.last_read_at'))
                    ->count();
                $unreadAnn = \App\Models\Announcement::unreadCountFor(auth()->user());
            @endphp
            <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> {{ __('Messages') }}
                @if($unreadMsg > 0)<span class="badge ms-auto rounded-pill" style="font-size:.65rem;background:rgba(255,255,255,.9);color:#1c3faa;">{{ $unreadMsg }}</span>@endif
            </a>
            <a href="{{ route('announcements.index') }}" class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone"></i> {{ __('Announcements') }}
                @if($unreadAnn > 0)<span class="badge ms-auto rounded-pill" style="font-size:.65rem;background:#fef9c3;color:#92400e;">{{ $unreadAnn }}</span>@endif
            </a>

            <div class="section-label">{{ __('Account') }}</div>
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> {{ __('My Profile') }}
            </a>

            @else
            {{-- ── Staff nav ── --}}
            <div class="section-label">{{ __('Main') }}</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
            </a>

            {{-- Buses section: visible to anyone who can view buses, work-orders, or bus assignments --}}
            @canany(['view buses', 'view work-orders', 'view maintenance-records', 'view preventive-maintenance', 'view bus-assignments'])
            <div class="section-label">{{ __('Buses') }}</div>
            @can('view bus-assignments')
            <a href="{{ route('fleet-board.index') }}" class="nav-link {{ request()->routeIs('fleet-board.*') ? 'active' : '' }}">
                <i class="bi bi-map"></i> {{ __('Bus Board') }}
            </a>
            <a href="{{ route('bus-assignments.index') }}" class="nav-link {{ request()->routeIs('bus-assignments.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i> {{ __('Bus Assignments') }}
            </a>
            @endcan
            @can('view buses')
            <a href="{{ route('buses.index') }}" class="nav-link {{ request()->routeIs('buses.*') ? 'active' : '' }}">
                <i class="bi bi-bus-front"></i> {{ __('Buses') }}
            </a>
            @endcan
            @can('view work-orders')
            <a href="{{ route('work-orders.index') }}" class="nav-link {{ request()->routeIs('work-orders.index') || (request()->routeIs('work-orders.*') && !request()->routeIs('work-orders.my-assignments')) ? 'active' : '' }}">
                <i class="bi bi-wrench-adjustable"></i> {{ __('Work Orders') }}
            </a>
            @role('Technician')
            <a href="{{ route('work-orders.my-assignments') }}" class="nav-link {{ request()->routeIs('work-orders.my-assignments') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> {{ __('My Assignments') }}
            </a>
            @endrole
            @endcan
            @can('view maintenance-records')
            <a href="{{ route('maintenance-records.index') }}" class="nav-link {{ request()->routeIs('maintenance-records.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check"></i> {{ __('Maintenance Records') }}
            </a>
            @endcan
            @can('view preventive-maintenance')
            <a href="{{ route('preventive-maintenance.index') }}" class="nav-link {{ request()->routeIs('preventive-maintenance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i> {{ __('Preventive Maintenance') }}
            </a>
            @endcan
            @endcanany

            {{-- Inventory: anyone with spare-parts permission --}}
            @can('view spare-parts')
            <div class="section-label">{{ __('Inventory') }}</div>
            <a href="{{ route('spare-parts.index') }}" class="nav-link {{ request()->routeIs('spare-parts.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> {{ __('Spare Parts') }}
            </a>
            @endcan

            {{-- Intelligence: only roles with IoT/Predictions access --}}
            @canany(['view iot', 'view predictions', 'view failure-patterns'])
            <div class="section-label">{{ __('Intelligence') }}</div>
            @can('view iot')
            <a href="{{ route('iot.dashboard') }}" class="nav-link {{ request()->routeIs('iot.*') ? 'active' : '' }}">
                <i class="bi bi-broadcast"></i> {{ __('IoT Monitor') }}
            </a>
            @endcan
            @can('view predictions')
            <a href="{{ route('predictions.index') }}" class="nav-link {{ request()->routeIs('predictions.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> {{ __('AI Predictions') }}
            </a>
            @endcan
            @can('view failure-patterns')
            <a href="{{ route('failure-patterns.index') }}" class="nav-link {{ request()->routeIs('failure-patterns.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> {{ __('Failure Patterns') }}
            </a>
            @endcan
            @endcanany

            {{-- Reports: Admin/Supervisor/Storekeeper --}}
            @can('view reports')
            <div class="section-label">{{ __('Reports') }}</div>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.maintenance') || request()->routeIs('reports.inventory') || request()->routeIs('reports.fleet') || request()->routeIs('reports.work-orders') || request()->routeIs('reports.preventive-maintenance') || request()->routeIs('reports.users') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i> {{ __('Reports') }}
            </a>
            @endcan

            {{-- My Work Orders Report: Technicians only --}}
            @can('view own-reports')
            @cannot('view reports')
            <div class="section-label">{{ __('Reports') }}</div>
            @endcannot
            <a href="{{ route('reports.technician') }}" class="nav-link {{ request()->routeIs('reports.technician*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-data"></i> {{ __('My Work Orders Report') }}
            </a>
            @endcan

            <div class="section-label">{{ __('Communication') }}</div>
            @php
                $unreadMsg = \DB::table('messages')
                    ->join('message_thread_participants as mtp', fn($j) => $j->on('mtp.thread_id', '=', 'messages.thread_id')->where('mtp.user_id', auth()->id()))
                    ->where('messages.sender_id', '!=', auth()->id())
                    ->where(fn($q) => $q->whereNull('mtp.last_read_at')->orWhereColumn('messages.created_at', '>', 'mtp.last_read_at'))
                    ->count();
                $unreadAnn = \App\Models\Announcement::unreadCountFor(auth()->user());
                $unread = \App\Models\Notification::where('is_read', false)
                    ->where(fn($q) => $q->whereNull('user_id')->orWhere('user_id', auth()->id()))
                    ->count();
            @endphp
            <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> {{ __('Messages') }}
                @if($unreadMsg > 0)<span class="badge ms-auto rounded-pill" style="font-size:.65rem;background:rgba(255,255,255,.9);color:#1c3faa;">{{ $unreadMsg }}</span>@endif
            </a>
            <a href="{{ route('announcements.index') }}" class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone"></i> {{ __('Announcements') }}
                @if($unreadAnn > 0)<span class="badge ms-auto rounded-pill" style="font-size:.65rem;background:#fef9c3;color:#92400e;">{{ $unreadAnn }}</span>@endif
            </a>
            <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> {{ __('Notifications') }}
                @if($unread > 0)<span class="badge bg-warning ms-auto rounded-pill" style="font-size:.65rem;">{{ $unread }}</span>@endif
            </a>

            @canany(['view users', 'manage users'])
            <div class="section-label">{{ __('Admin') }}</div>
            @can('view users')
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> {{ __('Manage Users') }}
            </a>
            @endcan
            @can('manage users')
            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> {{ __('Roles & Permissions') }}
            </a>
            @endcan
            @endcanany

            <div class="section-label">{{ __('Account') }}</div>
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> {{ __('My Profile') }}
            </a>
            @endrole
        </nav>

        {{-- User profile footer --}}
        <div class="user-section">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">{{ auth()->user()->initials() }}</div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="text-white fw-semibold text-truncate" style="font-size:.85rem;">{{ auth()->user()->name }}</div>
                    <div style="color:rgba(255,255,255,.5);font-size:.7rem;">{{ auth()->user()->getRoleNames()->first() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0" title="{{ __('Logout') }}" style="color:rgba(255,255,255,.5);">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <script>
        if (localStorage.getItem('sidebarCollapsed') === '1') {
            document.querySelector('.sidebar').classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    {{-- ═══ MAIN WRAPPER ═══ --}}
    <div class="main-wrapper">

        {{-- Topbar --}}
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="sidebarToggle" class="sidebar-toggle-btn" title="{{ __('Toggle sidebar') }}">
                    <i class="bi bi-layout-sidebar"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i></a></li>
                        @if($title)
                        <li class="breadcrumb-item active">{{ $title }}</li>
                        @else
                        <li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
                        @endif
                    </ol>
                </nav>
            </div>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-md-inline">
                    <i class="bi bi-clock me-1"></i>{{ now()->format('D, d M Y') }}
                </span>

                {{-- Language switcher --}}
                @php $currentLocale = app()->getLocale(); @endphp
                <div class="dropdown">
                    <button class="btn btn-sm btn-light d-flex align-items-center gap-2 px-2" style="border-radius:8px;font-size:.8rem;font-weight:600;min-width:80px;"
                            data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Language') }}">
                        @if($currentLocale === 'sw')
                            <span class="fi fi-tz" style="width:20px;height:15px;border-radius:3px;flex-shrink:0;"></span>
                            <span>SW</span>
                        @else
                            <span class="fi fi-gb" style="width:20px;height:15px;border-radius:3px;flex-shrink:0;"></span>
                            <span>EN</span>
                        @endif
                        <i class="bi bi-chevron-down ms-auto" style="font-size:.6rem;opacity:.55;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm p-1" style="border-radius:10px;min-width:170px;border:1px solid #f0f3fb;font-size:.875rem;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-2 {{ $currentLocale==='en'?'fw-semibold':''}}"
                               href="{{ route('lang.switch','en') }}"
                               style="{{ $currentLocale==='en'?'background:#eef2ff;color:#1c3faa;':'' }}">
                                <span class="fi fi-gb" style="width:22px;height:16px;border-radius:3px;flex-shrink:0;"></span>
                                English
                                @if($currentLocale==='en')<i class="bi bi-check2 ms-auto" style="color:#1c3faa;"></i>@endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-2 {{ $currentLocale==='sw'?'fw-semibold':''}}"
                               href="{{ route('lang.switch','sw') }}"
                               style="{{ $currentLocale==='sw'?'background:#eef2ff;color:#1c3faa;':'' }}">
                                <span class="fi fi-tz" style="width:22px;height:16px;border-radius:3px;flex-shrink:0;"></span>
                                Kiswahili
                                @if($currentLocale==='sw')<i class="bi bi-check2 ms-auto" style="color:#1c3faa;"></i>@endif
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light position-relative" style="border-radius:8px;">
                    <i class="bi bi-bell"></i>
                    @if(isset($unread) && $unread > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.58rem;">{{ $unread }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-light" style="border-radius:8px;" title="{{ __('My Profile') }}">
                    <i class="bi bi-person-circle"></i>
                </a>
            </div>
        </div>

        {{-- Flash messages --}}
        <div class="px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 d-flex align-items-center gap-2" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(22,163,74,.15);">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 d-flex align-items-center gap-2" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(220,38,38,.15);">
                    <i class="bi bi-exclamation-circle-fill text-danger"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <div class="flex-grow-1 p-4">
            {{ $slot }}
        </div>
    </div>
</div>

<script src="{{ asset('libs/js/bootstrap.bundle.min.js') }}"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        var sidebar = document.querySelector('.sidebar');
        var collapsed = sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    });
</script>
@stack('scripts')
</body>
</html>
