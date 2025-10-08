@extends('layouts.superadmin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-baltbep-blue">Edit Trip</h2>

    <form action="{{ route('trips.update', $trip->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Origin -->
        <div class="mb-4">
            <label for="origin" class="block text-sm font-medium text-gray-700">Origin</label>
            <input type="text" name="origin" id="origin" value="{{ old('origin', $trip->origin) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('origin')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Destination -->
        <div class="mb-4">
            <label for="destination" class="block text-sm font-medium text-gray-700">Destination</label>
            <input type="text" name="destination" id="destination" value="{{ old('destination', $trip->destination) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('destination')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Departure Time -->
        <div class="mb-4">
            <label for="departure_time" class="block text-sm font-medium text-gray-700">Departure Time</label>
            <input type="datetime-local" name="departure_time" id="departure_time"
                   value="{{ old('departure_time', \Carbon\Carbon::parse($trip->departure_time)->format('Y-m-d\TH:i')) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('departure_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Arrival Time -->
        <div class="mb-4">
            <label for="arrival_time" class="block text-sm font-medium text-gray-700">Arrival Time</label>
            <input type="datetime-local" name="arrival_time" id="arrival_time"
                   value="{{ old('arrival_time', \Carbon\Carbon::parse($trip->arrival_time)->format('Y-m-d\TH:i')) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('arrival_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Capacity -->
        <div class="mb-4">
            <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $trip->capacity) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="1" required>
            @error('capacity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700">Price (₱)</label>
            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $trip->price) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="0" required>
            @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-end">
            <a href="{{ route('trips.index') }}"
               class="mr-3 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
               Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Update Trip
            </button>
        </div>
    </form>
</div>
@endsection