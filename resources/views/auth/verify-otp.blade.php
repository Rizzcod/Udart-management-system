<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verify Email — UDART MMS</title>
<link href="/libs/css/bootstrap.min.css" rel="stylesheet">
<link href="/libs/css/bootstrap-icons.css" rel="stylesheet">
<style>
  body { background:linear-gradient(135deg,#1c3faa 0%,#2563eb 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Segoe UI',sans-serif; padding:24px; }
  .card { border:none; border-radius:18px; box-shadow:0 20px 60px rgba(0,0,0,.2); max-width:440px; width:100%; }
  .brand { display:flex; align-items:center; gap:12px; margin-bottom:8px; }
  .brand-icon { width:46px; height:46px; background:#1c3faa; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#fff; flex-shrink:0; }
  .otp-inputs { display:flex; gap:10px; justify-content:center; margin:20px 0; }
  .otp-digit { width:50px; height:60px; text-align:center; font-size:1.5rem; font-weight:700; border:2px solid #e5e7eb; border-radius:12px; color:#111827; transition:border-color .2s; }
  .otp-digit:focus { border-color:#1c3faa; box-shadow:0 0 0 3px rgba(28,63,170,.15); outline:none; }
  .otp-digit.filled { border-color:#1c3faa; background:#f0f6ff; }
</style>
</head>
<body>

<div class="card">
  <div class="card-body p-5">

    <div class="brand">
      <div class="brand-icon"><i class="bi bi-bus-front-fill"></i></div>
      <div>
        <div class="fw-bold" style="font-size:1rem;color:#111827;">UDART MMS</div>
        <div class="text-muted" style="font-size:.72rem;">Maintenance Management System</div>
      </div>
    </div>

    <hr class="my-4" style="border-color:#f0f3fb;">

    <div class="text-center mb-4">
      <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
           style="width:60px;height:60px;background:#dbeafe;">
        <i class="bi bi-shield-lock-fill" style="font-size:1.6rem;color:#1c3faa;"></i>
      </div>
      <h5 class="fw-bold mb-1" style="color:#111827;">Verify Your Email</h5>
      <p class="text-muted mb-0" style="font-size:.875rem;">
        Enter the 6-digit OTP sent to your email address to activate your account.
      </p>
    </div>

    {{-- Status message --}}
    @if(session('status'))
    <div class="alert py-2 d-flex align-items-center gap-2 mb-4"
         style="background:#dcfce7;border:none;border-radius:10px;color:#15803d;font-size:.875rem;">
      <i class="bi bi-check-circle-fill"></i>
      {{ session('status') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert py-2 d-flex align-items-start gap-2 mb-4"
         style="background:#fee2e2;border:none;border-radius:10px;color:#991b1b;font-size:.875rem;">
      <i class="bi bi-exclamation-circle-fill mt-1 flex-shrink-0"></i>
      <div>
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
      @csrf

      {{-- Email --}}
      <div class="mb-4">
        <label class="form-label fw-semibold small">Email Address</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0" style="border-radius:9px 0 0 9px;">
            <i class="bi bi-envelope text-muted"></i>
          </span>
          <input type="email" name="email" id="emailInput"
                 class="form-control border-start-0 @error('email') is-invalid @enderror"
                 value="{{ old('email', $email ?? '') }}"
                 placeholder="your@email.com"
                 style="border-radius:0 9px 9px 0;" required autofocus>
        </div>
        @error('email')<div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>@enderror
      </div>

      {{-- OTP digits --}}
      <div class="mb-2">
        <label class="form-label fw-semibold small">One-Time Password (OTP)</label>
        <div class="otp-inputs" id="otpBoxes">
          @for($i = 0; $i < 6; $i++)
          <input type="text" inputmode="numeric" maxlength="1"
                 class="otp-digit" id="digit{{ $i }}"
                 autocomplete="one-time-code">
          @endfor
        </div>
        <input type="hidden" name="otp" id="otpHidden" value="{{ old('otp') }}">
        @error('otp')<div class="text-danger text-center mt-1" style="font-size:.8rem;">{{ $message }}</div>@enderror
      </div>
      <p class="text-muted text-center mb-4" style="font-size:.78rem;">
        <i class="bi bi-clock me-1"></i>OTP expires 24 hours after your account was created.
      </p>

      <button type="submit" id="submitBtn" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2"
              style="background:#1c3faa;border-color:#1c3faa;border-radius:10px;font-weight:700;padding:.75rem;font-size:1rem;">
        <i class="bi bi-shield-check"></i> Verify Email
      </button>
    </form>

    <div class="text-center mt-4">
      <a href="{{ route('login') }}" style="color:#1c3faa;font-size:.82rem;text-decoration:none;">
        <i class="bi bi-arrow-left me-1"></i> Back to Login
      </a>
    </div>

    <div class="mt-4 p-3 text-center" style="background:#fff8f0;border-radius:10px;">
      <p class="mb-0" style="font-size:.78rem;color:#ea580c;">
        <i class="bi bi-info-circle me-1"></i>
        Didn't receive the OTP or it expired? Contact your system administrator to resend it.
      </p>
    </div>

  </div>
</div>

<script src="/libs/js/bootstrap.bundle.min.js"></script>
<script>
const digits = document.querySelectorAll('.otp-digit');
const hidden = document.getElementById('otpHidden');
const form   = document.getElementById('otpForm');

// Prefill if old value exists
if (hidden.value.length === 6) {
  hidden.value.split('').forEach((ch, i) => {
    digits[i].value = ch;
    digits[i].classList.add('filled');
  });
}

digits.forEach((input, idx) => {
  input.addEventListener('input', e => {
    const val = e.target.value.replace(/\D/g, '');
    e.target.value = val.slice(-1);
    e.target.classList.toggle('filled', val.length > 0);
    updateHidden();
    if (val && idx < 5) digits[idx + 1].focus();
  });

  input.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
      digits[idx - 1].value = '';
      digits[idx - 1].classList.remove('filled');
      digits[idx - 1].focus();
      updateHidden();
    }
  });

  input.addEventListener('paste', e => {
    e.preventDefault();
    const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
    pasted.split('').forEach((ch, i) => {
      if (digits[i]) {
        digits[i].value = ch;
        digits[i].classList.add('filled');
      }
    });
    updateHidden();
    if (pasted.length < 6) digits[pasted.length]?.focus();
    else digits[5].focus();
  });
});

function updateHidden() {
  hidden.value = Array.from(digits).map(d => d.value).join('');
}

// Auto-submit when all 6 filled
form.addEventListener('input', () => {
  if (hidden.value.length === 6) {
    document.getElementById('submitBtn').classList.add('btn-success');
    document.getElementById('submitBtn').style.background = '#16a34a';
    document.getElementById('submitBtn').style.borderColor = '#16a34a';
  }
});
</script>
</body>
</html>
