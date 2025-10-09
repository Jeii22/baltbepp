<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied - Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="antialiased bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <!-- Hidden content, will be covered by modal -->
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center opacity-0">
            <h1>Access Denied</h1>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const message = "{{ $exception->getMessage() ?: 'You do not have permission to access this area.' }}";
        const roleInfo = @auth "{{ 'Current Role: ' . auth()->user()->getRoleDisplayName() }}" @else "Please log in to access this area." @endauth;

        Swal.fire({
            icon: 'error',
            title: 'Access Denied',
            html: `
                <p class="text-left mb-4">${message}</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-left">
                    <p class="text-sm text-blue-800">${roleInfo}</p>
                    @auth
                    <p class="text-xs text-blue-600 mt-1">You are logged in as {{ auth()->user()->name }}</p>
                    @endauth
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Go to Dashboard',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                @auth
                    @if(auth()->user()->isSuperAdmin())
                        window.location.href = "{{ route('dashboard') }}";
                    @elseif(auth()->user()->isAdmin())
                        window.location.href = "{{ route('welcome') }}";
                    @else
                        window.location.href = "{{ route('welcome') }}";
                    @endif
                @else
                    window.location.href = "{{ route('login') }}";
                @endauth
            }
        });
    });
    </script>
</body>
</html>