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

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1 relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10" 
                           placeholder="you@example.com">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        📧
                    </span>
                </div>
            </div>

            <!-- Password -->
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
            </div>

            <!-- Confirm Password -->
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

            <!-- Button -->
            <button type="submit" 
                    class="w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-lg shadow hover:bg-gray-800 transition">
                Create account
            </button>
        </form>
    </div>
</body>
</html>
