<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-2xl p-8">
        <!-- Welcome -->
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Welcome to Balt-Bep Ferries</h2>
        <p class="text-center text-gray-500 mb-6">Sign in to continue</p>

        <!-- Google Login -->
        <a href="{{ url('auth/google') }}"
           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 mb-4">
            <svg class="w-5 h-5 mr-2" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.22 3.61l6.84-6.84C36.62 2.44 30.77 0 24 0 14.62 0 6.41 5.38 2.56 13.22l7.9 6.14C12.17 13.27 17.66 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.44 24.62c0-1.52-.14-2.98-.39-4.39H24v8.32h12.7c-.54 2.88-2.19 5.33-4.66 6.97l7.24 5.63c4.25-3.93 6.76-9.72 6.76-16.53z"/>
                <path fill="#FBBC05" d="M10.46 28.36c-.5-1.48-.78-3.05-.78-4.68s.28-3.2.78-4.68l-7.9-6.14C.89 15.82 0 19.76 0 23.68c0 3.92.89 7.86 2.56 11.14l7.9-6.46z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.92-2.13 15.9-5.8l-7.24-5.63c-2.01 1.37-4.59 2.18-8.66 2.18-6.34 0-11.83-3.77-14.46-9.22l-7.9 6.14C6.41 42.62 14.62 48 24 48z"/>
            </svg>
            Continue with Google
        </a>
        @error('google')
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ $message }}</div>
        @enderror

        <!-- Google is the only sign-in method on this page -->
        <p class="text-center text-gray-500 mb-2">Use your Google account to sign in.</p>

        <div class="text-center mt-4">
            <a href="{{ route('administration.login') }}" class="text-xs text-gray-300 hover:text-gray-400">&nbsp;</a>
        </div>
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
                title: 'Too many attempts',
                text: 'Your account is temporarily locked. Please try again later.',
                confirmButtonColor: '#f59e0b'
            })
        </script>
    @endif
</x-guest-layout>
