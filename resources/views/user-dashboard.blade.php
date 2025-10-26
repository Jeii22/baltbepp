<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-white text-gray-800">

    <nav class="absolute top-0 left-0 w-full z-20 bg-black/30 backdrop-blur-sm" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <button @click="open = !open" class="md:hidden text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="hidden md:flex space-x-8 text-white font-medium" x-show="open || window.innerWidth >= 768" :class="{ 'flex flex-col space-y-4 mt-4': open && window.innerWidth < 768 }">
            </div>
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

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
                        
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

    @php
        $user = auth()->user();
        $recentBookings = \App\Models\Booking::where('user_id', $user->id)
            ->with('trip')
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    <div class="relative bg-cover bg-center h-[60vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            
        </div>
    </div>

    <div class="relative -mt-48 max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-3xl shadow-2xl ring-1 ring-black/5 p-8 md:p-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-sm uppercase tracking-widest text-blue-600">Account Overview</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Hello, {{ $user->display_name ?? $user->name }}</h2>
                <p class="text-gray-600 mt-3 max-w-xl">View your profile details, track recent bookings, and plan your next trip with the same polished experience you love from our welcome page.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('profile.user-edit') }}" class="bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 transition shadow">
                    Edit Profile
                </a>
                <a href="{{ route('welcome') }}" class="bg-white text-blue-600 px-5 py-3 rounded-xl border border-blue-200 hover:bg-blue-50 transition shadow">
                    Book a Trip
                </a>
            </div>
        </div>
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="col-span-1 bg-white rounded-2xl shadow-lg border border-blue-100 p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Profile Snapshot</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Name</p>
                        <p class="text-lg text-gray-900">{{ $user->display_name ?? $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Email</p>
                        <p class="text-lg text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Registered Since</p>
                        <p class="text-lg text-gray-900">{{ $user->created_at ? \Illuminate\Support\Carbon::parse($user->created_at)->format('M d, Y') : 'Recently' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-span-1 lg:col-span-2 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg border border-blue-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-blue-700">Recent Bookings</h3>
                    <a href="{{ route('bookings.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">View all</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentBookings as $booking)
                        <div class="bg-white/70 backdrop-blur-sm border border-blue-100 rounded-xl p-4 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">#{{ $booking->id }} · {{ $booking->full_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $booking->origin }} → {{ $booking->destination }}</p>
                                    <p class="text-sm text-gray-500">{{ optional($booking->trip)?->departure_time?->format('M d, Y H:i') ?? 'TBA' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">₱{{ number_format($booking->total_amount, 2) }}</p>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">No recent bookings yet. Start your next adventure today.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <section class="py-16 bg-blue-50 mt-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                    <p class="text-sm uppercase tracking-widest text-blue-600">Next Steps</p>
                    <h3 class="text-xl font-bold text-gray-900 mt-2">Plan Another Trip</h3>
                    <p class="text-gray-600 mt-3">Explore routes, schedules, and fares with our booking engine.</p>
                    <a href="{{ route('booking.schedule') }}" class="inline-flex items-center mt-6 text-blue-600 font-semibold hover:text-blue-800">Search Trips</a>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                    <p class="text-sm uppercase tracking-widest text-blue-600">Account</p>
                    <h3 class="text-xl font-bold text-gray-900 mt-2">Update Information</h3>
                    <p class="text-gray-600 mt-3">Keep your contact and passenger details accurate for smoother bookings.</p>
                    <a href="{{ route('profile.user-edit') }}" class="inline-flex items-center mt-6 text-blue-600 font-semibold hover:text-blue-800">Manage Profile</a>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                    <p class="text-sm uppercase tracking-widest text-blue-600">Support</p>
                    <h3 class="text-xl font-bold text-gray-900 mt-2">Need Assistance?</h3>
                    <p class="text-gray-600 mt-3">Our team is ready to help with bookings, payments, and travel questions.</p>
                    <a href="mailto:support@baltbep.com" class="inline-flex items-center mt-6 text-blue-600 font-semibold hover:text-blue-800">Contact Support</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-blue-700">Ready for your next voyage?</h2>
                <p class="text-gray-600 mt-4">Access exclusive perks, track your bookings, and enjoy the elegance of our welcome experience.</p>
            </div>
            <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                <a href="{{ route('booking.schedule') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl shadow hover:bg-blue-700 transition">Start Booking</a>
                <a href="{{ route('bookings.index') }}" class="bg-white text-blue-600 px-6 py-3 rounded-xl border border-blue-200 shadow hover:bg-blue-50 transition">View My Bookings</a>
            </div>
        </div>
    </section>

</body>
</html>
