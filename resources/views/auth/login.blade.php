<x-guest-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @php
        $activeForm = old('form_type', 'login') === 'register' ? 'register' : 'login';
    @endphp

    <div
        x-data="{
            activeForm: '{{ $activeForm }}',
            showSecurityModal: false,
            termsError: '',
            handleRegisterClick() {
                if (!this.$refs.registerFormTerms.checked) {
                    this.termsError = 'You must accept the terms & conditions before continuing.';
                    return;
                }
                this.termsError = '';
                this.showSecurityModal = true;
            },
            proceedRegistration() {
                this.showSecurityModal = false;
                this.$refs.registerForm.submit();
            }
        }"
        class="max-w-md mx-auto bg-white shadow-lg rounded-2xl p-8 relative"
        @keydown.escape.window="showSecurityModal = false"
    >
        <!-- Welcome -->
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Welcome to Balt-Bep Ferries</h2>
        <p class="text-center text-gray-500 mb-6">Choose how you want to continue</p>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="flex-1 px-4 py-2 rounded-lg font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                :class="activeForm === 'login' ? 'bg-blue-600 text-white shadow focus:ring-blue-500' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 focus:ring-blue-500'"
                :aria-expanded="activeForm === 'login'"
                @click="activeForm = 'login'"
            >
                Login
            </button>
            <button
                type="button"
                class="flex-1 px-4 py-2 rounded-lg font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                :class="activeForm === 'register' ? 'bg-green-600 text-white shadow focus:ring-green-500' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 focus:ring-green-500'"
                :aria-expanded="activeForm === 'register'"
                @click="activeForm = 'register'"
            >
                Register
            </button>
        </div>

        <!-- Forms -->
        <div class="mt-6 space-y-6">
            <!-- Login Form -->
            <div x-show="activeForm === 'login'" x-transition x-cloak>
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="form_type" value="login">

                    <!-- Email Address -->
                    <div>
                        <label for="login_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="login_email" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="login_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="login_password" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="password" name="password" required autocomplete="current-password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" name="remember">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                        </label>
                        <a class="text-sm text-blue-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request.otp') }}">
                            Forgot your password?
                        </a>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Log in
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div x-show="activeForm === 'register'" x-transition x-cloak>
                <form x-ref="registerForm" method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="form_type" value="register">

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input id="name" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500" type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="register_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="register_email" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="register_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="register_password" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500" type="password" name="password" required autocomplete="new-password">
                        <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters and include one uppercase letter, one number, and one special character.</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500" type="password" name="password_confirmation" required autocomplete="new-password">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    <div>
                        <label class="inline-flex items-start text-sm text-gray-600">
                            <input
                                x-ref="registerFormTerms"
                                type="checkbox"
                                name="terms"
                                value="1"
                                class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                {{ old('terms') ? 'checked' : '' }}
                                @change="termsError = ''"
                            >
                            <span class="ml-2 leading-tight">
                                I agree to the
                                <a href="{{ route('terms-of-service') }}" class="text-green-600 hover:text-green-500" target="_blank" rel="noopener">Terms &amp; Conditions</a>
                                and
                                <a href="{{ route('privacy-policy') }}" class="text-green-600 hover:text-green-500" target="_blank" rel="noopener">Privacy Policy</a>.
                            </span>
                        </label>
                        <p x-show="termsError" x-text="termsError" class="mt-1 text-sm text-red-600"></p>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="button"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        @click="handleRegisterClick"
                    >
                        Create account
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('administration.login') }}" class="text-xs text-gray-300 hover:text-gray-400">&nbsp;</a>
        </div>

        <!-- Security Modal -->
        <div
            x-show="showSecurityModal"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <div class="absolute inset-0 bg-gray-900/60" @click="showSecurityModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Security Check</h3>
                        <p class="text-sm text-gray-500 mt-1">We take your account protection seriously.</p>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="showSecurityModal = false">
                        <span class="sr-only">Close</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 text-sm text-gray-600">
                    <p>After creating your account we will:</p>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Send a one-time passcode (OTP) to your registered Gmail address.</li>
                        <li>Guide you through setting up multi-factor authentication for enhanced security.</li>
                        <li>Monitor unusual sign-in attempts and alert you instantly.</li>
                    </ul>
                    <p class="text-sm text-gray-500">Make sure you have access to your Gmail inbox to complete the verification process.</p>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800"
                        @click="showSecurityModal = false"
                    >
                        Review Again
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        @click="proceedRegistration"
                    >
                        Confirm &amp; Register
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Something went wrong',
                text: '{{ $errors->first('email') ?? $errors->first() }}',
                confirmButtonColor: '#ef4444'
            })
        </script>
    @endif
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Welcome!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#10b981'
            })
        </script>
    @endif
    @if (session('status') === 'locked')
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Too many attempts',
                text: 'Your account is temporarily locked. Please try again later.',
                confirmButtonColor: '#f59e0b'
            })
        </script>
    @endif
</x-guest-layout>
