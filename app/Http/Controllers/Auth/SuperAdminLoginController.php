<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SuperAdminLoginController extends Controller
{
    /**
     * Get location data from IP address
     */
    private function getLocationFromIp(string $ip): array
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return [
                'country' => 'Local',
                'region' => 'Local',
                'city' => 'Localhost',
                'latitude' => null,
                'longitude' => null,
            ];
        }

        try {
            $response = Http::timeout(5)->get("http://ipapi.co/{$ip}/json/");
            $data = $response->json();

            return [
                'country' => $data['country_name'] ?? null,
                'region' => $data['region'] ?? null,
                'city' => $data['city'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'country' => null,
                'region' => null,
                'city' => null,
                'latitude' => null,
                'longitude' => null,
            ];
        }
    }

    // Show hidden superadmin login form
    public function show(): View
    {
        return view('auth.administration-login');
    }

    // Handle superadmin login via email/password
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $email = $validated['email'];
        $user = User::where('email', $email)->first();

        if (!$user || !in_array($user->role, ['super_admin', 'admin']) || !Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey($request));

            // Log failed attempt with geolocation
            $location = $this->getLocationFromIp($request->ip());
            LoginAttempt::create([
                'user_id' => $user?->id,
                'email' => $email,
                'ip_address' => $request->ip(),
                'country' => $location['country'],
                'region' => $location['region'],
                'city' => $location['city'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'user_agent' => $request->userAgent(),
                'successful' => false,
                'attempted_at' => now(),
            ]);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Log successful attempt with geolocation
        $location = $this->getLocationFromIp($request->ip());
        LoginAttempt::create([
            'user_id' => $user->id,
            'email' => $email,
            'ip_address' => $request->ip(),
            'country' => $location['country'],
            'region' => $location['region'],
            'city' => $location['city'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'user_agent' => $request->userAgent(),
            'successful' => true,
            'attempted_at' => now(),
        ]);

        Auth::login($user, $request->boolean('remember'));
        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        // Redirect based on role (use normalized checks)
        if ($user->isSuperAdmin()) {
            \Log::info('SuperAdmin login success', ['user_id' => $user->id, 'role' => $user->role]);
            return redirect()->route('superadmin.dashboard')->with('success', 'Welcome back, Super Administrator!');
        }
        if ($user->isAdmin()) {
            \Log::info('Admin login success', ['user_id' => $user->id, 'role' => $user->role]);
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Administrator!');
        }
        // Fallback: non-admin roles shouldn't reach here
        \Log::warning('Unexpected role through SuperAdminLoginController', ['user_id' => $user->id, 'role' => $user->role]);
        return redirect()->route('dashboard')->with('info', 'Logged in. Limited access.');
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        // Flag session as locked for UI
        $request->session()->put('status', 'locked');

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip()).':administration';
    }
}