<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt-Bep — Schedule</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar (same as welcome.blade) -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-transparent">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="{{ route('welcome') }}#book" class="hover:text-cyan-200">Book</a>
                <a href="#refund" class="hover:text-cyan-200">Refund & Rebooking</a>
                <a href="#info" class="hover:text-cyan-200">Travel Info</a>
                <a href="#updates" class="hover:text-cyan-200">Latest Updates</a>
                <a href="#contact" class="hover:text-cyan-200">Contact Us</a>
            </div>
            <!-- Auth area -->
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

    <!-- Hero Section (same background style) -->
    <div class="relative bg-cover bg-center h-[45vh] md:h-[55vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold">Choose your schedule</h1>
                <p class="mt-2 text-lg md:text-2xl italic">
                    {{ $criteria['origin'] }} → {{ $criteria['destination'] }} on
                    {{ \Carbon\Carbon::parse($criteria['departure_date'])->format('M d, Y') }}
                </p>
                @if(($criteria['tripType'] ?? '') === 'round' && !empty($criteria['return_date']))
                    <p class="text-white/90 mt-1">
                        Return: {{ $criteria['destination'] }} → {{ $criteria['origin'] }} on
                        {{ \Carbon\Carbon::parse($criteria['return_date'])->format('M d, Y') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Content Card (mirrors Trip Search Box style) -->
    <div class="relative -mt-16 max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        @include('bookings.partials.progress', ['current' => 'schedule'])

        <div class="flex items-center justify-between mt-4">
            <h2 class="text-xl md:text-2xl font-bold">Departure</h2>
            <button id="openCalendar" type="button" class="text-blue-600 hover:underline">Choose another date</button>
        </div>

        <!-- Calendar Modal -->
        <div id="calendarModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
            <div class="relative mx-auto mt-24 w-11/12 max-w-xl bg-white rounded-2xl shadow-2xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold">Select a date</h3>
                    <button id="closeCalendar" class="text-gray-500 hover:text-gray-800">✕</button>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">{{ $criteria['origin'] }} → {{ $criteria['destination'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="prevMonth" class="px-2 py-1 rounded border">‹</button>
                        <div id="monthLabel" class="font-medium"></div>
                        <button id="nextMonth" class="px-2 py-1 rounded border">›</button>
                    </div>
                </div>
                <div id="calendarGrid" class="grid grid-cols-7 gap-2 text-center"></div>
                <p class="text-xs text-gray-500 mt-3">Blue dates indicate available departures for the selected route.</p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-6">
            <!-- Schedules -->
            <div class="md:col-span-2">
                <h3 class="font-semibold text-gray-800 mb-2">Outbound</h3>
                @forelse($outbound as $trip)
                    <div class="border rounded-xl p-4 mb-3 flex items-center justify-between bg-white/80" data-card="outbound" data-id="{{ $trip->id }}" data-price="{{ $trip->price }}" data-origin="{{ $trip->origin }}" data-destination="{{ $trip->destination }}" data-departure="{{ \Carbon\Carbon::parse($trip->departure_time)->toIso8601String() }}" data-arrival="{{ \Carbon\Carbon::parse($trip->arrival_time)->toIso8601String() }}">
                        <div>
                            <p class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</p>
                            <p class="text-sm text-gray-600">Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</p>
                            <p class="text-sm text-gray-600">Arrive: {{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold">₱{{ number_format($trip->price, 2) }}</p>
                            <button type="button" class="selectTrip inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700" data-type="outbound" data-id="{{ $trip->id }}">Select</button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-800 p-4">
                        <p class="font-medium">No trips for the selected date</p>
                        <ul class="list-disc ml-5 mt-2 text-sm space-y-1">
                            <li>Service for this trip may only be available on specific days.</li>
                            <li>Service for this trip may only be seasonal.</li>
                            <li>Schedules for this route have not yet been announced.</li>
                        </ul>
                    </div>
                @endforelse

                @if(($criteria['tripType'] ?? '') === 'round')
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Return</h3>
                    @forelse($inbound as $trip)
                        <div class="border rounded-xl p-4 mb-3 flex items-center justify-between bg-white/80" data-card="inbound" data-id="{{ $trip->id }}" data-price="{{ $trip->price }}" data-origin="{{ $trip->origin }}" data-destination="{{ $trip->destination }}" data-departure="{{ \Carbon\Carbon::parse($trip->departure_time)->toIso8601String() }}" data-arrival="{{ \Carbon\Carbon::parse($trip->arrival_time)->toIso8601String() }}">
                            <div>
                                <p class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</p>
                                <p class="text-sm text-gray-600">Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</p>
                                <p class="text-sm text-gray-600">Arrive: {{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold">₱{{ number_format($trip->price, 2) }}</p>
                                <button type="button" class="selectTrip inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700" data-type="inbound" data-id="{{ $trip->id }}">Select</button>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-800 p-4">
                            <p class="font-medium">No return trips for the selected date</p>
                        </div>
                    @endforelse
                </div>
                @endif
            </div>

            <!-- Sidebar summary -->
            <aside>
                <div class="sticky top-4 space-y-3">
                    <div class="border rounded-xl bg-white p-4 shadow">
                        <h4 class="font-semibold mb-2">Booking details</h4>
                        <div class="text-sm text-gray-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Departure</span>
                                <span>{{ $criteria['origin'] }} → {{ $criteria['destination'] }}</span>
                            </div>
                            @if(($criteria['tripType'] ?? '') === 'round' && !empty($criteria['return_date']))
                            <div class="flex justify-between">
                                <span>Return</span>
                                <span>{{ $criteria['destination'] }} → {{ $criteria['origin'] }}</span>
                            </div>
                            @endif
                            <div class="pt-2">
                                <span class="font-medium">Passengers</span>
                                <div class="text-gray-600">
                                    Adult x {{ $criteria['adult'] ?? 1 }},
                                    Child x {{ $criteria['child'] ?? 0 }},
                                    Infant x {{ $criteria['infant'] ?? 0 }},
                                    PWD x {{ $criteria['pwd'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-xl bg-white p-4 shadow">
                        <div id="selectionSummary" class="text-sm text-gray-700 space-y-1">
                            <!-- Filled by JS: outbound/return selections -->
                        </div>
                        <div class="border-t my-3"></div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">Subtotal</span>
                            <span id="subtotal" class="font-semibold">₱0.00</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Prices may change. Booking fee and discounts will be applied after adding passenger details.</p>

                        <button id="proceedBtn" type="button" class="mt-3 w-full px-4 py-2 bg-green-600 text-white rounded-lg opacity-50 cursor-not-allowed" disabled>Proceed</button>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <button id="openEditTrip" type="button" class="inline-block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">Edit Trip</button>
                            <a href="{{ url()->previous() }}" class="inline-block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">Back</a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Edit Trip Modal -->
    <div id="editTripModal" class="hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative mx-auto mt-20 w-11/12 max-w-2xl bg-white rounded-2xl shadow-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Edit your trip</h3>
                <button id="closeEditTrip" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <form id="editTripForm" action="{{ route('booking.schedule') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">From</label>
                        <select id="edit_from" name="origin" class="border rounded-lg px-4 py-3 w-full">
                            <option value="Bantayan">Bantayan</option>
                            <option value="Cadiz">Cadiz</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">To</label>
                        <select id="edit_to" name="destination" class="border rounded-lg px-4 py-3 w-full">
                            <option value="Bantayan">Bantayan</option>
                            <option value="Cadiz">Cadiz</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="text-sm font-semibold mb-1 block">Departure Date</label>
                        <input type="date" id="edit_departure_date" name="departure_date" class="border rounded-lg px-4 py-3 w-full">
                    </div>
                    <div id="edit_return_wrap" class="hidden">
                        <label class="text-sm font-semibold mb-1 block">Return Date</label>
                        <input type="date" id="edit_return_date" name="return_date" class="border rounded-lg px-4 py-3 w-full">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold mb-1 block">Trip Type</label>
                    <div class="inline-flex rounded-lg bg-gray-100 p-1">
                        <label class="flex items-center px-3 py-2 rounded-md cursor-pointer text-sm font-medium" id="type_round_label">
                            <input type="radio" name="tripType" value="round" id="type_round" class="hidden" checked>
                            Round Trip
                        </label>
                        <label class="flex items-center px-3 py-2 rounded-md cursor-pointer text-sm font-medium" id="type_oneway_label">
                            <input type="radio" name="tripType" value="oneway" id="type_oneway" class="hidden">
                            One-way
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Adult</label>
                        <input type="number" min="1" id="edit_adult" name="adult" class="border rounded-lg px-3 py-2 w-full" value="1">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Child</label>
                        <input type="number" min="0" id="edit_child" name="child" class="border rounded-lg px-3 py-2 w-full" value="0">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Infant</label>
                        <input type="number" min="0" id="edit_infant" name="infant" class="border rounded-lg px-3 py-2 w-full" value="0">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">PWD</label>
                        <input type="number" min="0" id="edit_pwd" name="pwd" class="border rounded-lg px-3 py-2 w-full" value="0">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="cancelEditTrip" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Search</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const openBtn = document.getElementById('openCalendar');
        const modal = document.getElementById('calendarModal');
        const closeBtn = document.getElementById('closeCalendar');
        const prevBtn = document.getElementById('prevMonth');
        const nextBtn = document.getElementById('nextMonth');
        const grid = document.getElementById('calendarGrid');
        const monthLabel = document.getElementById('monthLabel');

        if (!openBtn || !modal) return;

        const parseYMD = (s) => { const [y,m,d] = s.split('-').map(Number); return new Date(y, m-1, d); };
        let current = parseYMD(@json(\Carbon\Carbon::parse($criteria['departure_date'])->format('Y-m-01')));
        const params = {
            origin: @json($criteria['origin']),
            destination: @json($criteria['destination']),
            tripType: @json($criteria['tripType']),
            adult: @json($criteria['adult'] ?? 1),
            child: @json($criteria['child'] ?? 0),
            infant: @json($criteria['infant'] ?? 0),
            pwd: @json($criteria['pwd'] ?? 0),
        };

        // Use local time, not UTC, to avoid off-by-one day shift
        const fmt = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        const yyyymm = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`;

        function toggle(show) { modal.classList.toggle('hidden', !show); }
        openBtn.addEventListener('click', () => toggle(true));
        closeBtn?.addEventListener('click', () => toggle(false));
        modal.addEventListener('click', (e) => { if (e.target === modal) toggle(false); });

        // Edit Trip modal wiring
        const editModal = document.getElementById('editTripModal');
        const openEdit = document.getElementById('openEditTrip');
        const closeEdit = document.getElementById('closeEditTrip');
        const cancelEdit = document.getElementById('cancelEditTrip');
        function toggleEdit(show) { editModal.classList.toggle('hidden', !show); }
        openEdit?.addEventListener('click', () => {
            // Prefill with current criteria
            document.getElementById('edit_from').value = params.origin;
            document.getElementById('edit_to').value = params.destination;
            document.getElementById('edit_departure_date').value = @json($criteria['departure_date']);
            const ret = @json($criteria['return_date'] ?? '');
            document.getElementById('edit_return_date').value = ret;
            const isRound = (params.tripType === 'round');
            document.getElementById('type_round').checked = isRound;
            document.getElementById('type_oneway').checked = !isRound;
            document.getElementById('edit_return_wrap').classList.toggle('hidden', !isRound);
            document.getElementById('edit_adult').value = params.adult;
            document.getElementById('edit_child').value = params.child;
            document.getElementById('edit_infant').value = params.infant;
            document.getElementById('edit_pwd').value = params.pwd;
            toggleEdit(true);
        });
        closeEdit?.addEventListener('click', () => toggleEdit(false));
        cancelEdit?.addEventListener('click', () => toggleEdit(false));
        editModal.addEventListener('click', (e) => { if (e.target === editModal) toggleEdit(false); });
        document.getElementById('type_round')?.addEventListener('change', () => document.getElementById('edit_return_wrap').classList.remove('hidden'));
        document.getElementById('type_oneway')?.addEventListener('change', () => document.getElementById('edit_return_wrap').classList.add('hidden'));

        async function load() {
            monthLabel.textContent = new Intl.DateTimeFormat('en', { month: 'long', year: 'numeric' }).format(current);
            const url = new URL('/booking/available-dates', window.location.origin);
            url.searchParams.set('origin', params.origin);
            url.searchParams.set('destination', params.destination);
            url.searchParams.set('month', yyyymm(current));
            const res = await fetch(url.toString());
            const data = await res.json();
            render(data.availableDates);
        }

        function render(available) {
            grid.innerHTML = '';
            const firstDay = new Date(current.getFullYear(), current.getMonth(), 1);
            const startWeekday = (firstDay.getDay()+6)%7; // make Monday first
            const daysInMonth = new Date(current.getFullYear(), current.getMonth()+1, 0).getDate();

            // Weekday headers
            ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].forEach(name => {
                const div = document.createElement('div');
                div.className = 'text-xs font-medium text-gray-500';
                div.textContent = name;
                grid.appendChild(div);
            });

            // Blank leading cells
            for (let i=0;i<startWeekday;i++) grid.appendChild(document.createElement('div'));

            // Days
            for (let day=1; day<=daysInMonth; day++) {
                const date = new Date(current.getFullYear(), current.getMonth(), day);
                const dateStr = fmt(date);
                const isAvailable = available.includes(dateStr);
                const a = document.createElement('a');
                a.className = 'block px-2 py-2 rounded text-sm ' + (isAvailable ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-500');
                a.textContent = String(day);
                if (isAvailable) {
                    const q = new URLSearchParams({
                        origin: params.origin,
                        destination: params.destination,
                        tripType: params.tripType,
                        departure_date: dateStr,
                        return_date: @json($criteria['return_date'] ?? ''),
                        adult: params.adult,
                        child: params.child,
                        infant: params.infant,
                        pwd: params.pwd,
                    });
                    a.href = `/booking/schedule?${q.toString()}`;
                } else {
                    a.href = 'javascript:void(0)';
                    a.classList.add('cursor-not-allowed');
                }
                grid.appendChild(a);
            }
        }

        prevBtn?.addEventListener('click', () => { current.setMonth(current.getMonth()-1); load(); });
        nextBtn?.addEventListener('click', () => { current.setMonth(current.getMonth()+1); load(); });

        // --- Selection handling ---
        const selection = { outbound: null, inbound: null };
        const counts = {
            adult: Number(params.adult) || 1,
            child: Number(params.child) || 0,
            infant: Number(params.infant) || 0,
            pwd: Number(params.pwd) || 0,
        };
        const selectionSummary = document.getElementById('selectionSummary');
        const subtotalEl = document.getElementById('subtotal');

        function formatCurrency(n) { return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(n); }

        function renderSummary() {
            const lines = [];
            let subtotal = 0;
            const fares = @json($fares);
            const breakdownLines = [];
            const payingBreakdown = [
                ['Adult', counts.adult, fares.adult || 0],
                ['Child', counts.child, fares.child || 0],
                ['PWD', counts.pwd, fares.pwd || 0],
                ['Infant', counts.infant, fares.infant || 0],
            ];

            // Show a short fare breakdown under outbound (and inbound if selected)
            function appendFareLines() {
                if (payingBreakdown.some(([,qty]) => qty > 0)) {
                    lines.push('<div class="mt-1 text-xs text-gray-600">Passenger fares:</div>');
                    payingBreakdown.forEach(([label, qty, fare]) => {
                        if (!qty) return;
                        lines.push(`<div class=\"flex justify-between text-xs text-gray-600\"><span>${label} × ${qty}</span><span>${formatCurrency(fare)} each</span></div>`);
                    });
                }
            }

            function totalPerTrip(basePrice) {
                // Base trip price + passenger-type fare add-ons
                let total = 0;
                payingBreakdown.forEach(([label, qty, fare]) => {
                    if (!qty) return;
                    total += qty * (fare);
                });
                // If your business logic is: total price = trip base price + per-passenger fares
                // add base price multiplied by all passengers (including infants if applicable)
                const allPax = counts.adult + counts.child + counts.pwd + counts.infant;
                total += basePrice * allPax;
                return total;
            }

            if (selection.outbound) {
                const s = selection.outbound;
                lines.push(`<div class="flex justify-between"><span>Selected Outbound</span><span>${s.origin} → ${s.destination}</span></div>`);
                lines.push(`<div class="flex justify-between text-xs text-gray-600"><span>Depart</span><span>${s.departure}</span></div>`);
                const tripTotal = totalPerTrip(s.price);
                const allPaxOutbound = counts.adult + counts.child + counts.infant + counts.pwd;
                lines.push(`<div class="flex justify-between text-xs text-gray-600"><span>Base</span><span>${formatCurrency(s.price)} × ${allPaxOutbound}</span></div>`);
                appendFareLines();
                lines.push(`<div class="flex justify-between text-xs text-gray-600"><span>Trip total</span><span>${formatCurrency(tripTotal)}</span></div>`);
                subtotal += tripTotal;
            }
            if (selection.inbound) {
                const s = selection.inbound;
                lines.push(`<div class="flex justify-between mt-2"><span>Selected Return</span><span>${s.origin} → ${s.destination}</span></div>`);
                lines.push(`<div class="flex justify-between text-xs text-gray-600"><span>Depart</span><span>${s.departure}</span></div>`);
                const tripTotal = totalPerTrip(s.price);
                const allPaxInbound = counts.adult + counts.child + counts.infant + counts.pwd;
                lines.push(`<div class="flex justify-between text-xs text-gray-600"><span>Base</span><span>${formatCurrency(s.price)} × ${allPaxInbound}</span></div>`);
                appendFareLines();
                lines.push(`<div class="flex justify-between text-xs text-gray-600"><span>Trip total</span><span>${formatCurrency(tripTotal)}</span></div>`);
                subtotal += tripTotal;
            }

            if (payingBreakdown.some(([,qty]) => qty > 0)) {
                breakdownLines.push('<div class="mt-2 text-xs text-gray-600">Passenger fares:</div>');
                payingBreakdown.forEach(([label, qty, fare]) => {
                    if (!qty) return;
                    breakdownLines.push(`<div class=\"flex justify-between text-xs text-gray-600\"><span>${label} × ${qty}</span><span>${formatCurrency(fare)} each</span></div>`);
                });
            }

            if (breakdownLines.length) lines.push(breakdownLines.join(''));
            if (!lines.length) {
                lines.push('<p class="text-gray-500">No trip selected yet.</p>');
            }
            selectionSummary.innerHTML = lines.join('');
            subtotalEl.textContent = formatCurrency(subtotal);
        }

        function clearSelection(type) {
            selection[type] = null;
            document.querySelectorAll(`[data-card="${type}"]`).forEach(c => c.classList.remove('ring-2','ring-blue-500','bg-blue-50'));
            document.querySelectorAll(`.selectTrip[data-type="${type}"]`).forEach(b => { b.textContent = 'Select'; b.classList.remove('bg-green-600'); b.classList.add('bg-blue-600'); });
        }

        function capture(cardEl) {
            const price = Number(cardEl.dataset.price);
            const origin = cardEl.dataset.origin;
            const destination = cardEl.dataset.destination;
            const departureISO = cardEl.dataset.departure;
            const departure = new Date(departureISO);
            const formatted = departure.toLocaleString('en-PH', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            return { price, origin, destination, departure: formatted };
        }

        function updateProceedState() {
            const needInbound = params.tripType === 'round' && @json(!empty($criteria['return_date']));
            const ok = !!selection.outbound && (!needInbound || !!selection.inbound);
            const btn = document.getElementById('proceedBtn');
            btn.disabled = !ok;
            btn.classList.toggle('opacity-50', !ok);
            btn.classList.toggle('cursor-not-allowed', !ok);
        }

        document.querySelectorAll('.selectTrip').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const type = btn.dataset.type; // outbound or inbound
                const card = btn.closest('[data-card]');
                if (!card) return;

                // Clear previous selection styles for this group
                document.querySelectorAll(`[data-card="${type}"]`).forEach(c => c.classList.remove('ring-2','ring-blue-500','bg-blue-50'));
                document.querySelectorAll(`.selectTrip[data-type="${type}"]`).forEach(b => { b.textContent = 'Select'; b.classList.remove('bg-green-600'); b.classList.add('bg-blue-600'); });

                // Set this one as selected
                selection[type] = capture(card);
                card.classList.add('ring-2','ring-blue-500','bg-blue-50');
                btn.textContent = 'Selected';
                btn.classList.remove('bg-blue-600');
                btn.classList.add('bg-green-600');

                renderSummary();
                updateProceedState();
            });
        });

        // Proceed gating
        updateProceedState();

        load();
    });
    </script>
</body>
</html>