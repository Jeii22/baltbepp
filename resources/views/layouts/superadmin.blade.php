<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt-Bep Admin Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Fixed Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 h-full bg-gradient-to-b from-blue-900 via-blue-800 to-blue-700 text-white shadow-xl flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out"
             x-bind:class="{ '-translate-x-full': !sidebarOpen }"   
             :class="{ 'translate-x-0': sidebarOpen }"
             x-show="sidebarOpen || window.innerWidth >= 768"
             x-transition:enter="transition-transform ease-in-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition-transform ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <!-- Logo -->
            <div class="p-4 flex justify-center border-b border-white/30 flex-shrink-0">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/baltbep-logo.png') }}" 
                         alt="BaltBep Logo" 
                         class="mx-auto mb-2 h-20 w-30 hover:scale-105 transition-transform duration-200">
                        <div class="p-4 text-2xl font-bold text-center border-b border-white/30">
                        {{ Auth::user()->getRoleDisplayName() }}
                    </div>
                </a>
            </div>

            <!-- Sidebar Navigation - Scrollable if needed -->
            <div class="flex-1 overflow-y-auto">
                <ul class="space-y-2 p-4">
                    <!-- Dashboard - Available to all admin roles -->
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : '' }}">
                            Dashboard
                        </a>
                    </li>
                    
                    <!-- Booking Management - Available to both SuperAdmin and Admin -->
                    <li>
                        <a href="{{ route('bookings.index') }}" 
                           class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('bookings.*') ? 'bg-white/20 text-white' : '' }}">
                            Booking Management
                        </a>
                    </li>
                    
                    <!-- Payment Management - Available to both SuperAdmin and Admin -->
                    <li>
                        <a href="{{ route('admin.payment-methods.index') }}" 
                           class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('admin.payment-methods.*') ? 'bg-white/20 text-white' : '' }}">
                            Payment Management
                            <span class="block text-xs opacity-80">Digital Wallets, Cards & COD</span>
                        </a>
                    </li>
                    
                    <!-- Reports - Available to both SuperAdmin and Admin -->
                    <li>
                        <a href="{{ route('reports.index') }}" 
                           class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('reports.*') ? 'bg-white/20 text-white' : '' }}">
                            Reports
                        </a>
                    </li>
                    
                    @if(Auth::user()->isSuperAdmin())
                        <!-- SuperAdmin Only Features -->
                        <li class="pt-4 border-t border-white/30">
                            <span class="block px-4 py-2 text-xs uppercase tracking-wide text-white/60 font-semibold">Super Admin Only</span>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}" 
                               class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('users.*') ? 'bg-white/20 text-white' : '' }}">
                                User Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('trips.index') }}" 
                               class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('trips.*') ? 'bg-white/20 text-white' : '' }}">
                                Trip Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('fares.index') }}" 
                               class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('fares.*') ? 'bg-white/20 text-white' : '' }}">
                                Fare Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}" 
                               class="block px-4 py-2 rounded hover:bg-white/20 {{ request()->routeIs('settings.*') ? 'bg-white/20 text-white' : '' }}">
                                Settings
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full overflow-hidden md:ml-64">
            
            <!-- Fixed Top Navigation -->
            <header class="bg-white shadow px-4 sm:px-6 py-3 flex justify-between items-center flex-shrink-0">
                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h1 class="text-lg font-semibold text-gray-900">Balt-Bep Shipping Express</h1>

                <!-- User Dropdown -->
                <div class="flex items-center space-x-2">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                        <span class="mr-2">{{ auth()->user()->name ?? 'Guest' }}</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Edit Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                Log Out
                            </button>
                        </form>
                    </div>
                    </div>
            </header>

            <!-- Scrollable Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            timer: 2000,
            showConfirmButton: false
        });
    });
    </script>
    @endif
    @if(session('error'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error'))
        });
    });
    </script>
    @endif
    <script>
    // Intercept forms with data-confirm for SweetAlert confirmation
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form[data-confirm]')?.forEach((form) => {
            form.addEventListener('submit', function(e) {
                const msg = this.getAttribute('data-confirm') || 'Are you sure?';
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Please confirm',
                    text: msg,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            }, { once: true });
        });
    });
    </script>
</body>
</html>
