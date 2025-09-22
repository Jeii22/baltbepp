<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt-Bep Super Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="h-screen flex overflow-hidden">

        <!-- Fixed Sidebar -->
        <div class="w-64 h-full bg-gradient-to-b from-blue-600 via-cyan-500 to-white text-white shadow-lg flex flex-col">
            
            <!-- Logo -->
            <div class="p-4 flex justify-center border-b border-white/20 flex-shrink-0">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/baltbep-logo.png') }}" 
                         alt="BaltBep Logo" 
                         class="mx-auto mb-2 h-20 w-30 hover:scale-105 transition-transform duration-200">
                        <div class="p-4 text-2xl font-bold text-center border-b border-blue-700">
                        Super Admin
                    </div>
                </a>
            </div>

            <!-- Sidebar Navigation - Scrollable if needed -->
            <div class="flex-1 overflow-y-auto">
                <ul class="space-y-2 p-4">
                    <li><a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-white/20">Dashboard</a></li>
                    <li><a href="{{ route('users.index') }}" class="block px-4 py-2 rounded hover:bg-white/20">User Management</a></li>
                    <li><a href="{{ route('trips.index') }}" class="block px-4 py-2 rounded hover:bg-white/20">Trip Management</a></li>
                    <li><a href="{{ route('fares.index') }}" class="block px-4 py-2 rounded hover:bg-white/20">Fare Management</a></li>
                    <li><a href="{{ route('bookings.index') }}" class="block px-4 py-2 rounded hover:bg-white/20">Booking Management</a></li>
                    <li>
                        <a href="{{ route('admin.payment-methods.index') }}" class="block px-4 py-2 rounded hover:bg-white/20">
                            Payment Management
                            <span class="block text-xs opacity-80">Digital Wallets, Cards & COD</span>
                        </a>
                    </li>
                    <li><a href="{{ route('reports.index') }}" class="block px-4 py-2 rounded hover:bg-white/20">Reports</a></li>
                    <li><a href="{{ route('settings.index') }}" class="block px-4 py-2 rounded hover:bg-white/20">Settings</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full overflow-hidden">
            
            <!-- Fixed Top Navigation -->
            <header class="bg-white border-b border-gray-200 shadow px-6 py-3 flex justify-between items-center flex-shrink-0">
                <h1 class="text-lg font-semibold">Balt-Bep Shipping Express</h1>

                <!-- Dropdown -->
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
