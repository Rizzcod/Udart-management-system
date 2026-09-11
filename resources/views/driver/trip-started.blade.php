<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Journey!</title>
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

        /* ── Animated sky gradient ── */
        .sky {
            position: fixed;
            inset: 0;
            background: linear-gradient(160deg, #0a0f2e 0%, #0d2150 40%, #1a3a6e 70%, #0e1c3d 100%);
            animation: skyPulse 6s ease-in-out infinite alternate;
            z-index: 0;
        }
        @keyframes skyPulse {
            0%   { background: linear-gradient(160deg, #0a0f2e 0%, #0d2150 40%, #1a3a6e 70%, #0e1c3d 100%); }
            100% { background: linear-gradient(160deg, #0d1a45 0%, #152d6e 40%, #1e4080 70%, #0a1530 100%); }
        }

        /* ── Stars ── */
        .stars {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
        }
        .star {
            position: absolute;
            background: #fff;
            border-radius: 50%;
            animation: twinkle var(--d) ease-in-out infinite alternate;
            animation-delay: var(--delay);
        }
        @keyframes twinkle {
            0%   { opacity: .15; transform: scale(.8); }
            100% { opacity: 1;   transform: scale(1.2); }
        }

        /* ── Confetti ── */
        .confetti-wrap {
            position: fixed;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            overflow: hidden;
        }
        .cf {
            position: absolute;
            top: -30px;
            border-radius: 2px;
            animation: confettiFall var(--dur) var(--delay) ease-in forwards;
        }
        @keyframes confettiFall {
            0%   { transform: translateY(0) rotate(0deg) scale(1);    opacity: 1; }
            80%  { opacity: 1; }
            100% { transform: translateY(110vh) rotate(var(--rot)) scale(.8); opacity: 0; }
        }

        /* ── Firework rings ── */
        .firework {
            position: fixed;
            border-radius: 50%;
            border: 3px solid;
            animation: fireworkBurst 1.4s ease-out forwards;
            pointer-events: none;
            z-index: 3;
        }
        @keyframes fireworkBurst {
            0%   { transform: scale(0); opacity: 1; }
            60%  { opacity: .7; }
            100% { transform: scale(4); opacity: 0; }
        }

        /* ── Road ── */
        .road {
            width: 100%;
            height: 36px;
            background: #1f2937;
            border-top: 3px solid #374151;
            border-bottom: 3px solid #374151;
            position: relative;
            overflow: hidden;
            border-radius: 4px;
            margin: 1.5rem 0;
        }
        .road-line {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 60px;
            height: 4px;
            background: #f59e0b;
            border-radius: 2px;
            animation: roadScroll 1.2s linear infinite;
        }
        @keyframes roadScroll {
            0%   { left: -70px; }
            100% { left: 110%; }
        }

        /* ── Bouncing bus ── */
        .bus-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: .5rem;
            animation: busHop .6s ease-in-out infinite alternate;
            filter: drop-shadow(0 12px 20px rgba(37,99,235,.7));
        }
        @keyframes busHop {
            0%   { transform: translateY(0)   rotate(-1deg); }
            100% { transform: translateY(-14px) rotate(1deg); }
        }
        .bus-icon {
            font-size: 5rem;
            color: #fff;
            text-shadow: 0 0 30px rgba(37,99,235,.9), 0 0 60px rgba(37,99,235,.5);
        }

        /* ── Main card ── */
        .card {
            position: relative;
            z-index: 10;
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 28px;
            padding: 2.5rem 2.75rem;
            max-width: 520px;
            width: 92%;
            text-align: center;
            box-shadow: 0 32px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.08);
            animation: cardPop .7s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes cardPop {
            0%   { transform: scale(.5) translateY(40px); opacity: 0; }
            100% { transform: scale(1) translateY(0);     opacity: 1; }
        }

        /* ── Text animations ── */
        .congrats-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #f59e0b;
            animation: fadeSlideDown .6s .5s both;
        }
        .congrats-heading {
            font-size: 2.6rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.1;
            margin: .4rem 0;
            text-shadow: 0 0 40px rgba(99,179,237,.8);
            animation: glowPulse 2s ease-in-out infinite alternate, fadeSlideDown .6s .65s both;
        }
        @keyframes glowPulse {
            0%   { text-shadow: 0 0 20px rgba(99,179,237,.6), 0 0 40px rgba(99,179,237,.3); }
            100% { text-shadow: 0 0 40px rgba(167,243,208,.8), 0 0 80px rgba(99,179,237,.5); }
        }
        .driver-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #93c5fd;
            animation: fadeSlideDown .6s .8s both;
        }
        .subtitle {
            font-size: .95rem;
            color: rgba(255,255,255,.6);
            margin-top: .35rem;
            animation: fadeSlideDown .5s 1s both;
        }

        /* ── Route pill ── */
        .route-pill {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            background: linear-gradient(135deg, rgba(37,99,235,.5), rgba(124,58,237,.5));
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 100px;
            padding: .7rem 1.5rem;
            margin: 1.25rem 0;
            animation: fadeSlideUp .6s 1.1s both, routeGlow 2s 1.8s ease-in-out infinite alternate;
        }
        @keyframes routeGlow {
            0%   { box-shadow: 0 0 15px rgba(37,99,235,.4); }
            100% { box-shadow: 0 0 30px rgba(124,58,237,.6), 0 0 60px rgba(37,99,235,.3); }
        }
        .route-icon { font-size: 1.3rem; color: #fde68a; }
        .route-text { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: .03em; }

        /* ── Safety message ── */
        .safety-msg {
            font-size: .9rem;
            color: rgba(255,255,255,.55);
            line-height: 1.6;
            animation: fadeSlideUp .5s 1.3s both;
        }
        .safety-msg strong { color: #a7f3d0; }

        /* ── Bus number badge ── */
        .bus-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 8px;
            padding: .35rem .85rem;
            font-size: .82rem;
            color: rgba(255,255,255,.75);
            margin-top: .75rem;
            animation: fadeSlideUp .5s 1.45s both;
        }

        /* ── Countdown bar ── */
        .countdown-wrap {
            margin-top: 1.75rem;
            animation: fadeSlideUp .5s 1.6s both;
        }
        .countdown-bar-bg {
            background: rgba(255,255,255,.1);
            border-radius: 100px;
            height: 6px;
            overflow: hidden;
        }
        .countdown-bar {
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            border-radius: 100px;
            width: 100%;
            animation: drainBar 6s linear forwards;
        }
        @keyframes drainBar {
            0%   { width: 100%; }
            100% { width: 0%; }
        }
        .countdown-text {
            font-size: .75rem;
            color: rgba(255,255,255,.4);
            margin-top: .5rem;
        }
        #sec { font-weight: 700; color: #93c5fd; }

        /* ── Dashboard button ── */
        .btn-dash {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: linear-gradient(135deg, #1c3faa, #2563eb);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: .7rem 1.6rem;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            margin-top: .85rem;
            transition: transform .15s, box-shadow .15s;
            animation: fadeSlideUp .5s 1.7s both;
        }
        .btn-dash:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37,99,235,.5);
            color: #fff;
        }

        /* ── Shared fade keyframes ── */
        @keyframes fadeSlideDown {
            0%   { opacity: 0; transform: translateY(-16px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeSlideUp {
            0%   { opacity: 0; transform: translateY(16px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ── Emoji sparkles floating up ── */
        .sparkle {
            position: fixed;
            font-size: 1.6rem;
            animation: floatUp var(--dur) var(--delay) ease-in forwards;
            z-index: 4;
            pointer-events: none;
            user-select: none;
        }
        @keyframes floatUp {
            0%   { transform: translateY(0) scale(1) rotate(0deg);   opacity: 1; }
            100% { transform: translateY(-90vh) scale(.3) rotate(360deg); opacity: 0; }
        }
    </style>
</head>
<body>

<div class="sky"></div>
<div class="stars" id="stars"></div>
<div class="confetti-wrap" id="confettiWrap"></div>

<div class="card">
    <div class="bus-wrap">
        <i class="bi bi-bus-front-fill bus-icon"></i>
    </div>

    <div class="road">
        @for($i = 0; $i < 6; $i++)
        <div class="road-line" style="animation-delay:{{ $i * 0.2 }}s;"></div>
        @endfor
    </div>

    <div class="congrats-label">🎉 &nbsp; You're on your way &nbsp; 🎉</div>
    <div class="congrats-heading">Congratulations!</div>
    <div class="driver-name">{{ $driver->name }}</div>
    <div class="subtitle">Your route has been confirmed and you're cleared to go!</div>

    @if($assignment->route)
    <div class="route-pill">
        <i class="bi bi-signpost-2-fill route-icon"></i>
        <span class="route-text">{{ $assignment->route }}</span>
    </div>
    @endif

    <div class="safety-msg">
        Drive safely, follow the road rules, and take care of your passengers.<br>
        <strong>Wishing you a smooth and wonderful journey today!</strong> 🌟
    </div>

    <div class="bus-badge">
        <i class="bi bi-bus-front-fill" style="color:#60a5fa;"></i>
        Bus: <strong>{{ $assignment->bus->registration_number }}</strong>
        &nbsp;&middot;&nbsp;
        {{ $assignment->bus->model }} {{ $assignment->bus->year }}
    </div>

    <div class="countdown-wrap">
        <div class="countdown-bar-bg">
            <div class="countdown-bar"></div>
        </div>
        <div class="countdown-text">Heading to dashboard in <span id="sec">6</span>s&hellip;</div>
    </div>

    <a href="{{ route('driver.dashboard') }}" class="btn-dash" id="dashBtn">
        <i class="bi bi-speedometer2"></i> Go to Dashboard
    </a>
</div>

<script>
(function () {
    // ── Stars ──
    const starsEl = document.getElementById('stars');
    for (let i = 0; i < 120; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        const size = Math.random() * 2.5 + 1;
        s.style.cssText = `
            width:${size}px; height:${size}px;
            top:${Math.random() * 100}%;
            left:${Math.random() * 100}%;
            --d:${(Math.random() * 2 + 1.5).toFixed(1)}s;
            --delay:-${(Math.random() * 3).toFixed(1)}s;
        `;
        starsEl.appendChild(s);
    }

    // ── Confetti ──
    const wrap = document.getElementById('confettiWrap');
    const colors = ['#f43f5e','#f97316','#facc15','#4ade80','#22d3ee','#818cf8','#e879f9','#fb7185','#34d399','#60a5fa'];
    for (let i = 0; i < 90; i++) {
        const cf = document.createElement('div');
        cf.className = 'cf';
        const size = Math.random() * 10 + 5;
        const isCircle = Math.random() > .5;
        cf.style.cssText = `
            width:${size}px;
            height:${isCircle ? size : size * 2.5}px;
            background:${colors[Math.floor(Math.random() * colors.length)]};
            border-radius:${isCircle ? '50%' : '2px'};
            left:${Math.random() * 100}vw;
            --dur:${(Math.random() * 3 + 2.5).toFixed(1)}s;
            --delay:${(Math.random() * 2.5).toFixed(1)}s;
            --rot:${Math.random() > .5 ? '' : '-'}${Math.floor(Math.random() * 540 + 180)}deg;
        `;
        wrap.appendChild(cf);
    }

    // ── Firework rings ──
    const fwColors = ['#f97316','#22d3ee','#e879f9','#4ade80','#facc15'];
    const positions = [[15,20],[85,15],[10,70],[90,65],[50,10],[30,85],[70,30]];
    positions.forEach(([left, top], idx) => {
        const fw = document.createElement('div');
        fw.className = 'firework';
        const size = Math.random() * 60 + 40;
        fw.style.cssText = `
            width:${size}px; height:${size}px;
            left:${left}%; top:${top}%;
            border-color:${fwColors[idx % fwColors.length]};
            animation-delay:${(idx * 0.3).toFixed(1)}s;
        `;
        document.body.appendChild(fw);
    });

    // ── Floating sparkle emojis ──
    const emojis = ['✨','🌟','⭐','🎊','🎉','🚀','💫','🎈','🏆','🌈'];
    function launchSparkle() {
        const sp = document.createElement('div');
        sp.className = 'sparkle';
        sp.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        sp.style.cssText = `
            left:${Math.random() * 100}vw;
            bottom:${Math.random() * 20}vh;
            --dur:${(Math.random() * 3 + 3).toFixed(1)}s;
            --delay:0s;
        `;
        document.body.appendChild(sp);
        setTimeout(() => sp.remove(), 7000);
    }
    for (let i = 0; i < 6; i++) setTimeout(launchSparkle, i * 600);
    setInterval(() => launchSparkle(), 1200);

    // ── Countdown + redirect ──
    let secs = 6;
    const secEl = document.getElementById('sec');
    const timer = setInterval(() => {
        secs--;
        if (secs <= 0) {
            clearInterval(timer);
            window.location.href = '{{ route("driver.dashboard") }}';
        } else {
            secEl.textContent = secs;
        }
    }, 1000);

    // Clicking the button clears the timer
    document.getElementById('dashBtn').addEventListener('click', () => clearInterval(timer));
})();
</script>
</body>
</html>
