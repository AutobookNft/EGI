{{-- resources/views/layouts/guest.blade.php --}}
{{-- 📜 Oracode Blade Layout: Guest Experience Foundation - Responsive Edition --}}
{{-- Questa è la base per tutte le viste pubbliche e per gli utenti con "weak auth". --}}
{{-- Integra la logica TypeScript modulare per interattività dinamica e responsività. --}}
{{-- SEO, ARIA, Schema.org e accessibilità mobile sono considerati per una comunicazione ottimale. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900 scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Fondamentale per le chiamate POST AJAX --}}
    <meta name="theme-color" content="#121212"> {{-- Color theme per browser mobile --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $title ?? __('guest_layout.default_title') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('guest_layout.default_description') }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    {{-- Preconnect per risorse critiche --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    {{-- Stylesheets --}}
    @vite(['resources/css/app.css','vendor/ultra/ultra-upload-manager/resources/css/app.css'])
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" media="print" onload="this.media='all'"/>

    {{-- Polyfill per browser legacy e stili essenziali inline --}}
    <script>
        // Polyfill per IntersectionObserver in browser legacy
        if (!('IntersectionObserver' in window)) {
            document.write('<script src="https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver"><\/script>');
        }
    </script>

    {{-- Stili inline per elementi con background dinamico o posizionamento critico --}}
    <style>
        /* Stili essenziali per il rendering iniziale corretto (mobile-first) */
        :root {
            --app-height: 100%;
            --doc-height: 100%;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            height: 100vh;
            height: var(--app-height);
            scroll-behavior: smooth;
        }

        @supports (-webkit-touch-callout: none) {
            /* Fix per viewport height su Safari iOS */
            .min-h-screen {
                min-height: var(--doc-height);
            }
        }

        #background-image-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/default/random_background/15.jpg') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.35;
            z-index: 1;
        }

        #background-gradient {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6));
        }

        #backgroundCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 3;
        }

        .hero-content-overlay {
            position: relative;
            z-index: 10;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 6rem;
            padding-bottom: 2rem;
        }

        @media (min-width: 768px) {
            .hero-content-overlay {
                padding-top: 8rem;
                padding-bottom: 4rem;
            }
        }

        /* Prevenzione di sfarfallio durante il caricamento di elementi dinamici */
        .invisible-until-ready {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .invisible-until-ready.ready {
            opacity: 1;
        }

        /* Miglioramento accessibilità focus visibile */
        :focus:not(:focus-visible) {
            outline: none;
        }
        :focus-visible {
            outline: 2px solid #4f46e5;
            outline-offset: 2px;
        }

        /* Utility per Responsive Typography */
        .text-responsive-xl {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            line-height: 1.2;
        }
        .text-responsive-lg {
            font-size: clamp(1.25rem, 3vw, 2rem);
            line-height: 1.3;
        }
        .text-responsive-base {
            font-size: clamp(0.875rem, 2vw, 1rem);
            line-height: 1.5;
        }
    </style>

    {{-- Schema.org JSON-LD per il Sito Web --}}
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

    {{-- Placeholder per eventuale Schema.org specifico della pagina (es. Prodotto, Articolo) --}}
    {{ $schemaMarkup ?? '' }}
    {{-- Placeholder per meta tag extra specifici della pagina --}}
    {{ $headExtra ?? '' }}
    @stack('styles') {{-- Per stili specifici della vista pushati dallo stack --}}
</head>
<body class="flex flex-col min-h-screen antialiased text-gray-300 bg-gray-900">
    {{-- Script iniziale per calcolo altezza viewport su dispositivi mobili --}}
    <script>
        // Fix per 100vh su dispositivi mobili
        function setDocHeight() {
            document.documentElement.style.setProperty('--doc-height', `${window.innerHeight}px`);
            document.documentElement.style.setProperty('--app-height', `${window.innerHeight}px`);
        }
        window.addEventListener('resize', setDocHeight);
        window.addEventListener('orientationchange', setDocHeight);
        setDocHeight();
    </script>


    <!-- Navbar -->

<header class="sticky top-0 z-50 w-full border-b border-gray-800 shadow-lg bg-gray-900/90 backdrop-blur-md" role="banner" aria-label="{{ __('guest_layout.header_aria_label') }}">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            {{-- Logo --}}
            <div class="flex items-center flex-shrink-0">
                <a href="{{ url('/home') }}" class="flex items-center gap-2 group" aria-label="{{ __('guest_layout.logo_aria_label') }}">
                    <img src="{{ asset('images/logo/logo_1.webp') }}" alt="{{ __('guest_layout.logo_alt_text') }}" class="w-auto h-7 sm:h-8 md:h-9">
                    <span class="hidden text-base font-semibold text-gray-100 transition group-hover:text-emerald-400 md:text-lg sm:inline">{{ __('guest_layout.brand_name') }}</span>
                </a>
            </div>

            {{-- Navigazione Desktop --}}
            <nav class="items-center hidden space-x-1 md:flex" role="navigation" aria-label="{{ __('guest_layout.desktop_nav_aria_label') }}">
                @php $navLinkClasses = "text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40"; @endphp
                @unless(request()->routeIs('home') || request()->is('/'))
                    <a href="{{ url('/') }}" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.home_link_aria_label') }}">{{ __('guest_layout.home') }}</a>
                @endunless {{-- Mostra Home se non siamo sulla home --}}

                {{-- Link "Collections" Generico (nascosto se utente loggato, gestito da TS) --}}
                <a href="{{ route('home.collections.index') }}" id="generic-collections-link-desktop" class="{{ $navLinkClasses }} @if(request()->routeIs('home.collections.*')) text-emerald-400 font-semibold @endif" aria-label="{{ __('guest_layout.collections_link_aria_label') }}">{{ __('guest_layout.collections') }}</a>

                {{-- NUOVO: Dropdown "My Galleries" (HTML presente, visibilità gestita da TS) --}}
                <div id="collection-list-dropdown-container" class="relative hidden"> {{-- Nascosto di default, TS lo mostra per utenti loggati --}}
                    <button id="collection-list-dropdown-button" type="button"
                            class="{{ $navLinkClasses }} inline-flex items-center"
                            aria-expanded="false" aria-haspopup="true" aria-controls="collection-list-dropdown-menu" aria-label="{{ __('guest_layout.my_galleries_dropdown_aria_label') }}">
                        <span class="mr-1 text-base material-symbols-outlined" aria-hidden="true">view_carousel</span>
                        <span id="collection-list-button-text">{{ __('guest_layout.my_galleries') }}</span> {{-- Testo che potrebbe cambiare? O fisso? --}}
                        <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                    </button>
                    <div id="collection-list-dropdown-menu"
                         class="absolute right-0 z-20 hidden py-1 mt-2 origin-top-right bg-gray-900 rounded-md shadow-xl max-h-[60vh] overflow-y-auto w-72 ring-1 ring-gray-700 backdrop-blur-sm border border-gray-800 focus:outline-none"
                         role="menu" aria-orientation="vertical" aria-labelledby="collection-list-dropdown-button" tabindex="-1">
                        <div id="collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-400" role="status">{{ __('guest_layout.loading_galleries') }}</div>
                        <div id="collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-400">{{ __('guest_layout.no_galleries_found') }} <a href="{{ route('collections.create') }}" class="underline hover:text-emerald-400">{{ __('guest_layout.create_one_question') }}</a></div>
                        <div id="collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-400" role="alert">{{ __('guest_layout.error_loading_galleries') }}</div>
                        {{-- Voci menu popolate da TS --}}
                    </div>
                </div>

                <a href="{{ route('epps.index') }}" class="{{ $navLinkClasses }} @if(request()->routeIs('epps.*')) text-emerald-400 font-semibold @endif" aria-label="{{ __('guest_layout.epps_link_aria_label') }}">{{ __('guest_layout.epps') }}</a>

                <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $navLinkClasses }} flex items-center gap-1" aria-label="{{ __('guest_layout.create_egi_aria_label') }}"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('guest_layout.create_egi') }} </button>
                <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $navLinkClasses }} flex items-center gap-1" aria-label="{{ __('guest_layout.create_collection_aria_label') }}"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('guest_layout.create_collection') }} </button>

                <span class="h-6 mx-2 border-l border-gray-700" aria-hidden="true"></span>

                {{-- NUOVO: Badge Collection Corrente (HTML presente, visibilità gestita da TS) --}}
                <div id="current-collection-badge-container" class="items-center hidden ml-2"> {{-- Nascosto di default, TS lo mostra --}}
                    <a href="#" id="current-collection-badge-link" {{-- href e title impostati da TS --}}
                       class="flex items-center px-3 py-1.5 border border-sky-700 bg-sky-900/60 text-sky-300 text-xs font-semibold rounded-full hover:bg-sky-800 hover:border-sky-600 transition"
                       aria-label="{{ __('guest_layout.current_collection_badge_aria_label') }}"> {{-- aria-label più specifico verrà aggiunto da TS se necessario --}}
                        <span class="material-symbols-outlined mr-1.5 text-sm leading-none" aria-hidden="true">folder_managed</span>
                        <span id="current-collection-badge-name"></span>
                    </a>
                </div>

                <div id="wallet-cta-container" class="ml-2">
                    <button id="connect-wallet-button" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.connect_wallet_aria_label') }}"> <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg> {{ __('guest_layout.connect_wallet') }} </button>
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
                                {{ __('guest_layout.disconnect') }} {{-- Testo cambierà in Logout se utente è 'logged-in' --}}
                            </button>
                        </div>
                    </div>
                </div>

                <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.login_link_aria_label') }}">{{ __('guest_layout.login') }}</a>
                <a href="{{ route('register') }}" id="register-link-desktop" class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.register_link_aria_label') }}">{{ __('guest_layout.register') }}</a>
            </nav>

            {{-- Menu Mobile Button --}}
            <div class="flex -mr-2 md:hidden">
                 <button type="button" class="inline-flex items-center justify-center p-2 text-gray-400 bg-gray-900 rounded-md hover:text-gray-300 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button" aria-label="{{ __('guest_layout.toggle_mobile_menu_aria_label') }}">
                    <span class="sr-only">{{ __('guest_layout.open_mobile_menu_sr') }}</span>
                    <svg class="block w-6 h-6" id="hamburger-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    <svg class="hidden w-6 h-6" id="close-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu Mobile (Contenuto) --}}
    <div class="hidden md:hidden" id="mobile-menu" role="navigation" aria-label="{{ __('guest_layout.mobile_nav_aria_label') }}">
        @php $mobileNavLinkClasses = "text-gray-300 hover:bg-gray-800 hover:text-emerald-400 block px-3 py-2.5 rounded-md text-base font-medium transition"; @endphp
        <div class="px-2 pt-2 pb-3 space-y-1 border-b border-gray-800 sm:px-3">
             @unless(request()->routeIs('home') || request()->is('/')) <a href="{{ url('/') }}" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('guest_layout.mobile_home_link_aria_label') }}">{{ __('guest_layout.home') }}</a> @endunless
             <a href="{{ route('home.collections.index') }}" id="generic-collections-link-mobile" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('guest_layout.mobile_collections_link_aria_label') }}">{{ __('guest_layout.collections') }}</a> {{-- Visibilità gestita da TS --}}
             {{-- Qui TS potrebbe inserire "My Galleries" se utente loggato --}}
             <a href="{{ route('epps.index') }}" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('guest_layout.mobile_epps_link_aria_label') }}">{{ __('guest_layout.epps') }}</a>
             <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $mobileNavLinkClasses }} w-full text-left" aria-label="{{ __('guest_layout.mobile_create_egi_aria_label') }}">{{ __('guest_layout.create_egi') }}</button>
             <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $mobileNavLinkClasses }} w-full text-left" aria-label="{{ __('guest_layout.mobile_create_collection_aria_label') }}">{{ __('guest_layout.create_collection') }}</button>
        </div>
         <div class="px-4 pt-4 pb-3 space-y-3 border-b border-gray-800" id="mobile-auth-section">
             <div id="wallet-cta-container-mobile">
                 <button id="connect-wallet-button-mobile" type="button" class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.mobile_connect_wallet_aria_label') }}"> <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg> {{ __('guest_layout.connect_wallet') }} </button>
                 {{-- Per utenti loggati/connessi, TS potrebbe mostrare qui info wallet e link dashboard/disconnect --}}
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

         {{-- Mobile: Badge Collection Corrente --}}
         <div id="current-collection-badge-container-mobile" class="hidden px-4 py-3 text-center border-t border-gray-800">
            <div class="inline-flex items-center px-3 py-2 text-xs font-medium text-center border rounded-full text-sky-300 border-sky-700 bg-sky-900/60">
                <span class="material-symbols-outlined mr-1.5 text-sm" aria-hidden="true">folder_managed</span>
                <span id="current-collection-badge-name-mobile"></span>
            </div>
         </div>
    </div>
