{{-- resources/views/layouts/guest.blade.php --}}
{{-- 📜 Oracode Blade Layout: Guest Experience Foundation --}}
{{-- Questa è la base per tutte le viste pubbliche e per gli utenti con "weak auth". --}}
{{-- Integra la logica TypeScript modulare per interattività dinamica. --}}
{{-- SEO, ARIA, e Schema.org sono considerati per una comunicazione ottimale. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Fondamentale per le chiamate POST AJAX --}}
    <title>{{ $title ?? __('guest_layout.default_title') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('guest_layout.default_description') }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Stylesheets --}}
    @vite(['resources/css/app.css','vendor/ultra/ultra-upload-manager/resources/css/app.css'])
    {{-- <script>console.log('Padmin Blade: resources/views/layouts/guest.blade.php loaded');</script> --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" media="print" onload="this.media='all'"/>

    {{-- Stili inline per elementi con background dinamico o posizionamento critico --}}
    <style>
        /* Stili essenziali per il rendering iniziale corretto */
        #background-image-layer { position: absolute; inset: 0; width: 100%; height: 100%; background-image: url('{{ asset('images/default/random_background/15.jpg') }}'); background-size: cover; background-position: center; opacity: 0.35; z-index: 1; }
        #background-gradient { position: absolute; inset: 0; z-index: 2; /* Es: background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.5)); */ }
        #backgroundCanvas { position: absolute; inset: 0; width: 100%; height: 100%; z-index: 3; }
        .hero-content-overlay { position: relative; z-index: 10; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-top: 8rem; padding-bottom: 4rem; }
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


</script>
</head>
<body class="flex flex-col min-h-screen antialiased text-gray-800 bg-gray-50">

    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white shadow-md" role="banner" aria-label="{{ __('guest_layout.header_aria_label') }}">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group" aria-label="{{ __('guest_layout.logo_aria_label') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="{{ __('guest_layout.logo_alt_text') }}" class="w-auto h-8">
                        <span class="hidden text-lg font-semibold text-gray-800 transition group-hover:text-green-600 sm:inline">{{ __('guest_layout.brand_name') }}</span>
                    </a>
                </div>

                {{-- Navigazione Desktop --}}
                <nav class="items-center hidden space-x-1 md:flex" role="navigation" aria-label="{{ __('guest_layout.desktop_nav_aria_label') }}">
                    @php $navLinkClasses = "text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium"; @endphp
                    @unless(request()->routeIs('home') || request()->is('/')) <a href="{{ url('/') }}" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.home_link_aria_label') }}">{{ __('guest_layout.home') }}</a> @endunless {{-- Mostra Home se non siamo sulla home --}}

                    {{-- Link "Collections" Generico (nascosto se utente loggato, gestito da TS) --}}
                    <a href="{{ route('home.collections.index') }}" id="generic-collections-link-desktop" class="{{ $navLinkClasses }} @if(request()->routeIs('home.collections.*')) text-green-600 font-semibold @endif" aria-label="{{ __('guest_layout.collections_link_aria_label') }}">{{ __('guest_layout.collections') }}</a>

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
                             class="absolute right-0 z-20 hidden py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg w-72 ring-1 ring-black ring-opacity-5 focus:outline-none"
                             role="menu" aria-orientation="vertical" aria-labelledby="collection-list-dropdown-button" tabindex="-1">
                            <div id="collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-500" role="status">{{ __('guest_layout.loading_galleries') }}</div>
                            <div id="collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-500">{{ __('guest_layout.no_galleries_found') }} <a href="{{ route('collections.create') }}" class="underline hover:text-green-600">{{ __('guest_layout.create_one_question') }}</a></div>
                            <div id="collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-600" role="alert">{{ __('guest_layout.error_loading_galleries') }}</div>
                            {{-- Voci menu popolate da TS --}}
                        </div>
                    </div>

                    <a href="{{ route('epps.index') }}" class="{{ $navLinkClasses }} @if(request()->routeIs('epps.*')) text-green-600 font-semibold @endif" aria-label="{{ __('guest_layout.epps_link_aria_label') }}">{{ __('guest_layout.epps') }}</a>

                    <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $navLinkClasses }} flex items-center gap-1" aria-label="{{ __('guest_layout.create_egi_aria_label') }}"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('guest_layout.create_egi') }} </button>
                    <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $navLinkClasses }} flex items-center gap-1" aria-label="{{ __('guest_layout.create_collection_aria_label') }}"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('guest_layout.create_collection') }} </button>

                    <span class="h-6 mx-2 border-l border-gray-300" aria-hidden="true"></span>

                    {{-- NUOVO: Badge Collection Corrente (HTML presente, visibilità gestita da TS) --}}
                    <div id="current-collection-badge-container" class="items-center hidden ml-2"> {{-- Nascosto di default, TS lo mostra --}}
                        <a href="#" id="current-collection-badge-link" {{-- href e title impostati da TS --}}
                           class="flex items-center px-3 py-1.5 border border-sky-300 bg-sky-50 text-sky-700 text-xs font-semibold rounded-full hover:bg-sky-100 hover:border-sky-400 transition"
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
                                <span id="wallet-display-text">{{ __('guest_layout.wallet') }}</span>
                                <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                            </button>
                            <div id="wallet-dropdown-menu" class="absolute right-0 z-20 hidden w-56 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="wallet-dropdown-button" tabindex="-1">
                                <a href="{{ route('dashboard') }}" id="wallet-dashboard-link" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" aria-label="{{ __('guest_layout.dashboard_link_aria_label') }}">
                                    <span class="w-5 h-5 mr-2 text-gray-500 material-symbols-outlined" aria-hidden="true">dashboard</span>
                                    {{ __('guest_layout.dashboard') }}
                                </a>
                                <button id="wallet-copy-address" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" aria-label="{{ __('guest_layout.copy_wallet_address_aria_label') }}">
                                     <span class="w-5 h-5 mr-2 text-gray-500 material-symbols-outlined" aria-hidden="true">content_copy</span>
                                     {{ __('guest_layout.copy_address') }}
                                 </button>
                                <button id="wallet-disconnect" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" aria-label="{{ __('guest_layout.disconnect_wallet_aria_label') }}">
                                    <span class="w-5 h-5 mr-2 text-gray-500 material-symbols-outlined" aria-hidden="true">logout</span>
                                    {{ __('guest_layout.disconnect') }} {{-- Testo cambierà in Logout se utente è 'logged-in' --}}
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.login_link_aria_label') }}">{{ __('guest_layout.login') }}</a>
                    <a href="{{ route('register') }}" id="register-link-desktop" class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.register_link_aria_label') }}">{{ __('guest_layout.register') }}</a>
                </nav>

                {{-- Menu Mobile Button --}}
                <div class="flex -mr-2 md:hidden">
                     <button type="button" class="inline-flex items-center justify-center p-2 text-gray-400 bg-white rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">{{ __('guest_layout.open_mobile_menu_sr') }}</span>
                        <svg class="block w-6 h-6" id="hamburger-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg class="hidden w-6 h-6" id="close-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile (Contenuto) --}}
        <div class="hidden md:hidden" id="mobile-menu" role="navigation" aria-label="{{ __('guest_layout.mobile_nav_aria_label') }}">
            @php $mobileNavLinkClasses = "text-gray-600 hover:bg-gray-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium"; @endphp
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                 @unless(request()->routeIs('home') || request()->is('/')) <a href="{{ url('/') }}" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('guest_layout.mobile_home_link_aria_label') }}">{{ __('guest_layout.home') }}</a> @endunless
                 <a href="{{ route('home.collections.index') }}" id="generic-collections-link-mobile" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('guest_layout.mobile_collections_link_aria_label') }}">{{ __('guest_layout.collections') }}</a> {{-- Visibilità gestita da TS --}}
                 {{-- Qui TS potrebbe inserire "My Galleries" se utente loggato --}}
                 <a href="{{ route('epps.index') }}" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('guest_layout.mobile_epps_link_aria_label') }}">{{ __('guest_layout.epps') }}</a>
                 <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $mobileNavLinkClasses }} w-full text-left" aria-label="{{ __('guest_layout.mobile_create_egi_aria_label') }}">{{ __('guest_layout.create_egi') }}</button>
                 <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $mobileNavLinkClasses }} w-full text-left" aria-label="{{ __('guest_layout.mobile_create_collection_aria_label') }}">{{ __('guest_layout.create_collection') }}</button>
            </div>
             <div class="px-5 pt-4 pb-3 space-y-3 border-t border-gray-200" id="mobile-auth-section">
                 <div id="wallet-cta-container-mobile">
                     <button id="connect-wallet-button-mobile" type="button" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('guest_layout.mobile_connect_wallet_aria_label') }}"> <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg> {{ __('guest_layout.connect_wallet') }} </button>
                     {{-- Per utenti loggati/connessi, TS potrebbe mostrare qui info wallet e link dashboard/disconnect --}}
                 </div>
                 <div class="flex justify-center gap-3" id="mobile-login-register-buttons">
                       <a href="{{ route('login') }}" class="flex-1 px-4 py-2 text-sm font-medium text-center text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" aria-label="{{ __('guest_layout.mobile_login_link_aria_label') }}">{{ __('guest_layout.login') }}</a>
                       <a href="{{ route('register') }}" class="flex-1 px-4 py-2 text-sm font-medium text-center text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700" aria-label="{{ __('guest_layout.mobile_register_link_aria_label') }}">{{ __('guest_layout.register') }}</a>
                  </div>
             </div>
        </div>
    </header>

    {{-- Hero Section --}}
    @unless(isset($noHero) && $noHero)
    <section id="hero-section" class="relative flex items-center min-h-screen overflow-hidden" aria-labelledby="hero-main-title">
        {{-- Titolo H1 nascosto per screen reader ma presente per SEO e struttura --}}
        <h1 id="hero-main-title" class="sr-only">{{ $title ?? __('guest_layout.default_title') }}</h1>
        <div id="background-gradient" class="absolute inset-0 z-2" aria-hidden="true"></div>
        <div id="background-image-layer" class="absolute inset-0 z-1" aria-hidden="true"></div>
        <canvas id="backgroundCanvas" class="absolute inset-0 w-full h-full z-3" aria-hidden="true"></canvas>

        <div class="container flex flex-col items-center px-4 mx-auto hero-content-overlay sm:px-6 lg:px-8">
            {{-- Layout a 3 colonne per hero content con proporzioni 3-6-3 --}}
            <div class="z-10 grid grid-cols-1 gap-8 mb-10 lg:grid-cols-12 md:mb-12 lg:gap-6">
                {{-- Colonna sinistra (25%) --}}
                <div class="flex items-center text-left lg:col-span-3" role="region" aria-label="{{ __('guest_layout.hero_left_content_aria_label') }}">
                    {{ $heroContentLeft ?? '' }}
                </div>

                {{-- Colonna centrale: Carousel (50%) --}}
                <div class="flex items-center justify-center lg:col-span-6" role="region" aria-label="{{ __('guest_layout.hero_carousel_aria_label') }}">
                    {{ $heroCarousel ?? '' }}
                </div>

                {{-- Colonna destra (25%) --}}
                <div class="flex items-center text-left lg:col-span-3" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                    {{ $heroContentRight ?? '' }}
                </div>
            </div>

            {{-- Contenuto sotto l'hero --}}
            <div class="w-full max-w-6xl mx-auto below-hero-content" role="region" aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}">
                {{ $belowHeroContent ?? '' }}
            </div>
        </div>
    </section>
    @endunless
    
    {{-- Main Content Area --}}
    <main id="main-content" role="main" class="flex-grow">
        {{ $slot }} {{-- Contenuto specifico della pagina (es. home.blade.php) --}}
    </main>

    {{-- Footer --}}
    <footer class="py-8 mt-auto bg-white border-t" role="contentinfo" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">{{ __('guest_layout.footer_sr_heading') }}</h2>
        <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8 md:flex md:justify-between md:items-center">
            <p class="mb-4 text-sm text-gray-500 md:mb-0">© {{ date('Y') }} {{ __('guest_layout.copyright_holder') }}. {{ __('guest_layout.all_rights_reserved') }}</p>
            <div class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-y-0 md:space-x-4">
                {{-- Link Privacy e Cookie (DA IMPLEMENTARE) --}}
                {{-- <a href="{{ route('privacy.policy') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('guest_layout.privacy_policy') }}</a>
                <a href="{{ route('cookie.settings') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('guest_layout.cookie_settings') }}</a> --}}
                <span class="text-sm text-gray-500">{{ __('guest_layout.total_co2_offset') }}: <strong class="text-gray-700">123.456 Kg</strong></span>
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('guest_layout.algorand_carbon_negative') }}</div>
            </div>
        </div>
    </footer>

    {{-- Modali --}}
    {{-- Modale Upload EGI (contenuto da egimodule) --}}
    <div id="upload-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1" aria-labelledby="upload-modal-title">
        <div class="relative bg-gray-800 rounded-lg shadow-xl max-w-4xl w-11/12 md:w-3/4 lg:w-1/2 max-h-[90vh] overflow-y-auto p-6 md:p-8" role="document">
            {{-- Il titolo dovrebbe essere dentro uploading_form_content o aggiunto qui se manca --}}
            {{-- <h2 id="upload-modal-title" class="sr-only">{{ __('guest_layout.upload_modal_title') }}</h2> --}}
            <button id="close-upload-modal" class="absolute text-3xl leading-none text-gray-400 top-4 right-4 hover:text-white" aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">×</button>
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

    {{-- Vite per il caricamento degli asset --}}

    {{-- 🔥 INCLUSIONE SCRIPT TYPESCRIPT COMPILATO --}}
    @vite(['resources/ts/main.ts', 'resources/js/app.js'])

    @stack('scripts') {{-- Per script specifici della vista pushati dallo stack --}}

</body>
</html>
