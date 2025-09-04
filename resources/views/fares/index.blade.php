@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Fare Management</h1>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">{{ session('success') }}</div>
    @endif

    <a href="{{ route('fares.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Fare</a>

    <table class="w-full mt-4 border">
        <thead class="bg-blue-100">
            <tr>
                <th class="px-4 py-2">Passenger Type</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($fares as $fare)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $fare->passenger_type }}</td>
                    <td class="px-4 py-2">₱{{ number_format($fare->price, 2) }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded text-xs {{ $fare->active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $fare->active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('fares.edit', $fare->id) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('fares.destroy', $fare->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline" onclick="return confirm('Delete this fare?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-600">No fares yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection