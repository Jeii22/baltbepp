<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-2xl p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Verify Your Account</h2>
            <p class="text-gray-500 mt-2">We've sent a 6-digit verification code to your Gmail address.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Verification Form -->
        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf

            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                <input 
                    id="code" 
                    type="text" 
                    name="code" 
                    maxlength="6"
                    class="block w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="000000"
                    required 
                    autofocus
                    autocomplete="one-time-code"
                >
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button 
                type="submit" 
                class="w-full px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Verify Code
            </button>
        </form>

        <!-- Resend Code -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
            <form method="POST" action="{{ route('two-factor.resend') }}">
                @csrf
                <button 
                    type="submit" 
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium focus:outline-none focus:underline"
                >
                    Resend Code
                </button>
            </form>
        </div>

        <!-- Back to Login -->
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Back to Login
            </a>
        </div>
    </div>

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Account Verification Failed',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#ef4444'
            })
        </script>
    @endif
</x-guest-layout>