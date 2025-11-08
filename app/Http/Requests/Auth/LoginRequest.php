<?php

namespace App\Http\Requests\Auth;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'recaptcha_token' => ['required', 'string'],
        ];
    }

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


    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Perform reCAPTCHA verification via service
        app(\App\Services\RecaptchaService::class)->assertValid($this->input('recaptcha_token'), 'login');

        $email = $this->string('email');
        $user = User::where('email', $email)->first();

        // Check if account is locked
        if ($user && $user->isLocked()) {
            $lockedUntil = \Carbon\Carbon::parse($user->locked_until);
            $minutes = now()->diffInMinutes($lockedUntil);
            throw ValidationException::withMessages([
                'email' => "Your account has been locked due to multiple failed login attempts. Please try again in {$minutes} minutes.",
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Increment failed attempts for the user
            if ($user) {
                $user->incrementFailedAttempts();
            }

            // Log failed attempt
            $location = $this->getLocationFromIp($this->ip());
            LoginAttempt::create([
                'user_id' => $user?->id,
                'email' => $email,
                'ip_address' => $this->ip(),
                'country' => $location['country'],
                'region' => $location['region'],
                'city' => $location['city'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'user_agent' => $this->userAgent(),
                'successful' => false,
                'attempted_at' => now(),
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Reset failed attempts on successful login
        if ($user) {
            $user->resetFailedAttempts();
            $user->updateLastLogin($this->ip());
        }

        // Log successful attempt
        $location = $this->getLocationFromIp($this->ip());
        LoginAttempt::create([
            'user_id' => $user?->id,
            'email' => $email,
            'ip_address' => $this->ip(),
            'country' => $location['country'],
            'region' => $location['region'],
            'city' => $location['city'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'user_agent' => $this->userAgent(),
            'successful' => true,
            'attempted_at' => now(),
        ]);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Reduced to 3 attempts to match account lockout
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Flag session as locked for UI feedback
        session(['status' => 'locked']);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
