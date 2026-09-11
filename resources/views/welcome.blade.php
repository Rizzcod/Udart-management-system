@php
    $demoEnabled  = config('demo.enabled');
    $demoAccounts = config('demo.accounts');
    $demoPassword = config('demo.password');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UDART Maintenance Management System — Raphael Joseph</title>
    <meta name="description" content="UDART MMS: a Laravel fleet maintenance platform covering dispatch, breakdown reporting, work orders, preventive maintenance, spare parts, IoT monitoring and reporting. Portfolio project by Raphael Joseph.">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/udart-logo.jpg') }}">
    <link href="{{ asset('libs/css/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        :root {
            --ink: #0b1433;
            --navy: #08195a;
            --blue: #1c3faa;
            --blue-soft: #eef2ff;
            --text: #1f2937;
            --muted: #5b6475;
            --line: #e3e7f0;
            --bg: #f6f7fb;
            --card: #ffffff;
            --radius: 14px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; scroll-padding-top: 72px; }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text);
            background: var(--bg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        a { color: var(--blue); }
        img { max-width: 100%; }
        .wrap { width: 100%; max-width: 1120px; margin: 0 auto; padding-inline: 24px; }
        :focus-visible { outline: 3px solid #7c9cff; outline-offset: 2px; border-radius: 6px; }

        /* ── Top bar ── */
        .topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(8, 25, 90, .97);
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .topbar .wrap { display: flex; align-items: center; gap: 1.5rem; height: 60px; }
        .brand { display: flex; align-items: center; gap: .6rem; color: #fff; text-decoration: none; font-weight: 700; letter-spacing: -.01em; }
        .brand img { height: 32px; width: auto; border-radius: 7px; }
        .topnav { display: flex; gap: 1.25rem; margin-left: auto; }
        .topnav a { color: rgba(255,255,255,.75); text-decoration: none; font-size: .88rem; }
        .topnav a:hover { color: #fff; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            font: inherit; font-weight: 600; font-size: .92rem;
            padding: .7rem 1.25rem; border-radius: 10px;
            text-decoration: none; cursor: pointer; border: 1.5px solid transparent;
            transition: background .15s, border-color .15s, color .15s;
        }
        .btn-primary { background: #fff; color: var(--navy); }
        .btn-primary:hover { background: #e8edff; }
        .btn-ghost { color: #fff; border-color: rgba(255,255,255,.4); }
        .btn-ghost:hover { border-color: #fff; background: rgba(255,255,255,.08); }
        .btn-solid { background: var(--blue); color: #fff; }
        .btn-solid:hover { background: var(--navy); }
        .btn-sm { padding: .45rem .9rem; font-size: .84rem; }

        /* ── Hero ── */
        .hero {
            position: relative; color: #fff;
            background: var(--navy) url('{{ asset('images/brt-bus.jpg') }}') center / cover no-repeat;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(100deg, rgba(5,14,54,.95) 0%, rgba(8,25,90,.88) 55%, rgba(8,25,90,.7) 100%);
        }
        .hero .wrap { position: relative; padding-block: 88px 80px; }
        .eyebrow {
            display: inline-flex; align-items: center; gap: .45rem;
            font-size: .78rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase;
            color: #c7d3ff; margin-bottom: 1.1rem;
        }
        .hero h1 { font-size: clamp(2rem, 4.6vw, 3.25rem); line-height: 1.12; font-weight: 800; letter-spacing: -.025em; max-width: 16ch; }
        .hero .tagline { font-size: clamp(1.05rem, 2vw, 1.3rem); color: #dbe3ff; margin-top: .75rem; font-weight: 500; }
        .hero .summary { max-width: 62ch; margin-top: 1.25rem; color: rgba(255,255,255,.82); }
        .hero .dev { margin-top: 1.5rem; font-size: .92rem; color: rgba(255,255,255,.75); }
        .hero .dev strong { color: #fff; }
        .hero .actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 2rem; }

        /* ── Sections ── */
        section.block { padding-block: 72px; }
        section.block.alt { background: #fff; border-block: 1px solid var(--line); }
        .kicker { font-size: .78rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--blue); }
        .block h2 { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; letter-spacing: -.02em; color: var(--ink); margin-top: .35rem; }
        .block .intro { color: var(--muted); max-width: 68ch; margin-top: .6rem; }

        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-top: 2rem; }
        .panel { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); padding: 1.5rem; }
        .alt .panel { background: var(--bg); }
        .panel h3 { font-size: 1.02rem; color: var(--ink); display: flex; align-items: center; gap: .5rem; margin-bottom: .5rem; }
        .panel h3 i { color: var(--blue); }
        .panel p { color: var(--muted); font-size: .95rem; }

        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-top: 2rem; }
        .feature { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); padding: 1.35rem; }
        .feature .ico {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: var(--blue-soft); color: var(--blue); font-size: 1.15rem; margin-bottom: .85rem;
        }
        .feature h3 { font-size: 1rem; color: var(--ink); margin-bottom: .35rem; }
        .feature p { font-size: .9rem; color: var(--muted); }

        /* Roles */
        .roles { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 1rem; margin-top: 2rem; }
        .role { border: 1px solid var(--line); border-radius: var(--radius); padding: 1.25rem; background: var(--bg); }
        .role i { font-size: 1.3rem; color: var(--blue); }
        .role h3 { font-size: .98rem; color: var(--ink); margin: .5rem 0 .5rem; }
        .role ul { list-style: none; font-size: .86rem; color: var(--muted); display: grid; gap: .35rem; }
        .role li { padding-left: .9rem; position: relative; }
        .role li::before { content: ''; position: absolute; left: 0; top: .62em; width: 5px; height: 5px; border-radius: 50%; background: #9aaee8; }

        /* Workflow */
        .flow { list-style: none; counter-reset: step; display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: .75rem; margin-top: 2rem; }
        .flow li { counter-increment: step; background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); padding: 1.1rem 1rem; position: relative; }
        .flow li::before {
            content: counter(step);
            display: inline-flex; align-items: center; justify-content: center;
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--navy); color: #fff; font-size: .78rem; font-weight: 700; margin-bottom: .6rem;
        }
        .flow h3 { font-size: .93rem; color: var(--ink); margin-bottom: .3rem; }
        .flow p { font-size: .84rem; color: var(--muted); }
        .flow .who { display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: var(--blue); margin-bottom: .25rem; }
        .statuses { margin-top: 1.5rem; display: flex; flex-wrap: wrap; align-items: center; gap: .4rem; font-size: .8rem; }
        .statuses .label { color: var(--muted); font-weight: 600; margin-right: .25rem; }
        .statuses code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; background: var(--blue-soft); color: var(--navy); padding: .15rem .5rem; border-radius: 6px; }
        .statuses i { color: #9aaee8; font-size: .75rem; }
        .pm-note { margin-top: 1.25rem; }

        /* Stack */
        .stack { margin-top: 2rem; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; background: var(--card); }
        .stack-row { display: grid; grid-template-columns: 230px 1fr; border-top: 1px solid var(--line); }
        .stack-row:first-child { border-top: 0; }
        .stack-row dt { padding: .9rem 1.25rem; font-weight: 600; color: var(--ink); font-size: .9rem; background: var(--bg); }
        .stack-row dd { padding: .9rem 1.25rem; color: var(--muted); font-size: .92rem; }

        /* Demo */
        .notice {
            display: flex; gap: .75rem; align-items: flex-start;
            background: #fffbeb; border: 1px solid #fde68a; color: #78350f;
            border-radius: 12px; padding: .9rem 1.1rem; font-size: .9rem; margin-top: 1.5rem;
        }
        .notice i { margin-top: .15rem; }
        .accounts { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .account { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); padding: 1.25rem; display: flex; flex-direction: column; gap: .8rem; }
        .account-head { display: flex; align-items: center; gap: .65rem; }
        .account-head i { font-size: 1.2rem; color: var(--blue); }
        .account-head h3 { font-size: 1rem; color: var(--ink); }
        .account p { font-size: .86rem; color: var(--muted); }
        .cred { display: grid; gap: .4rem; }
        .cred-row { display: flex; align-items: center; gap: .5rem; background: var(--bg); border: 1px solid var(--line); border-radius: 9px; padding: .35rem .4rem .35rem .75rem; }
        .cred-row span.k { font-size: .74rem; font-weight: 600; color: var(--muted); width: 64px; flex-shrink: 0; }
        .cred-row code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: .8rem; color: var(--ink); flex: 1; min-width: 0; overflow-wrap: anywhere; }
        .copy {
            border: 1px solid var(--line); background: #fff; color: var(--muted);
            border-radius: 7px; width: 30px; height: 30px; cursor: pointer; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .copy:hover { color: var(--blue); border-color: #b9c6f0; }
        .copy.done { color: #15803d; border-color: #86efac; }
        .account .btn { margin-top: auto; }
        .launch-panel { margin-top: 1.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }

        /* Developer */
        .developer { display: flex; align-items: center; gap: 1.25rem; margin-top: 1.75rem; }
        .avatar {
            width: 64px; height: 64px; border-radius: 50%; flex-shrink: 0;
            background: var(--navy); color: #fff; font-weight: 700; font-size: 1.3rem;
            display: flex; align-items: center; justify-content: center;
        }
        .developer h3 { font-size: 1.15rem; color: var(--ink); }
        .developer p { color: var(--muted); font-size: .95rem; }

        footer { background: var(--ink); color: rgba(255,255,255,.6); font-size: .84rem; }
        footer .wrap { padding-block: 28px; display: flex; flex-wrap: wrap; gap: .75rem 2rem; justify-content: space-between; }

        .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .roles { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .flow { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 820px) {
            .topnav { display: none; }
            .topbar .btn { margin-left: auto; }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .two-col { grid-template-columns: 1fr; }
            .roles { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .flow { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stack-row { grid-template-columns: 1fr; }
            .stack-row dt { padding-bottom: .4rem; }
            .stack-row dd { padding-top: .4rem; }
        }
        @media (max-width: 560px) {
            .wrap { padding-inline: 16px; }
            .hero .wrap { padding-block: 56px 52px; }
            section.block { padding-block: 52px; }
            .grid, .roles, .flow { grid-template-columns: 1fr; }
            .hero .actions .btn { flex: 1 1 100%; }
            .brand span { display: none; }
            .accounts { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="wrap">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('images/udart-logo.jpg') }}" alt="UDART logo">
            <span>UDART MMS</span>
        </a>
        <nav class="topnav" aria-label="Page sections">
            <a href="#overview">Overview</a>
            <a href="#capabilities">Capabilities</a>
            <a href="#roles">Roles</a>
            <a href="#workflow">Workflow</a>
            <a href="#stack">Stack</a>
            <a href="#demo">Demo</a>
        </nav>
        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Launch demo
        </a>
    </div>
</header>

<main>
    {{-- ── Hero ── --}}
    <section class="hero">
        <div class="wrap">
            <p class="eyebrow"><i class="bi bi-code-slash" aria-hidden="true"></i> Portfolio project · Final-year engineering capstone</p>
            <h1>UDART Maintenance Management System</h1>
            <p class="tagline">Digital Maintenance &amp; Fleet Management Platform</p>
            <p class="summary">
                A web platform for running a bus fleet's maintenance operation. It covers daily dispatch,
                breakdown reporting, a controlled work-order pipeline, preventive-maintenance scheduling,
                spare-parts inventory, IoT sensor monitoring and operational reporting, with a separate
                workspace for each role.
            </p>
            <p class="dev">Developed by <strong>Raphael Joseph</strong> · Computer Engineering | System Developer</p>
            <div class="actions">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Enter system
                </a>
                @if ($demoEnabled)
                <a href="#demo" class="btn btn-ghost">
                    <i class="bi bi-person-badge" aria-hidden="true"></i> View demo accounts
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ── Overview ── --}}
    <section class="block" id="overview">
        <div class="wrap">
            <p class="kicker">Project overview</p>
            <h2>One system for the whole maintenance loop</h2>
            <p class="intro">
                UDART MMS replaces paper records and phone-call coordination with a structured workflow
                shared by drivers, technicians, storekeepers, supervisors and administrators.
            </p>
            <div class="two-col">
                <div class="panel">
                    <h3><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> The problem</h3>
                    <p>
                        When breakdowns are reported by phone and repairs are tracked on paper, nobody has a
                        reliable view of which buses are safe to dispatch, what state each repair is in, when a
                        service is due, or whether the parts needed are in stock.
                    </p>
                </div>
                <div class="panel">
                    <h3><i class="bi bi-diagram-3" aria-hidden="true"></i> The approach</h3>
                    <p>
                        Every breakdown becomes a work order that moves through an enforced sequence of statuses,
                        and the bus's own status follows it. Preventive-maintenance schedules are generated per bus
                        and enforced by daily scheduled jobs, so buses with overdue maintenance are locked out of dispatch.
                        Each role only sees the tools its permissions allow.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Capabilities ── --}}
    <section class="block alt" id="capabilities">
        <div class="wrap">
            <p class="kicker">Key capabilities</p>
            <h2>What the system does</h2>
            <div class="grid">
                <div class="feature">
                    <div class="ico"><i class="bi bi-signpost-split" aria-hidden="true"></i></div>
                    <h3>Fleet board &amp; daily dispatch</h3>
                    <p>A live board of buses on the road, at the depot and in maintenance. Buses are assigned to drivers and routes each day, either manually or automatically.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-clipboard2-pulse" aria-hidden="true"></i></div>
                    <h3>Work-order pipeline</h3>
                    <p>Repairs move from report through assessment, approval, assignment, repair and testing to return-to-service. Only valid transitions are allowed.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-calendar-check" aria-hidden="true"></i></div>
                    <h3>Preventive maintenance</h3>
                    <p>Service schedules are generated per bus and service type. A priority score based on age, breakdowns, maintenance history and mileage shortens intervals for high-risk buses.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-box-seam" aria-hidden="true"></i></div>
                    <h3>Spare-parts inventory</h3>
                    <p>A parts catalogue with minimum stock levels and low-stock flags. Parts used in a repair are recorded on the work order and deducted from stock.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-broadcast" aria-hidden="true"></i></div>
                    <h3>IoT sensor monitoring</h3>
                    <p>A token-protected endpoint receives readings from ESP32 devices. Each reading is classified as normal, warning or critical, and every bus has a history chart.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></div>
                    <h3>Failure prediction &amp; patterns</h3>
                    <p>A separate Python/Flask service scores each bus's failure risk, and pattern analysis surfaces recurring faults and high-risk vehicles.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-bus-front" aria-hidden="true"></i></div>
                    <h3>Driver portal</h3>
                    <p>Drivers see their bus and route for the day, start and end trips, log mileage and report breakdowns straight into the maintenance queue.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-file-earmark-arrow-down" aria-hidden="true"></i></div>
                    <h3>Reports &amp; exports</h3>
                    <p>Maintenance, work-order, fleet, inventory, preventive-maintenance and user reports include charts and export to PDF, Excel and CSV.</p>
                </div>
                <div class="feature">
                    <div class="ico"><i class="bi bi-chat-dots" aria-hidden="true"></i></div>
                    <h3>Notifications &amp; messaging</h3>
                    <p>In-app alerts fire for assignments, breakdowns and maintenance deadlines. Threaded internal messaging and staff announcements are built in.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Roles ── --}}
    <section class="block" id="roles">
        <div class="wrap">
            <p class="kicker">System roles</p>
            <h2>Five roles, each with its own workspace</h2>
            <p class="intro">Access is role-based, with granular permissions. Administrators can grant or revoke individual permissions per user.</p>
            <div class="roles">
                <div class="role">
                    <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    <h3>Administrator</h3>
                    <ul>
                        <li>Creates user accounts and assigns roles</li>
                        <li>Manages role and per-user permissions</li>
                        <li>Full access to fleet, maintenance and reports</li>
                    </ul>
                </div>
                <div class="role">
                    <i class="bi bi-clipboard-check" aria-hidden="true"></i>
                    <h3>Supervisor</h3>
                    <ul>
                        <li>Assigns buses to drivers each day</li>
                        <li>Assesses, approves and assigns work orders</li>
                        <li>Manages preventive-maintenance schedules</li>
                    </ul>
                </div>
                <div class="role">
                    <i class="bi bi-wrench-adjustable" aria-hidden="true"></i>
                    <h3>Technician</h3>
                    <ul>
                        <li>Works through assigned work orders</li>
                        <li>Logs maintenance records</li>
                        <li>Checks sensor data and predictions</li>
                    </ul>
                </div>
                <div class="role">
                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                    <h3>Storekeeper</h3>
                    <ul>
                        <li>Maintains the spare-parts catalogue</li>
                        <li>Updates stock and watches low-stock items</li>
                        <li>Runs inventory reports</li>
                    </ul>
                </div>
                <div class="role">
                    <i class="bi bi-bus-front" aria-hidden="true"></i>
                    <h3>Driver</h3>
                    <ul>
                        <li>Sees today's assigned bus and route</li>
                        <li>Starts, ends and logs trips</li>
                        <li>Reports breakdowns and follows repairs</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Workflow ── --}}
    <section class="block alt" id="workflow">
        <div class="wrap">
            <p class="kicker">Workflow</p>
            <h2>From breakdown back to service</h2>
            <ol class="flow">
                <li>
                    <span class="who">Supervisor</span>
                    <h3>Dispatch</h3>
                    <p>Buses that are cleared for service are assigned to drivers and routes for the day.</p>
                </li>
                <li>
                    <span class="who">Driver</span>
                    <h3>Report breakdown</h3>
                    <p>A work order is created, the bus is flagged and maintenance staff are notified.</p>
                </li>
                <li>
                    <span class="who">Supervisor</span>
                    <h3>Assess &amp; approve</h3>
                    <p>The fault is inspected, approved and assigned to a technician.</p>
                </li>
                <li>
                    <span class="who">Technician</span>
                    <h3>Repair</h3>
                    <p>Work goes through in progress, awaiting parts and testing. Parts used are deducted from stock.</p>
                </li>
                <li>
                    <span class="who">Technician</span>
                    <h3>Complete</h3>
                    <p>Repair and testing are finished. The completion date is recorded and the reporting driver is notified.</p>
                </li>
                <li>
                    <span class="who">Supervisor</span>
                    <h3>Return to service</h3>
                    <p>Once confirmed, the bus is active again and available for dispatch.</p>
                </li>
            </ol>
            <div class="statuses" aria-label="Work-order statuses">
                <span class="label">Statuses:</span>
                <code>reported</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>assessment</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>pending_approval</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>assigned</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>awaiting_parts</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>in_progress</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>testing</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>completed</code><i class="bi bi-chevron-right" aria-hidden="true"></i>
                <code>returned_to_service</code>
            </div>
            <div class="panel pm-note">
                <h3><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Preventive-maintenance loop</h3>
                <p>
                    Scheduled jobs run every morning. They generate missing schedules, send warnings 7 and 3 days
                    before a service is due, mark past-due services overdue and lock those buses out of dispatch.
                    Completing the service sets the next due date from the bus's priority score and releases the lock.
                </p>
            </div>
        </div>
    </section>

    {{-- ── Technology stack ── --}}
    <section class="block" id="stack">
        <div class="wrap">
            <p class="kicker">Technology stack</p>
            <h2>How it's built</h2>
            <dl class="stack">
                <div class="stack-row"><dt>Backend</dt><dd>Laravel 11 (PHP 8.2+), MVC with service classes</dd></div>
                <div class="stack-row"><dt>Database</dt><dd>MySQL 8, with migrations and seeders</dd></div>
                <div class="stack-row"><dt>Frontend</dt><dd>Blade templates, Bootstrap 5, Bootstrap Icons, Chart.js</dd></div>
                <div class="stack-row"><dt>Authentication &amp; access</dt><dd>Laravel Breeze session auth, Spatie Laravel-Permission, email OTP account activation</dd></div>
                <div class="stack-row"><dt>Documents</dt><dd>DomPDF (PDF), Laravel Excel (XLSX / CSV)</dd></div>
                <div class="stack-row"><dt>Automation</dt><dd>Laravel task scheduler for dispatch, maintenance checks and predictions</dd></div>
                <div class="stack-row"><dt>Machine learning</dt><dd>Python, Flask, scikit-learn, pandas: a separate prediction service</dd></div>
                <div class="stack-row"><dt>IoT</dt><dd>ESP32 firmware posting sensor readings to a REST endpoint</dd></div>
            </dl>
        </div>
    </section>

    {{-- ── Demo access ── --}}
    <section class="block alt" id="demo">
        <div class="wrap">
            <p class="kicker">Live demo</p>
            <h2>Explore the system</h2>
            @if ($demoEnabled)
                <p class="intro">Sign in with any role below to see that role's dashboard, navigation and permissions. Each account only sees what its role allows.</p>
                <div class="notice" role="note">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    <span>
                        This is a demonstration environment. All buses, staff and records are sample data created by the
                        project's database seeders, and these accounts are demo-only credentials, not production accounts.
                        Features that depend on external services (the Flask prediction service, ESP32 devices, email)
                        show seeded sample data.
                    </span>
                </div>
                <div class="accounts">
                    @foreach ($demoAccounts as $account)
                    <article class="account">
                        <div class="account-head">
                            <i class="bi {{ $account['icon'] }}" aria-hidden="true"></i>
                            <h3>{{ $account['role'] }}</h3>
                        </div>
                        <p>{{ $account['summary'] }}</p>
                        <div class="cred">
                            <div class="cred-row">
                                <span class="k">Email</span>
                                <code>{{ $account['email'] }}</code>
                                <button type="button" class="copy" data-copy="{{ $account['email'] }}" aria-label="Copy {{ $account['role'] }} email">
                                    <i class="bi bi-copy" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="cred-row">
                                <span class="k">Password</span>
                                <code>{{ $demoPassword }}</code>
                                <button type="button" class="copy" data-copy="{{ $demoPassword }}" aria-label="Copy {{ $account['role'] }} password">
                                    <i class="bi bi-copy" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <a href="{{ route('login', ['as' => $account['key']]) }}" class="btn btn-solid btn-sm">
                            Sign in as {{ $account['role'] }} <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </article>
                    @endforeach
                </div>
            @else
                <div class="panel launch-panel">
                    <p>Sign in with an account issued by a system administrator.</p>
                    <a href="{{ route('login') }}" class="btn btn-solid">
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Go to sign in
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ── Developer ── --}}
    <section class="block" id="developer">
        <div class="wrap">
            <p class="kicker">Developer</p>
            <div class="developer">
                <div class="avatar" aria-hidden="true">RJ</div>
                <div>
                    <h3>Raphael Joseph</h3>
                    <p>Computer Engineering | System Developer</p>
                    <p>Designed and built UDART MMS as a final-year engineering capstone project.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="wrap">
        <span>&copy; {{ date('Y') }} Raphael Joseph · UDART Maintenance Management System</span>
        <span>Portfolio demonstration. Sample data only.</span>
    </div>
</footer>

<p class="sr-only" aria-live="polite" id="copy-status"></p>

@if ($demoEnabled)
<script>
    // Copy-to-clipboard for demo credentials, with a fallback for non-secure (plain HTTP) origins.
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy');
            var done = function () {
                var icon = btn.querySelector('i');
                btn.classList.add('done');
                icon.className = 'bi bi-check2';
                document.getElementById('copy-status').textContent = 'Copied to clipboard';
                setTimeout(function () { btn.classList.remove('done'); icon.className = 'bi bi-copy'; }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done);
            } else {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'absolute';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                done();
            }
        });
    });
</script>
@endif
</body>
</html>
