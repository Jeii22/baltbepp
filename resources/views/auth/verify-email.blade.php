<x-guest-layout>
    <div class="max-w-lg mx-auto bg-white shadow-xl rounded-2xl p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-gray-900">Confirm your Balt Bep account</h1>
            <p class="text-sm text-gray-600">We sent a 6-digit OTP to your Gmail along with a "Confirm email to Balt Bep" button.</p>
        </div>

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 text-sm rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('status') == 'verification-link-sent')
            <div class="p-3 bg-blue-100 text-blue-700 text-sm rounded-lg">
                A new verification email has been sent.
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 text-sm rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.otp') }}" class="space-y-4">
            @csrf
            <label for="code" class="block text-sm font-medium text-gray-700">Enter OTP</label>
            <input id="code" name="code" type="text" maxlength="6" required autocomplete="one-time-code" value="{{ old('code') }}"
                   class="w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="000000">
            @error('code')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                Confirm Email
            </button>
        </form>

        <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
            @csrf
            <button type="submit" class="w-full py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                Resend confirmation email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full text-sm text-gray-500 hover:text-gray-700">
                Log out
            </button>
        </form>
    </div>

    <div id="otp-waiting-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 space-y-4">
            <h2 class="text-xl font-semibold text-gray-900 text-center">Waiting for confirmation</h2>
            <p class="text-sm text-gray-600 text-center">Solve the captcha, check your Gmail for the OTP, then click the "Confirm email to Balt Bep" button in the message.</p>
            <button id="otp-modal-close" class="w-full py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                Enter OTP
            </button>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            const modal = document.getElementById('otp-waiting-modal');
            const close = document.getElementById('otp-modal-close');
            if (modal && close) {
                close.addEventListener('click', function () {
                    modal.style.display = 'none';
                });
            }
        });
    </script>
</x-guest-layout>
