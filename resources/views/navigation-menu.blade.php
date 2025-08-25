<script>console.log('resources/views/navigation-menu.blade.php');</script>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:border-gray-700 dark:bg-gray-800">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block w-auto h-9" />
                    </a>
                </div>

                <!-- Navigation Links Desktop -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('home.collections.index') }}" :active="request()->routeIs('home.collections.*')">
                        {{ __('Collections') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('epps.index') }}" :active="request()->routeIs('epps.*')">
                        {{ __('EPPS') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="flex">
                <div class="flex items-center text-4xl text-gray-700 shrink-0 dark:text-gray-500">
                    {{ Auth::user()?->name ?? '' }}
                </div>
            </div>

            <!-- Desktop Mega Menu Component -->
            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                <x-navigation.desktop-mega-menu />
            </div>

            <!-- Sezione mobile (schermi piccoli) con due pulsanti: uno per la navbar, uno per la sidebar -->
            <div class="flex items-center -me-2 sm:hidden">
                <!-- Pulsante per togglare la SIDEBAR (Drawer DaisyUI) -->
                <!-- Usa un'icona diversa per distinguere questo dal menu navbar -->
                <label for="main-drawer"
                    class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md ms-2 hover:bg-gray-100 hover:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <!-- Ad esempio puntini che simboleggiano la sidebar -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6h9M10 12h9M10 18h9M4 6h.01M4 12h.01M4 18h.01" />
                    </svg>
                </label>

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
    <x-navigation.mobile-navigation />
</nav>
