{{-- resources/views/layouts/collection.blade.php --}}
{{-- 📜 Oracode Layout: Collection Detail with Guest Styling --}}
{{-- Layout specific for viewing a single collection's details. --}}
{{-- Excludes the main hero section for better focus on collection content. --}}
{{-- Uses Guest layout styling with dark theme and modern design. --}}
{{-- Includes Navbar, Footer, Upload Modal structure. --}}
{{-- Expects $title, $metaDescription, $headExtra, $headMetaExtra, $schemaMarkup slots/variables. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900 scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#121212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- SEO & Semantica --}}
    <title>{{ $title ?? 'Collection Detail | FlorenceEGI' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Details for this EGI collection on FlorenceEGI.' }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    <!-- === FONTS E PRECONNECT (stesso del Guest) === -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ asset('images/logo/logo_1.webp') }}" as="image">

    <!-- Caricamento Font Principali (FlorenceEGI Brand Guidelines) -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"></noscript>

    {{-- Asset CSS (Vite) --}}
    @vite([
        'resources/css/app.css',
        'resources/css/guest.css',
        'vendor/ultra/ultra-upload-manager/resources/css/app.css',
    ])

    <style>
/* DEBUG: Previeni QUALSIASI comportamento di scroll */
* {
    scroll-behavior: auto !important;
}

html, body {
    scroll-behavior: auto !important;
    overflow-anchor: none !important;
}

/* Ultra-Wide Upload Modal Styling */
#upload-modal .modal-container {
    width: 83.333333% !important;    /* 10/12 */
    height: 83.333333% !important;   /* 5/6 */
    max-width: none !important;
    max-height: none !important;
    display: flex !important;
    flex-direction: column !important;
}

/* Il contenuto del form deve riempire tutto lo spazio */
#upload-modal .modal-content {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    min-height: 0 !important;
}

/* Il container del form upload deve essere flessibile */
#upload-modal #upload-container {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    overflow: visible !important;
}

/* Altre modali mantengono il comportamento originale */
#connect-wallet-modal,
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

    {{-- Schema.org Markup per il WebSite (Generale) --}}
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "https://florenceegi.com/",
            "name": "{{ __('FlorenceEGI | Frangette') }}",
            "description": "{{ __('Explore and create ecological digital assets (EGI) on the FlorenceEGI platform by Frangette, supporting environmental protection projects.') }}",
            "publisher": {
                "@type": "Organization",
                "name": "{{ __('Frangette Cultural Promotion Association') }}",
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

<body class="flex flex-col min-h-screen antialiased text-gray-300 bg-gray-900 font-body">

    <!-- Header - Stile Guest Layout -->
    <header class="sticky top-0 z-50 w-full border-b border-gray-800 shadow-lg bg-gray-900/90 backdrop-blur-md" role="banner" aria-label="Main navigation">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">

                {{-- Logo --}}
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group" aria-label="FlorenceEGI Home">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="Frangette Logo" class="w-auto h-7 sm:h-8 md:h-9" loading="lazy" decoding="async">
                        <span class="hidden text-base font-semibold text-gray-400 transition group-hover:text-emerald-400 md:text-lg sm:inline">{{ __('Frangette') }}</span>
                    </a>
                </div>

                @php
                    $navLinkClasses = 'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40';
                @endphp

                {{-- Nav Desktop --}}
                <nav class="items-center hidden space-x-1 md:flex" role="navigation" aria-label="Main navigation">
                    @include('partials.nav-links', ['isMobile' => false])

                    {{-- Dropdown My Galleries --}}
                    <div id="collection-list-dropdown-container" class="relative hidden">
                        <button id="collection-list-dropdown-button" type="button" class="{{ $navLinkClasses }} inline-flex items-center" aria-expanded="false" aria-haspopup="true">
                            <span class="mr-1 text-base material-symbols-outlined" aria-hidden="true">view_carousel</span>
                            <span id="collection-list-button-text">{{ __('My Galleries') }}</span>
                            <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                        <div id="collection-list-dropdown-menu" class="absolute right-0 z-20 hidden py-1 mt-2 origin-top-right bg-gray-900 rounded-md shadow-xl max-h-[60vh] overflow-y-auto w-72 ring-1 ring-gray-700 backdrop-blur-sm border border-gray-800 focus:outline-none">
                            <div id="collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-400">{{ __('Loading galleries...') }}</div>
                            <div id="collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-400">{{ __('No galleries found') }} <a href="{{ route('collections.create') }}" class="underline hover:text-emerald-400">{{ __('Create one?') }}</a></div>
                            <div id="collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-400">{{ __('Error loading galleries') }}</div>
                        </div>
                    </div>

                    {{-- Wallet e Auth --}}
                    <span class="h-6 mx-2 border-l border-gray-700" aria-hidden="true"></span>

                    <div id="current-collection-badge-container" class="items-center hidden ml-2">
                        <a href="#" id="current-collection-badge-link" class="flex items-center px-3 py-1.5 border border-sky-700 bg-sky-900/60 text-sky-300 text-xs font-semibold rounded-full hover:bg-sky-800 hover:border-sky-600 transition">
                            <span class="material-symbols-outlined mr-1.5 text-sm leading-none" aria-hidden="true">folder_managed</span>
                            <span id="current-collection-badge-name"></span>
                        </a>
                    </div>

                    <div id="wallet-cta-container" class="ml-2">
                        <button id="connect-wallet-button" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                            {{ __('Connect Wallet') }}
                        </button>
                        <div id="wallet-dropdown-container" class="relative hidden">
                            <button id="wallet-dropdown-button" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" aria-expanded="false" aria-haspopup="true">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                                <span id="wallet-display-text" class="hidden sm:inline">{{ __('Wallet') }}</span>
                                <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                            </button>
                            <div id="wallet-dropdown-menu" class="absolute right-0 z-20 hidden w-56 py-1 mt-2 origin-top-right bg-gray-900 border border-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none backdrop-blur-sm">
                                <a href="{{ route('dashboard') }}" id="wallet-dashboard-link" class="flex items-center w-full px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined" aria-hidden="true">dashboard</span>
                                    {{ __('Dashboard') }}
                                </a>
                                <button id="wallet-copy-address" class="flex items-center w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined" aria-hidden="true">content_copy</span>
                                    {{ __('Copy Address') }}
                                </button>
                                <button id="wallet-disconnect" class="flex items-center w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined" aria-hidden="true">logout</span>
                                    {{ __('Disconnect') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" id="register-link-desktop" class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Register') }}</a>
                </nav>

                {{-- Menu Mobile Button --}}
                <div class="flex -mr-2 md:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-2 text-gray-400 bg-gray-900 rounded-md hover:text-gray-300 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block w-6 h-6" id="hamburger-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg class="hidden w-6 h-6" id="close-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile Content --}}
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 border-b border-gray-800 sm:px-3">
                @include('partials.nav-links', ['isMobile' => true])
            </div>
            <div class="px-4 pt-4 pb-3 space-y-3 border-b border-gray-800" id="mobile-auth-section">
                <div id="wallet-cta-container-mobile">
                    <button id="connect-wallet-button-mobile" type="button" class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                        {{ __('Connect Wallet') }}
                    </button>
                    <div id="mobile-wallet-info-container" class="hidden pt-2 pb-1 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-emerald-400">
                                <span class="w-5 h-5 mr-2 text-emerald-500 material-symbols-outlined">account_balance_wallet</span>
                                <span id="mobile-wallet-address" class="font-mono text-sm text-emerald-300">0x1234...5678</span>
                            </div>
                            <button id="mobile-copy-address" class="p-1 text-gray-400 rounded-md hover:bg-gray-800">
                                <span class="material-symbols-outlined">content_copy</span>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('dashboard') }}" id="mobile-dashboard-link" class="flex items-center justify-center flex-1 px-3 py-1.5 text-sm text-gray-300 bg-gray-800 rounded-md hover:bg-gray-700">
                                <span class="material-symbols-outlined mr-1.5 text-sm">dashboard</span>
                                {{ __('Dashboard') }}
                            </a>
                            <button id="mobile-disconnect" class="flex items-center justify-center flex-1 px-3 py-1.5 text-sm text-gray-300 bg-gray-800 rounded-md hover:bg-gray-700">
                                <span class="material-symbols-outlined mr-1.5 text-sm">logout</span>
                                {{ __('Disconnect') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-3" id="mobile-login-register-buttons">
                    <a href="{{ route('login') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-center text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-center text-white bg-green-800 border border-green-600 rounded-md hover:bg-green-700">{{ __('Register') }}</a>
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

    {{-- NESSUNA SEZIONE HERO - Focus diretto sul contenuto --}}

    {{-- Contenuto specifico della pagina - Ruolo ARIA main --}}
    <main id="main-content" role="main" class="flex-grow bg-gray-900">
        {{ $slot }}
    </main>

    <!-- Footer - Stile Guest Layout -->
    <footer class="py-6 mt-auto bg-gray-900 border-t border-gray-800 md:py-8" role="contentinfo">
        <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8 md:flex md:justify-between md:items-center">
            <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('Frangette APS') }}. {{ __('All rights reserved') }}</p>
            <div class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-y-0 md:space-x-4">
                <x-environmental-stats format="footer" />
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-900/50 text-green-400 border border-green-800">{{ __('Algorand Blue Mission') }}</div>
            </div>
        </div>
    </footer>

    {{-- Modal di Upload Ultra-Wide (10/12 del display) --}}
    <div id="upload-modal" class="fixed inset-0 z-[10000] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
        <div class="relative p-4 bg-gray-800 rounded-lg shadow-xl modal-container md:p-6 lg:p-8 xl:p-10">
            <button id="close-upload-modal" class="absolute z-10 text-2xl leading-none text-gray-400 md:text-3xl top-3 right-4 md:top-6 md:right-6 hover:text-white" aria-label="Close upload modal">×</button>
            <div class="pr-8 modal-content md:pr-12">
                @include('egimodule::partials.uploading_form_content')
            </div>
        </div>
    </div>
    <x-wallet-connect-modal />

    {{-- Asset JS (Vite) --}}
    @vite([
        'resources/js/app.js',
        'resources/js/guest.js',
        'resources/js/polyfills.js',
        'resources/ts/main.ts',
        'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts',
    ])

    {{-- Stack per script specifici della pagina --}}
    @stack('scripts')

</body>
</html>
