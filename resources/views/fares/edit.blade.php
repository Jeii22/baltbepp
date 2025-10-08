@extends('layouts.superadmin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-baltbep-blue">Edit Fare</h2>

    <form action="{{ route('fares.update', $fare->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Passenger Type</label>
            <select name="passenger_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="Regular" {{ old('passenger_type', $fare->passenger_type)=='Regular'?'selected':'' }}>Regular</option>
                <option value="Student" {{ old('passenger_type', $fare->passenger_type)=='Student'?'selected':'' }}>Student</option>
                <option value="Senior Citizen / PWD" {{ old('passenger_type', $fare->passenger_type)=='Senior Citizen / PWD'?'selected':'' }}>Senior Citizen / PWD</option>
                <option value="Child (2-11)" {{ old('passenger_type', $fare->passenger_type)=='Child (2-11)'?'selected':'' }}>Child (2-11)</option>
                <option value="Infant" {{ old('passenger_type', $fare->passenger_type)=='Infant'?'selected':'' }}>Infant</option>
            </select>
            @error('passenger_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Price (₱)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $fare->price) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="active" value="1" class="mr-2" {{ old('active', $fare->active) ? 'checked' : '' }}>
                Active
            </label>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('fares.index') }}" class="mr-3 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Fare</button>
        </div>
    </form>
</div>
@endsection