<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @media (max-width: 768px) {
            nav .nav-links {
                display: none; /* Hide navigation links */
            }
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- SweetAlert for login welcome -->
    @auth
        @if(session('just_logged_in'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome {{ Auth::user()->name }}',
                        text: 'Ready to take a Trip',
                        confirmButtonColor: '#3085d6',
                        timer: 3500,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif
    @endauth
    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-black/30 backdrop-blur-sm" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
          
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium" x-show="open || window.innerWidth >= 768" :class="{ 'flex flex-col space-y-4 mt-4': open && window.innerWidth < 768 }">
                <a href="#book" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Book</a>
                <a href="#promos" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Promos</a>
                <a href="#routes" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Routes</a>
                <a href="#why-choose-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Why Choose Us</a>
                <a href="#about-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">About Us</a>
                <a href="#contact-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Contact Us</a>
            </div>
            <!-- Auth area: show user name if logged in, otherwise Sign In -->
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
                        <!-- My Profile -->
                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-gray-800 hover:bg-blue-50 rounded-t-lg">
                            My Profile
                        </a>
                        <!-- Logout -->
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

    <!-- Hero Section -->
        <div class="relative bg-cover bg-center h-[80vh]" style="background-image: url('/images/barko.png');">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <div class="text-center text-white px-6">
                    <h1 class="text-4xl md:text-5xl font-bold">Take you where the sea takes your destination</h1>
                    <p class="mt-2 text-2xl italic">Adventures await!</p>
                </div>
            </div>
        </div>

        <!-- Trip Search Box -->
<div id="book" class="relative -mt-64 max-w-5xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
    <h2 class="text-2xl font-bold mb-2 text-gray-800">Where’s your next adventure?</h2>
    <p class="text-gray-600 mb-6">Let’s make your next trip one to remember, book now!</p>

    <!-- Trip Type -->
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <div class="inline-flex rounded-lg bg-gray-100 p-1">
            <label class="flex items-center px-3 py-2 rounded-md cursor-pointer text-sm font-medium transition data-[checked=true]:bg-white data-[checked=true]:shadow">
                <input type="radio" name="tripType" value="oneway" class="tripType hidden" checked>
                One-way
            </label>
            <label class="flex items-center px-3 py-2 rounded-md cursor-pointer text-sm font-medium transition data-[checked=true]:bg-white data-[checked=true]:shadow" data-checked="true">
                <input type="radio" name="tripType" value="round" class="tripType hidden" >
                Round Trip
            </label>
        </div>
        <p class="text-xs text-gray-500">Swap ports with the arrow. Return Date appears for round trips.</p>
    </div>

    <!-- Grid for Inputs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-center">

        <!-- From / To -->
        <div class="col-span-2 grid grid-cols-7 gap-3 items-center">
            <div class="col-span-3">
                <label class="text-xs font-semibold text-gray-600 mb-1 block">From</label>
                <div class="relative">
                    <select id="fromSelect" class="border rounded-lg px-4 py-3 w-full focus:ring-2 focus:ring-blue-500 appearance-none">
                        <option value="Bantayan" selected>Bantayan</option>
                        <option value="Cadiz">Cadiz</option>
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">▾</span>
                </div>
            </div>
            <div class="col-span-1 flex justify-center items-end pb-1">
                <button type="button" id="tripArrow" class="cursor-pointer text-xl bg-blue-100 text-blue-600 px-3 py-2 rounded-full shadow hover:bg-blue-200" title="Swap" aria-label="Swap origin and destination">⇆</button>
            </div>
            <div class="col-span-3">
                <label class="text-xs font-semibold text-gray-600 mb-1 block">To</label>
                <div class="relative">
                    <select id="toSelect" class="border rounded-lg px-4 py-3 w-full focus:ring-2 focus:ring-blue-500 appearance-none">
                        <option value="Bantayan">Bantayan</option>
                        <option value="Cadiz" selected>Cadiz</option>
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">▾</span>
                </div>
            </div>
        </div>

        <!-- Date -->
        <div>
            <label for="departure_date" class="text-sm font-semibold mb-1 block">Departure Date</label>
            <input type="date" id="departure_date" name="departure_date" 
                   class="border rounded-lg px-4 py-3 w-full focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Return Date (only for Round Trip) -->
        <div id="returnDateContainer" class="hidden">
            <label for="return_date" class="text-sm font-semibold mb-1 block">Return Date</label>
            <input type="date" id="return_date" name="return_date" 
                   class="border rounded-lg px-4 py-3 w-full focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Passengers -->
        <div class="relative">
            <label class="text-sm font-semibold mb-1 block">Passengers</label>
            <button type="button" id="passengerDropdownBtn" 
                class="border rounded-lg px-4 py-3 w-full text-left focus:ring-2 focus:ring-blue-500">
                <span id="totalPassengers">1 Adult</span>
            </button>

            <!-- Dropdown -->
            <div id="passengerDropdown" 
                 class="hidden absolute z-20 mt-2 w-80 bg-white border rounded-xl shadow-lg p-4">

                @php
                    $passengerTypeMap = [
                        'Regular' => ['key' => 'adult', 'label' => 'Adult', 'description' => 'Ages 12+ years old', 'default' => 1],
                        'Child (2-11)' => ['key' => 'child', 'label' => 'Child', 'description' => 'Ages 2-11', 'default' => 0],
                        'Infant' => ['key' => 'infant', 'label' => 'Infant', 'description' => 'Under 2', 'default' => 0],
                        'Senior Citizen / PWD' => ['key' => 'pwd', 'label' => 'PWD/Senior', 'description' => 'Persons With Disability / Senior Citizens', 'default' => 0],
                        'Student' => ['key' => 'student', 'label' => 'Student', 'description' => 'With valid student ID', 'default' => 0],
                    ];
                    $fareLookup = $fares->keyBy('passenger_type');
                @endphp

                @foreach($passengerTypeMap as $fareType => $typeInfo)
                    @php
                        $fareEntry = $fareLookup->get($fareType);
                        $price = $fareEntry ? $fareEntry->price : 0;
                    @endphp
                    <div class="flex items-center justify-between {{ !$loop->last ? 'mb-3' : '' }}">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold">{{ $typeInfo['label'] }}</p>
                                <p class="text-sm font-medium text-green-600">₱{{ number_format($price, 0) }}</p>
                            </div>
                            <p class="text-xs text-gray-500">{{ $typeInfo['description'] }}</p>
                        </div>
                        <div class="flex items-center ml-4">
                            <button type="button" class="decrement bg-gray-200 px-2 py-1 rounded-l hover:bg-gray-300" data-type="{{ $typeInfo['key'] }}">-</button>
                            <span id="{{ $typeInfo['key'] }}Count" class="px-3 font-semibold min-w-[2rem] text-center">{{ $typeInfo['default'] }}</span>
                            <button type="button" class="increment bg-blue-600 text-white px-2 py-1 rounded-r hover:bg-blue-700" data-type="{{ $typeInfo['key'] }}">+</button>
                        </div>
                    </div>
                @endforeach

                <div class="border-t pt-3 mt-3">
                    <p class="text-xs text-gray-500">
                        ⚠ Max 10 passengers only (adults, children & PWD).
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        💡 Prices shown are base fares per person.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Button - Outside Grid -->
    <form action="{{ route('booking.schedule') }}" method="GET" class="mt-6">
        <input type="hidden" name="origin" id="originField">
        <input type="hidden" name="destination" id="destinationField">
        <input type="hidden" name="tripType" id="tripTypeField" value="round">
        <input type="hidden" name="departure_date" id="departureField">
        <input type="hidden" name="return_date" id="returnField">
        <input type="hidden" name="adult" id="adultField" value="1">
        <input type="hidden" name="child" id="childField" value="0">
        <input type="hidden" name="infant" id="infantField" value="0">
        <input type="hidden" name="pwd" id="pwdField" value="0">
        <input type="hidden" name="student" id="studentField" value="0">

        <button class="bg-blue-600 text-white font-medium rounded-lg px-6 py-3 w-full hover:bg-blue-700 active:bg-blue-800 transition shadow">
            Search Trips
        </button>
        <p class="mt-2 text-xs text-gray-400 text-center">By continuing, you agree to our terms.</p>
    </form>
</div>



    <!-- Stats Section 
    <section class="py-12 bg-white">
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <h2 class="text-2xl font-bold text-blue-600">12+</h2>
                <p class="text-gray-600">Daily Trips</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-blue-600">25K+</h2>
                <p class="text-gray-600">Happy Passengers</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-blue-600">4.9 ⭐</h2>
                <p class="text-gray-600">Average Rating</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-blue-600">1.5hrs</h2>
                <p class="text-gray-600">Travel Time</p>
            </div>
        </div>
    </section> -->

    <!-- Promos Section -->
    <section id="promos" class="py-16 bg-blue-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-blue-700">Special Promos & Offers</h2>
                <p class="text-gray-600 mt-2">Don't miss out on our amazing deals and discounts</p>
            </div>
           <!-- <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition border-l-4 border-blue-500">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-blue-600 ml-3">Early Bird Special</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Book 7 days in advance and get 15% off your ferry tickets!</p>
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="text-sm text-blue-700 font-semibold">Valid until December 31, 2024</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition border-l-4 border-green-500">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17 20a2 2 0 002-2V8a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H6a2 2 0 00-2 2v2H3a2 2 0 00-2 2v10a2 2 0 002 2h14zM6 4h8v2H6V4zm11 14H3V8h14v10z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-green-600 ml-3">Group Discount</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Travel with 5 or more passengers and save 20% on total fare!</p>
                    <div class="bg-green-50 p-3 rounded-lg">
                        <p class="text-sm text-green-700 font-semibold">Perfect for family trips</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition border-l-4 border-purple-500">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-purple-600 ml-3">Student Discount</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Students get 10% off with valid school ID. Study and travel smart!</p>
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <p class="text-sm text-purple-700 font-semibold">Valid ID required</p>
                    </div>
                </div>
            </div> -->
        </div>
    </section>

    <!-- Routes Section -->
    <section id="routes" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-blue-700">Our Routes</h2>
                <p class="text-gray-600 mt-2">Connecting beautiful destinations across the Philippines</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-blue-700">Bantayan ⇄ Cadiz</h3>
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">Popular</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Duration:</strong> 3 hours</p>
                        <p><strong>Daily Trips:</strong> 1 departures</p>
                        <p><strong>Starting from:</strong> ₱900</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-blue-100">
                        <p class="text-xs text-blue-600">Scenic route with beautiful ocean views</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-green-700">Cadiz ⇄ Bantayan</h3>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">Popular</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Duration:</strong> 3 hours</p>
                        <p><strong>Daily Trips:</strong> 1 departures</p>
                        <p><strong>Starting from:</strong> ₱900</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-green-100">
                        <p class="text-xs text-green-600">Gateway to the mystical island</p>
                    </div>
                </div>
                <!-- <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-purple-700">Cebu ⇄ Dumaguete</h3>
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold">Express</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Duration:</strong> 3 hours</p>
                        <p><strong>Daily Trips:</strong> 3 departures</p>
                        <p><strong>Starting from:</strong> ₱300</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-purple-100">
                        <p class="text-xs text-purple-600">Direct route to the City of Gentle People</p>
                    </div>
                </div> -->
            </div>
            <div class="text-center mt-8">
                <p class="text-gray-600 mb-4">More routes available! Check our booking system for complete schedules.</p>
                <a href="#book" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition smooth-scroll">
                    View All Routes
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose BaltBep -->
    <section id="why-choose-us" class="py-16 bg-gray-100">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-blue-700">Why Choose BaltBep?</h2>
                <p class="text-gray-600 mt-2">Your trusted ferry booking platform with seamless features</p>
            </div>
        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">Easy Booking</h3>
                <p class="text-gray-600">Book your tickets online in just a few clicks.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">Secure Payments</h3>
                <p class="text-gray-600">Your transactions are safe with us.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">Real-time Updates</h3>
                <p class="text-gray-600">Stay informed about trip schedules and changes.</p>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about-us" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-blue-700 mb-6">About BaltBep</h2>
                    <div class="space-y-4 text-gray-600">
                        <p class="text-lg leading-relaxed">
                            BaltBep is your premier ferry booking platform, connecting the beautiful islands of the Philippines with safe, reliable, and comfortable sea travel experiences.
                        </p>
                        <p>
                            Founded with a vision to make island hopping accessible to everyone, we've been serving thousands of travelers who seek adventure, relaxation, and unforgettable memories across the Philippine archipelago.
                        </p>
                        <p>
                            Our commitment to excellence, safety, and customer satisfaction has made us the trusted choice for both local and international travelers exploring the stunning beauty of the Philippines.
                        </p>
                    </div>
                    <div class="mt-8 grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">25K+</div>
                            <div class="text-sm text-gray-600">Happy Passengers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">12+</div>
                            <div class="text-sm text-gray-600">Daily Trips</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">4.9★</div>
                            <div class="text-sm text-gray-600">Average Rating</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">5+</div>
                            <div class="text-sm text-gray-600">Years Experience</div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-100 to-cyan-100 rounded-2xl p-8">
                        <div class="bg-white rounded-xl p-6 shadow-lg">
                            <h3 class="text-xl font-bold text-blue-700 mb-4">Our Mission</h3>
                            <p class="text-gray-600 mb-6">
                                To provide safe, reliable, and affordable ferry transportation while promoting sustainable tourism across the Philippine islands.
                            </p>
                            <h3 class="text-xl font-bold text-blue-700 mb-4">Our Vision</h3>
                            <p class="text-gray-600">
                                To be the leading ferry booking platform in the Philippines, connecting communities and creating memorable travel experiences.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact-us" class="py-16 bg-blue-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-blue-700">Contact Us</h2>
                <p class="text-gray-600 mt-2">Get in touch with us for any questions or assistance</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Contact Information -->
                <div class="space-y-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-blue-700 mb-6">Get In Touch</h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Email</h4>
                                    <p class="text-gray-600">info@baltbep.com</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Phone</h4>
                                    <p class="text-gray-600">+63 949 883 3551</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <!--<div>
                                    <h4 class="font-semibold text-gray-800">Office</h4>
                                    <p class="text-gray-600">Tagbilaran Port, Bohol, Philippines</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div> -->
                                <div>
                                    <h4 class="font-semibold text-gray-800">Business Hours</h4>
                                    <p class="text-gray-600">Mon - Sun: 6:00 AM - 10:00 AM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-blue-700 mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="https://web.facebook.com/baltbepshippingexpress" class="bg-blue-800 text-white p-3 rounded-full hover:bg-blue-900 transition" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="mailto:support@baltbep.com" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition" aria-label="Email">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2 4h20a2 2 0 012 2v12a2 2 0 01-2 2H2a2 2 0 01-2-2V6a2 2 0 012-2zm10 7l10-5H2l10 5zm-2.236-.132L2 8.118V18h20V8.118l-7.764 2.75a4 4 0 01-4.472 0z"/>
                                </svg>
                            </a>
                            <a href="tel:+639498833551" class="bg-green-600 text-white p-3 rounded-full hover:bg-green-700 transition" aria-label="Call us">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2.003 5.884l3.682-.737a1 1 0 011.115.595l1.516 3.538a1 1 0 01-.23 1.09l-2.21 2.1a16.053 16.053 0 007.257 7.257l2.1-2.21a1 1 0 011.09-.23l3.538 1.516a1 1 0 01.595 1.115l-.737 3.682A1 1 0 0118.25 24C8.175 24 0 15.825 0 5.75a1 1 0 011.003-1.003z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h3 class="text-xl font-bold text-blue-700 mb-6">Send us a Message</h3>
                    <form class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Your first name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Your last name">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="your.email@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="What is this about?">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tell us how we can help you..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const tripTypes = document.querySelectorAll(".tripType");
        const arrow = document.getElementById("tripArrow");
        const fromSelect = document.getElementById("fromSelect");
        const toSelect = document.getElementById("toSelect");
        const returnDateContainer = document.getElementById("returnDateContainer");

        const departureInput = document.getElementById("departure_date");
        const returnInput = document.getElementById("return_date");

        // Hidden fields to submit
        const originField = document.getElementById("originField");
        const destinationField = document.getElementById("destinationField");
        const tripTypeField = document.getElementById("tripTypeField");
        const departureField = document.getElementById("departureField");
        const returnField = document.getElementById("returnField");

        function updateHiddenBasics() {
            originField.value = fromSelect.value;
            destinationField.value = toSelect.value;
            const checked = Array.from(tripTypes).find(t => t.checked)?.value || 'round';
            tripTypeField.value = checked;
            departureField.value = departureInput.value;
            returnField.value = returnInput ? returnInput.value : '';
        }

        function syncReturnMin() {
            if (!returnInput) return;
            if (departureInput && departureInput.value) {
                returnInput.min = departureInput.value;
                if (returnInput.value && returnInput.value < departureInput.value) {
                    returnInput.value = departureInput.value;
                }
                returnInput.disabled = false;
            } else {
                returnInput.value = "";
                returnInput.disabled = true;
            }
        }

        function setRoundTripUI(isRound) {
            if (isRound) {
                arrow.textContent = "⇆";
                returnDateContainer.classList.remove("hidden");
                syncReturnMin();
            } else {
                arrow.textContent = "→";
                returnDateContainer.classList.add("hidden");
                if (returnInput) {
                    returnInput.value = "";
                    returnInput.disabled = true;
                }
            }
            updateHiddenBasics();
        }

        // Initialize
        setRoundTripUI(Array.from(tripTypes).find(t => t.checked)?.value === 'round');
        updateHiddenBasics();

        // React to trip type changes
        tripTypes.forEach(type => {
            type.addEventListener("change", () => {
                setRoundTripUI(type.value === "round");
            });
        });

        // Keep return date in range when departure changes
        if (departureInput) {
            departureInput.addEventListener("change", () => {
                syncReturnMin();
                updateHiddenBasics();
            });
        }
        if (returnInput) {
            returnInput.addEventListener("change", updateHiddenBasics);
        }

        // Swap From/To on arrow click
        arrow.addEventListener("click", () => {
            const tmp = fromSelect.value;
            fromSelect.value = toSelect.value;
            toSelect.value = tmp;
            updateHiddenBasics();
        });

        // Keep hidden fields updated on dropdown changes
        fromSelect.addEventListener("change", updateHiddenBasics);
        toSelect.addEventListener("change", updateHiddenBasics);
    });
    </script>


    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const dropdownBtn = document.getElementById("passengerDropdownBtn");
        const dropdown = document.getElementById("passengerDropdown");
        const totalDisplay = document.getElementById("totalPassengers");

        // Initialize counts dynamically based on available passenger types
        let counts = {
            @foreach($passengerTypeMap as $typeInfo)
                '{{ $typeInfo['key'] }}': {{ $typeInfo['default'] }},
            @endforeach
        };

        // Toggle dropdown
        dropdownBtn.addEventListener("click", () => {
            dropdown.classList.toggle("hidden");
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add("hidden");
            }
        });

        // Increment / Decrement buttons
        document.querySelectorAll(".increment, .decrement").forEach(btn => {
            btn.addEventListener("click", () => {
                const type = btn.getAttribute("data-type");
                const isIncrement = btn.classList.contains("increment");
                
                if (isIncrement) {
                    // Check total passenger limit (excluding infants from count)
                    const totalCountablePassengers = Object.keys(counts)
                        .filter(key => key !== 'infant')
                        .reduce((sum, key) => sum + counts[key], 0);
                    
                    if (totalCountablePassengers < 10) {
                        counts[type]++;
                    }
                } else {
                    // Prevent decrementing below 0, and keep at least 1 adult
                    if (counts[type] > 0 && !(type === "adult" && counts.adult === 1)) {
                        counts[type]--;
                    }
                }

                // Update UI
                const countElement = document.getElementById(type + "Count");
                if (countElement) {
                    countElement.textContent = counts[type];
                }
                updateTotal();
                updateHiddenFields();
            });
        });

        function updateTotal() {
            let displayParts = [];
            
            @foreach($passengerTypeMap as $typeInfo)
                if (counts['{{ $typeInfo['key'] }}'] > 0) {
                    displayParts.push(`${counts['{{ $typeInfo['key'] }}']} {{ $typeInfo['label'] }}`);
                }
            @endforeach
            
            totalDisplay.textContent = displayParts.length > 0 ? displayParts.join(', ') : '1 Adult';
        }

        function updateHiddenFields() {
            // Update hidden form fields
            Object.keys(counts).forEach(type => {
                const field = document.getElementById(type + "Field");
                if (field) {
                    field.value = counts[type];
                }
            });
        }

        // Initialize display and hidden fields
        updateTotal();
        updateHiddenFields();
    });
