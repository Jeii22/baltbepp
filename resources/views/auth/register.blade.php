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
</head>
<body class="min-h-screen flex items-center justify-center 
             bg-gradient-to-b from-blue-600 via-cyan-400 to-white">

    <!-- Card -->
    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 space-y-6">
        <a href="{{ route('login') }}" class="flex items-center text-sm text-gray-500 hover:text-gray-800">
            ← Back to sign in
        </a>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-center text-gray-900">
            Create your account
        </h2>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <div class="mt-1 relative">
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="Juan Dela Cruz">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        👤
                    </span>
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1 relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="you@gmail.com">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        📧
                    </span>
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1 relative">
                    <input id="password" type="password" name="password" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="Min. 8 characters">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        🔒
                    </span>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="mt-1 relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="Re-enter password">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        🔒
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Captcha</label>
                <div class="mt-1 flex items-center justify-between gap-3">
                    <span class="text-sm font-semibold text-gray-900">{{ $captchaQuestion }}</span>
                    <a href="{{ route('register') }}" class="text-xs text-blue-600 hover:text-blue-800">Refresh</a>
                </div>
                <input id="captcha" type="text" name="captcha" required
                       class="mt-2 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                       placeholder="Answer here">
                @error('captcha')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-start space-x-3">
                <input id="terms" type="checkbox" name="terms" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('terms') ? 'checked' : '' }}>
                <label for="terms" class="text-sm text-gray-600">
                    I agree to the <a href="{{ route('terms-of-service') }}" class="text-blue-600 hover:text-blue-800">Terms of Service</a> and
                    <a href="{{ route('privacy-policy') }}" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>.
                </label>
            </div>
            @error('terms')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit"
                    class="w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-lg shadow hover:bg-gray-800 transition">
                Create account
            </button>
        </form>
    </div>
</body>
</html>