</header>


{{-- Hero Section --}}
@unless(isset($noHero) && $noHero)
    <section id="hero-section" class="relative flex flex-col items-center overflow-hidden min-h-[100vh]" aria-labelledby="hero-main-title">
        {{-- Titolo H1 nascosto per screen reader ma presente per SEO e struttura --}}
        <h1 id="hero-main-title" class="sr-only">{{ $title ?? __('guest_layout.default_title') }}</h1>
        <div id="background-gradient" aria-hidden="true"></div>
        <div id="background-image-layer" aria-hidden="true"></div>
        <canvas id="backgroundCanvas" aria-hidden="true"></canvas>

        <div class="container relative flex flex-col items-center w-full px-4 mx-auto hero-content-overlay sm:px-6 lg:px-8">
            {{-- Layout responsive per hero content:
                - Mobile: Verticale (sinistra/centrale/destra stacked)
                - Tablet: 2 colonne (sinistra+destra/centrale)
                - Desktop: 3 colonne (sinistra/centrale/destra) --}}
            <div class="z-10 grid w-full grid-cols-1 gap-8 mb-10 md:grid-cols-12 md:mb-12 lg:gap-6">
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
                <div class="hidden md:block md:col-span-3" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                    {{ $heroContentRight ?? '' }}
                    <x-natan-assistant />
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

            {{-- Contenuto sotto l'hero --}}
            <div class="w-full mx-auto below-hero-content max-w-7xl" role="region" aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}">
                {{ $belowHeroContent ?? '' }}
            </div>
        </div>

        {{-- Freccia scorri verso il basso (visibile solo su mobile/tablet) --}}
        <div class="absolute z-20 transform -translate-x-1/2 bottom-6 left-1/2 md:hidden animate-bounce-slow">
            <button type="button" aria-label="{{ __('guest_layout.scroll_down_aria_label') }}" class="flex items-center justify-center w-10 h-10 text-white bg-black rounded-full bg-opacity-30 hover:bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="document.getElementById('main-content').scrollIntoView({behavior: 'smooth'});">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </button>
        </div>
    </section>

    {{-- Stili responsive per hero layout --}}
    <style>
        /* Mobile optimization */
        @media (max-width: 768px) {
            #hero-section {
                min-height: 100vh;
                justify-content: flex-start;
                padding-top: 2rem;
            }
            .hero-content-overlay {
                padding-top: 2rem;
                padding-bottom: 5rem; /* Space for scroll arrow */
            }

            /* Scale down images on mobile */
            #hero-section img {
                max-height: 250px;
                width: auto;
            }

            /* Improve NFT cards on small screens */
            .nft-glass-card {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }
        }

        /* Tablet optimization */
        @media (min-width: 768px) and (max-width: 1023px) {
            #hero-section {
                min-height: 100vh;
            }
            .hero-content-overlay {
                padding-top: 3rem;
            }

            /* Adjusted layout for tablet */
            .md\:grid-cols-2 > div {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            /* Scale down images slightly on tablet */
            #hero-section img {
                max-height: 300px;
                width: auto;
            }
        }

        /* Add animation for scroll arrow */
        .animate-bounce-slow {
            animation: bounce 1.5s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0) translateX(-50%);
            }
            50% {
                transform: translateY(-10px) translateX(-50%);
            }
        }

        /* Improve NFT effects for all device sizes */
        @media (prefers-reduced-motion: no-preference) {
            .nft-pulse-only {
                animation-duration: 3s; /* Slightly faster on mobile */
            }

            .nft-badge-shine {
                animation-duration: 4s; /* Slower for better performance */
            }
        }

        /* Add responsive typography for hero section */
        #hero-section h2, #hero-section .text-xl {
            font-size: clamp(1.25rem, 4vw, 1.75rem);
            line-height: 1.3;
        }

        #hero-section .text-lg {
            font-size: clamp(1rem, 3vw, 1.25rem);
            line-height: 1.4;
        }

        /* Improve carousel responsive behavior */
        #hero-section .relative.w-full.p-1.mt-4.overflow-hidden.rounded-xl {
            max-width: min(90vw, 600px); /* Limit width on very wide screens */
            margin-left: auto;
            margin-right: auto;
        }
    </style>
