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

        @keyframes neonGlow {
            0%, 100% {
                filter: drop-shadow(0 0 10px currentColor) drop-shadow(0 0 20px currentColor);
            }
            50% {
                filter: drop-shadow(0 0 20px currentColor) drop-shadow(0 0 40px currentColor);
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

        .neon-blue {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.8), 0 0 40px rgba(59, 130, 246, 0.6), 0 0 60px rgba(59, 130, 246, 0.4);
        }

        .neon-blue:hover {
            box-shadow: 0 0 30px rgba(59, 130, 246, 1), 0 0 60px rgba(59, 130, 246, 0.8), 0 0 90px rgba(59, 130, 246, 0.6);
        }

        .neon-purple {
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.8), 0 0 40px rgba(168, 85, 247, 0.6), 0 0 60px rgba(168, 85, 247, 0.4);
        }

        .neon-purple:hover {
            box-shadow: 0 0 30px rgba(168, 85, 247, 1), 0 0 60px rgba(168, 85, 247, 0.8), 0 0 90px rgba(168, 85, 247, 0.6);
        }

        .neon-green {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.8), 0 0 40px rgba(34, 197, 94, 0.6), 0 0 60px rgba(34, 197, 94, 0.4);
        }

        .neon-green:hover {
            box-shadow: 0 0 30px rgba(34, 197, 94, 1), 0 0 60px rgba(34, 197, 94, 0.8), 0 0 90px rgba(34, 197, 94, 0.6);
        }

        .neon-orange {
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.8), 0 0 40px rgba(249, 115, 22, 0.6), 0 0 60px rgba(249, 115, 22, 0.4);
        }

        .neon-orange:hover {
            box-shadow: 0 0 30px rgba(249, 115, 22, 1), 0 0 60px rgba(249, 115, 22, 0.8), 0 0 90px rgba(249, 115, 22, 0.6);
        }

        .neon-pink {
            box-shadow: 0 0 20px rgba(236, 72, 153, 0.8), 0 0 40px rgba(236, 72, 153, 0.6), 0 0 60px rgba(236, 72, 153, 0.4);
        }

        .neon-pink:hover {
            box-shadow: 0 0 30px rgba(236, 72, 153, 1), 0 0 60px rgba(236, 72, 153, 0.8), 0 0 90px rgba(236, 72, 153, 0.6);
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
        <div class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
            <div class="text-center text-white px-6 mb-20">
                <h1 class="text-5xl md:text-6xl font-bold mb-4">Meet Our Team</h1>
                <p class="text-xl md:text-2xl italic">The minds behind Balt Bep</p>
            </div>
        </div>

        <!-- Floating Developer Containers -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-6 max-w-6xl w-full mt-32">
                
                <!-- Developer 1: Jake Rodriguez -->
                <a href="https://www.facebook.com/kajake.rodriguez" target="_blank" class="float-animation neon-blue bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 block cursor-pointer">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full mb-4 shadow-lg overflow-hidden border-4 border-blue-500">
                            <img src="{{ asset('images/developers/jake.jpg') }}" alt="Jake Rodriguez" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center" style="display:none;">
                                <span class="text-3xl font-bold text-white">JR</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Jake Rodriguez</h3>
                        <p class="text-blue-600 font-semibold mb-3">Programmer</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Full-stack developer crafting seamless digital experiences</p>
                    </div>
                </a>

                <!-- Developer 2: Melchades Mansueto -->
                <a href="https://www.facebook.com/melchades.mansueto" target="_blank" class="float-animation neon-purple bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 block cursor-pointer">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full mb-4 shadow-lg overflow-hidden border-4 border-purple-500">
                            <img src="{{ asset('images/developers/melchades.jpg') }}" alt="Melchades Mansueto" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center" style="display:none;">
                                <span class="text-3xl font-bold text-white">MM</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Melchades Mansueto</h3>
                        <p class="text-purple-600 font-semibold mb-3">Designer 1</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Creative mind bringing visual harmony to every pixel</p>
                    </div>
                </a>

                <!-- Developer 3: Kyle Gadiano -->
                <a href="https://www.facebook.com/kyle.gadiano" target="_blank" class="float-animation neon-green bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 block cursor-pointer">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full mb-4 shadow-lg overflow-hidden border-4 border-green-500">
                            <img src="{{ asset('images/developers/kyle.jpg') }}" alt="Kyle Gadiano" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center" style="display:none;">
                                <span class="text-3xl font-bold text-white">KG</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Kyle Gadiano</h3>
                        <p class="text-green-600 font-semibold mb-3">Designer 2</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Passionate designer creating intuitive user interfaces</p>
                    </div>
                </a>

                <!-- Developer 4: Rudelyn Illut -->
                <a href="https://www.facebook.com/rudelyn.illut" target="_blank" class="float-animation neon-orange bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 block cursor-pointer">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full mb-4 shadow-lg overflow-hidden border-4 border-orange-500">
                            <img src="{{ asset('images/developers/rudelyn.jpg') }}" alt="Rudelyn Illut" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center" style="display:none;">
                                <span class="text-3xl font-bold text-white">RI</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Rudelyn Illut</h3>
                        <p class="text-orange-600 font-semibold mb-3">Researcher 1</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-orange-400 to-orange-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Dedicated researcher uncovering insights and solutions</p>
                    </div>
                </a>

                <!-- Developer 5: Jona Mae Illut -->
                <a href="https://www.facebook.com/jonamae.illut" target="_blank" class="float-animation neon-pink bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300 md:col-span-2 lg:col-span-1 block cursor-pointer">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full mb-4 shadow-lg overflow-hidden border-4 border-pink-500">
                            <img src="{{ asset('images/developers/jonamae.jpg') }}" alt="Jona Mae Illut" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-gradient-to-br from-pink-500 to-pink-700 flex items-center justify-center" style="display:none;">
                                <span class="text-3xl font-bold text-white">JI</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Jona Mae Illut</h3>
                        <p class="text-pink-600 font-semibold mb-3">Researcher 2</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-pink-400 to-pink-600 rounded-full mb-3"></div>
                        <p class="text-gray-600 text-sm">Analytical researcher driving innovation through data</p>
                    </div>
                </a>

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
