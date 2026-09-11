<?php

namespace App\Http\Controllers;

use App\Mail\UserOtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    public function show(Request $request): View
    {
        return view('auth.verify-otp', [
            'email' => session('unverified_email', $request->get('email', '')),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string|size:6',
        ], [
            'email.exists' => 'No account found with that email address.',
            'otp.size'     => 'OTP must be exactly 6 digits.',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->email_verified_at) {
            return redirect()->route('login')
                ->with('status', 'Your email is already verified. Please log in.');
        }

        if (!$user->otp || !$user->otp_expires_at) {
            return back()
                ->withErrors(['otp' => 'No OTP found for this account. Contact your administrator.'])
                ->withInput();
        }

        if ($user->otp_expires_at->isPast()) {
            return back()
                ->withErrors(['otp' => 'OTP has expired. Contact your administrator to send a new code.'])
                ->withInput();
        }

        if (!Hash::check($request->otp, $user->otp)) {
            return back()
                ->withErrors(['otp' => 'Incorrect OTP. Please check your email and try again.'])
                ->withInput();
        }

        $user->update([
            'email_verified_at' => now(),
            'otp'               => null,
            'otp_expires_at'    => null,
        ]);

        return redirect()->route('login')
            ->with('status', 'Email verified successfully! You can now log in.');
    }

    public function resend(User $user): RedirectResponse
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp'               => Hash::make($otp),
            'otp_expires_at'    => now()->addHours(24),
            'email_verified_at' => null,
        ]);

        try {
            Mail::to($user->email)->send(new UserOtpMail($user, $otp));
            return redirect()->route('users.index')
                ->with('success', "New OTP sent to {$user->name} ({$user->email}). Expires in 24 hours.");
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User updated but email could not be sent. Check your mail configuration.');
        }
    }
}
