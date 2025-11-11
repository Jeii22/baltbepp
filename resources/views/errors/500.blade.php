
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error | Balt Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-gradient-to-br from-cyan-100 via-blue-100 to-white min-h-screen flex flex-col">
    <!-- Navbar (minimal, logo only) -->
    <nav class="w-full z-20 bg-black/30 backdrop-blur-sm py-2 px-4 flex items-center">
        <a href="/" class="flex items-center space-x-2">
            <img src="{{ asset('images/baltbep-logo.png') }}" class="h-16 md:h-20" alt="BaltBep Logo">
        </a>
    </nav>

    <!-- Hero Section -->
    <div class="flex-1 flex flex-col justify-center items-center relative">
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('/images/barko.png'); z-index: 0;"></div>
        <div class="relative z-10 max-w-xl w-full px-6 py-12 bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 flex flex-col items-center">
            <h1 class="text-4xl md:text-5xl font-bold text-blue-700 mb-4 text-center">Oops! Something went wrong</h1>
            <p class="text-lg text-gray-700 mb-2 text-center">{{ $message ?? 'An unexpected error occurred. Please try again later.' }}</p>
            <p class="text-sm text-gray-500 mb-6 text-center">If this continues, please contact support and include the time of the error.</p>
            <div class="flex gap-4 justify-center">
                <a href="/" class="px-6 py-3 rounded-lg bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition">Go to Home</a>
                <a href="javascript:location.reload()" class="px-6 py-3 rounded-lg border border-blue-600 text-blue-700 font-semibold bg-white hover:bg-blue-50 transition">Reload</a>
            </div>
        </div>
    </div>

    <footer class="w-full text-center text-gray-400 py-4 text-sm">
        &copy; {{ date('Y') }} Balt Bep. All rights reserved.
    </footer>
</body>
</html>
