<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
@php
    $user = auth()->user();
    $passwordErrors = $errors->getBag('updatePassword');
@endphp
<body class="antialiased bg-white text-gray-800">

    <nav class="absolute top-0 left-0 w-full z-20 bg-black/30 backdrop-blur-sm" x-data="{ open: false, dropdownOpen: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <button @click="open = !open" class="md:hidden text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="relative">
                @auth
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-white hover:text-cyan-200 transition">
                        <span>Welcome {{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('dashboard') }}" class="ml-3 bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg transition">
                            Super Admin Dashboard
                        </a>
                    @elseif(Auth::user()->isAdmin())
                        <a href="{{ route('dashboard') }}" class="ml-3 bg-green-600 hover:bg-green-700 px-3 py-1 rounded-lg transition">
                            Admin Dashboard
                        </a>
                    @endif

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-gray-800 hover:bg-blue-50">My Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-800 hover:bg-blue-50 rounded-b-lg">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="border border-white px-4 py-2 rounded-lg text-white hover:bg-white hover:text-blue-600 transition">Sign In</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="relative bg-cover bg-center h-[50vh]" style="background-image: url('/images/barko.png');">
        
    </div>

    <div class="relative -mt-40 max-w-5xl mx-auto bg-white/90 backdrop-blur-md rounded-3xl shadow-2xl ring-1 ring-black/5 p-8 md:p-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-sm uppercase tracking-widest text-blue-600">Account Settings</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Hello, {{ $user->display_name ?? $user->name }}</h2>
                <p class="text-gray-600 mt-3 max-w-xl">Review and update your personal details, or change your password to keep your journeys protected.</p>
                <p class="text-sm text-gray-500 mt-2">Member since {{ $user->created_at ? \Illuminate\Support\Carbon::parse($user->created_at)->format('M d, Y') : 'Recently' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('customer.dashboard') }}" class="bg-white text-blue-600 px-5 py-3 rounded-xl border border-blue-200 hover:bg-blue-50 transition shadow">Back to Profile</a>
                <a href="{{ route('welcome') }}" class="bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 transition shadow">Book a Trip</a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8">
            <section class="bg-white rounded-2xl border border-blue-100 shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-900">Profile Information</h3>
                <p class="mt-1 text-sm text-gray-500">Keep your name and email address up to date.</p>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
                    @csrf
                </form>

                @if ($errors->any())
                    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="photo" class="block text-sm font-semibold text-gray-700">Profile Photo</label>
                        <div class="mt-2 flex items-end gap-4">
                            @if($user->photo)
                                <img src="data:image/jpeg;base64,{{ base64_encode($user->photo) }}" alt="Profile Photo" class="h-24 w-24 rounded-xl border border-gray-200 object-cover" />
                            @else
                                <div class="h-24 w-24 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                            @endif
                            <input id="photo" name="photo" type="file" accept="image/*" class="flex-1 rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                            <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Accepted: JPG, JPEG, PNG, GIF.</p>
</form>
<script>
document.getElementById('photo')?.addEventListener('change', function(e){
    const file = e.target.files?.[0];
    if (file && file.size > 2 * 1024 * 1024) { // 2MB
        alert('Selected file exceeds the 2MB limit. Please choose a smaller image.');
        e.target.value='';
    }
});
</script>
                        </div>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-semibold text-gray-700">Display Name</label>
                        <input id="display_name" name="display_name" type="text" value="{{ old('display_name', $user->display_name ?? $user->name) }}" autocomplete="nickname" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-3 rounded-lg border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-700">
                                Your email address is unverified.
                                <button form="send-verification" class="ml-2 font-semibold text-blue-600 hover:text-blue-800">Resend verification email</button>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-xl font-semibold shadow hover:bg-blue-700 transition">Save changes</button>
                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-green-600">Profile updated successfully.</p>
                        @endif
                    </div>
                </form>
            </section>

            <section class="bg-white rounded-2xl border border-blue-100 shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-900">Update Password</h3>
                <p class="mt-1 text-sm text-gray-500">Use a strong, unique password to protect your adventures.</p>

                @if ($passwordErrors->any())
                    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <ul class="space-y-1">
                            @foreach ($passwordErrors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-gray-700">Current Password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700">New Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-xl font-semibold shadow hover:bg-blue-700 transition">Update password</button>
                        @if (session('status') === 'password-updated')
                            <p class="text-sm text-green-600">Password updated successfully.</p>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    <section class="py-16 bg-blue-50 mt-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                    <p class="text-sm uppercase tracking-widest text-blue-600">Next Steps</p>
                    <h3 class="text-xl font-bold text-gray-900 mt-2">Plan Another Trip</h3>
                    <p class="text-gray-600 mt-3">Explore routes, schedules, and fares with our booking engine.</p>
                    <a href="{{ route('booking.schedule') }}" class="inline-flex items-center mt-6 text-blue-600 font-semibold hover:text-blue-800">Search Trips</a>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                    <p class="text-sm uppercase tracking-widest text-blue-600">Account</p>
                    <h3 class="text-xl font-bold text-gray-900 mt-2">Need to make more changes?</h3>
                    <p class="text-gray-600 mt-3">Return to your dashboard anytime to review bookings and updates.</p>
                    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center mt-6 text-blue-600 font-semibold hover:text-blue-800">Go to Dashboard</a>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                    <p class="text-sm uppercase tracking-widest text-blue-600">Support</p>
                    <h3 class="text-xl font-bold text-gray-900 mt-2">Need Assistance?</h3>
                    <p class="text-gray-600 mt-3">Our team is ready to help with bookings, payments, and travel questions.</p>
                    <a href="mailto:support@baltbep.com" class="inline-flex items-center mt-6 text-blue-600 font-semibold hover:text-blue-800">Contact Support</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('profile.update') }}"]');
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        Swal.fire({
                            title: 'Profile Updated!',
                            text: 'Your profile has been successfully updated.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = '{{ route('welcome') }}';
                        }, 2000);
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'An error occurred');
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    </script>
</body>
</html>
