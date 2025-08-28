{{-- Vanilla Desktop Mega Menu Component - No Alpine.js --}}

{{-- Component-specific styles --}}
@push('styles')
    @vite('resources/css/mega-menu.css')
@endpush

{{-- Component-specific JavaScript --}}
@push('scripts')
    @vite('resources/js/components/vanilla-desktop-menu.js')
@endpush

@php
    $user = App\Helpers\FegiAuth::user();
    $authType = App\Helpers\FegiAuth::getAuthType();
@endphp

<div class="relative ms-3" data-dropdown-container>
    <!-- Dropdown Trigger Button -->
    <div class="relative">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <button type="button" data-dropdown-trigger
                class="flex text-sm transition duration-300 transform border-2 border-transparent rounded-full focus:border-gray-300 focus:outline-none hover:scale-110">
                <img class="object-cover transition-all duration-300 rounded-full size-8 ring-2 ring-blue-500/20 hover:ring-blue-500/60"
                    src="{{ Auth::user()?->profile_photo_url ?? null }}"
                    alt="{{ Auth::user()?->name ?? '' }}" />
            </button>
        @else
            <span class="inline-flex rounded-md">
                <button type="button" data-dropdown-trigger
                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition-all duration-300 ease-in-out bg-white border border-transparent rounded-lg hover:text-gray-700 hover:bg-gray-50 hover:shadow-lg hover:scale-105 focus:bg-gray-50 focus:outline-none active:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:bg-gray-700 dark:active:bg-gray-700 group">
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-br from-blue-500 to-purple-600">
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
    </div>

    <!-- Dropdown Content -->
    <div data-dropdown-content
         class="absolute right-0 z-50 invisible mt-2 transition-all duration-200 ease-out origin-top-right transform scale-95 opacity-0">

        <!-- Revolutionary Mega Menu Container -->
        <div class="mega-menu-container bg-white/95 backdrop-blur-xl border border-gray-200/50 rounded-2xl shadow-2xl p-6 min-w-[380px] sm:min-w-[420px] lg:min-w-[500px] dark:bg-gray-900/95 dark:border-gray-700/50">

            <!-- User Header Card -->
            <div class="p-4 mb-6 border user-header-card bg-gradient-to-r from-blue-500 to-purple-600 mobile-header-gradient rounded-xl border-blue-300/40 dark:border-blue-700/40">
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
            </div>

            <!-- Main Navigation (aligned with mobile) -->
            <div class="mb-6">
                <h4 class="px-1 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">{{ __('menu.navigation') }}</h4>
                <div class="space-y-1">

                    {{-- Guest layout (Home) navigation --}}
                    @if(View::getSection('title') === __('guest_home.page_title') || request()->routeIs('home') || request()->is('/'))
                        <a href="{{ url('/') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors {{ (request()->routeIs('home') || request()->is('/')) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <span class="font-medium">{{ __('guest_layout.home') }}</span>
                        </a>

                        <a href="{{ url('/creator') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors {{ request()->routeIs('creator.index') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span class="font-medium">{{ __('guest_layout.creators') }}</span>
                        </a>

                        <a href="{{ route('collections.carousel') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors {{ request()->routeIs('home.collections.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-emerald-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <span class="font-medium">{{ __('guest_layout.collections') }}</span>
                        </a>

                        <a href="{{ route('collector.index') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors {{ request()->routeIs('collector.*') ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-cyan-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <span class="font-medium">{{ __('guest_layout.collectors') }}</span>
                        </a>

                        <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-colors {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-orange-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="font-medium">{{ __('guest_layout.epps') }}</span>
                        </a>

                        {{-- Le mie Collezioni (guest layout) --}}
                        @can('create_EGI')
                            @auth
                                <button type="button" id="desktop-collection-list-dropdown-button" class="flex items-center justify-between w-full px-3 py-2 text-gray-700 transition-colors dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg" aria-expanded="false" aria-haspopup="true">
                                    <span class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <span class="font-medium">{{ __('collection.my_galleries') }}</span>
                                    </span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                                </button>
                                <div id="desktop-collection-list-dropdown-menu" class="mx-2 mb-2 mt-1 hidden max-h-[40vh] overflow-y-auto rounded-xl border border-gray-200 bg-white py-2 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    <div id="desktop-collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">{{ __('collection.loading_galleries') }}</div>
                                    <div id="desktop-collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">{{ __('collection.no_galleries_found') }} <a href="{{ route('collections.create') }}" class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</a></div>
                                    <div id="desktop-collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-500">{{ __('collection.error_loading_galleries') }}</div>
                                </div>
                            @endauth
                        @endcan

                        @can('create_EGI')
                            <button type="button" class="flex items-center w-full px-3 py-2 space-x-3 text-gray-700 transition-colors js-create-egi-contextual-button dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg" data-action="open-create-egi-contextual" data-auth-type="{{ $authType }}" aria-label="{{ __('guest_layout.create_egi') }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-lg">
                                    <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
                                </div>
                                <span class="font-medium js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
                            </button>
                        @endcan

                        @can('create_collection')
                            <button type="button" data-action="open-create-collection-modal" class="flex items-center w-full px-3 py-2 space-x-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg" aria-label="{{ __('collection.create_collection') }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-indigo-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
                                </div>
                                <span class="font-medium">{{ __('collection.create_collection') }}</span>
                            </button>
                        @endcan

                    @else
                        {{-- App layout (Dashboard) navigation --}}
                        <a href="{{ route('home') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <span class="font-medium">{{ __('Home') }}</span>
                        </a>

                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <span class="font-medium">{{ __('Dashboard') }}</span>
                        </a>

                        <a href="{{ route('collections.carousel') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors {{ request()->routeIs('collections.carousel.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-emerald-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <span class="font-medium">{{ __('Collections') }}</span>
                        </a>

                        <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-colors {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 text-white bg-orange-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="font-medium">{{ __('EPPS') }}</span>
                        </a>

                        {{-- Le mie Collezioni (app layout) --}}
                        @can('create_EGI')
                            @auth
                                <button type="button" id="desktop-collection-list-dropdown-button-app" class="flex items-center justify-between w-full px-3 py-2 text-gray-700 transition-colors dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg" aria-expanded="false" aria-haspopup="true">
                                    <span class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                            <span class="text-sm material-symbols-outlined" aria-hidden="true">view_carousel</span>
                                        </div>
                                        <span class="font-medium">{{ __('collection.my_galleries') }}</span>
                                    </span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                                </button>
                                <div id="desktop-collection-list-dropdown-menu-app" class="mx-2 mb-2 mt-1 hidden max-h-[40vh] overflow-y-auto rounded-md border border-gray-700 bg-gray-800 py-1 shadow-lg">
                                    <div id="desktop-collection-list-loading-app" class="px-4 py-3 text-sm text-center text-gray-400">{{ __('collection.loading_galleries') }}</div>
                                    <div id="desktop-collection-list-empty-app" class="hidden px-4 py-3 text-sm text-center text-gray-400">{{ __('collection.no_galleries_found') }} <a href="{{ route('collections.create') }}" class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</a></div>
                                    <div id="desktop-collection-list-error-app" class="hidden px-4 py-3 text-sm text-center text-red-400">{{ __('collection.error_loading_galleries') }}</div>
                                </div>
                            @endauth
                        @endcan

                        @can('create_EGI')
                            <button type="button" class="flex items-center w-full px-3 py-2 space-x-3 text-gray-700 transition-colors js-create-egi-contextual-button dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg" data-action="open-create-egi-contextual" data-auth-type="{{ Auth::check() ? 'authenticated' : 'guest' }}" aria-label="{{ __('guest_layout.create_egi') }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-lg">
                                    <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
                                </div>
                                <span class="font-medium js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
                            </button>
                        @endcan

                        @can('create_collection')
                            <button type="button" data-action="open-create-collection-modal" class="flex items-center w-full px-3 py-2 space-x-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg" aria-label="{{ __('collection.create_collection') }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-indigo-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
                                </div>
                                <span class="font-medium">{{ __('collection.create_collection') }}</span>
                            </button>
                        @endcan

                    @endif

                </div>
            </div>

            <!-- Menu Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <!-- Account Management Card -->
                <div class="p-4 border bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl border-emerald-200/30 dark:border-emerald-800/30 mega-card">
                    <div class="flex items-center mb-3 space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.manage_account') }}</h4>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('user.domains.personal-data') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-white/50 dark:hover:bg-black/20">
                            {{ __('menu.edit_personal_data') }}
                        </a>
                        @can('manage_profile')
                            <a href="{{ route('profile.show') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('Profile') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Privacy & GDPR Card -->
                <div class="p-4 border bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border-blue-200/30 dark:border-blue-800/30 mega-card">
                    <div class="flex items-center mb-3 space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.gdpr_privacy') }}</h4>
                    </div>
                    <div class="space-y-2">
                        @can('manage_consents')
                            <a href="{{ route('gdpr.consent') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('gdpr.menu.gdpr_center') }}
                            </a>
                        @endcan
                        <a href="{{ route('gdpr.security') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                            {{ __('menu.security_password') }}
                        </a>
                        <a href="{{ route('gdpr.profile-images') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                            {{ __('menu.profile_images') }}
                        </a>
                        @can('gdpr.export_data')
                            <a href="{{ route('gdpr.export-data') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.export_data') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Collections Card -->
                @can('create_collection')
                    <div class="p-4 border bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl border-purple-200/30 dark:border-purple-800/30 mega-card">
                        <div class="flex items-center mb-3 space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.collections') }}</h4>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('collections.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.my_collections') }}
                            </a>
                            <a href="{{ route('collections.create') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.new_collection') }}
                            </a>
                        </div>
                    </div>
                @endcan

                <!-- Activity & Notifications Card -->
                <div class="p-4 border bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-2xl border-orange-200/30 dark:border-orange-800/30 mega-card">
                    <div class="flex items-center mb-3 space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-orange-500 to-red-500">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.activity') }}</h4>
                    </div>
                    <div class="space-y-2">
                        @can('view_activity_log')
                            <a href="{{ route('gdpr.activity-log') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.activity_log') }}
                            </a>
                        @endcan
                        @can('view_notifications')
                            <a href="{{ route('notifications.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.notifications') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Admin Tools Card -->
                @can('manage_roles')
                    <div class="col-span-1 p-4 border sm:col-span-2 bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-2xl border-gray-200/30 dark:border-gray-800/30 mega-card">
                        <div class="flex items-center mb-3 space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-gray-600 to-slate-600">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.admin_tools') }}</h4>
                        </div>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                            <a href="{{ route('admin.roles.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.permissions_roles') }}
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.user_management') }}
                            </a>
                            @can('view_statistics')
                                <a href="{{ route('statistics.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.statistics') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcan

            </div>

            <!-- Support Section -->
            <div class="pt-4 mt-6 border-t border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-4">
                        <a href="{{ route('gdpr.privacy-policy') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('menu.privacy_policy') }}
                        </a>
                        <a href="{{ route('gdpr.terms') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('menu.terms_of_service') }}
                        </a>
                    </div>

                    <!-- Danger Zone & Logout -->
                    <div class="flex items-center space-x-4">
                        @can('gdpr.delete_account')
                            <a href="{{ route('gdpr.delete-account') }}" class="text-xs text-red-500 transition-colors hover:text-red-700">
                                {{ __('menu.delete_account') }}
                            </a>
                        @endcan

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center px-4 py-2 space-x-2 text-sm text-white transition-all duration-300 bg-red-500 rounded-lg hover:bg-red-600 hover:scale-105 hover:shadow-lg">
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
</div>
