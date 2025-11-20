@extends('layouts.superadmin')

@section('content')
<div class="w-full">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Administrator</h1>
                <p class="text-gray-600">Update administrator account details and permissions.</p>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Users
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Form Card -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <div class="px-12 py-8 bg-gradient-to-r from-blue-600 to-cyan-600">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                    {{ strtoupper(substr($user->display_name, 0, 2)) }}
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-white">{{ $user->display_name }}</h2>
                    <p class="text-blue-100 text-sm mt-1">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}" class="px-12 py-12 space-y-12" onsubmit="showLoading('Updating user...')">
            @csrf
            @method('PUT')
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: @json(session('success')),
                            confirmButtonColor: '#3085d6',
                        });
                    });
                </script>
            @endif
            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: @json(session('error')),
                            confirmButtonColor: '#d33',
                        });
                    });
                </script>
            @endif

            <!-- Personal Information Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                    <p class="text-sm text-gray-500 mt-1">Update basic details for the administrator account</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="first_name">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter first name" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="last_name">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter last name" required>
                    </div>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                    <p class="text-sm text-gray-500 mt-1">Update login credentials and account settings</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="admin@baltbep.com" required>
                        <p class="text-xs text-gray-500 mt-1">This will be used for login and notifications</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="username">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter username" required>
                        <p class="text-xs text-gray-500 mt-1">Unique identifier for the admin account</p>
                    </div>
                </div>
            </div>

            <!-- Role Selection Section -->
            @if(auth()->user()->role === 'super_admin')
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Role & Permissions</h3>
                    <p class="text-sm text-gray-500 mt-1">Assign role and access level for this administrator</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Administrator Role <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-6 cursor-pointer border-2 rounded-xl transition-all {{ old('role', $user->role) === 'admin' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-200 bg-white' }}">
                            <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} 
                                   class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500" required>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                        Admin
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mt-2">Regular Administrator</p>
                                <p class="text-xs text-gray-500 mt-1">Can manage bookings, trips, and view reports. Limited access to system settings.</p>
                            </div>
                        </label>

                        <label class="relative flex items-start p-6 cursor-pointer border-2 rounded-xl transition-all {{ old('role', $user->role) === 'super_admin' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-200 bg-white' }}">
                            <input type="radio" name="role" value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'checked' : '' }} 
                                   class="mt-1 h-4 w-4 text-purple-600 focus:ring-purple-500" required>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-2">
                                        Super Admin
                                    </span>
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mt-2">Super Administrator</p>
                                <p class="text-xs text-gray-500 mt-1">Full system access including user management, system settings, and all administrative functions.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            @else
            <input type="hidden" name="role" value="{{ $user->role }}">
            @endif

            <!-- Password Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Change Password</h3>
                    <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="password">
                            New Password <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input id="password" name="password" type="password" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter new password">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="password_confirmation">
                            Confirm New Password
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Confirm new password">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                <a href="{{ route('users.index') }}" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Administrator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
