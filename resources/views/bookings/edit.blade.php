@extends('layouts.superadmin')

@section('content')
<div class="p-6 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Edit Booking #{{ $booking->id }}</h1>

    <form method="POST" action="{{ route('bookings.update', $booking) }}" class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 mb-2">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="full_name" value="{{ old('full_name', $booking->full_name) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $booking->email) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $booking->phone) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Adult</label>
                <input type="number" name="adult" min="0" value="{{ old('adult', $booking->adult) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Child</label>
                <input type="number" name="child" min="0" value="{{ old('child', $booking->child) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Infant</label>
                <input type="number" name="infant" min="0" value="{{ old('infant', $booking->infant) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">PWD</label>
                <input type="number" name="pwd" min="0" value="{{ old('pwd', $booking->pwd) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Student</label>
                <input type="number" name="student" min="0" value="{{ old('student', $booking->student) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="pending" @selected(old('status', $booking->status)==='pending')>Pending</option>
                <option value="confirmed" @selected(old('status', $booking->status)==='confirmed')>Confirmed</option>
                <option value="cancelled" @selected(old('status', $booking->status)==='cancelled')>Cancelled</option>
            </select>
        </div>

        <div class="flex items-center gap-2 pt-2">
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Save Changes</button>
        </div>
    </form>
</div>
@endsection