<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied - Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Error Icon -->
            <div class="w-20 h-20 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                </svg>
            </div>

            <!-- Error Message -->
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Access Denied</h1>
            <p class="text-gray-600 mb-6">
                {{ $exception->getMessage() ?: 'You do not have permission to access this area.' }}
            </p>

            <!-- User Role Info -->
            @auth
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>Current Role:</strong> {{ auth()->user()->getRoleDisplayName() }}
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        You are logged in as {{ auth()->user()->name }}
                    </p>
                </div>
            @endauth


            <!-- Contact Support -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Need help? Contact our support team for assistance.
                </p>
            </div>
        </div>
    </div>
</body>
</html>