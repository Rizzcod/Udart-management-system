<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — UDART MMS</title>
    <link rel="icon" type="image/jpeg" href="/images/udart-logo.jpg">
    <link href="/libs/css/bootstrap.min.css" rel="stylesheet">
    <link href="/libs/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* Full-page background photo */
        .bg-photo {
            position: fixed;
            inset: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }
        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg,
                rgba(3,10,45,0.82) 0%,
                rgba(7,22,82,0.76) 50%,
                rgba(10,32,115,0.70) 100%);
            z-index: -1;
        }

        /* Layout */
        .auth-wrap {
            display: flex;
            width: 100%;
            min-height: 100vh;
            align-items: center;
        }

        /* Left: branding — transparent, photo shows through */
        .brand-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 4rem;
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.18;
            letter-spacing: -.02em;
            text-shadow: 0 2px 16px rgba(0,0,0,.4);
        }

        .brand-footer {
            margin-top: auto;
            padding-top: 3rem;
            color: rgba(255,255,255,.3);
            font-size: .72rem;
        }

        /* Right: transparent wrapper — photo shows through */
        .form-side {
            width: 500px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 2.5rem;
            min-height: 100vh;
        }

        /* Floating white card */
        .login-box {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.97);
            border-radius: 20px;
            padding: 1.2rem 1.6rem 1.4rem;
            box-shadow: 0 12px 60px rgba(0,0,0,.35), 0 2px 8px rgba(0,0,0,.15);
        }

        /* Badge */
        .sec-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: #eef2ff;
            border-radius: 50px;
            padding: .28rem .85rem;
            font-size: .74rem;
            color: #3b5bdb;
            font-weight: 600;
            margin-bottom: 0;
        }

        /* Form fields */
        .field-group { margin-bottom: .6rem; }
        .field-label {
            display: block;
            font-size: .81rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .22rem;
        }
        .field-row {
            display: flex;
            align-items: center;
            border: 1.5px solid #e5e7eb;
            border-radius: 11px;
            background: #f9fafb;
            overflow: hidden;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .field-row:focus-within {
            border-color: #0a1f6e;
            box-shadow: 0 0 0 3px rgba(10,31,110,.09);
            background: #fff;
        }
        .field-row.has-error { border-color: #ef4444; }
        .f-ico {
            width: 44px;
            display: flex; align-items: center; justify-content: center;
            color: #9ca3af;
            font-size: .95rem;
            flex-shrink: 0;
        }
        .f-input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: .9rem;
            padding: .45rem .4rem .45rem 0;
            color: #111827;
            outline: none;
        }
        .f-input::placeholder { color: #c5cad6; }
        .f-btn {
            background: none;
            border: none;
            padding: 0 .8rem;
            color: #9ca3af;
            cursor: pointer;
            font-size: .95rem;
            line-height: 1;
            transition: color .15s;
        }
        .f-btn:hover { color: #6b7280; }
        .field-err { font-size: .76rem; color: #dc2626; margin-top: .3rem; }

        /* Alerts */
        .msg-ok  { background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:.75rem 1rem; font-size:.83rem; color:#065f46; margin-bottom:1.1rem; }
        .msg-err { background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:.75rem 1rem; font-size:.83rem; color:#991b1b; margin-bottom:1.1rem; }

        /* Back to project overview */
        .back-link {
            align-self: flex-start;
            display: inline-flex; align-items: center; gap: .4rem;
            color: rgba(255,255,255,.8); text-decoration: none;
            font-size: .85rem; font-weight: 600; margin-bottom: 1.25rem;
        }
        .back-link:hover { color: #fff; }

        /* Demo accounts (DEMO_MODE only) */
        .demo-box {
            background: #f5f7ff;
            border: 1px solid #dfe5fb;
            border-radius: 12px;
            padding: .6rem .7rem .55rem;
            margin-bottom: .8rem;
        }
        .demo-head {
            display: flex; align-items: center; justify-content: space-between; gap: .5rem;
            font-size: .76rem; font-weight: 700; color: #08195a; margin-bottom: .45rem;
        }
        .demo-pw { font-weight: 500; color: #6b7280; }
        .demo-head code { color: #08195a; background: #fff; border: 1px solid #dfe5fb; border-radius: 5px; padding: 0 .3rem; }
        .demo-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .35rem; }
        @media (max-width: 420px) { .demo-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        .demo-btn {
            display: flex; align-items: center; justify-content: center; gap: .3rem;
            background: #fff; border: 1.5px solid #dfe5fb; border-radius: 8px;
            font-size: .74rem; font-weight: 600; color: #374151;
            padding: .38rem .3rem; cursor: pointer;
            transition: border-color .15s, color .15s, background .15s;
        }
        .demo-btn:hover { border-color: #1535a8; color: #1535a8; }
        .demo-btn.active { border-color: #08195a; background: #08195a; color: #fff; }
        .demo-hint { font-size: .7rem; color: #9ca3af; margin-top: .4rem; text-align: center; }

        /* Submit */
        .btn-sign {
            width: 100%;
            background: linear-gradient(135deg, #08195a 0%, #1535a8 100%);
            border: none;
            border-radius: 11px;
            color: #fff;
            font-weight: 700;
            font-size: .96rem;
            padding: .7rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: opacity .15s, transform .1s, box-shadow .15s;
        }
        .btn-sign:hover {
            opacity: .9;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(8,25,90,.35);
        }
        .btn-sign:active { transform: translateY(0); }

        .form-footer {
            margin-top: .7rem;
            padding-top: .6rem;
            border-top: 1px solid #f0f2f8;
            text-align: center;
            font-size: .73rem;
            color: #b0b8cc;
            line-height: 1.7;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .brand-side { padding: 2rem 2.5rem; }
            .brand-title { font-size: 2rem; }
            .form-side { width: 420px; padding: 2rem 1.5rem; }
        }
        @media (max-width: 650px) {
            .auth-wrap { flex-direction: column; justify-content: center; padding: 2rem 1rem; }
            .brand-side { padding: 1.5rem 1rem; flex: none; }
            .brand-title { font-size: 1.5rem; }
            .brand-footer { display: none; }
            .form-side { width: 100%; min-height: auto; padding: 1rem; }
        }
    </style>
</head>
<body>

<div class="bg-photo" style="background-image: url('{{ asset('images/brt-bus.jpg') }}');"></div>
<div class="bg-overlay"></div>

<div class="auth-wrap">

    {{-- LEFT: Branding --}}
    <div class="brand-side">
        <a href="{{ url('/') }}" class="back-link"><i class="bi bi-arrow-left"></i> Project overview</a>
        <h1 class="brand-title">UDART Maintenance<br>Management System</h1>
        <p class="brand-footer">&copy; {{ date('Y') }} UDART &mdash; All rights reserved</p>
    </div>

    {{-- RIGHT: Floating login card --}}
    <div class="form-side">
        <div class="login-box">

            {{-- Card header: centered --}}
            <div style="text-align:center;margin-bottom:.9rem;padding-bottom:.8rem;border-bottom:1px solid #f0f2f8;">
                <img src="{{ asset('images/udart-logo.jpg') }}" alt="UDART"
                     style="height:52px;width:auto;border-radius:9px;margin-bottom:.5rem;">
                <h2 style="font-size:1.4rem;font-weight:800;color:#08195a;letter-spacing:-.02em;margin-bottom:.15rem;">
                    Welcome
                </h2>
                <p style="color:#6b7280;font-size:.8rem;margin-bottom:.5rem;">
                    Sign in to access your account
                </p>
                <div class="sec-badge" style="justify-content:center;display:inline-flex;">
                    <i class="bi bi-shield-lock-fill"></i> Secure Access
                </div>
            </div>

            @if (session('status'))
            <div class="msg-ok">
                <i class="bi bi-check-circle-fill me-2" style="color:#10b981;"></i>{{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="msg-err">
                <i class="bi bi-exclamation-triangle-fill me-2" style="color:#ef4444;"></i>{{ $errors->first() }}
            </div>
            @endif

            @if (config('demo.enabled'))
            {{-- Demo accounts: fills the form below; sign-in still goes through the normal login request --}}
            <div class="demo-box">
                <div class="demo-head">
                    <span><i class="bi bi-person-badge me-1"></i>Demo accounts</span>
                    <span class="demo-pw">Password: <code>{{ config('demo.password') }}</code></span>
                </div>
                <div class="demo-grid">
                    @foreach (config('demo.accounts') as $account)
                    <button type="button" class="demo-btn"
                            data-key="{{ $account['key'] }}"
                            data-email="{{ $account['email'] }}"
                            title="{{ $account['summary'] }}">
                        <i class="bi {{ $account['icon'] }}"></i>{{ $account['role'] === 'Administrator' ? 'Admin' : $account['role'] }}
                    </button>
                    @endforeach
                </div>
                <p class="demo-hint">Pick a role to fill in the form, then select Sign In.</p>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="field-group">
                    <label for="email" class="field-label">Email Address</label>
                    <div class="field-row @error('email') has-error @enderror">
                        <span class="f-ico"><i class="bi bi-envelope"></i></span>
                        <input id="email" type="email" name="email" class="f-input"
                               value="{{ old('email') }}"
                               placeholder="you@udart.co.tz"
                               required autofocus autocomplete="username">
                    </div>
                    @error('email')
                    <div class="field-err"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.38rem;">
                        <label for="password" class="field-label" style="margin:0;">Password</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           style="font-size:.78rem;color:#0a1f6e;text-decoration:none;font-weight:600;">
                            Forgot password?
                        </a>
                        @endif
                    </div>
                    <div class="field-row @error('password') has-error @enderror">
                        <span class="f-ico"><i class="bi bi-lock"></i></span>
                        <input id="password" type="password" name="password" class="f-input"
                               placeholder="Enter your password"
                               required autocomplete="current-password">
                        <button type="button" class="f-btn" onclick="togglePw()">
                            <i class="bi bi-eye" id="pw-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <div class="field-err"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember --}}
                <div style="margin-bottom:.6rem;">
                    <label style="display:flex;align-items:center;gap:.55rem;cursor:pointer;width:fit-content;">
                        <input type="checkbox" name="remember"
                               style="width:16px;height:16px;accent-color:#0a1f6e;cursor:pointer;flex-shrink:0;">
                        <span style="font-size:.84rem;color:#6b7280;">Keep me signed in</span>
                    </label>
                </div>

                <button type="submit" class="btn-sign">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div class="form-footer">
                &copy; {{ date('Y') }} UDART &mdash; Authorised personnel only
            </div>

        </div>
    </div>

</div>

<script>
function togglePw() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pw-eye');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}
</script>
@if (config('demo.enabled'))
<script>
// Demo quick-fill: only populates the fields — the visitor still submits the normal login form.
(function () {
    var buttons = document.querySelectorAll('.demo-btn');
    var password = @json(config('demo.password'));

    function select(btn) {
        buttons.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        document.getElementById('email').value = btn.dataset.email;
        document.getElementById('password').value = password;
        document.querySelector('.btn-sign').focus();
    }

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () { select(btn); });
    });

    // Pre-select the role chosen on the landing page (/login?as=<key>)
    var requested = new URLSearchParams(window.location.search).get('as');
    buttons.forEach(function (btn) {
        if (btn.dataset.key === requested) select(btn);
    });
})();
</script>
@endif
</body>
</html>
