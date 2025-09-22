@extends('layouts.superadmin')

@section('content')
<div class="p-6 space-y-6" x-data="{ showModal: false, selected: null, showPassengers: false }">
    <!-- Title -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Booking Management - Super Admin</h1>
    </div>

    <!-- Search and Filters -->
    <form method="GET" class="bg-white border border-gray-200 rounded-xl p-4 md:p-5 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 md:gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search booking ID, customer name, email" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">From</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">To</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Payment Status -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Status</label>
                <select name="payment_status" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All</option>
                    <option value="paid" @selected(request('payment_status')==='paid')>Paid</option>
                    <option value="pending" @selected(request('payment_status')==='pending')>Pending</option>
                    <option value="refunded" @selected(request('payment_status')==='refunded')>Refunded</option>
                </select>
            </div>

            <!-- Booking Status (maps to existing 'status' filter) -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Booking Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All</option>
                    <option value="pending" @selected(request('status')==='pending')>Pending</option>
                    <option value="confirmed" @selected(request('status')==='confirmed')>Confirmed</option>
                    <option value="cancelled" @selected(request('status')==='cancelled')>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full min-w-[1200px] text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Booking ID</th>
                    <th class="px-4 py-3 text-left font-semibold">Customer Name</th>
                    <th class="px-4 py-3 text-left font-semibold">Origin → Destination</th>
                    <th class="px-4 py-3 text-left font-semibold">Departure & Arrival Time</th>
                    <th class="px-4 py-3 text-left font-semibold">No. of Passengers</th>
                    <th class="px-4 py-3 text-left font-semibold">Total Price</th>
                    <th class="px-4 py-3 text-left font-semibold">Payment Status</th>
                    <th class="px-4 py-3 text-left font-semibold">Date Booked</th>
                    <th class="px-4 py-3 text-left font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $b)
                    @php
                        $tickets = intval($b->adult) + intval($b->child) + intval($b->infant) + intval($b->pwd) + intval($b->student ?? 0);
                        $capacity = optional($b->trip)->capacity ?? optional($b->trip)->seats_total ?? null;
                        $departAt = $b->departure_time ?: optional($b->trip)->departure_time;
                        $arriveAt = optional($b->trip)->arrival_time;
                        $paymentStatus = match($b->status) {
                            'confirmed' => 'Paid',
                            'cancelled' => 'Refunded/Cancelled',
                            default => 'Pending',
                        };
                        $paymentBadgeClasses = match($paymentStatus) {
                            'Paid' => 'bg-green-100 text-green-800',
                            'Refunded/Cancelled' => 'bg-purple-100 text-purple-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <!-- Booking ID -->
                        <td class="px-4 py-3 align-top font-medium">#{{ $b->id }}</td>

                        <!-- Customer Name -->
                        <td class="px-4 py-3 align-top">
                            <div class="font-medium text-gray-900">{{ $b->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $b->email }}</div>
                            @if($b->phone)
                                <div class="text-xs text-gray-500">{{ $b->phone }}</div>
                            @endif
                        </td>

                        <!-- Route -->
                        <td class="px-4 py-3 align-top">{{ $b->origin }} → {{ $b->destination }}</td>

                        <!-- Times -->
                        <td class="px-4 py-3 align-top">
                            <div><span class="text-xs text-gray-500">Dep:</span> {{ optional($departAt)->format('M d, Y • h:i A') }}</div>
                            <div><span class="text-xs text-gray-500">Arr:</span> {{ optional($arriveAt)->format('M d, Y • h:i A') }}</div>
                        </td>

                        <!-- Passengers -->
                        <td class="px-4 py-3 align-top">
                            <div class="font-medium text-gray-900">{{ $tickets }} ticket{{ $tickets === 1 ? '' : 's' }}</div>
                            <div class="text-xs text-gray-500">
                                @if($capacity)
                                    {{ $tickets }} / {{ $capacity }} used
                                @else
                                    {{ $tickets }} booked
                                @endif
                            </div>
                        </td>

                        <!-- Total Price -->
                        <td class="px-4 py-3 align-top font-medium">₱{{ number_format($b->total_amount, 2) }}</td>

                        <!-- Payment Status -->
                        <td class="px-4 py-3 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $paymentBadgeClasses }}">{{ $paymentStatus }}</span>
                        </td>

                        <!-- Date Booked -->
                        <td class="px-4 py-3 align-top">{{ optional($b->created_at)->format('M d, Y • h:i A') }}</td>

                        <!-- Actions -->
                        <td class="px-4 py-3 align-top">
                            <div class="flex flex-wrap gap-2">
                                <!-- Single View button opens floating modal -->
                                <button type="button"
                                        class="px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700"
                                        @click.prevent="selected = {
                                            id: {{ $b->id }},
                                            full_name: @js($b->full_name),
                                            email: @js($b->email),
                                            phone: @js($b->phone),
                                            origin: @js($b->origin),
                                            destination: @js($b->destination),
                                            depart_at: @js(optional($departAt)->format('M d, Y • h:i A')),
                                            arrive_at: @js(optional($arriveAt)->format('M d, Y • h:i A')),
                                            tickets: {{ $tickets }},
                                            adult: {{ (int) $b->adult }},
                                            child: {{ (int) $b->child }},
                                            infant: {{ (int) $b->infant }},
                                            pwd: {{ (int) $b->pwd }},
                                            student: {{ (int) ($b->student ?? 0) }},
                                            total_amount_label: @js('₱'.number_format($b->total_amount, 2)),
                                            status: @js($b->status),
                                            payment_status: @js($paymentStatus),
                                            created_at: @js(optional($b->created_at)->format('M d, Y • h:i A')),
                                            update_status_url: @js(route('bookings.updateStatus', $b)),
                                        }; showPassengers = false; showModal = true">
                                    View
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-gray-500">No bookings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">{{ $bookings->links() }}</div>

    <!-- Floating modal for viewing + actions -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/40" @click="showModal=false"></div>

        <!-- Panel -->
        <div class="relative w-full sm:max-w-2xl bg-white rounded-t-2xl sm:rounded-2xl shadow-xl p-6 sm:p-7 translate-y-0 sm:translate-y-0">
            <div class="flex items-start justify-between gap-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Booking #<span x-text="selected?.id"></span> Details</h3>
                <button class="text-gray-500 hover:text-gray-700" @click="showModal=false">✕</button>
            </div>

            <!-- Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <!-- Passenger details toggle -->
                <div class="sm:col-span-2">
                    <button type="button"
                            class="inline-flex items-center gap-2 text-sm px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50"
                            @click="showPassengers = !showPassengers">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        View Details
                    </button>
                    <div x-show="showPassengers" x-collapse class="mt-3 rounded-lg border border-gray-200 p-4 bg-gray-50">
                        <div class="text-gray-700 font-medium mb-2">Passenger Details</div>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>Adult: <span class="font-medium" x-text="selected?.adult"></span></li>
                            <li>Child: <span class="font-medium" x-text="selected?.child"></span></li>
                            <li>Infant: <span class="font-medium" x-text="selected?.infant"></span></li>
                            <li>PWD: <span class="font-medium" x-text="selected?.pwd"></span></li>
                            <li>Student: <span class="font-medium" x-text="selected?.student"></span></li>
                        </ul>
                        <p class="mt-3 text-xs text-gray-500">Note: Showing category counts. If you later store per-passenger records, we can render a full list here (names, seat numbers, etc.).</p>
                    </div>
                </div>
                <div>
                    <div class="text-gray-500">Customer</div>
                    <div class="font-medium" x-text="selected?.full_name"></div>
                    <div class="text-gray-500" x-text="selected?.email"></div>
                    <div class="text-gray-500" x-text="selected?.phone"></div>
                </div>
                <div>
                    <div class="text-gray-500">Route</div>
                    <div class="font-medium"><span x-text="selected?.origin"></span> → <span x-text="selected?.destination"></span></div>
                    <div><span class="text-gray-500">Dep:</span> <span x-text="selected?.depart_at"></span></div>
                    <div><span class="text-gray-500">Arr:</span> <span x-text="selected?.arrive_at"></span></div>
                </div>
                <div>
                    <div class="text-gray-500">Tickets</div>
                    <div class="font-medium"><span x-text="selected?.tickets"></span> total</div>
                    <div class="text-gray-500">Adult: <span x-text="selected?.adult"></span>, Child: <span x-text="selected?.child"></span>, Infant: <span x-text="selected?.infant"></span>, PWD: <span x-text="selected?.pwd"></span>, Student: <span x-text="selected?.student"></span></div>
                </div>
                <div>
                    <div class="text-gray-500">Payment</div>
                    <div class="font-medium" x-text="selected?.total_amount_label"></div>
                    <div class="text-gray-500">Status: <span x-text="selected?.payment_status"></span></div>
                </div>
                <div>
                    <div class="text-gray-500">Created</div>
                    <div class="font-medium" x-text="selected?.created_at"></div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-wrap items-center justify-end gap-2">
                <form :action="selected?.update_status_url" method="POST" onsubmit="return confirm('Confirm this booking?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="px-4 py-2 rounded-md bg-yellow-500 text-white hover:bg-yellow-600">Confirm</button>
                </form>
                <form :action="selected?.update_status_url" method="POST" onsubmit="return confirm('Cancel this booking?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Cancel</button>
                </form>
                <button type="button" class="px-4 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700" title="Integrate refund logic with payment gateway" onclick="alert('Refund flow to be integrated with your payment gateway.')">Refund</button>
            </div>
        </div>
    </div>
</div>
@endsection