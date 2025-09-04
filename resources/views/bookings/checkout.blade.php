@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6">
    <h1 class="text-2xl font-bold mb-4">Payment</h1>

    <div class="border rounded-lg p-4 mb-6">
        <p class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</p>
        <p class="text-sm text-gray-600">Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</p>
    </div>

    <div class="border rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Order</h2>
        <ul class="text-sm text-gray-700 space-y-1">
            <li>Adult × {{ $counts['adult'] }} @ ₱{{ number_format($fares['adult'],2) }}</li>
            <li>Child × {{ $counts['child'] }} @ ₱{{ number_format($fares['child'],2) }}</li>
            <li>Infant × {{ $counts['infant'] }} @ ₱{{ number_format($fares['infant'],2) }}</li>
            <li>PWD/Senior × {{ $counts['pwd'] }} @ ₱{{ number_format($fares['pwd'],2) }}</li>
        </ul>
        <div class="mt-3 text-right">
            <p class="text-xl font-bold">Total: ₱{{ number_format($subtotal, 2) }}</p>
        </div>
    </div>

    <form action="{{ route('bookings.process') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <span>Cash on Departure</span>
                </label>
                <label class="flex items-center gap-2 opacity-60">
                    <input type="radio" name="payment_method" value="card" disabled>
                    <span>Card (coming soon)</span>
                </label>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded">Pay Now</button>
        </div>
    </form>
</div>
@endsection