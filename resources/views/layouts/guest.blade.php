<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900 scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#121212"> {{-- O il Blu Algoritmo se preferisci: #1B365D --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $title ?? __('guest_layout.default_title') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('guest_layout.default_description') }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    <!-- === INIZIO SEZIONE FONT E PRECONNECT === -->

    <!-- Preconnect per Google Fonts e altri CDN se usati (es. per flag-icons) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @if(config('app.env') === 'production') {{-- Esempio: solo se usi cdn per flag-icons in prod --}}
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    @endif

    <!-- Preload Logo (buona pratica se è above-the-fold) -->
    <link rel="preload" href="{{ asset('images/logo/logo_1.webp') }}" as="image">

    <!-- Caricamento Font Principali (FlorenceEGI Brand Guidelines) -->
    {{-- Playfair Display (Titoli), Source Sans Pro (Corpo), JetBrains Mono (Codice) --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Caricamento Icon Font: Material Symbols Outlined (Primario) -->
    {{-- Questo pattern carica in modo asincrono e non bloccante --}}
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"></noscript>

    <!-- Caricamento Icon Font: Material Icons (Legacy/Fallback, se ancora necessario) -->
    {{-- Valuta se puoi migrare tutte le icone a Material Symbols Outlined per semplificare --}}
    @if(false) {{-- Disabilitalo o rimuovilo se Material Symbols Outlined è sufficiente --}}
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" media="print" onload="this.media='all'">
    @endif

    <!-- Flag Icons (se usate attivamente) -->
    {{-- Se non le usi, puoi rimuovere questo e il preconnect a cdn.jsdelivr.net --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" media="print" onload="this.media='all'"/> --}}

    <!-- === FINE SEZIONE FONT E PRECONNECT === -->

    <!-- Stili Principali dell'Applicazione (Vite) -->
    @vite(['resources/css/app.css', 'resources/css/guest.css', 'vendor/ultra/ultra-upload-manager/resources/css/app.css'])

    <style>
/* DEBUG: Previeni QUALSIASI comportamento di scroll */
* {
    scroll-behavior: auto !important;
}

html, body {
    scroll-behavior: auto !important;
    overflow-anchor: none !important;
}

/* DEBUG: Forza positioning delle modali */
#connect-wallet-modal,
#upload-modal,
#secret-display-modal {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    width: auto !important;
    height: auto !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
    z-index: 999999 !important;
}
    </style>

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ __('guest_layout.schema_website_name') }}",
        "url": "{{ url('/') }}",
        "description": "{{ $metaDescription ?? __('guest_layout.schema_website_description') }}",
        "publisher": {
            "@type": "Organization",
            "name": "{{ __('guest_layout.schema_organization_name') }}",
            "url": "https://frangette.com/",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo/Frangette_Logo_1000x1000_transparent.png') }}",
                "width": 1000,
                "height": 1000
            }
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                 "@type": "EntryPoint",
                 "urlTemplate": "{{ route('home.collections.index') }}?search={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    {{ $schemaMarkup ?? '' }}
    {{ $headExtra ?? '' }}
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen antialiased text-gray-300 bg-gray-900 font-body">
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full border-b border-gray-800 shadow-lg bg-gray-900/90 backdrop-blur-md" role="banner" aria-label="{{ __('guest_layout.header_aria_label') }}">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group" aria-label="{{ __('guest_layout.logo_aria_label') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="{{ __('guest_layout.logo_alt_text') }}" class="w-auto h-7 sm:h-8 md:h-9" loading="lazy" decoding="async">
                        <span class="hidden text-base font-semibold text-gray-400 transition group-hover:text-emerald-400 md:text-lg sm:inline">{{ __('guest_layout.navbar_brand_name') }}</span>
                    </a>
                </div>
                @php
                    $navLinkClasses = 'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40';
                @endphp
                <!-- Nav Desktop -->
                <nav class="items-center hidden space-x-1 md:flex" role="navigation" aria-label="{{ __('guest_layout.desktop_nav_aria_label') }}">
                    @include('partials.nav-links', ['isMobile' => false])
                    <!-- Dropdown My Galleries -->
                    <div id="collection-list-dropdown-container" class="relative hidden">
                        <button id="collection-list-dropdown-button" type="button" class="{{ $navLinkClasses }} inline-flex items-center" aria-expanded="false" aria-haspopup="true" aria-controls="collection-list-dropdown-menu" aria-label="{{ __('guest_layout.my_galleries_dropdown_aria_label') }}">
                            <span class="mr-1 text-base material-symbols-outlined" aria-hidden="true">view_carousel</span>
                            <span id="collection-list-button-text">{{ __('guest_layout.my_galleries') }}</span>
                            <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                        <div id="collection-list-dropdown-menu" class="absolute right-0 z-20 hidden py-1 mt-2 origin-top-right bg-gray-900 rounded-md shadow-xl max-h-[60vh] overflow-y-auto w-72 ring-1 ring-gray-700 backdrop-blur-sm border border-gray-800 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="collection-list-dropdown-button" tabindex="-1">
                            <div id="collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-400" role="status">{{ __('guest_layout.loading_galleries') }}</div>
                            <div id="collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-400">{{ __('guest_layout.no_galleries_found') }} <a href="{{ route('collections.create') }}" class="underline hover:text-emerald-400">{{ __('guest_layout.create_one_question') }}</a></div>
                            <div id="collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-400" role="alert">{{ __('guest_layout.error_loading_galleries') }}</div>
                        </div>
                    </div>
                    <!-- Wallet e Auth -->
                    <span class="h-6 mx-2 border-l border-gray-700" aria-hidden="true"></span>
                    <div id="current-collection-badge-container" class="items-center hidden ml-2">
                        <a href="#" id="current-collection-badge-link" class="flex items-center px-3 py-1.5 border border-sky-700 bg-sky-900/60 text-sky-300 text-xs font-semibold rounded-full hover:bg-sky-800 hover:border-sky-600 transition" aria-label="{{ __('guest_layout.current_collection_badge_aria_label') }}">
                            <span class="material-symbols-outlined mr-1.5 text-sm leading-none" aria-hidden="true">folder_managed</span>
                            <span id="current-collection-badge-name"></span>
                        </a>
                    </div>
                    <div id="wallet-cta-container" class="ml-2">
                        <button id="connect-wallet-button" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.connect_wallet_aria_label') }}">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                            {{ __('guest_layout.connect_wallet') }}
                        </button>
                        <div id="wallet-dropdown-container" class="relative hidden">
                            <button id="wallet-dropdown-button" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" aria-expanded="false" aria-haspopup="true" aria-controls="wallet-dropdown-menu">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                                <span id="wallet-display-text" class="hidden sm:inline">{{ __('guest_layout.wallet') }}</span>
                                <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                            </button>
                            <div id="wallet-dropdown-menu" class="absolute right-0 z-20 hidden w-56 py-1 mt-2 origin-top-right bg-gray-900 border border-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none backdrop-blur-sm" role="menu" aria-orientation="vertical" aria-labelledby="wallet-dropdown-button" tabindex="-1">
                                <a href="{{ route('dashboard') }}" id="wallet-dashboard-link" class="flex items-center w-full px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white" role="menuitem" tabindex="-1" aria-label="{{ __('guest_layout.dashboard_link_aria_label') }}">
                                    <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined" aria-hidden="true">dashboard</span>
                                    {{ __('guest_layout.dashboard') }}
                                </a>
                                <button id="wallet-copy-address" class="flex items-center w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-gray-800 hover:text-white" role="menuitem" tabindex="-1" aria-label="{{ __('guest_layout.copy_wallet_address_aria_label') }}">
                                    <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined" aria-hidden="true">content_copy</span>
                                    {{ __('guest_layout.copy_address') }}
                                </button>
                                <button id="wallet-disconnect" class="flex items-center w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-gray-800 hover:text-white" role="menuitem" tabindex="-1" aria-label="{{ __('guest_layout.disconnect_wallet_aria_label') }}">
                                    <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined" aria-hidden="true">logout</span>
                                    {{ __('guest_layout.disconnect') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.login_link_aria_label') }}">{{ __('guest_layout.login') }}</a>
                    <a href="{{ route('register') }}" id="register-link-desktop" class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.register_link_aria_label') }}">{{ __('guest_layout.register') }}</a>
                </nav>

                <!-- Menu Mobile Button -->
                <div class="flex -mr-2 md:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-2 text-gray-400 bg-gray-900 rounded-md hover:text-gray-300 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button" aria-label="{{ __('guest_layout.toggle_mobile_menu_aria_label') }}">
                        <span class="sr-only">{{ __('guest_layout.open_mobile_menu_sr') }}</span>
                        <svg class="block w-6 h-6" id="hamburger-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg class="hidden w-6 h-6" id="close-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Mobile Content -->
        <div class="hidden md:hidden" id="mobile-menu" role="navigation" aria-label="{{ __('guest_layout.mobile_nav_aria_label') }}">
            <div class="px-2 pt-2 pb-3 space-y-1 border-b border-gray-800 sm:px-3">
                @include('partials.nav-links', ['isMobile' => true])
            </div>
            <div class="px-4 pt-4 pb-3 space-y-3 border-b border-gray-800" id="mobile-auth-section">
                <div id="wallet-cta-container-mobile">
                    <button id="connect-wallet-button-mobile" type="button" class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.mobile_connect_wallet_aria_label') }}">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                        {{ __('guest_layout.connect_wallet') }}
                    </button>
                    <div id="mobile-wallet-info-container" class="hidden pt-2 pb-1 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-emerald-400">
                                <span class="w-5 h-5 mr-2 text-emerald-500 material-symbols-outlined">account_balance_wallet</span>
                                <span id="mobile-wallet-address" class="font-mono text-sm text-emerald-300">0x1234...5678</span>
                            </div>
                            <button id="mobile-copy-address" class="p-1 text-gray-400 rounded-md hover:bg-gray-800" aria-label="{{ __('guest_layout.copy_address') }}">
                                <span class="material-symbols-outlined">content_copy</span>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('dashboard') }}" id="mobile-dashboard-link" class="flex items-center justify-center flex-1 px-3 py-1.5 text-sm text-gray-300 bg-gray-800 rounded-md hover:bg-gray-700">
                                <span class="material-symbols-outlined mr-1.5 text-sm">dashboard</span>
                                {{ __('guest_layout.dashboard') }}
                            </a>
                            <button id="mobile-disconnect" class="flex items-center justify-center flex-1 px-3 py-1.5 text-sm text-gray-300 bg-gray-800 rounded-md hover:bg-gray-700">
                                <span class="material-symbols-outlined mr-1.5 text-sm">logout</span>
                                {{ __('guest_layout.disconnect') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-3" id="mobile-login-register-buttons">
                    <a href="{{ route('login') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-center text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white" aria-label="{{ __('guest_layout.mobile_login_link_aria_label') }}">{{ __('guest_layout.login') }}</a>
                    <a href="{{ route('register') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-center text-white bg-green-800 border border-green-600 rounded-md hover:bg-green-700" aria-label="{{ __('guest_layout.mobile_register_link_aria_label') }}">{{ __('guest_layout.register') }}</a>
                </div>
            </div>
            <div id="current-collection-badge-container-mobile" class="hidden px-4 py-3 text-center border-t border-gray-800">
                <div class="inline-flex items-center px-3 py-2 text-xs font-medium text-center border rounded-full text-sky-300 border-sky-700 bg-sky-900/60">
                    <span class="material-symbols-outlined mr-1.5 text-sm" aria-hidden="true">folder_managed</span>
                    <span id="current-collection-badge-name-mobile"></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    @unless(isset($noHero) && $noHero)
        <section id="hero-section" class="relative flex flex-col items-center overflow-hidden min-h-[100vh]" aria-labelledby="hero-main-title">
            {{--
                RIMOZIONE DEGLI ELEMENTI DI SFONDO PRECEDENTI:
                <div id="background-gradient" class="background-gradient" aria-hidden="true"></div>
                <div id="background-image-layer" class="background-image-layer" style="background-image: url('{{ asset('images/default/random_background/15.jpg') }}');" aria-hidden="true"></div>
                <canvas id="backgroundCanvas" aria-hidden="true"></canvas>

                Lo sfondo verrà gestito principalmente via CSS su #hero-section.
                Potrebbe essere un bg-gray-900 o un colore simile dalla palette.
            --}}
            <h1 id="hero-main-title" class="sr-only">{{ $title ?? __('guest_layout.default_title') }}</h1>

            @isset($heroFullWidth)
                {{-- Layout a colonna intera --}}
                <div class="relative z-10 w-full px-4 mx-auto mt-auto mb-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $heroFullWidth }}
                </div>

                <div class="natan-assistant fixed bottom-6 right-6 z-[100000] flex flex-col items-end" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                    {{ $heroNatanAssistant ?? '' }}
                </div>

            @else
                {{-- Layout a colonne esistente (NON USATO DALLA HOME ATTUALE) --}}
                <div class="container relative z-10 flex flex-col items-center w-full px-4 mx-auto hero-content-overlay sm:px-6 lg:px-8">
                    <div class="grid w-full grid-cols-1 gap-8 mb-10 md:grid-cols-12 md:mb-12 lg:gap-6">
                    {{-- Colonna mobile: sinistra+destra in un contenitore--}}
                    <div class="flex flex-col items-center gap-8 md:hidden">
                        {{-- Colonna sinistra (Natan) --}}
                        <div class="w-full" role="region" aria-label="{{ __('guest_layout.hero_left_content_aria_label') }}">
                            {{ $heroContentLeft ?? '' }}
                        </div>

                        {{-- Colonna destra (Badge impatto) --}}
                        <div class="relative z-10 w-full" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                            {{ $heroContentRight ?? '' }}
                        </div>
                    </div>

                    {{-- Colonna sinistra (25%) - Visibile solo su tablet+ --}}
                    <div class="hidden md:block md:col-span-3" role="region" aria-label="{{ __('guest_layout.hero_left_content_aria_label') }}">
                        {{ $heroContentLeft ?? '' }}
                    </div>

                    {{-- Colonna centrale: Carousel (50% su desktop, 100% su tablet) --}}
                    <div class="flex flex-col items-center justify-center w-full md:col-span-12 lg:col-span-6" role="region" aria-label="{{ __('guest_layout.hero_carousel_aria_label') }}">
                        {{ $heroCarousel ?? '' }}
                    </div>

                    {{-- Colonna destra (25%) - Visibile solo su tablet+ --}}
                    <div class="relative z-10 w-full" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                        {{ $heroContentRight ?? '' }}
                    </div>

                    {{-- Layout alternativo tablet: due colonne laterali affiancate sotto il carousel --}}
                    <div class="hidden md:grid md:grid-cols-2 md:gap-6 md:col-span-12 lg:hidden">
                        {{-- Colonna sinistra (Natan) - Ripetuta per layout tablet --}}
                        <div class="flex items-center" role="region" aria-label="{{ __('guest_layout.hero_left_content_tablet_aria_label') }}">
                            {{ $heroContentLeft ?? '' }}
                        </div>

                        {{-- Colonna destra (Badge impatto) - Ripetuta per layout tablet --}}
                        <div class="flex items-center" role="region" aria-label="{{ __('guest_layout.hero_right_content_tablet_aria_label') }}">
                            {{ $heroContentRight ?? '' }}
                        </div>
                    </div>
                </div>
            @endisset

            {{-- Contenuto sotto l'hero --}}
            <div class="relative z-10 w-full mt-auto ml-10 mr-10 below-hero-content" role="region" aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10 e w-full --}}
                {{ $belowHeroContent ?? '' }}
            </div>

             <div class="relative z-10 w-full mt-12 mb-12 ml-10 mr-10 below-hero-content" role="region" aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10, w-full e mt-12 --}}
                {{ $belowHeroContent_2 ?? '' }}
            </div>

            <div class="absolute z-20 transform -translate-x-1/2 bottom-6 left-1/2 md:hidden animate-bounce-slow">
                <button type="button" aria-label="{{ __('guest_layout.scroll_down_aria_label') }}" class="flex items-center justify-center w-10 h-10 text-white bg-black rounded-full bg-opacity-30 hover:bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="document.getElementById('main-content').scrollIntoView({behavior: 'smooth'});">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </button>
            </div>
        </section>
    @endunless

    {{-- NUOVO SLOT PER IL CONTENUTO DEGLI ATTORI --}}
    @isset($actorContent)
        <div id="guest-layout-actors-section-wrapper" class="relative z-10 w-full"> {{-- Wrapper opzionale per stili globali se necessario --}}
            {{ $actorContent }}
        </div>
    @endisset

    <!-- Main Content -->
    <main id="main-content" role="main" class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="py-6 mt-auto bg-gray-900 border-t border-gray-800 md:py-8" role="contentinfo" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">{{ __('guest_layout.footer_sr_heading') }}</h2>
        <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8 md:flex md:justify-between md:items-center">
            <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('guest_layout.copyright_holder') }}. {{ __('guest_layout.all_rights_reserved') }}</p>
            <div class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-y-0 md:space-x-4">
                <x-environmental-stats format="footer" />
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-900/50 text-green-400 border border-green-800">{{ __('guest_layout.algorand_blue_mission') }}</div>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <div id="upload-modal" class="fixed inset-0 z-[10000] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1" aria-labelledby="upload-modal-title">
        <div class="relative bg-gray-800 rounded-lg shadow-xl w-[95%] max-w-4xl max-h-[90vh] overflow-y-auto p-4 md:p-6 lg:p-8" role="document">
            <button id="close-upload-modal" class="absolute text-2xl leading-none text-gray-400 md:text-3xl top-2 right-3 md:top-4 md:right-4 hover:text-white" aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">×</button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>
    <x-wallet-connect-modal />

    <!-- Logout Form -->
    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
        @csrf
        <button type="submit" class="sr-only">{{ __('guest_layout.logout_sr_button') }}</button>
    </form>


    <!-- Scripts -->
    @include('layouts.guest_script')
    @vite(['resources/js/guest.js', 'resources/js/polyfills.js', 'resources/ts/main.ts', 'resources/js/app.js'])
    @stack('scripts')
</body>
</html>
