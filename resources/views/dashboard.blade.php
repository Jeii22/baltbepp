@extends('layouts.superadmin')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

    <!-- Example dashboard cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-600">Total Users</h2>
            <p class="text-3xl font-bold text-blue-600">120</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-600">Total Bookingsdsdsdsd</h2>
            <p class="text-3xl font-bold text-green-600">350</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-600">Revenue</h2>
            <p class="text-3xl font-bold text-cyan-600">₱250,000</p>
        </div>
    </div>
@endsection
