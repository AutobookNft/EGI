{{-- Mobile Navigation Menu Component --}}
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
                        <div class="flex items-center justify-center w-12 h-12 text-lg font-bold text-white rounded-full bg-white/20">
                            {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-white">{{ Auth::user()?->name ?? '' }}</h3>
                        <p class="text-sm text-white/80">{{ Auth::user()?->email ?? '' }}</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 transition-colors rounded-lg text-white/80 hover:text-white hover:bg-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Content -->
            <div class="flex-1 p-4 space-y-4 overflow-y-auto mobile-menu-content">

                <!-- Main Navigation -->
                <div class="space-y-2">
                    <h4 class="px-3 text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">{{ __('menu.navigation') }}</h4>
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <span class="font-medium">{{ __('Home') }}</span>
                        </a>

                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('dashboard') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <span class="font-medium">{{ __('Dashboard') }}</span>
                        </a>

                        <a href="{{ route('home.collections.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('home.collections.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-emerald-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <span class="font-medium">{{ __('Collections') }}</span>
                        </a>

                        <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-orange-500 rounded-lg">
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
                    <div class="flex items-center mb-3 space-x-2">
                        <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-500">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.manage_account') }}</h4>
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
                    <div class="flex items-center mb-3 space-x-2">
                        <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-blue-500">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.gdpr_privacy') }}</h4>
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
                        <div class="flex items-center mb-3 space-x-2">
                            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-purple-500">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.collections') }}</h4>
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
                    <div class="flex items-center mb-3 space-x-2">
                        <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-orange-500">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.activity') }}</h4>
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
                        <div class="flex items-center mb-3 space-x-2">
                            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-600">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.admin_tools') }}</h4>
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
            <div class="p-4 space-y-3 border-t border-gray-200 dark:border-gray-700">
                <!-- Support & Legal -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('gdpr.privacy-policy') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ __('menu.privacy_policy') }}
                    </a>
                    <a href="{{ route('gdpr.terms') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ __('menu.terms_of_service') }}
                    </a>
                </div>

                <!-- Danger Zone & Logout -->
                <div class="flex items-center justify-between">
                    @can('gdpr.delete_account')
                        <a href="{{ route('gdpr.delete-account') }}" class="text-xs text-red-500 transition-colors hover:text-red-700">
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
