<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $cfg = config('services.google');
        if (empty($cfg['client_id']) || empty($cfg['client_secret'])) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Google sign-in is not configured. Set GOOGLE_CLIENT_ID/GOOGLE_CLIENT_SECRET in .env.']);
        }
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Retry in stateless mode to tolerate local session/cookie issues
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Throwable $e2) {
                return redirect()->route('login')->withErrors([
                    'google' => 'Login failed due to session/state mismatch. Clear cookies and ensure you use the same host (localhost vs 127.0.0.1).'
                ]);
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'redirect_uri_mismatch')) {
                return redirect()->route('login')->withErrors([
                    'google' => 'Redirect URI mismatch. In Google Console, add: '.config('services.google.redirect')
                ]);
            }
            return redirect()->route('login')->withErrors([
                'google' => 'Google login failed: '.$msg
            ]);
        }

        // Ensure only normal users (not admin/super_admin)
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user && in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'This account is not allowed to sign in with Google.']);
        }

        if (! $user) {
            $name = $googleUser->getName() ?: $googleUser->getNickname() ?: $googleUser->getEmail();
            $user = User::create([
                'email' => $googleUser->getEmail(),
                'name' => $name,
                'first_name' => (string) \Illuminate\Support\Str::of($name)->before(' '),
                'last_name' => (string) \Illuminate\Support\Str::of($name)->after(' '),
                'username' => \Illuminate\Support\Str::slug((($googleUser->getNickname() ?: \Illuminate\Support\Str::random(6)).'-'.\Illuminate\Support\Str::random(4))),
                'role' => 'user',
                // Random password to satisfy not-null; not used for login
                'password' => Hash::make(Str::random(32)),
            ]);
        }

        Auth::login($user, remember: true);

        // After Google login, go back to the main page (welcome)
        return redirect()->intended(route('welcome'));
    }
}