</script>

@auth
<!-- Role-Based Access Test Section (for demonstration) -->
<div class="bg-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-6">
        <h3 class="text-2xl font-bold text-center mb-6">Role-Based Access Test</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <!-- Super Admin Test -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h4 class="font-semibold text-blue-600 mb-2">Super Admin Area</h4>
                <p class="text-sm text-gray-600 mb-4">Only Super Admins can access this.</p>
                <a href="{{ route('dashboard') }}" class="block w-full text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    Super Admin Dashboard
                </a>
            </div>
            
            <!-- Admin Test -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h4 class="font-semibold text-green-600 mb-2">Admin Area</h4>
                <p class="text-sm text-gray-600 mb-4">Only Admins can access this.</p>
                <a href="{{ route('admin.test') }}" class="block w-full text-center bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
                    Admin Test Page
                </a>
            </div>
            
            <!-- Customer Test -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h4 class="font-semibold text-cyan-600 mb-2">Customer Area</h4>
                <p class="text-sm text-gray-600 mb-4">Only Customers can access this.</p>
                <a href="{{ route('customer.test') }}" class="block w-full text-center bg-cyan-600 text-white py-2 rounded hover:bg-cyan-700 transition">
                    Customer Test Page
                </a>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Current Role: <strong class="text-blue-600">{{ auth()->user()->getRoleDisplayName() }}</strong>
                | Try accessing different areas to test role-based restrictions.
            </p>
        </div>
    </div>
</div>
@endauth

<script>
// Smooth scrolling for navigation links
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to all links with smooth-scroll class
    const smoothScrollLinks = document.querySelectorAll('.smooth-scroll, a[href^="#"]');
    
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                // Calculate offset for fixed navbar
                const navbarHeight = 80; // Adjust based on your navbar height
                const targetPosition = targetSection.offsetTop - navbarHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add active state to navigation links based on scroll position
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.smooth-scroll');
    
    function updateActiveNavLink() {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.offsetHeight;
            
            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('bg-white/20', 'text-cyan-200');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('bg-white/20', 'text-cyan-200');
            }
        });
    }
    
    // Update active link on scroll
    window.addEventListener('scroll', updateActiveNavLink);
    
    // Update active link on page load
    updateActiveNavLink();
});
</script>

@if(isset($showAlert) && $showAlert)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Profile Updated!',
                text: 'Your profile has been successfully updated.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
@endif

</body>
</html>
