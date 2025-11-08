## reCAPTCHA Integration

This application uses Google reCAPTCHA v3 (score-based, invisible) on login and registration to mitigate automated abuse.

### 1. Environment Variables

Add the following to your `.env` (never commit real secrets):

```
RECAPTCHA_PUBLIC_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_SCORE_THRESHOLD=0.5
```

Adjust the threshold (0.0–1.0). A higher value (e.g. 0.7) is stricter.

### 2. How It Works

The front-end executes `grecaptcha.execute(site_key, {action: 'login'})` or `{action: 'register'}` just before form submission, placing the token in a hidden input `recaptcha_token`.

The backend (centralized in `App\Services\RecaptchaService`) posts the token to Google, checks:

* success flag
* score against `RECAPTCHA_SCORE_THRESHOLD`
* optional action consistency (prevents token reuse across forms)

On failure, a validation error keyed `recaptcha` is returned and surfaced via SweetAlert / inline feedback.

### 3. Visible Attribution

reCAPTCHA v3 does not render a floating badge automatically. Google requires attribution. We include a Blade partial: `resources/views/components/recaptcha-v3-info.blade.php` with required Privacy & Terms links.

If you prefer the *visible checkbox* experience, switch to reCAPTCHA v2 (checkbox) or invisible v2:

1. Create keys for v2 in Google admin console.
2. Add a feature flag in `.env` like `RECAPTCHA_VERSION=v2`.
3. Render `<div class="g-recaptcha" data-sitekey="..."></div>` and load `https://www.google.com/recaptcha/api.js`.
4. Adjust the verification service to skip score checks and rely on success only.

### 4. Extending the Service

`App\Services\RecaptchaService` can be extended for: 
* v2 support (omit score + action; check `success` only)
* enterprise features (challenge protection, risk analysis)
* caching results for repeated tokens (normally unnecessary)

### 5. Troubleshooting

| Symptom | Cause | Fix |
|---------|-------|-----|
| Always fails | Wrong secret key | Verify `.env` and clear config cache (`php artisan config:clear`). |
| Low scores | Legitimate users flagged | Lower `RECAPTCHA_SCORE_THRESHOLD` to 0.3–0.4. |
| Token missing | JS not executed | Ensure no JS errors; check CSP allows `https://www.google.com` & `https://www.gstatic.com`. |
| Action mismatch | Token reused between pages | Confirm each form passes correct `{action: 'login'}` or `{action: 'register'}`. |

### 6. Security Notes

* Treat network errors as failures (fail closed) to avoid bypass.
* Validate action string to reduce token replay risk.
* Keep keys secret; rotate periodically.
* Log aggregate failure rates (future enhancement) to detect credential stuffing.

### 7. Testing

Feature tests: `RecaptchaLoginTest` and `RecaptchaRegistrationTest` fake the Google API response using `Http::fake()` allowing deterministic score scenarios.

### 8. Accessibility

Because v3 is invisible, ensure alternative rate limiting / anomaly detection for users with JS disabled—currently such submissions will fail recaptcha and prompt a retry.

### 9. Next Steps

* Add metrics logging for score distributions.
* Introduce fallback (simple math captcha) if reCAPTCHA API unreachable.
* Implement configurable version switch (v2 ↔ v3) in `config/services.php`.
