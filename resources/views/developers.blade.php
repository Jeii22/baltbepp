<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Developers - Balt Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        .float-animation:nth-child(2) {
            animation-delay: 0.5s;
        }

        .float-animation:nth-child(3) {
            animation-delay: 1s;
        }

        .float-animation:nth-child(4) {
            animation-delay: 1.5s;
        }

        .float-animation:nth-child(5) {
            animation-delay: 2s;
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-black/30 backdrop-blur-sm" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
          
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="/" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Home</a>
                <a href="/#book" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Book</a>
                <a href="/#about-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">About Us</a>
                <a href="/#contact-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Contact Us</a>
            </div>

            <!-- Auth area -->
            <div x-data="{ dropdownOpen: false }" class="relative">
                @auth
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-white hover:text-cyan-200 transition">
                        <span>Welcome {{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('dashboard') }}" class="ml-3 bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg transition">
                            Super Admin Dashboard
                        </a>
                    @elseif(Auth::user()->isAdmin())
                        <a href="{{ route('dashboard') }}" class="ml-3 bg-green-600 hover:bg-green-700 px-3 py-1 rounded-lg transition">
                            Admin Dashboard
                        </a>
                    @endif

                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-gray-800 hover:bg-blue-50 rounded-t-lg">
                            My Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-800 hover:bg-blue-50 rounded-b-lg">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="border border-white px-4 py-2 rounded-lg text-white hover:bg-white hover:text-blue-600 transition">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section with Same Background -->
    <div class="relative bg-cover bg-center h-screen" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="text-center text-white px-6 mb-20">
                <h1 class="text-5xl md:text-6xl font-bold mb-4">Meet Our Team</h1>
                <p class="text-xl md:text-2xl italic">The minds behind Balt Bep</p>
            </div>
        </div>

        <!-- Floating Developer Containers -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-6 max-w-6xl w-full mt-32">
                
                <!-- Developer 1: Jake Rodriguez -->
                <div class="float-animation bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 hover:shadow-blue-500/50">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center mb-4 shadow-lg">
                            <span class="text-3xl font-bold text-white">JR</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Jake Rodriguez</h3>
                        <p class="text-blue-600 font-semibold mb-3">Programmer</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Full-stack developer crafting seamless digital experiences</p>
                    </div>
                </div>

                <!-- Developer 2: Melchades Mansueto -->
                <div class="float-animation bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 hover:shadow-purple-500/50">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center mb-4 shadow-lg">
                            <span class="text-3xl font-bold text-white">MM</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Melchades Mansueto</h3>
                        <p class="text-purple-600 font-semibold mb-3">Designer 1</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Creative mind bringing visual harmony to every pixel</p>
                    </div>
                </div>

                <!-- Developer 3: Kyle Gadiano -->
                <div class="float-animation bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 hover:shadow-green-500/50">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center mb-4 shadow-lg">
                            <span class="text-3xl font-bold text-white">KG</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Kyle Gadiano</h3>
                        <p class="text-green-600 font-semibold mb-3">Designer 2</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Passionate designer creating intuitive user interfaces</p>
                    </div>
                </div>

                <!-- Developer 4: Rudelyn Illut -->
                <div class="float-animation bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 hover:shadow-orange-500/50">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-orange-700 rounded-full flex items-center justify-center mb-4 shadow-lg">
                            <span class="text-3xl font-bold text-white">RI</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Rudelyn Illut</h3>
                        <p class="text-orange-600 font-semibold mb-3">Researcher 1</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-orange-400 to-orange-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Dedicated researcher uncovering insights and solutions</p>
                    </div>
                </div>

                <!-- Developer 5: Jona Mae Illut -->
                <div class="float-animation bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 hover:shadow-pink-500/50 md:col-span-2 lg:col-span-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-pink-500 to-pink-700 rounded-full flex items-center justify-center mb-4 shadow-lg">
                            <span class="text-3xl font-bold text-white">JI</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Jona Mae Illut</h3>
                        <p class="text-pink-600 font-semibold mb-3">Researcher 2</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-pink-400 to-pink-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Analytical researcher driving innovation through data</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} Balt Bep. All rights reserved.</p>
            <p class="text-gray-500 text-sm mt-2">Built with passion by our amazing team</p>
        </div>
    </footer>

</body>
</html>
