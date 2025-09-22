@extends('layouts.superadmin')

@section('content')
    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Show success toast after login/redirect
            Swal.fire({
                icon: 'success',
                title: 'Welcome! ',
                text: '{{ session('success') }}',
                confirmButtonColor: '#10b981'
            })
        </script>
    @endif

    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800">Superadmin Dashboard</h2>
            <p class="mt-2 text-gray-600">Welcome, {{ auth()->user()->getRoleDisplayName() }}.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg p-5">
                <div class="text-gray-500 text-sm">Users</div>
                <div class="text-2xl font-bold">—</div>
                <a href="{{ route('users.index') }}" class="text-blue-600 text-sm hover:underline mt-2 inline-block">Manage users</a>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <div class="text-gray-500 text-sm">Trips</div>
                <div class="text-2xl font-bold">—</div>
                <a href="{{ route('trips.index') }}" class="text-blue-600 text-sm hover:underline mt-2 inline-block">Manage trips</a>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <div class="text-gray-500 text-sm">Fares</div>
                <div class="text-2xl font-bold">—</div>
                <a href="{{ route('fares.index') }}" class="text-blue-600 text-sm hover:underline mt-2 inline-block">Manage fares</a>
            </div>
        </div>
    </div>
@endsection