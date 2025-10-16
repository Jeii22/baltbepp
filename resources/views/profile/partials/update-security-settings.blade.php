<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Security Settings
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Manage your account security and two-factor authentication.
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <!-- Two-Factor Authentication -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">Two-Factor Authentication</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Add an extra layer of security to your account by requiring a verification code sent to your email.
                    </p>
                    
                    @if (auth()->user()->two_factor_enabled)
                        <div class="mt-3 flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-700">Two-factor authentication is enabled</span>
                        </div>
                    @else
                        <div class="mt-3 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Two-factor authentication is disabled</span>
                        </div>
                    @endif
                </div>

                <div class="ml-4">
                    @if (auth()->user()->two_factor_enabled)
                        <!-- Disable 2FA Form -->
                        <form method="POST" action="{{ route('two-factor.disable') }}" x-data="{ showConfirm: false }">
                            @csrf
                            @method('DELETE')

                            <button
                                type="button"
                                @click="showConfirm = true"
                                class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Disable
                            </button>

                            <!-- Confirmation Modal -->
                            <div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
                                <div class="absolute inset-0 bg-gray-900/60" @click="showConfirm = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Disable Two-Factor Authentication</h3>
                                    <p class="text-sm text-gray-600 mb-4">Please enter your password to confirm.</p>

                                    <div class="mb-4">
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                                            required
                                        >
                                    </div>

                                    <div class="flex items-center justify-end gap-3">
                                        <button
                                            type="button"
                                            @click="showConfirm = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700"
                                        >
                                            Disable 2FA
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <!-- Enable 2FA Form -->
                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Enable
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Account Security Info -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Security Information</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Never' }}</li>
                            @if(auth()->user()->last_login_ip)
                                <li>Last login IP: {{ auth()->user()->last_login_ip }}</li>
                            @endif
                            <li>Account created: {{ auth()->user()->created_at->format('M d, Y') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>