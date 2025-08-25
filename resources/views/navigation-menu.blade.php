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

                {{-- @if (Auth::user()->can('superadmin')) --}}
                {{-- @endif --}}

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


            <div class="hidden sm:ms-6 sm:flex sm:items-center">

                <!-- Advanced Mega Menu Dropdown -->
                <div class="relative ms-3">
                    <x-dropdown align="right" width="96" contentClasses="bg-transparent border-0 shadow-none p-0">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    data-dropdown-toggle="mega-menu"
                                    class="flex text-sm transition border-2 border-transparent rounded-full focus:border-gray-300 focus:outline-none hover:scale-110 transform duration-300">
                                    <img class="object-cover rounded-full size-8 ring-2 ring-blue-500/20 hover:ring-blue-500/60 transition-all duration-300"
                                        src="{{ Auth::user()?->profile_photo_url ?? null }}"
                                        alt="{{ Auth::user()?->name ?? '' }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        data-dropdown-toggle="mega-menu"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition-all duration-300 ease-in-out bg-white border border-transparent rounded-lg hover:text-gray-700 hover:bg-gray-50 hover:shadow-lg hover:scale-105 focus:bg-gray-50 focus:outline-none active:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:bg-gray-700 dark:active:bg-gray-700 group">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span class="hidden sm:block">{{ Auth::user()?->name ?? '' }}</span>
                                        </div>
                                        <svg class="-me-0.5 ms-2 size-4 transition-transform duration-300 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Revolutionary Mega Menu Container -->
                            <div class="mega-menu-container bg-white/95 backdrop-blur-xl border border-gray-200/50 rounded-2xl shadow-2xl p-6 min-w-[380px] sm:min-w-[420px] lg:min-w-[500px] dark:bg-gray-900/95 dark:border-gray-700/50">
                                
                                <!-- User Header Card -->
                                <div class="user-header-card bg-gradient-to-r from-blue-500/10 to-purple-600/10 rounded-xl p-4 mb-6 border border-blue-200/30 dark:border-blue-800/30">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ Auth::user()?->name ?? '' }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()?->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Grid -->
                                <div class="menu-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    
                                    <!-- Account Management Card -->
                                    <div class="menu-card group bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-4 border border-emerald-200/30 dark:border-emerald-800/30 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.manage_account') }}</h4>
                                        </div>
                                        <div class="space-y-2">
                                            <a href="{{ route('user.domains.personal-data') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">
                                                {{ __('menu.edit_personal_data') }}
                                            </a>
                                            @can('manage_profile')
                                                <a href="{{ route('profile.show') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">
                                                    {{ __('Profile') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <!-- Privacy & GDPR Card -->
                                    <div class="menu-card group bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200/30 dark:border-blue-800/30 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                </svg>
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.gdpr_privacy') }}</h4>
                                        </div>
                                        <div class="space-y-2">
                                            @can('manage_consents')
                                                <a href="{{ route('gdpr.consent') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                                    {{ __('gdpr.menu.gdpr_center') }}
                                                </a>
                                            @endcan
                                            <a href="{{ route('gdpr.security') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                                {{ __('menu.security_password') }}
                                            </a>
                                            <a href="{{ route('gdpr.profile-images') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                                {{ __('menu.profile_images') }}
                                            </a>
                                            @can('gdpr.export_data')
                                                <a href="{{ route('gdpr.export-data') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                                    {{ __('menu.export_data') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <!-- Collections Card -->
                                    @can('create_collection')
                                        <div class="menu-card group bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200/30 dark:border-purple-800/30 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                                            <div class="flex items-center space-x-3 mb-3">
                                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                    </svg>
                                                </div>
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.collections') }}</h4>
                                            </div>
                                            <div class="space-y-2">
                                                <a href="{{ route('collections.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200">
                                                    {{ __('menu.my_collections') }}
                                                </a>
                                                <a href="{{ route('collections.create') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200">
                                                    {{ __('menu.new_collection') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endcan

                                    <!-- Activity & Notifications Card -->
                                    <div class="menu-card group bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-4 border border-orange-200/30 dark:border-orange-800/30 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"/>
                                                </svg>
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.activity') }}</h4>
                                        </div>
                                        <div class="space-y-2">
                                            @can('view_activity_log')
                                                <a href="{{ route('gdpr.activity-log') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors duration-200">
                                                    {{ __('menu.activity_log') }}
                                                </a>
                                            @endcan
                                            @can('view_notifications')
                                                <a href="{{ route('notifications.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors duration-200">
                                                    {{ __('menu.notifications') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <!-- Admin Tools Card (Only for admins) -->
                                    @can('manage_roles')
                                        <div class="menu-card group bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-xl p-4 border border-gray-200/30 dark:border-gray-800/30 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer sm:col-span-2">
                                            <div class="flex items-center space-x-3 mb-3">
                                                <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                </div>
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.admin_tools') }}</h4>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <a href="{{ route('admin.roles.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200">
                                                    {{ __('menu.permissions_roles') }}
                                                </a>
                                                <a href="{{ route('admin.users.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200">
                                                    {{ __('menu.user_management') }}
                                                </a>
                                                @can('view_statistics')
                                                    <a href="{{ route('statistics.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200">
                                                        {{ __('menu.statistics') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    @endcan

                                </div>

                                <!-- Support & Legal Section -->
                                <div class="support-section mt-6 pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex space-x-4">
                                            <a href="{{ route('gdpr.privacy-policy') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                                                {{ __('menu.privacy_policy') }}
                                            </a>
                                            <a href="{{ route('gdpr.terms') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                                                {{ __('menu.terms_of_service') }}
                                            </a>
                                            @can('contact_dpo')
                                                <a href="{{ route('gdpr.contact-dpo') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                                                    {{ __('menu.contact_dpo') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>

                                <!-- Danger Zone & Logout -->
                                <div class="action-section mt-4 pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                                    <div class="flex items-center justify-between">
                                        @can('gdpr.delete_account')
                                            <a href="{{ route('gdpr.delete-account') }}" class="text-xs text-red-500 hover:text-red-700 transition-colors duration-200">
                                                {{ __('menu.delete_account') }}
                                            </a>
                                        @endcan
                                        
                                        <form method="POST" action="{{ route('logout') }}" x-data class="ml-auto">
                                            @csrf
                                            <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                                <span>{{ __('menu.logout') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
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

    <!-- Revolutionary Mobile Navigation Menu -->
    <!-- Full Screen Mobile Experience with Card-based Layout -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <!-- Mobile Menu Overlay -->
        <div class="fixed inset-0 z-50 bg-black/20 backdrop-blur-sm mobile-menu-overlay" @click="open = false"></div>
        
        <!-- Mobile Menu Container -->
        <div class="fixed inset-0 z-50 flex">
            <!-- Menu Content -->
            <div class="relative flex flex-col w-full max-w-sm ml-auto bg-white dark:bg-gray-900 shadow-2xl mobile-menu-container">
                
                <!-- Header Section with User Info -->
                <div class="flex items-center justify-between p-6 bg-gradient-to-r from-blue-500 to-purple-600 mobile-header-gradient">
                    <div class="flex items-center space-x-3">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="object-cover rounded-full size-12 ring-2 ring-white/30"
                                src="{{ Auth::user()?->profile_photo_url ?? null }}"
                                alt="{{ Auth::user()?->name ?? '' }}" />
                        @else
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-white">{{ Auth::user()?->name ?? '' }}</h3>
                            <p class="text-sm text-white/80">{{ Auth::user()?->email ?? '' }}</p>
                        </div>
                    </div>
                    <button @click="open = false" class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Content -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 mobile-menu-content">
                    
                    <!-- Main Navigation -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-3">{{ __('menu.navigation') }}</h4>
                        <div class="space-y-1">
                            <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('Home') }}</span>
                            </a>
                            
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('dashboard') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('Dashboard') }}</span>
                            </a>
                            
                            <a href="{{ route('home.collections.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('home.collections.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
                                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('Collections') }}</span>
                            </a>
                            
                            <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('EPPS') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Account Management Card -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl p-4 border border-emerald-200/30 dark:border-emerald-800/30 mobile-card">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ __('menu.manage_account') }}</h4>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('user.domains.personal-data') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.edit_personal_data') }}
                            </a>
                            @can('manage_profile')
                                <a href="{{ route('profile.show') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('Profile') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <!-- Privacy & GDPR Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-4 border border-blue-200/30 dark:border-blue-800/30 mobile-card">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-6 h-6 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ __('menu.gdpr_privacy') }}</h4>
                        </div>
                        <div class="space-y-2">
                            @can('manage_consents')
                                <a href="{{ route('gdpr.consent') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('gdpr.menu.gdpr_center') }}
                                </a>
                            @endcan
                            <a href="{{ route('gdpr.security') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.security_password') }}
                            </a>
                            <a href="{{ route('gdpr.profile-images') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.profile_images') }}
                            </a>
                            @can('gdpr.export_data')
                                <a href="{{ route('gdpr.export-data') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.export_data') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <!-- Collections Card -->
                    @can('create_collection')
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-4 border border-purple-200/30 dark:border-purple-800/30 mobile-card">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-6 h-6 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ __('menu.collections') }}</h4>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('collections.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.my_collections') }}
                                </a>
                                <a href="{{ route('collections.create') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.new_collection') }}
                                </a>
                            </div>
                        </div>
                    @endcan

                    <!-- Activity & Notifications Card -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-2xl p-4 border border-orange-200/30 dark:border-orange-800/30 mobile-card">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-6 h-6 bg-orange-500 rounded-lg flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ __('menu.activity') }}</h4>
                        </div>
                        <div class="space-y-2">
                            @can('view_activity_log')
                                <a href="{{ route('gdpr.activity-log') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.activity_log') }}
                                </a>
                            @endcan
                            @can('view_notifications')
                                <a href="{{ route('notifications.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.notifications') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <!-- Admin Tools Card -->
                    @can('manage_roles')
                        <div class="bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-2xl p-4 border border-gray-200/30 dark:border-gray-800/30 mobile-card">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-6 h-6 bg-gray-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ __('menu.admin_tools') }}</h4>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('admin.roles.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.permissions_roles') }}
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.user_management') }}
                                </a>
                                @can('view_statistics')
                                    <a href="{{ route('statistics.index') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200 px-2 py-1 rounded-lg hover:bg-white/50 dark:hover:bg-black/20">
                                        {{ __('menu.statistics') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endcan

                </div>

                <!-- Footer Section -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <!-- Support & Legal -->
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('gdpr.privacy-policy') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            {{ __('menu.privacy_policy') }}
                        </a>
                        <a href="{{ route('gdpr.terms') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            {{ __('menu.terms_of_service') }}
                        </a>
                    </div>

                    <!-- Danger Zone & Logout -->
                    <div class="flex items-center justify-between">
                        @can('gdpr.delete_account')
                            <a href="{{ route('gdpr.delete-account') }}" class="text-xs text-red-500 hover:text-red-700 transition-colors">
                                {{ __('menu.delete_account') }}
                            </a>
                        @endcan
                        
                        <form method="POST" action="{{ route('logout') }}" x-data class="ml-auto">
                            @csrf
                            <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>{{ __('menu.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>
