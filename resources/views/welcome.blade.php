<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-black/30 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="#book" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Book</a>
                <a href="#refund" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Refund & Rebooking</a>
                <a href="#info" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Travel Info</a>
                <a href="#updates" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Latest Updates</a>
                <a href="#contact" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200">Contact Us</a>
            </div>
            <!-- Auth area: show user name if logged in, otherwise Sign In -->
            <div>
                @auth
                    <div class="flex items-center space-x-3 text-white">
                        <div class="flex items-center space-x-2">
                            <span>Hi, {{ Auth::user()->name }}</span>
                            <span class="text-xs bg-white/20 px-2 py-1 rounded-full">{{ Auth::user()->getRoleDisplayName() }}</span>
                        </div>
                        
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg transition">
                                Super Admin Dashboard
                            </a>
                        @elseif(Auth::user()->isAdmin())
                            <span class="bg-green-600 px-3 py-1 rounded-lg">
                                Admin Panel
                            </span>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="border border-white px-3 py-1 rounded-lg hover:bg-white hover:text-blue-600 transition">Log out</button>
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
                    // Map database passenger types to our form fields
                    $passengerTypeMap = [
                        'Regular' => ['key' => 'adult', 'label' => 'Adult', 'description' => 'Ages 12+ years old', 'default' => 1],
                        'Child (2-11)' => ['key' => 'child', 'label' => 'Child', 'description' => 'Ages 2-11', 'default' => 0],
                        'Infant' => ['key' => 'infant', 'label' => 'Infant', 'description' => 'Under 2', 'default' => 0],
                        'Senior Citizen / PWD' => ['key' => 'pwd', 'label' => 'PWD/Senior', 'description' => 'Persons With Disability / Senior Citizens', 'default' => 0],
                        'Student' => ['key' => 'student', 'label' => 'Student', 'description' => 'With valid student ID', 'default' => 0],
                    ];
                @endphp

                @foreach($fares as $index => $fare)
                    @if(isset($passengerTypeMap[$fare->passenger_type]))
                        @php $typeInfo = $passengerTypeMap[$fare->passenger_type]; @endphp
                        <div class="flex items-center justify-between {{ $index < count($fares) - 1 ? 'mb-3' : '' }}">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold">{{ $typeInfo['label'] }}</p>
                                    <p class="text-sm font-medium text-green-600">₱{{ number_format($fare->price, 0) }}</p>
                                </div>
                                <p class="text-xs text-gray-500">{{ $typeInfo['description'] }}</p>
                            </div>
                            <div class="flex items-center ml-4">
                                <button type="button" class="decrement bg-gray-200 px-2 py-1 rounded-l hover:bg-gray-300" data-type="{{ $typeInfo['key'] }}">-</button>
                                <span id="{{ $typeInfo['key'] }}Count" class="px-3 font-semibold min-w-[2rem] text-center">{{ $typeInfo['default'] }}</span>
                                <button type="button" class="increment bg-blue-600 text-white px-2 py-1 rounded-r hover:bg-blue-700" data-type="{{ $typeInfo['key'] }}">+</button>
                            </div>
                        </div>
                    @endif
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

    <!-- Why Choose BaltBep -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-6xl mx-auto text-center mb-12">
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
        let counts = {};
        @foreach($fares as $fare)
            @if(isset($passengerTypeMap[$fare->passenger_type]))
                @php $typeInfo = $passengerTypeMap[$fare->passenger_type]; @endphp
                counts['{{ $typeInfo['key'] }}'] = {{ $typeInfo['default'] }};
            @endif
        @endforeach

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
            
            @foreach($fares as $fare)
                @if(isset($passengerTypeMap[$fare->passenger_type]))
                    @php $typeInfo = $passengerTypeMap[$fare->passenger_type]; @endphp
                    if (counts['{{ $typeInfo['key'] }}'] > 0) {
                        displayParts.push(`${counts['{{ $typeInfo['key'] }}']} {{ $typeInfo['label'] }}`);
                    }
                @endif
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

</body>
</html>