@endunless

    {{-- Main Content Area --}}
<main id="main-content" role="main" class="flex-grow">
    {{ $slot }} {{-- Contenuto specifico della pagina (es. home.blade.php) --}}
</main>

{{-- Footer --}}
<footer class="py-6 mt-auto bg-gray-900 border-t border-gray-800 md:py-8" role="contentinfo" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">{{ __('guest_layout.footer_sr_heading') }}</h2>
    <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8 md:flex md:justify-between md:items-center">
        <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('guest_layout.copyright_holder') }}. {{ __('guest_layout.all_rights_reserved') }}</p>
        <div class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-y-0 md:space-x-4">
            {{-- Link Privacy e Cookie (DA IMPLEMENTARE) --}}
            {{-- <a href="{{ route('privacy.policy') }}" class="text-sm text-gray-400 hover:text-gray-300">{{ __('guest_layout.privacy_policy') }}</a>
            <a href="{{ route('cookie.settings') }}" class="text-sm text-gray-400 hover:text-gray-300">{{ __('guest_layout.cookie_settings') }}</a> --}}
            <x-environmental-stats format="footer" />
            <div class="text-xs px-2 py-0.5 rounded-full bg-green-900/50 text-green-400 border border-green-800">{{ __('guest_layout.algorand_blue_mission') }}</div>
        </div>
    </div>
