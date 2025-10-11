<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OtpPasswordReset;

class OtpPasswordResetController extends Controller
{
    /**
     * Display the OTP password reset request view.
     */
    public function create()
    {
        return view('auth.forgot-password-otp');
    }

    /**
     * Handle an incoming OTP password reset request.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 10 minutes
        Cache::put('password_reset_otp_' . $user->id, $otp, now()->addMinutes(10));

        // Send OTP via email
        Mail::to($user->email)->send(new OtpPasswordReset($otp));

        return back()->with(['status' => 'OTP sent to your email address.', 'email' => $request->email]);
    }

    /**
     * Display the OTP verification form.
     */
    public function showVerifyForm(Request $request)
    {
        if (!$request->has('email')) {
            return redirect()->route('password.request.otp');
        }

        return view('auth.verify-otp', ['email' => $request->email]);
    }

    /**
     * Verify the OTP and show reset form.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->first();
        $cachedOtp = Cache::get('password_reset_otp_' . $user->id);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // OTP verified, allow password reset
        Cache::forget('password_reset_otp_' . $user->id);
        Cache::put('password_reset_verified_' . $user->id, true, now()->addMinutes(15));

        return redirect()->route('password.reset.otp', ['email' => $request->email]);
    }

    /**
     * Show the password reset form after OTP verification.
     */
    public function showResetForm(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!Cache::get('password_reset_verified_' . $user->id)) {
            return redirect()->route('password.request.otp');
        }

        return view('auth.reset-password-otp', ['email' => $request->email]);
    }

    /**
     * Reset the password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (!Cache::get('password_reset_verified_' . $user->id)) {
            return redirect()->route('password.request.otp');
        }

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        Cache::forget('password_reset_verified_' . $user->id);

        return redirect()->route('login')->with('status', 'Password has been reset successfully.');
    }
}