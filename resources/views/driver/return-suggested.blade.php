<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Trip Suggested</title>
    <link rel="stylesheet" href="/libs/css/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0a0f2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ── Sky ── */
        .sky {
            position: fixed; inset: 0; z-index: 0;
            background: linear-gradient(160deg, #0a1628 0%, #0e2444 40%, #133060 70%, #0a1628 100%);
            animation: skyPulse 8s ease-in-out infinite alternate;
        }
        @keyframes skyPulse {
            0%   { background: linear-gradient(160deg, #0a1628 0%, #0e2444 40%, #133060 70%, #0a1628 100%); }
            100% { background: linear-gradient(160deg, #0d1e38 0%, #122d55 40%, #163870 70%, #0d1e38 100%); }
        }

        /* ── Stars ── */
        .stars { position: fixed; inset: 0; z-index: 1; pointer-events: none; }
        .star {
            position: absolute; background: #fff; border-radius: 50%;
            animation: twinkle var(--d) ease-in-out infinite alternate;
            animation-delay: var(--delay);
        }
        @keyframes twinkle {
            0%   { opacity: .1; transform: scale(.7); }
            100% { opacity: .85; transform: scale(1.3); }
        }

        /* ── Road (going right → return direction) ── */
        .road {
            width: 100%; height: 34px; background: #1f2937;
            border-top: 3px solid #374151; border-bottom: 3px solid #374151;
            position: relative; overflow: hidden; border-radius: 4px; margin: 1.25rem 0;
        }
        .road-line {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 60px; height: 4px; background: #38bdf8; border-radius: 2px;
            animation: roadReturn 1.5s linear infinite;
        }
        @keyframes roadReturn {
            0%   { left: 110%; }
            100% { left: -70px; }
        }

        /* ── Main card ── */
        .card {
            position: relative; z-index: 10;
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(56,189,248,.25);
            border-radius: 28px; padding: 2.5rem 2.75rem;
            max-width: 580px; width: 92%; text-align: center;
            box-shadow: 0 32px 80px rgba(0,0,0,.5), 0 0 60px rgba(56,189,248,.06);
            animation: cardPop .7s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes cardPop {
            0%   { transform: scale(.5) translateY(40px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        /* ── Icon ── */
        .icon-wrap {
            display: flex; justify-content: center; margin-bottom: .75rem;
            animation: iconBounce 2s ease-in-out infinite alternate;
            filter: drop-shadow(0 8px 20px rgba(56,189,248,.5));
        }
        @keyframes iconBounce {
            0%   { transform: translateY(0); }
            100% { transform: translateY(-10px); }
        }
        .icon-main { font-size: 4.5rem; color: #38bdf8; }

        /* ── Text ── */
        .label {
            font-size: .72rem; font-weight: 700; letter-spacing: .18em;
            text-transform: uppercase; color: #38bdf8;
            animation: fadeSlideDown .6s .4s both;
        }
        .heading {
            font-size: 2.4rem; font-weight: 900; color: #fff;
            line-height: 1.1; margin: .4rem 0;
            animation: fadeSlideDown .6s .55s both;
        }
        .driver-name { font-size: 1.2rem; font-weight: 700; color: #7dd3fc; animation: fadeSlideDown .6s .7s both; }
        .subtitle { font-size: .9rem; color: rgba(255,255,255,.55); margin-top: .3rem; animation: fadeSlideDown .5s .85s both; }

        /* ── Route suggestion card ── */
        .route-box {
            background: linear-gradient(135deg, rgba(56,189,248,.15), rgba(14,165,233,.15));
            border: 1px solid rgba(56,189,248,.35);
            border-radius: 16px; padding: 1.2rem 1.5rem;
            margin: 1.4rem 0;
            animation: fadeSlideUp .6s 1s both, routeGlow 2.5s 1.5s ease-in-out infinite alternate;
        }
        @keyframes routeGlow {
            0%   { box-shadow: 0 0 12px rgba(56,189,248,.2); }
            100% { box-shadow: 0 0 28px rgba(56,189,248,.45), 0 0 60px rgba(14,165,233,.2); }
        }
        .route-label {
            font-size: .68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .14em; color: #38bdf8; margin-bottom: .5rem;
        }
        .route-value {
            font-size: 1.35rem; font-weight: 800; color: #fff;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .route-arrow { color: #38bdf8; font-size: 1.1rem; }

        /* ── Supervisor note ── */
        .supervisor-note {
            background: rgba(251,191,36,.08); border: 1px solid rgba(251,191,36,.25);
            border-radius: 10px; padding: .75rem 1rem;
            font-size: .8rem; color: rgba(255,255,255,.6);
            margin-bottom: 1.5rem;
            animation: fadeSlideUp .5s 1.2s both;
            display: flex; align-items: flex-start; gap: .6rem; text-align: left;
        }
        .supervisor-note i { color: #fbbf24; flex-shrink: 0; margin-top: .1rem; }

        /* ── Bus badge ── */
        .bus-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);
            border-radius: 8px; padding: .35rem .85rem;
            font-size: .82rem; color: rgba(255,255,255,.7);
            margin-bottom: 1.5rem; animation: fadeSlideUp .5s 1.1s both;
        }

        /* ── Actions ── */
        .actions { display: flex; flex-direction: column; gap: .85rem; animation: fadeSlideUp .5s 1.3s both; }
        .btn-action {
            display: flex; align-items: center; justify-content: center; gap: .6rem;
            border: none; border-radius: 14px; padding: .9rem 1.6rem;
            font-size: .95rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: transform .15s, box-shadow .15s;
            width: 100%;
        }
        .btn-return {
            background: linear-gradient(135deg, #0369a1, #0284c7); color: #fff;
        }
        .btn-return:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(2,132,199,.5); color: #fff; }
        .btn-done {
            background: rgba(255,255,255,.08); color: rgba(255,255,255,.75);
            border: 1px solid rgba(255,255,255,.18);
        }
        .btn-done:hover { transform: translateY(-2px); background: rgba(255,255,255,.14); color: #fff; }

        .or-divider {
            display: flex; align-items: center; gap: .75rem;
            color: rgba(255,255,255,.3); font-size: .75rem;
        }
        .or-divider::before, .or-divider::after {
            content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.12);
        }

        /* ── Refresh note ── */
        .refresh-note {
            margin-top: 1.4rem;
            font-size: .7rem; color: rgba(255,255,255,.28);
            animation: fadeSlideUp .5s 1.5s both;
        }

        @keyframes fadeSlideDown {
            0%   { opacity: 0; transform: translateY(-14px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeSlideUp {
            0%   { opacity: 0; transform: translateY(14px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="sky"></div>
<div class="stars" id="stars"></div>

<div class="card">
    <div class="icon-wrap">
        <i class="bi bi-arrow-repeat icon-main"></i>
    </div>

    <div class="road">
        @for($i = 0; $i < 6; $i++)
        <div class="road-line" style="animation-delay:{{ $i * 0.25 }}s;"></div>
        @endfor
    </div>

    <div class="label"><i class="bi bi-robot me-1"></i> Return Trip Auto-Suggested</div>
    <div class="heading">Ready to Head Back?</div>
    <div class="driver-name">{{ $driver->name }}</div>
    <div class="subtitle">The system has suggested your return route based on where you arrived.</div>

    <div class="route-box">
        <div class="route-label"><i class="bi bi-signpost-split me-1"></i>Suggested Return Route</div>
        <div class="route-value">
            @php
                $parts = explode(' → ', $assignment->route ?? '');
            @endphp
            @if(count($parts) === 2)
                <span>{{ $parts[0] }}</span>
                <i class="bi bi-arrow-right route-arrow"></i>
                <span>{{ $parts[1] }}</span>
            @else
                <span>{{ $assignment->route ?? '—' }}</span>
            @endif
        </div>
    </div>

    <div class="bus-badge">
        <i class="bi bi-bus-front-fill" style="color:#38bdf8;"></i>
        Bus: <strong>{{ $assignment->bus->registration_number }}</strong>
        &nbsp;&middot;&nbsp;
        At terminal &mdash; ready to depart
    </div>

    <div class="supervisor-note">
        <i class="bi bi-shield-check"></i>
        <span>
            <strong style="color:rgba(255,255,255,.75);">Supervisor can change this route.</strong>
            If there are many passengers waiting for a different destination, your supervisor may update the route before you depart. This page refreshes automatically every 30 seconds.
        </span>
    </div>

    <div class="actions">
        <form method="POST" action="{{ route('driver.trip.start') }}">
            @csrf
            <button type="submit" class="btn-action btn-return">
                <i class="bi bi-play-circle-fill"></i>
                Start Return: {{ $assignment->route ?? 'Return Trip' }}
            </button>
        </form>

        <div class="or-divider">or</div>

        <form method="POST" action="{{ route('driver.trip.finish-day') }}">
            @csrf
            <button type="submit" class="btn-action btn-done">
                <i class="bi bi-moon-stars"></i> Done for Today
            </button>
        </form>
    </div>

    <div class="refresh-note">
        <i class="bi bi-arrow-clockwise me-1"></i>
        Checking for route updates in <span id="refreshSec" style="color:#38bdf8;font-weight:700;">30</span>s
        &nbsp;&middot;&nbsp;
        <a href="{{ route('driver.return-suggested') }}" style="color:rgba(255,255,255,.35);text-decoration:underline;font-size:.68rem;">Refresh now</a>
    </div>
</div>

<script>
(function () {
    const starsEl = document.getElementById('stars');
    for (let i = 0; i < 100; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        const size = Math.random() * 2.5 + 1;
        s.style.cssText = `width:${size}px;height:${size}px;top:${Math.random()*100}%;left:${Math.random()*100}%;--d:${(Math.random()*2+1.5).toFixed(1)}s;--delay:-${(Math.random()*3).toFixed(1)}s;`;
        starsEl.appendChild(s);
    }

    const secEl = document.getElementById('refreshSec');
    let remaining = 30;
    const ticker = setInterval(function () {
        remaining--;
        if (secEl) secEl.textContent = remaining;
        if (remaining <= 0) {
            clearInterval(ticker);
            window.location.reload();
        }
    }, 1000);
}());
</script>
</body>
</html>