</footer>

{{-- Modali --}}
{{-- Modale Upload EGI (contenuto da egimodule) --}}
<div id="upload-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1" aria-labelledby="upload-modal-title">
    <div class="relative bg-gray-800 rounded-lg shadow-xl w-[95%] max-w-4xl max-h-[90vh] overflow-y-auto p-4 md:p-6 lg:p-8" role="document">
        {{-- Il titolo dovrebbe essere dentro uploading_form_content o aggiunto qui se manca --}}
        {{-- <h2 id="upload-modal-title" class="sr-only">{{ __('guest_layout.upload_modal_title') }}</h2> --}}
        <button id="close-upload-modal" class="absolute text-2xl leading-none text-gray-400 md:text-3xl top-2 right-3 md:top-4 md:right-4 hover:text-white" aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">×</button>
        @include('egimodule::partials.uploading_form_content') {{-- Assicurati che questo parziale abbia un titolo h2 appropriato per aria-labelledby --}}
    </div>
</div>

{{-- Modale Connessione Wallet --}}
<x-wallet-connect-modal />

{{-- Form di Logout (nascosto) --}}
<form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
    @csrf
    <button type="submit" class="sr-only">{{ __('guest_layout.logout_sr_button') }}</button>
</form>

{{-- LAYOUTS.GUEST_SCRIPT è stato rimosso perché la sua logica è ora nel main.ts o in partials dedicati --}}
{{-- Se conteneva script di terze parti (es. Alpine.js CDN), vanno inclusi qui o gestiti via npm e Vite --}}
{{-- Esempio se Alpine era lì: <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}

@include('layouts.guest_script') {{-- Includi eventuali script Blade specifici --}}

{{-- Aggiungiamo uno script per migliorare il supporto alla responsività dinamica --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix per il menu mobile
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                const expanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
                mobileMenuButton.setAttribute('aria-expanded', !expanded);
                mobileMenu.classList.toggle('hidden');
                hamburgerIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');

                // Previeni lo scroll del body quando il menu è aperto
                document.body.classList.toggle('overflow-hidden', !expanded);
            });
        }

        // Supporto per lazy loading delle immagini
        if ('loading' in HTMLImageElement.prototype) {
            // Il browser supporta lazy loading nativo
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            lazyImages.forEach(img => {
                // Possiamo lasciare l'attributo loading="lazy" e il browser farà il resto
            });
        } else {
            // Fallback per browser che non supportano lazy loading nativo
            // Potresti voler aggiungere una libreria di lazy loading qui
            console.log('This browser does not support native lazy loading');
        }

        // Animazione elementi al viewport
        if ('IntersectionObserver' in window) {
            const animateOnScrollElements = document.querySelectorAll('.animate-on-scroll');
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            animateOnScrollElements.forEach(el => {
                observer.observe(el);
            });
        }

        // Gestione responsive per modali
        function adjustModalForMobile() {
            const uploadModal = document.getElementById('upload-modal');
            if (uploadModal && window.innerWidth < 768) {
                // Aggiustamenti per dispositivi mobili
                const modalContent = uploadModal.querySelector('div[role="document"]');
                if (modalContent) {
                    modalContent.style.maxHeight = '85vh';
                }
            }
        }

        window.addEventListener('resize', adjustModalForMobile);
        adjustModalForMobile();
    });
