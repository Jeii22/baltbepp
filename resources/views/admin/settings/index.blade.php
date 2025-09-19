@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Settings</h1>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('settings.index') }}" class="bg-white border rounded p-6 space-y-4 shadow-sm">
            @csrf
            @method('PUT')

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

            <div class="pt-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection