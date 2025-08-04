<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth bg-gray-900">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#121212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- SEO & Semantica --}}
    <title>{{ $title ?? __('collection.default_page_title') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('collection.default_meta_description') }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ asset('images/logo/logo_1.webp') }}" as="image">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="preload"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    </noscript>


    {{-- Asset CSS (Vite) --}}
    @vite(['resources/css/app.css', 'resources/css/guest.css', 'resources/css/modal-fix.css'])

    {{-- Font Awesome --}}
    {{-- Schema.org Markup per il WebSite (Generale) --}}
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "https://florenceegi.com/",
            "name": "{{ __('site.schema.website.name') }}",
            "description": "{{ __('site.schema.website.description') }}",
            "publisher": {
                "@type": "Organization",
                "name": "{{ __('site.schema.publisher.name') }}",
                "url": "https://frangette.com/",
                "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/frangette-logo.png') }}"
                }
            }
        }
    </script>


    {{-- Slot per Schema.org specifico della pagina --}}
    {{ $schemaMarkup ?? '' }}

    {{-- Slot per meta tag aggiuntivi specifici della pagina --}}
    {{ $headExtra ?? '' }}

    {{-- Stack per stili specifici della pagina --}}
    @stack('styles')

</head>

<body class="flex min-h-screen flex-col bg-gray-900 font-body text-gray-300 antialiased">

    <header class="sticky top-0 z-50 w-full border-b border-gray-800 bg-gray-900/90 shadow-lg backdrop-blur-md"
        role="banner" aria-label="{{ __('guest_layout.header_aria_label') }}">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between md:h-20">

                {{-- Logo --}}
                <div class="flex flex-shrink-0 items-center">
                    <a href="{{ url('/home') }}" class="group flex items-center gap-2"
                        aria-label="{{ __('collection.logo_home_link_aria_label') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="Frangette Logo"
                            class="h-7 w-auto sm:h-8 md:h-9" loading="lazy" decoding="async">
                        <span
                            class="hidden text-base font-semibold text-gray-400 transition group-hover:text-emerald-400 sm:inline md:text-lg">{{ __('Frangette') }}</span>
                    </a>

                    {{-- Natan Assistant - Posizionato vicino al logo --}}
                    <div class="ml-6 hidden md:block">
                        <x-natan-assistant :suffix="'-desktop'" />
                    </div>
                </div>
                @php
                    $navLinkClasses =
                        'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40';
                @endphp

                {{-- Nav Desktop --}}
                <nav class="hidden items-center space-x-1 md:flex" role="navigation"
                    aria-label="{{ __('collection.main_navigation_aria_label') }}">
                    @include('partials.nav-links', ['isMobile' => false])

                    {{-- Dropdown My Galleries --}}
                    <div id="collection-list-dropdown-container" class="relative hidden">
                        <button id="collection-list-dropdown-button" type="button"
                            class="{{ $navLinkClasses }} inline-flex items-center" aria-expanded="false"
                            aria-haspopup="true">
                            <span class="material-symbols-outlined mr-1 text-base"
                                aria-hidden="true">view_carousel</span>
                            <span id="collection-list-button-text">{{ __('collection.my_galleries') }}</span>
                            <svg class="-mr-1 ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="collection-list-dropdown-menu"
                            class="absolute right-0 z-20 mt-2 hidden max-h-[60vh] w-72 origin-top-right overflow-y-auto rounded-md border border-gray-800 bg-gray-900 py-1 shadow-xl ring-1 ring-gray-700 backdrop-blur-sm focus:outline-none">
                            <div id="collection-list-loading" class="px-4 py-3 text-center text-sm text-gray-400">
                                {{ __('collection.loading_galleries') }}</div>
                            <div id="collection-list-empty" class="hidden px-4 py-3 text-center text-sm text-gray-400">
                                {{ __('collection.no_galleries_found') }} <a href="{{ route('collections.create') }}"
                                    class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</a>
                            </div>
                            <div id="collection-list-error" class="hidden px-4 py-3 text-center text-sm text-red-400">
                                {{ __('collection.error_loading_galleries') }}</div>
                        </div>
                    </div>

                    {{-- Wallet e Auth --}}
                    <span class="mx-2 h-6 border-l border-gray-700" aria-hidden="true"></span>

                    <div id="current-collection-badge-container" class="ml-2 hidden items-center">
                        <a href="#" id="current-collection-badge-link"
                            class="flex items-center rounded-full border border-sky-700 bg-sky-900/60 px-3 py-1.5 text-xs font-semibold text-sky-300 transition hover:border-sky-600 hover:bg-sky-800">
                            <span class="material-symbols-outlined mr-1.5 text-sm leading-none"
                                aria-hidden="true">folder_managed</span>
                            <span id="current-collection-badge-name"></span>
                        </a>
                    </div>

                    <div id="wallet-cta-container" class="ml-2">
                        <button id="connect-wallet-button" type="button"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" />
                            </svg>
                            {{ __('collection.wallet.button_wallet_connect') }}
                        </button>
                        <div id="wallet-dropdown-container" class="relative hidden">
                            <button id="wallet-dropdown-button" type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                aria-expanded="false" aria-haspopup="true">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" />
                                </svg>
                                <span id="wallet-display-text"
                                    class="hidden sm:inline">{{ __('collection.wallet.wallet') }}</span>
                                <svg class="-mr-1 ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="wallet-dropdown-menu"
                                class="absolute right-0 z-20 mt-2 hidden w-56 origin-top-right rounded-md border border-gray-800 bg-gray-900 py-1 shadow-lg ring-1 ring-black ring-opacity-5 backdrop-blur-sm focus:outline-none">
                                <a href="{{ route('dashboard') }}" id="wallet-dashboard-link"
                                    class="flex w-full items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <span class="material-symbols-outlined mr-2 h-5 w-5 text-gray-400"
                                        aria-hidden="true">dashboard</span>
                                    {{ __('collection.dashboard') }}
                                </a>
                                <button id="wallet-copy-address"
                                    class="flex w-full items-center px-4 py-2 text-left text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <span class="material-symbols-outlined mr-2 h-5 w-5 text-gray-400"
                                        aria-hidden="true">content_copy</span>
                                    {{ __('collection.wallet.copy_address') }}
                                </button>
                                <button id="wallet-disconnect"
                                    class="flex w-full items-center px-4 py-2 text-left text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <span class="material-symbols-outlined mr-2 h-5 w-5 text-gray-400"
                                        aria-hidden="true">logout</span>
                                    {{ __('collection.wallet.button_wallet_disconnect') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" id="login-link-desktop"
                        class="{{ $navLinkClasses }}">{{ __('collection.login') }}</a>
                    <a href="{{ route('register') }}" id="register-link-desktop"
                        class="ml-2 inline-flex items-center rounded-md border border-gray-700 bg-gray-800 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ __('collection.register') }}</a>
                </nav>

                {{-- Menu Mobile Button --}}
                <div class="-mr-2 flex items-center gap-2 md:hidden">
                    {{-- Natan Assistant Mobile --}}
                    <div class="block md:hidden">
                        <x-natan-assistant :suffix="'-mobile'" />
                    </div>

                    <button type="button"
                        class="inline-flex items-center justify-center rounded-md bg-gray-900 p-2 text-gray-400 hover:bg-gray-800 hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">{{ __('collection.open_main_menu') }}</span>
                        <svg class="block h-6 w-6" id="hamburger-icon" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg class="hidden h-6 w-6" id="close-icon" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile Content --}}
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="space-y-1 border-b border-gray-800 px-2 pb-3 pt-2 sm:px-3">
                @include('partials.nav-links', ['isMobile' => true])
            </div>
            <div class="space-y-3 border-b border-gray-800 px-4 pb-3 pt-4" id="mobile-auth-section">
                <div id="wallet-cta-container-mobile">
                    <button id="connect-wallet-button-mobile" type="button"
                        class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" />
                        </svg>
                        {{ __('collection.wallet.button_wallet_connect') }}
                    </button>
                    <div id="mobile-wallet-info-container" class="hidden space-y-2 pb-1 pt-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-emerald-400">
                                <span
                                    class="material-symbols-outlined mr-2 h-5 w-5 text-emerald-500">account_balance_wallet</span>
                                <span id="mobile-wallet-address"
                                    class="font-mono text-sm text-emerald-300">0x1234...5678</span>
                            </div>
                            <button id="mobile-copy-address" class="rounded-md p-1 text-gray-400 hover:bg-gray-800">
                                <span class="material-symbols-outlined">content_copy</span>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('dashboard') }}" id="mobile-dashboard-link"
                                class="flex flex-1 items-center justify-center rounded-md bg-gray-800 px-3 py-1.5 text-sm text-gray-300 hover:bg-gray-700">
                                <span class="material-symbols-outlined mr-1.5 text-sm">dashboard</span>
                                {{ __('collection.dashboard') }}
                            </a>
                            <button id="mobile-disconnect"
                                class="flex flex-1 items-center justify-center rounded-md bg-gray-800 px-3 py-1.5 text-sm text-gray-300 hover:bg-gray-700">
                                <span class="material-symbols-outlined mr-1.5 text-sm">logout</span>
                                {{ __('collection.wallet.button_wallet_disconnect') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-3" id="mobile-login-register-buttons">
                    <a href="{{ route('login') }}"
                        class="flex-1 rounded-md border border-gray-700 bg-gray-800 px-4 py-2.5 text-center text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">{{ __('collection.login') }}</a>
                    <a href="{{ route('register') }}"
                        class="flex-1 rounded-md border border-green-600 bg-green-800 px-4 py-2.5 text-center text-sm font-medium text-white hover:bg-green-700">{{ __('collection.register') }}</a>
                </div>
            </div>
            <div id="current-collection-badge-container-mobile"
                class="hidden border-t border-gray-800 px-4 py-3 text-center">
                <a href="#" id="current-collection-badge-link-mobile"
                    class="inline-flex items-center rounded-full border border-sky-700 bg-sky-900/60 px-3 py-2 text-center text-xs font-medium text-sky-300 transition hover:border-sky-600 hover:bg-sky-800">
                    <span class="material-symbols-outlined mr-1.5 text-sm" aria-hidden="true">folder_managed</span>
                    <span id="current-collection-badge-name-mobile"></span>
                </a>
            </div>
        </div>
    </header>
