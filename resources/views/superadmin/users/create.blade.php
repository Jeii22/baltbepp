@extends('layouts.superadmin')

@section('content')
<div class="w-full">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Administrator</h1>
        <p class="text-gray-600">Create a new administrator account with access to the super admin panel.</p>
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
            <h2 class="text-xl font-semibold text-white">Administrator Information</h2>
            <p class="text-blue-100 text-sm mt-1">Fill in the details below to create a new admin account</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="px-12 py-12 space-y-12">
            @csrf

            <!-- Personal Information Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                    <p class="text-sm text-gray-500 mt-1">Basic details for the administrator account</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="first_name">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter first name" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="last_name">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter last name" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="middle_name">
                            Middle Name <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter middle name">
                    </div>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                    <p class="text-sm text-gray-500 mt-1">Login credentials and account settings</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="admin@baltbep.com" required>
                        <p class="text-xs text-gray-500 mt-1">This will be used for login and notifications</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="username">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter username" required>
                        <p class="text-xs text-gray-500 mt-1">Unique identifier for the admin account</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="role">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select id="role" name="role" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="admin" selected>Administrator</option>
                            <option value="super_admin">Super Administrator</option>
                            <option value="moderator">Moderator</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Defines access level and permissions</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="phone">
                            Phone Number <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="+63 912 345 6789">
                        <p class="text-xs text-gray-500 mt-1">Contact number for account recovery</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="department">
                            Department <span class="text-gray-400">(Optional)</span>
                        </label>
                        <select id="department" name="department" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Department</option>
                            <option value="operations">Operations</option>
                            <option value="customer_service">Customer Service</option>
                            <option value="finance">Finance</option>
                            <option value="management">Management</option>
                            <option value="it">IT Support</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Administrative department assignment</p>
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Security Settings</h3>
                    <p class="text-sm text-gray-500 mt-1">Set up secure login credentials</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="password">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input id="password" name="password" type="password" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Enter secure password" required>
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters recommended</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="password_confirmation">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Confirm password" required>
                        <p class="text-xs text-gray-500 mt-1">Must match the password above</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="access_level">
                            Access Level <span class="text-red-500">*</span>
                        </label>
                        <select id="access_level" name="access_level" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="full" selected>Full Access</option>
                            <option value="limited">Limited Access</option>
                            <option value="read_only">Read Only</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">System access permissions level</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="status">
                            Account Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending Activation</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Initial account status</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="notes">
                            Admin Notes <span class="text-gray-400">(Optional)</span>
                        </label>
                        <textarea id="notes" name="notes" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" 
                                  placeholder="Additional notes about this administrator...">{{ old('notes') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Internal notes for reference</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-cyan-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-lg">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Administrator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection