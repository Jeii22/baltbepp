@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Trip Management</h1>
    <a href="{{ route('trips.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Trip</a>

    <table class="w-full mt-4 border">
        <thead class="bg-blue-100">
            <tr>
                <th class="px-4 py-2">Origin</th>
                <th class="px-4 py-2">Destination</th>
                <th class="px-4 py-2">Departure</th>
                <th class="px-4 py-2">Arrival</th>
                <th class="px-4 py-2">Capacity</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trips as $trip)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $trip->origin }}</td>
                <td class="px-4 py-2">{{ $trip->destination }}</td>
                <td class="px-4 py-2">{{ $trip->departure_time }}</td>
                <td class="px-4 py-2">{{ $trip->arrival_time }}</td>
                <td class="px-4 py-2">{{ $trip->capacity }}</td>
                <td class="px-4 py-2">₱{{ $trip->price }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('trips.edit', $trip->id) }}" class="text-blue-500">Edit</a> |
                    <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-500" onclick="return confirm('Delete this trip?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
