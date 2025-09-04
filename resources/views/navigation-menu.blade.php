<script>console.log('resources/views/navigation-menu.blade.php');</script>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:border-gray-700 dark:bg-gray-800">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                @php
                $user = App\Helpers\FegiAuth::user(); // User object or null
                @endphp
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group"
                        aria-label="{{ __('collection.logo_home_link_aria_label') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="Frangette Logo"
                            class="w-auto h-7 sm:h-8 md:h-9" loading="lazy" decoding="async">
                        <span
                            class="hidden text-base font-semibold text-gray-400 transition group-hover:text-emerald-400 sm:inline md:text-lg">{{
                            __('Frangette') }}</span>
                    </a>
                    {{-- Welcome Message - Dopo il logo per tutte le dimensioni --}}
                    @if ($user)
                    <div class="flex items-center ml-4">
                        <span class="hidden text-sm font-medium text-emerald-400 sm:inline">{{ App\Helpers\FegiAuth::getWelcomeMessage() }}</span>
                        <span class="text-xs font-medium text-emerald-500 sm:hidden">
                            @php
                            $welcomeMessage = App\Helpers\FegiAuth::getWelcomeMessage();
                            // Rimuovi "Benvenuto/a," dalla versione mobile
                            $mobileMessage = preg_replace('/^Benvenuto\/a,?\s*/i', '', $welcomeMessage);
                            @endphp
                            {{ $mobileMessage }}
                        </span>
                    </div>
                    @endif
                </div>
                {{-- Notification Badge (Desktop) --}}
                @if(App\Helpers\FegiAuth::check())
                <div class="hidden md:block">
                    <x-notification-badge />
                </div>
                @endif
                <!-- Navigation Links Desktop -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @include('partials.nav-links', ['isMobile' => false])
                </div>
                 
            </div>

            <div class="flex">
                <div class="flex items-center text-4xl text-gray-700 shrink-0 dark:text-gray-500">
                    {{ Auth::user()?->name ?? '' }}
                </div>
            </div>

            <!-- Desktop Mega Menu Component -->
            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                <x-navigation.vanilla-desktop-menu />
            </div>

            <!-- Sezione mobile (schermi piccoli) con due pulsanti: uno per la navbar, uno per la sidebar -->
            <div class="flex items-center -me-2 sm:hidden">
                <!-- Pulsante per togglare il menu della NAVBAR (Alpine) -->
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:bg-gray-100 hover:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <!-- Icona hamburger (visibile quando open = false) -->
                        <path :class="{ 'inline-flex': !open, 'hidden': open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <!-- Icona X (visibile quando open = true) -->
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Component -->
    <x-navigation.vanilla-mobile-menu />
</nav>
