@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Settings</h1>

        <!-- User Profile Information -->
        <div class="bg-white border rounded p-6 mb-6 shadow-sm">
            <h2 class="text-lg font-medium mb-4">Profile Information</h2>
            <p class="text-sm text-gray-600 mb-4">Update your account's profile information and email address.</p>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name" />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username" />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Profile</button>

                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-green-600">Profile updated successfully.</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- App Settings -->
        <form method="POST" action="{{ route('settings.update') }}" class="bg-white border rounded p-6 space-y-6 shadow-sm">
            @csrf

            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium">Enable COD (Cash on Departure)</div>
                    <div class="text-sm text-gray-600">If enabled, customers can select COD at checkout.</div>
                </div>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="cod_enabled" class="sr-only" {{ $codEnabled ? 'checked' : '' }}>
                    <span class="w-11 h-6 bg-gray-200 rounded-full relative transition">
                        <span class="dot absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition {{ $codEnabled ? 'translate-x-5 bg-blue-600' : '' }}"></span>
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium">Enable PayMongo (GCash/Card)</div>
                    <div class="text-sm text-gray-600">If disabled, PayMongo options will be hidden and unusable.</div>
                </div>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="paymongo_enabled" class="sr-only" {{ $paymongoEnabled ? 'checked' : '' }}>
                    <span class="w-11 h-6 bg-gray-200 rounded-full relative transition">
                        <span class="dot absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition {{ $paymongoEnabled ? 'translate-x-5 bg-blue-600' : '' }}"></span>
                    </span>
                </label>
            </div>

            <!-- Dark Mode Toggle -->
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium">Dark Mode</div>
                    <div class="text-sm text-gray-600">Toggle between light and dark theme.</div>
                </div>
                <label class="inline-flex items-center cursor-pointer" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', value => { localStorage.setItem('darkMode', value); if (value) { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') } }); if (darkMode) document.documentElement.classList.add('dark')">
                    <input type="checkbox" x-model="darkMode" class="sr-only">
                    <span class="w-11 h-6 bg-gray-200 rounded-full relative transition" :class="{ 'bg-blue-600': darkMode }">
                        <span class="dot absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition" :class="{ 'translate-x-5': darkMode }"></span>
                    </span>
                </label>
            </div>

            <div class="pt-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection