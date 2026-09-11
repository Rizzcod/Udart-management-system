<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Your UDART Account</title>
<style>
  body { margin:0; padding:0; background:#eef1f8; font-family:'Segoe UI',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(28,63,170,.1); }
  .header { background:linear-gradient(135deg,#1c3faa 0%,#2563eb 100%); padding:36px 40px; text-align:center; }
  .header-icon { width:64px; height:64px; background:rgba(255,255,255,.18); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px; }
  .header h1 { color:#fff; margin:0; font-size:22px; font-weight:700; letter-spacing:-.3px; }
  .header p { color:rgba(255,255,255,.7); margin:4px 0 0; font-size:13px; }
  .body { padding:36px 40px; }
  .greeting { font-size:16px; color:#111827; font-weight:600; margin-bottom:8px; }
  .intro { font-size:14px; color:#6b7280; line-height:1.6; margin-bottom:28px; }
  .otp-box { background:#f0f6ff; border:2px dashed #1c3faa; border-radius:12px; padding:28px 20px; text-align:center; margin-bottom:28px; }
  .otp-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#6b7280; margin-bottom:12px; }
  .otp-code { font-size:42px; font-weight:800; letter-spacing:12px; color:#1c3faa; font-family:'Courier New',monospace; }
  .otp-expiry { font-size:12px; color:#ea580c; margin-top:10px; font-weight:600; }
  .divider { height:1px; background:#f0f3fb; margin:24px 0; }
  .steps { margin-bottom:28px; }
  .steps h3 { font-size:13px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin-bottom:14px; }
  .step { display:flex; align-items:flex-start; gap:12px; margin-bottom:12px; }
  .step-num { width:24px; height:24px; background:#1c3faa; color:#fff; border-radius:50%; font-size:12px; font-weight:700; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
  .step-text { font-size:13px; color:#374151; line-height:1.5; }
  .verify-btn { display:block; background:#1c3faa; color:#fff !important; text-decoration:none; text-align:center; padding:14px 32px; border-radius:10px; font-size:15px; font-weight:700; margin-bottom:20px; }
  .security { background:#fff5f5; border-left:4px solid #dc2626; border-radius:8px; padding:14px 16px; font-size:12px; color:#991b1b; line-height:1.5; margin-bottom:24px; }
  .footer { background:#f8faff; padding:24px 40px; text-align:center; border-top:1px solid #f0f3fb; }
  .footer p { font-size:12px; color:#9ca3af; margin:0; line-height:1.6; }
  .footer a { color:#1c3faa; text-decoration:none; }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="header-icon">
      <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><path fill="#fff" d="M4 7h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Zm0 0 8 5 8-5"/><path stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Zm0 0 8 5 8-5"/></svg>
    </div>
    <h1>UDART MMS</h1>
    <p>Intelligent Maintenance Management System</p>
  </div>

  <div class="body">
    <div class="greeting">Hello, {{ $user->name }}!</div>
    <p class="intro">
      Your UDART MMS account has been created by the system administrator.
      To activate your account and start using the system, please verify your email address
      by entering the OTP code below.
    </p>

    <div class="otp-box">
      <div class="otp-label">Your One-Time Password (OTP)</div>
      <div class="otp-code">{{ $otp }}</div>
      <div class="otp-expiry">⏰ Expires in 24 hours — {{ now()->addHours(24)->format('d M Y, H:i') }}</div>
    </div>

    <div class="steps">
      <h3>How to verify your account</h3>
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-text">Click the button below to go to the verification page.</div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-text">Enter your email address: <strong>{{ $user->email }}</strong></div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-text">Enter the 6-digit OTP code shown above.</div>
      </div>
      <div class="step">
        <div class="step-num">4</div>
        <div class="step-text">Once verified, log in using the password provided by your administrator.</div>
      </div>
    </div>

    <a href="{{ url('/verify-otp?email='.urlencode($user->email)) }}" class="verify-btn">
      Verify My Email Address →
    </a>

    <div class="security">
      <strong>Security Notice:</strong> This OTP is confidential and should not be shared with anyone.
      UDART MMS staff will never ask you for your OTP or password. If you did not request this,
      please contact your system administrator immediately.
    </div>

    <div class="divider"></div>
    <p style="font-size:13px;color:#6b7280;margin:0;">
      Account details: <strong>{{ $user->email }}</strong><br>
      Role: <strong>{{ $user->getRoleNames()->first() ?? 'User' }}</strong>
      @if($user->department) · {{ $user->department }}@endif
    </p>
  </div>

  <div class="footer">
    <p>
      This email was sent by <a href="{{ url('/') }}">UDART Maintenance Management System</a>.<br>
      University of Dar es Salaam Transportation Department<br>
      &copy; {{ date('Y') }} UDART MMS. All rights reserved.
    </p>
  </div>

</div>
</body>
</html>
