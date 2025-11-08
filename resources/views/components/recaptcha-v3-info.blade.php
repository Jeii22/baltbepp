<div class="text-xs text-gray-500 mt-4 space-y-1">
    <p>
        This site is protected by reCAPTCHA and the Google
        <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="underline">Privacy Policy</a>
        and
        <a href="https://policies.google.com/terms" target="_blank" rel="noopener" class="underline">Terms of Service</a>
        apply.
    </p>
    @if(app()->environment('local'))
        <p class="opacity-75">Dev mode: reCAPTCHA action context <code>{{ $action ?? (Route::currentRouteName() === 'login' ? 'login' : (Route::currentRouteName() === 'register' ? 'register' : 'unknown')) }}</code>.</p>
    @endif
</div>
