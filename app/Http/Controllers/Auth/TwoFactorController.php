<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorCode;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA verification form
     */
    public function show()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = session('2fa:user:id');
        $user = User::findOrFail($userId);

        if (!TwoFactorCode::verify($user, $request->code, 'login')) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is invalid or has expired.',
            ]);
        }

        // Clear 2FA session data
        session()->forget('2fa:user:id');

        // Enable 2FA in profile security settings after successful verification
        if (!$user->two_factor_enabled) {
            $user->update(['two_factor_enabled' => true]);
        }

        // Log the user in
        Auth::login($user, session('2fa:remember', false));
        session()->forget('2fa:remember');

        $request->session()->regenerate();

        // Update last login
        $user->updateLastLogin($request->ip());

        // Redirect based on role
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard')->with('success', 'Welcome back, Super Administrator!');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Administrator!');
        } else {
            return redirect()->route('customer.dashboard')->with('success', 'Welcome back!');
        }
    }

    /**
     * Resend the 2FA code
     */
    public function resend(Request $request)
    {
        $userId = session('2fa:user:id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // Generate new code
        $twoFactorCode = TwoFactorCode::createForUser($user, 'login');

        // Send notification
        $user->notify(new TwoFactorCodeNotification($twoFactorCode->code, 'login'));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    /**
     * Enable 2FA for the authenticated user
     */
    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return back()->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate and send verification code
        $twoFactorCode = TwoFactorCode::createForUser($user, 'enable_2fa');
        $user->notify(new TwoFactorCodeNotification($twoFactorCode->code, 'login'));

        return back()->with('success', 'A verification code has been sent to your email. Please verify to enable 2FA.');
    }

    /**
     * Confirm enabling 2FA
     */
    public function confirmEnable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if (!TwoFactorCode::verify($user, $request->code, 'enable_2fa')) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is invalid or has expired.',
            ]);
        }

        $user->update(['two_factor_enabled' => true]);

        return back()->with('success', 'Two-factor authentication has been enabled successfully!');
    }

    /**
     * Disable 2FA for the authenticated user
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }
}
