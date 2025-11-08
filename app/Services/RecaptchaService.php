<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Central reCAPTCHA verification service.
 * Supports Google reCAPTCHA v3 (score based) and can be extended for v2.
 */
class RecaptchaService
{
    /**
     * Verify a reCAPTCHA v3 token and assert validity (throws on failure).
     *
     * @param string|null $token           The token returned by grecaptcha.execute()
     * @param string      $expectedAction  The expected action string (e.g. "login", "register")
     */
    public function assertValid(?string $token, string $expectedAction): void
    {
        if (!$token) {
            throw ValidationException::withMessages([
                'recaptcha' => 'reCAPTCHA verification is required.',
            ]);
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'recaptcha' => 'reCAPTCHA verification temporarily unavailable. Please retry.',
            ]);
        }

        $result = $response->json();

        // Basic success flag
        if (!($result['success'] ?? false)) {
            throw ValidationException::withMessages([
                'recaptcha' => 'reCAPTCHA verification failed. Please try again.',
            ]);
        }

        // Score threshold (v3 only)
        $score = $result['score'] ?? 0;
        $threshold = (float) config('services.recaptcha.score_threshold', 0.5);
        if ($score < $threshold) {
            throw ValidationException::withMessages([
                'recaptcha' => 'Suspicious activity detected (low reCAPTCHA score). Please retry.',
            ]);
        }

        // Optional action validation (prevent token reuse across forms)
        $action = $result['action'] ?? null; // present for v3 enterprise / some implementations
        if ($action && $action !== $expectedAction) {
            throw ValidationException::withMessages([
                'recaptcha' => 'Invalid reCAPTCHA action. Please refresh and try again.',
            ]);
        }

        // (Optional) Could inspect hostname: $result['hostname']
    }
}
