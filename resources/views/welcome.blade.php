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
    <nav class="absolute top-0 left-0 w-full z-20 bg-transparent">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="#book" class="hover:text-cyan-200">Book</a>
                <a href="#refund" class="hover:text-cyan-200">Refund & Rebooking</a>
                <a href="#info" class="hover:text-cyan-200">Travel Info</a>
                <a href="#updates" class="hover:text-cyan-200">Latest Updates</a>
                <a href="#contact" class="hover:text-cyan-200">Contact Us</a>
            </div>
            <!-- Auth area: show user name if logged in, otherwise Sign In -->
            <div>
                @auth
                    <div class="flex items-center space-x-3 text-white">
                        <span>Hi, {{ Auth::user()->name }}</span>
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
<div id="book" class="relative -mt-16 max-w-5xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
    <h2 class="text-2xl font-bold mb-2 text-gray-800">Where’s your next adventure?</h2>
    <p class="text-gray-600 mb-6">Let’s make your next trip one to remember, book now!</p>

    <!-- Trip Type -->
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <div class="inline-flex rounded-lg bg-gray-100 p-1">
            <label class="flex items-center px-3 py-2 rounded-md cursor-pointer text-sm font-medium transition data-[checked=true]:bg-white data-[checked=true]:shadow" data-checked="true">
                <input type="radio" name="tripType" value="round" class="tripType hidden" checked>
                Round Trip
            </label>
            <label class="flex items-center px-3 py-2 rounded-md cursor-pointer text-sm font-medium transition data-[checked=true]:bg-white data-[checked=true]:shadow">
                <input type="radio" name="tripType" value="oneway" class="tripType hidden">
                One-way
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
                 class="hidden absolute z-20 mt-2 w-72 bg-white border rounded-xl shadow-lg p-4">

                <!-- Adult -->
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="font-semibold">Adult</p>
                        <p class="text-xs text-gray-500">Ages 12+ years old</p>
                    </div>
                    <div class="flex items-center">
                        <button type="button" class="decrement bg-gray-200 px-2 py-1 rounded-l" data-type="adult">-</button>
                        <span id="adultCount" class="px-3 font-semibold">1</span>
                        <button type="button" class="increment bg-blue-600 text-white px-2 py-1 rounded-r" data-type="adult">+</button>
                    </div>
                </div>

                <!-- Child -->
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="font-semibold">Child</p>
                        <p class="text-xs text-gray-500">Ages 2-11</p>
                    </div>
                    <div class="flex items-center">
                        <button type="button" class="decrement bg-gray-200 px-2 py-1 rounded-l" data-type="child">-</button>
                        <span id="childCount" class="px-3 font-semibold">0</span>
                        <button type="button" class="increment bg-blue-600 text-white px-2 py-1 rounded-r" data-type="child">+</button>
                    </div>
                </div>

                <!-- Infant -->
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="font-semibold">Infant</p>
                        <p class="text-xs text-gray-500">Under 2</p>
                    </div>
                    <div class="flex items-center">
                        <button type="button" class="decrement bg-gray-200 px-2 py-1 rounded-l" data-type="infant">-</button>
                        <span id="infantCount" class="px-3 font-semibold">0</span>
                        <button type="button" class="increment bg-blue-600 text-white px-2 py-1 rounded-r" data-type="infant">+</button>
                    </div>
                </div>

                <!-- PWD -->
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold">PWD</p>
                        <p class="text-xs text-gray-500">Persons With Disability</p>
                    </div>
                    <div class="flex items-center">
                        <button type="button" class="decrement bg-gray-200 px-2 py-1 rounded-l" data-type="pwd">-</button>
                        <span id="pwdCount" class="px-3 font-semibold">0</span>
                        <button type="button" class="increment bg-blue-600 text-white px-2 py-1 rounded-r" data-type="pwd">+</button>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-3">
                    ⚠ Max 10 passengers only (adults, children & PWD).
                </p>
            </div>
        </div>

        <!-- Search -->
        <div>
            <form action="{{ route('booking.schedule') }}" method="GET" class="mt-6 md:mt-0">
                <input type="hidden" name="origin" id="originField">
                <input type="hidden" name="destination" id="destinationField">
                <input type="hidden" name="tripType" id="tripTypeField" value="round">
                <input type="hidden" name="departure_date" id="departureField">
                <input type="hidden" name="return_date" id="returnField">
                <input type="hidden" name="adult" id="adultField" value="1">
                <input type="hidden" name="child" id="childField" value="0">
                <input type="hidden" name="infant" id="infantField" value="0">
                <input type="hidden" name="pwd" id="pwdField" value="0">

                <button class="bg-blue-600 text-white font-medium rounded-lg px-6 py-3 w-full hover:bg-blue-700 active:bg-blue-800 transition shadow">
                    Search Trips
                </button>
                <p class="mt-2 text-xs text-gray-400 text-center">By continuing, you agree to our terms.</p>
            </form>
        </div>
    </div>
</div>



    <!-- Stats Section -->
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
    </section>

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

        let counts = { adult: 1, child: 0, infant: 0, pwd: 0 };

        // Toggle dropdown
        dropdownBtn.addEventListener("click", () => {
            dropdown.classList.toggle("hidden");
        });

        // Increment / Decrement buttons
        document.querySelectorAll(".increment, .decrement").forEach(btn => {
            btn.addEventListener("click", () => {
                const type = btn.getAttribute("data-type");
                if (btn.classList.contains("increment")) {
                    if (counts.adult + counts.child + counts.pwd < 10) counts[type]++;
                } else {
                    if (counts[type] > 0 && !(type === "adult" && counts.adult === 1)) {
                        counts[type]--;
                    }
                }

                // Update UI
                document.getElementById(type + "Count").textContent = counts[type];
                updateTotal();
            });
        });

        function updateTotal() {
            let total = counts.adult + counts.child + counts.infant + counts.pwd;
            totalDisplay.textContent = 
                `${counts.adult} Adult, ${counts.child} Child, ${counts.infant} Infant, ${counts.pwd} PWD`;
        }

        updateTotal();
    });
</script>


</body>
</html>
