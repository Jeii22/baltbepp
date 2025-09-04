<nav class="bg-blue-700 text-white shadow-md px-6 py-3 flex justify-end">
    @auth
        <!-- Right: User Dropdown (authenticated) -->
        <div>
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center text-white hover:text-cyan-200">
                        <span class="mr-2">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <!-- Edit Profile -->
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Edit Profile') }}
                    </x-dropdown-link>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                         onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    @else
        <!-- Right: Auth links (guest) -->
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="hover:underline">Log in</a>
            <a href="{{ route('register') }}" class="px-3 py-1 rounded bg-white text-blue-700 font-medium hover:bg-blue-50">Register</a>
        </div>
    @endauth
</nav>
