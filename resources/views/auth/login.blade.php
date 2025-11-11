<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BaltBep Ticketing') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(config('services.recaptcha.version', 'v3') === 'v2')
        <!-- Google reCAPTCHA v2 Invisible -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @else
        <!-- Google reCAPTCHA v3 -->
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif
    <style>
        /* Ensure the official reCAPTCHA badge is visible at bottom-right */
        .grecaptcha-badge {
            visibility: visible !important;
            opacity: 1 !important;
            position: fixed !important;
            right: 12px !important;
            bottom: 12px !important;
            z-index: 9999 !important;
        }
        
        /* Modal styles */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 28rem;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function onLoginSubmit(e) {
            e.preventDefault();
            @if(config('services.recaptcha.version', 'v3') === 'v2')
                // v2 invisible: badge appears bottom-right; callback will submit
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.execute();
                } else {
                    e.target.submit();
                }
            @else
                // v3: execute to get score token, then submit
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'login'}).then(function(token) {
                        document.getElementById('recaptcha_token').value = token;
                        e.target.submit();
                    });
                });
            @endif
        }

        // reCAPTCHA v2 callback receives token
        function onRecaptchaV2Login(token) {
            document.getElementById('recaptcha_token').value = token;
            var form = document.querySelector('form[action="{{ route('login') }}"]');
            if (form) form.submit();
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center 
             bg-gradient-to-b from-blue-600 via-cyan-400 to-white">

    

    <!-- Card -->
    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 space-y-6">
        <!-- Move the logo above the card -->
    <div class="flex justify-center mb-6">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/baltbep-logo.png') }}" alt="BaltBep Logo" class="h-16">
        </a>
    </div>
        <!-- Title -->
        <h2 class="text-2xl font-bold text-center text-gray-900">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Welcome to Balt-Bep Ferries</h2>
        </h2>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" id="recaptcha_token" name="recaptcha_token">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request.otp') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>

            <x-input-error :messages="$errors->get('recaptcha')" class="mt-2" />

            <div class="flex items-center justify-end mt-4">
                @if(config('services.recaptcha.version', 'v3') === 'v2')
                    <x-primary-button class="ml-3 g-recaptcha"
                        data-sitekey="{{ config('services.recaptcha.site_key') }}"
                        data-callback="onRecaptchaV2Login"
                        data-size="invisible">
                        {{ __('Log in') }}
                    </x-primary-button>
                @else
                    <x-primary-button class="ml-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                @endif
            </div>
    </form>
    <script>
        // Attach the reCAPTCHA v3 handler to the login form
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.querySelector('form[action="{{ route('login') }}"]');
            if (form) {
                form.addEventListener('submit', onLoginSubmit);
            }
        });
    </script>

    <!-- Official reCAPTCHA badge is shown bottom-right -->

        <!-- Add a register link below the sign-in button -->
                <!-- Add a register link below the sign-in button -->
        <p class="text-center text-sm text-gray-600 mt-4">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">Register here</a>
        </p>
    </div>

    <!-- 2FA Verification Modal -->
    <div id="otpModal" class="modal-overlay">
        <div class="modal-content">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Verify Your Login</h2>
                <p class="text-gray-500 mt-2 text-sm">We've sent a 6-digit verification code to your email.</p>
            </div>

            <!-- Success Message -->
            <div id="otpSuccess" class="hidden mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm"></div>
            
            <!-- Error Message -->
            <div id="otpError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm"></div>

            <!-- Verification Form -->
            <form id="otpForm" method="POST" action="{{ route('two-factor.verify') }}">
                @csrf
                <div class="mb-6">
                    <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                    <input 
                        id="otp_code" 
                        type="text" 
                        name="code" 
                        maxlength="6"
                        class="block w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="000000"
                        required 
                        autofocus
                        autocomplete="one-time-code"
                    >
                    <p id="codeError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>

                <button 
                    type="submit" 
                    id="verifyBtn"
                    class="w-full px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Verify Code
                </button>
            </form>

            <!-- Resend Code -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
                <form id="resendForm" method="POST" action="{{ route('two-factor.resend') }}">
                    @csrf
                    <button 
                        type="submit" 
                        id="resendBtn"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium focus:outline-none focus:underline"
                    >
                        Resend Code
                    </button>
                </form>
            </div>

            <!-- Back to Login -->
            <div class="mt-4 text-center">
                <button onclick="closeOtpModal()" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Back to Login
                </button>
            </div>
        </div>
    </div>

    <script>
        function showOtpModal() {
            document.getElementById('otpModal').classList.add('active');
            document.getElementById('otp_code').focus();
        }

        function closeOtpModal() {
            document.getElementById('otpModal').classList.remove('active');
            document.getElementById('otp_code').value = '';
            document.getElementById('otpError').classList.add('hidden');
            document.getElementById('otpSuccess').classList.add('hidden');
            // Remove query parameter from URL
            const url = new URL(window.location);
            url.searchParams.delete('show_otp');
            window.history.replaceState({}, '', url);
        }

        // Check if we should show the modal (from query parameter)
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('show_otp') === '1') {
                showOtpModal();
            }
        });

        // Handle OTP form submission via AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const otpForm = document.getElementById('otpForm');
            if (otpForm) {
                otpForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const code = document.getElementById('otp_code').value;
                    const verifyBtn = document.getElementById('verifyBtn');
                    const errorDiv = document.getElementById('codeError');
                    const otpError = document.getElementById('otpError');
                    
                    // Clear previous errors
                    errorDiv.classList.add('hidden');
                    otpError.classList.add('hidden');
                    
                    // Disable button
                    verifyBtn.disabled = true;
                    verifyBtn.textContent = 'Verifying...';
                    
                    // Submit via fetch
                    fetch('{{ route('two-factor.verify') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ code: code })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Redirect to dashboard
                            window.location.href = data.redirect || '{{ route('dashboard') }}';
                        } else {
                            // Show error
                            errorDiv.textContent = data.message || 'Invalid or expired code.';
                            errorDiv.classList.remove('hidden');
                            verifyBtn.disabled = false;
                            verifyBtn.textContent = 'Verify Code';
                        }
                    })
                    .catch(error => {
                        otpError.textContent = 'An error occurred. Please try again.';
                        otpError.classList.remove('hidden');
                        verifyBtn.disabled = false;
                        verifyBtn.textContent = 'Verify Code';
                    });
                });
            }

            // Handle resend form
            const resendForm = document.getElementById('resendForm');
            if (resendForm) {
                resendForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const resendBtn = document.getElementById('resendBtn');
                    const successDiv = document.getElementById('otpSuccess');
                    const errorDiv = document.getElementById('otpError');
                    
                    resendBtn.disabled = true;
                    resendBtn.textContent = 'Sending...';
                    
                    fetch('{{ route('two-factor.resend') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            successDiv.textContent = 'A new code has been sent to your email.';
                            successDiv.classList.remove('hidden');
                            errorDiv.classList.add('hidden');
                        } else {
                            errorDiv.textContent = data.message || 'Failed to resend code.';
                            errorDiv.classList.remove('hidden');
                        }
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend Code';
                    })
                    .catch(error => {
                        errorDiv.textContent = 'An error occurred. Please try again.';
                        errorDiv.classList.remove('hidden');
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend Code';
                    });
                });
            }
        });
    </script>
</body>
</html>
```
    </div>
</body>
</html>