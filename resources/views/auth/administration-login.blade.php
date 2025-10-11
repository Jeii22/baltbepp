<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-xl rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">
            Administration Access
        </h2>
        <p class="text-center text-gray-500 mb-6">Enter credentials</p>

        <form method="POST" action="{{ route('administration.login.attempt') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('password.request.otp') }}" class="text-sm text-gray-500 hover:underline">Forgot password?</a>
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:underline">Back to user login</a>
            </div>

            <x-primary-button class="w-full">
                {{ __('Log in') }}
            </x-primary-button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login failed',
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
                title: 'Account locked',
                text: 'Too many failed attempts. Please try again later.',
                confirmButtonColor: '#f59e0b'
            })
        </script>
    @endif
</x-guest-layout>