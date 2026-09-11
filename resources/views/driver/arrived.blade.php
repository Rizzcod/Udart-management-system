<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arrived at Terminal!</title>
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

        /* ── Warm amber sky ── */
        .sky {
            position: fixed; inset: 0; z-index: 0;
            background: linear-gradient(160deg, #1a0a00 0%, #3d1f00 40%, #6b3500 70%, #3a1800 100%);
            animation: skyPulse 6s ease-in-out infinite alternate;
        }
        @keyframes skyPulse {
            0%   { background: linear-gradient(160deg, #1a0a00 0%, #3d1f00 40%, #6b3500 70%, #3a1800 100%); }
            100% { background: linear-gradient(160deg, #2a1200 0%, #4f2900 40%, #7c4200 70%, #2a1000 100%); }
        }

        /* ── Stars ── */
        .stars { position: fixed; inset: 0; z-index: 1; pointer-events: none; }
        .star {
            position: absolute; background: #ffd700; border-radius: 50%;
            animation: twinkle var(--d) ease-in-out infinite alternate;
            animation-delay: var(--delay);
        }
        @keyframes twinkle {
            0%   { opacity: .1; transform: scale(.7); }
            100% { opacity: .9; transform: scale(1.3); }
        }

        /* ── Gold particles ── */
        .particles { position: fixed; inset: 0; z-index: 2; pointer-events: none; overflow: hidden; }
        .particle {
            position: absolute; top: -20px;
            animation: particleFall var(--dur) var(--delay) ease-in forwards;
        }
        @keyframes particleFall {
            0%   { transform: translateY(0) rotate(0deg); opacity: 1; }
            80%  { opacity: .8; }
            100% { transform: translateY(110vh) rotate(var(--rot)); opacity: 0; }
        }

        /* ── Flag pole ── */
        .flag-wrap {
            display: flex; justify-content: center; margin-bottom: .75rem;
            animation: flagWave 1s ease-in-out infinite alternate;
            filter: drop-shadow(0 12px 24px rgba(251,191,36,.6));
        }
        @keyframes flagWave {
            0%   { transform: translateY(0) rotate(-3deg); }
            100% { transform: translateY(-10px) rotate(3deg); }
        }
        .flag-icon { font-size: 5rem; color: #fbbf24; text-shadow: 0 0 40px rgba(251,191,36,.9), 0 0 80px rgba(251,191,36,.5); }

        /* ── Road (arrival direction reversed) ── */
        .road {
            width: 100%; height: 34px; background: #1f2937;
            border-top: 3px solid #374151; border-bottom: 3px solid #374151;
            position: relative; overflow: hidden; border-radius: 4px; margin: 1.25rem 0;
        }
        .road-line {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 60px; height: 4px; background: #f59e0b; border-radius: 2px;
            animation: roadSlowdown 2s linear infinite;
        }
        @keyframes roadSlowdown {
            0%   { left: 110%; }
            100% { left: -70px; }
        }

        /* ── Main card ── */
        .card {
            position: relative; z-index: 10;
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(251,191,36,.3);
            border-radius: 28px; padding: 2.5rem 2.75rem;
            max-width: 560px; width: 92%; text-align: center;
            box-shadow: 0 32px 80px rgba(0,0,0,.5), 0 0 60px rgba(251,191,36,.08);
            animation: cardPop .7s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes cardPop {
            0%   { transform: scale(.5) translateY(40px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        /* ── Text ── */
        .arrived-label {
            font-size: .72rem; font-weight: 700; letter-spacing: .18em;
            text-transform: uppercase; color: #fbbf24;
            animation: fadeSlideDown .6s .5s both;
        }
        .arrived-heading {
            font-size: 2.6rem; font-weight: 900; color: #fff;
            line-height: 1.1; margin: .4rem 0;
            animation: goldGlow 2s ease-in-out infinite alternate, fadeSlideDown .6s .65s both;
        }
        @keyframes goldGlow {
            0%   { text-shadow: 0 0 20px rgba(251,191,36,.6), 0 0 40px rgba(251,191,36,.3); }
            100% { text-shadow: 0 0 40px rgba(253,224,71,.9), 0 0 80px rgba(251,191,36,.5); }
        }
        .driver-name { font-size: 1.3rem; font-weight: 700; color: #fde68a; animation: fadeSlideDown .6s .8s both; }
        .subtitle { font-size: .95rem; color: rgba(255,255,255,.6); margin-top: .35rem; animation: fadeSlideDown .5s 1s both; }

        /* ── Route pill ── */
        .route-pill {
            display: inline-flex; align-items: center; gap: .6rem;
            background: linear-gradient(135deg, rgba(180,83,9,.5), rgba(146,64,14,.5));
            border: 1px solid rgba(251,191,36,.35); border-radius: 100px;
            padding: .7rem 1.5rem; margin: 1.25rem 0;
            animation: fadeSlideUp .6s 1.1s both, routeGlow 2s 1.8s ease-in-out infinite alternate;
        }
        @keyframes routeGlow {
            0%   { box-shadow: 0 0 15px rgba(251,191,36,.3); }
            100% { box-shadow: 0 0 30px rgba(251,191,36,.5), 0 0 60px rgba(180,83,9,.3); }
        }
        .route-icon { font-size: 1.3rem; color: #fde68a; }
        .route-text { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: .03em; }

        /* ── Bus badge ── */
        .bus-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(255,255,255,.1); border: 1px solid rgba(251,191,36,.2);
            border-radius: 8px; padding: .35rem .85rem;
            font-size: .82rem; color: rgba(255,255,255,.75);
            margin-bottom: 1.5rem; animation: fadeSlideUp .5s 1.25s both;
        }

        /* ── Action buttons ── */
        .actions { display: flex; flex-direction: column; gap: .85rem; animation: fadeSlideUp .5s 1.4s both; }
        .btn-action {
            display: flex; align-items: center; justify-content: center; gap: .6rem;
            border: none; border-radius: 14px; padding: .85rem 1.6rem;
            font-size: .95rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: transform .15s, box-shadow .15s;
            width: 100%;
        }
        .btn-return {
            background: linear-gradient(135deg, #1c3faa, #2563eb); color: #fff;
        }
        .btn-return:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,.5); color: #fff; }
        .btn-endshift {
            background: rgba(255,255,255,.1); color: rgba(255,255,255,.85);
            border: 1px solid rgba(255,255,255,.2);
        }
        .btn-endshift:hover { transform: translateY(-2px); background: rgba(255,255,255,.18); color: #fff; }

        /* ── Divider ── */
        .or-divider {
            display: flex; align-items: center; gap: .75rem;
            color: rgba(255,255,255,.3); font-size: .75rem;
        }
        .or-divider::before, .or-divider::after {
            content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.15);
        }

        @keyframes fadeSlideDown {
            0%   { opacity: 0; transform: translateY(-16px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeSlideUp {
            0%   { opacity: 0; transform: translateY(16px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ── Gold sparkles ── */
        .sparkle {
            position: fixed; font-size: 1.6rem;
            animation: floatUp var(--dur) var(--delay) ease-in forwards;
            z-index: 4; pointer-events: none; user-select: none;
        }
        @keyframes floatUp {
            0%   { transform: translateY(0) scale(1) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-90vh) scale(.3) rotate(360deg); opacity: 0; }
        }
    </style>
</head>
<body>

<div class="sky"></div>
<div class="stars" id="stars"></div>
<div class="particles" id="particles"></div>

<div class="card">
    <div class="flag-wrap">
        <i class="bi bi-flag-fill flag-icon"></i>
    </div>

    <div class="road">
        @for($i = 0; $i < 6; $i++)
        <div class="road-line" style="animation-delay:{{ $i * 0.2 }}s;"></div>
        @endfor
    </div>

    <div class="arrived-label">🏁 &nbsp; Terminal Reached &nbsp; 🏁</div>
    <div class="arrived-heading">You've Arrived!</div>
    <div class="driver-name">{{ $driver->name }}</div>
    <div class="subtitle">Great job! Your bus has reached the terminal safely.</div>

    @if($assignment->route)
    <div class="route-pill">
        <i class="bi bi-signpost-2-fill route-icon"></i>
        <span class="route-text">{{ $assignment->route }}</span>
    </div>
    @endif

    <div class="bus-badge">
        <i class="bi bi-bus-front-fill" style="color:#fbbf24;"></i>
        Bus: <strong>{{ $assignment->bus->registration_number }}</strong>
        &nbsp;&middot;&nbsp;
        Arrived {{ now()->format('H:i') }}
    </div>

    {{-- Route change alert from supervisor --}}
    @if($routeNotif)
    <div id="routeAlert" style="
        background:rgba(251,191,36,.2);border:1px solid rgba(251,191,36,.5);
        border-radius:12px;padding:.85rem 1.1rem;margin-bottom:1.25rem;
        animation:fadeSlideDown .5s .3s both;text-align:left;">
        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#fbbf24;margin-bottom:.3rem;">
            <i class="bi bi-bell-fill me-1"></i>Route Updated by Supervisor
        </div>
        <div style="font-size:.85rem;color:rgba(255,255,255,.85);">{{ $routeNotif->message }}</div>
    </div>
    @endif

    <div class="actions">
        <form method="POST" action="{{ route('driver.trip.start') }}">
            @csrf
            <button type="submit" class="btn-action btn-return">
                <i class="bi bi-arrow-repeat"></i>
                Start {{ $assignment->route ? 'Route: ' . $assignment->route : 'Return Trip' }}
            </button>
        </form>

        <div class="or-divider">or</div>

        <form method="POST" action="{{ route('driver.trip.end') }}">
            @csrf
            <button type="submit" class="btn-action btn-endshift">
                <i class="bi bi-arrow-repeat"></i> End Shift & Get Return Route
            </button>
        </form>
    </div>

    {{-- Auto-refresh countdown --}}
    <div style="margin-top:1.5rem;animation:fadeSlideUp .5s 1.6s both;">
        <div style="font-size:.7rem;color:rgba(255,255,255,.3);">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Checking for route updates in <span id="refreshSec" style="color:#fbbf24;font-weight:700;">30</span>s
            &nbsp;&middot;&nbsp;
            <a href="{{ route('driver.trip-arrived') }}" style="color:rgba(255,255,255,.4);text-decoration:underline;font-size:.68rem;">Refresh now</a>
        </div>
    </div>
</div>

<script>
(function () {
    // Stars (gold tinted)
    const starsEl = document.getElementById('stars');
    for (let i = 0; i < 100; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        const size = Math.random() * 2.5 + 1;
        s.style.cssText = `width:${size}px;height:${size}px;top:${Math.random()*100}%;left:${Math.random()*100}%;--d:${(Math.random()*2+1.5).toFixed(1)}s;--delay:-${(Math.random()*3).toFixed(1)}s;`;
        starsEl.appendChild(s);
    }

    // Gold particles
    const wrap = document.getElementById('particles');
    const goldColors = ['#fbbf24','#f59e0b','#fcd34d','#fde68a','#fff','#fb923c','#fca5a1'];
    for (let i = 0; i < 80; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random() * 10 + 4;
        const isCircle = Math.random() > .4;
        p.innerHTML = `<div style="width:${size}px;height:${isCircle?size:size*2.5}px;background:${goldColors[Math.floor(Math.random()*goldColors.length)]};border-radius:${isCircle?'50%':'2px'};opacity:.9;"></div>`;
        p.style.cssText = `left:${Math.random()*100}vw;--dur:${(Math.random()*3+2).toFixed(1)}s;--delay:${(Math.random()*2).toFixed(1)}s;--rot:${Math.random()>.5?'':'-'}${Math.floor(Math.random()*540+180)}deg;`;
        wrap.appendChild(p);
    }

    // Floating emojis
    const emojis = ['🏁','🥇','🎉','✨','⭐','🌟','🏆','🎊','💛','🔔'];
    function launchSparkle() {
        const sp = document.createElement('div');
        sp.className = 'sparkle';
        sp.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        sp.style.cssText = `left:${Math.random()*100}vw;bottom:${Math.random()*20}vh;--dur:${(Math.random()*3+3).toFixed(1)}s;--delay:0s;`;
        document.body.appendChild(sp);
        setTimeout(() => sp.remove(), 7000);
    }
    for (let i = 0; i < 8; i++) setTimeout(launchSparkle, i * 500);
    setInterval(launchSparkle, 1000);

    // Auto-refresh countdown (checks for supervisor route updates)
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
