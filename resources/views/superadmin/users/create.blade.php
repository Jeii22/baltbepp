@extends('layouts.superadmin')

@section('content')
<h1 class="text-2xl font-bold mb-6">Add Admin</h1>

@if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.store') }}" class="bg-white shadow rounded-xl p-6 max-w-xl">
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1" for="first_name">First Name</label>
            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="last_name">Last Name</label>
            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1" for="username">Username</label>
        <input id="username" name="username" type="text" value="{{ old('username') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1" for="role">Role</label>
        <select id="role" name="role" class="w-full border rounded px-3 py-2" required>
            <option value="admin" selected>Admin</option>
        </select>
    </div>

    <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create</button>
    </div>
</form>
@endsection