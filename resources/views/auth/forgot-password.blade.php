<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password — UDART MMS</title>
    <link rel="icon" type="image/jpeg" href="/images/udart-logo.jpg">
    <link href="/libs/css/bootstrap.min.css" rel="stylesheet">
    <link href="/libs/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; min-height: 100vh; background: #eef1f8; }
        .auth-wrapper { min-height: 100vh; display: flex; }
        .auth-left {
            width: 45%;
            background: linear-gradient(145deg, #0f2880 0%, #1c3faa 55%, #2551d4 100%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 3rem 2.5rem; position: relative; overflow: hidden;
        }
        .auth-left::before { content:''; position:absolute; top:-120px; right:-120px; width:380px; height:380px; background:rgba(255,255,255,.06); border-radius:50%; }
        .auth-left::after  { content:''; position:absolute; bottom:-100px; left:-80px;  width:300px; height:300px; background:rgba(255,255,255,.05); border-radius:50%; }
        .auth-right { flex:1; display:flex; align-items:center; justify-content:center; padding:2.5rem 2rem; background:#f5f7fc; }
        .auth-card { width:100%; max-width:440px; background:#fff; border-radius:18px; padding:2.5rem; box-shadow:0 4px 32px rgba(28,63,170,.08); }
        .form-label { font-size:.83rem; font-weight:600; color:#374151; margin-bottom:.3rem; }
        .form-control { border-radius:9px; border-color:#e5e7eb; font-size:.9rem; padding:.6rem .9rem; transition:border-color .15s, box-shadow .15s; }
        .form-control:focus { border-color:#1c3faa; box-shadow:0 0 0 3px rgba(28,63,170,.12); }
        .input-group-text { background:#f9fafb; border-color:#e5e7eb; color:#9ca3af; }
        .btn-primary-custom { background:#1c3faa; border:none; border-radius:10px; color:#fff; font-weight:700; font-size:.95rem; padding:.72rem 1.5rem; width:100%; transition:background .15s, transform .1s, box-shadow .15s; }
        .btn-primary-custom:hover { background:#1634a0; transform:translateY(-1px); box-shadow:0 4px 16px rgba(28,63,170,.3); color:#fff; }
        @media (max-width: 767px) { .auth-left { display:none; } .auth-right { padding:1.5rem 1rem; background:#eef1f8; } }
    </style>
</head>
<body>
<div class="auth-wrapper">

    {{-- Left panel --}}
    <div class="auth-left">
        <div class="text-center mb-4" style="position:relative;z-index:1;">
            <img src="{{ asset('images/udart-logo.jpg') }}" alt="UDART Logo"
                 style="max-width:180px;width:100%;filter:brightness(0) invert(1);">
        </div>
        <div class="text-center" style="position:relative;z-index:1;">
            <h2 class="fw-bold text-white mb-2" style="font-size:1.5rem;">Account Recovery</h2>
            <p class="mb-0" style="color:rgba(255,255,255,.65);font-size:.92rem;max-width:280px;line-height:1.6;">
                We'll send a secure password reset link to your registered email address.
            </p>
        </div>
        <div style="width:50px;height:3px;background:rgba(255,255,255,.3);border-radius:3px;margin:2rem auto;"></div>
        <div class="text-center" style="position:relative;z-index:1;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto"
                 style="width:80px;height:80px;background:rgba(255,255,255,.12);font-size:2rem;color:rgba(255,255,255,.8);">
                <i class="bi bi-shield-lock"></i>
            </div>
            <p class="mt-3 mb-0" style="color:rgba(255,255,255,.5);font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;">
                Secure Reset via Gmail
            </p>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="auth-right">
        <div class="auth-card">

            {{-- Mobile logo --}}
            <div class="d-md-none text-center mb-4">
                <img src="{{ asset('images/udart-logo.jpg') }}" alt="UDART" style="max-width:130px;">
            </div>

            {{-- Back link --}}
            <a href="{{ route('login') }}" class="d-inline-flex align-items-center gap-1 mb-4 text-decoration-none"
               style="font-size:.82rem;color:#6b7280;">
                <i class="bi bi-arrow-left"></i> Back to sign in
            </a>

            {{-- Header --}}
            <div class="mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
                     style="width:52px;height:52px;background:#eff6ff;">
                    <i class="bi bi-envelope-paper" style="font-size:1.4rem;color:#1c3faa;"></i>
                </div>
                <h4 class="fw-bold mb-1" style="color:#111827;font-size:1.3rem;">Forgot your password?</h4>
                <p class="mb-0" style="color:#6b7280;font-size:.87rem;line-height:1.5;">
                    Enter your email address and we'll send you a link to reset your password.
                </p>
            </div>

            {{-- Status --}}
            @if (session('status'))
            <div class="alert py-2 mb-3" style="border-radius:9px;font-size:.84rem;border:none;background:#dcfce7;color:#166534;">
                <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:9px 0 0 9px;border-right:none;">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email" type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="your-email@example.com"
                               required autofocus
                               style="border-left:none;border-radius:0 9px 9px 0;">
                    </div>
                    @error('email')
                    <div class="mt-1" style="font-size:.78rem;color:#dc2626;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-send me-2"></i>Send Reset Link
                </button>
            </form>

            <div class="text-center mt-4" style="font-size:.78rem;color:#9ca3af;">
                Remembered your password?
                <a href="{{ route('login') }}" style="color:#1c3faa;text-decoration:none;font-weight:500;">Sign in here</a>
            </div>

        </div>
    </div>

</div>
<script src="/libs/js/bootstrap.bundle.min.js"></script>
</body>
</html>
