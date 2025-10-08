@extends('layouts.superadmin')

@section('content')
<div class="py-12">
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <header class="bg-white border-b border-gray-200 shadow px-6 py-3 flex justify-between items-center">
                <h1 class="text-lg font-semibold">Profile</h1>
    </header>
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>

@endsection