</script>

{{-- Vite per il caricamento degli asset --}}

{{-- 🔥 INCLUSIONE SCRIPT TYPESCRIPT COMPILATO --}}
@vite(['resources/ts/main.ts', 'resources/js/app.js'])

{{-- CSS Responsive globale aggiuntivo --}}
<style>
    /* Miglioramenti generali alla responsività delle card NFT */
    .collection-card-nft {
        height: auto;
        min-height: 260px;
        transition: transform 0.3s ease;
    }

    @media (max-width: 640px) {
        .collection-card-nft {
            min-height: 220px;
        }

        .collection-card-nft img {
            max-height: 140px;
            object-fit: cover;
        }

        /* Fix per griglia su schermi piccoli */
        .grid-cols-2, .grid-cols-3, .grid-cols-4 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .sm\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 640px) and (max-width: 768px) {
        /* Fix per griglia su tablet piccoli */
        .grid-cols-3, .grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Migliora la visualizzazione del testo su card NFT */
    .collection-card-nft h3, .collection-card-nft .text-xl {
        font-size: clamp(1rem, 2.5vw, 1.25rem);
        line-height: 1.3;
    }

    .collection-card-nft p, .collection-card-nft .text-sm {
        font-size: clamp(0.75rem, 2vw, 0.875rem);
        line-height: 1.4;
    }

    /* Miglioramenti effetti NFT responsivi */
    @media (prefers-reduced-motion: reduce) {
        .nft-rotating-border, .nft-animated-border, .nft-badge-shine,
        .nft-pulsing-border, .nft-pulse, .nft-3d-float, .nft-text-glow,
        .nft-shimmering-text, .animate-bounce-slow, .nft-hover-float:hover,
        .nft-hover-tilt:hover {
            animation: none !important;
            transform: none !important;
            transition: none !important;
            filter: none !important;
        }
    }

    /* Correzioni per tastiera virtuale su mobile */
    @media (max-width: 768px) and (max-height: 600px) {
        #hero-section {
            min-height: 100%;
            height: auto;
        }
    }

    /* Miglioramenti per le stats cards */
    .nft-stat-card {
        width: 100%;
    }

    /* Fix per contenitori scrollabili su iOS */
    .overflow-y-auto, .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }

    /* Miglioramenti accessibilità per bottoni ed elementi interattivi */
    button, a[role="button"], [tabindex="0"] {
        touch-action: manipulation;
    }
</style>

@stack('scripts') {{-- Per script specifici della vista pushati dallo stack --}}

</body>
</html>
