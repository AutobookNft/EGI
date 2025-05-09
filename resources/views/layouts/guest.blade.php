{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Frangette | Ecological Goods Invent' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Esplora e crea asset digitali ecologici (EGI) sulla piattaforma FlorenceEGI di Frangette, sostenendo progetti di protezione ambientale.' }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css','vendor/ultra/ultra-upload-manager/resources/css/app.css'])
    <script>console.log('resources/views/layouts/guest.blade.php');</script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>
    <style>
        #background-image-layer { position: absolute; inset: 0; width: 100%; height: 100%; background-image: url('{{ asset('images/default/random_background/15.jpg') }}'); background-size: cover; background-position: center; opacity: 0.35; z-index: 1; }
        #background-gradient { position: absolute; inset: 0; z-index: 2; /* Aggiungi stile gradiente se usato */ }
        #backgroundCanvas { position: absolute; inset: 0; width: 100%; height: 100%; z-index: 3; }
        .hero-content-overlay { position: relative; z-index: 10; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-top: 8rem; padding-bottom: 4rem; }
    </style>
    <script type="application/ld+json"> { "@context": "https://schema.org", "@type": "WebSite", "url": "{{ url('/') }}", "name": "{{ __('FlorenceEGI | Frangette') }}", "description": "{{ $metaDescription ?? '...' }}", "publisher": { "@type": "Organization", "name": "{{ __('Frangette Cultural Promotion Association') }}", "url": "https://frangette.com/", "logo": { "@type": "ImageObject", "url": "{{ asset('images/logo/Frangette_Logo_1000x1000_transparent.png') }}"} }, "potentialAction": { "@type": "SearchAction", "target": "{{ route('home.collections.index') }}?search={search_term_string}", "query-input": "required name=search_term_string" } } </script>
    {{ $schemaMarkup ?? '' }}
    {{ $headExtra ?? '' }}
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group" aria-label="{{ __('Frangette Home') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="Frangette Logo" class="h-8 w-auto">
                        <span class="font-semibold text-lg text-gray-800 group-hover:text-green-600 transition hidden sm:inline">{{ __('Frangette') }}</span>
                    </a>
                </div>

                {{-- Navigazione Desktop --}}
                <nav class="hidden md:flex items-center space-x-1" role="navigation" aria-label="{{ __('Main navigation') }}">
                    @php $navLinkClasses = "text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium"; @endphp
                    @unless(request()->is('/')) <a href="{{ url('/') }}" class="{{ $navLinkClasses }}">{{ __('Home') }}</a> @endunless
                    <a href="{{ route('home.collections.index') }}" class="{{ $navLinkClasses }} @if(request()->routeIs('home.collections.*')) text-green-600 font-semibold @endif">{{ __('Collections') }}</a>
                    <a href="{{ route('epps.index') }}" class="{{ $navLinkClasses }} @if(request()->routeIs('epps.*')) text-green-600 font-semibold @endif">{{ __('EPPs') }}</a>

                    {{-- Pulsanti Create (triggerano modal connect per guest) --}}
                    <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $navLinkClasses }} flex items-center gap-1"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('Create EGI') }} </button>
                    <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $navLinkClasses }} flex items-center gap-1"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('Create Collection') }} </button>

                    <span class="border-l border-gray-300 h-6 mx-2" aria-hidden="true"></span>

                    {{-- Contenitore dinamico per Connect/Dropdown Wallet --}}
                    <div id="wallet-cta-container">
                        {{-- Bottone Connect (visibile di default) --}}
                        <button id="connect-wallet-button" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"> <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg> {{ __('Connect Wallet') }} </button>

                        {{-- Dropdown Wallet (nascosto di default) --}}
                        <div id="wallet-dropdown-container" class="relative hidden">
                            <button id="wallet-dropdown-button" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" aria-expanded="false" aria-haspopup="true">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                                <span id="wallet-display-text">Wallet</span>
                                <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                            </button>
                            <div id="wallet-dropdown-menu" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="wallet-dropdown-button" tabindex="-1">
                                <a href="{{ route('dashboard') }}" id="wallet-dashboard-link" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3 4.75A.75.75 0 013.75 4h12.5a.75.75 0 010 1.5H3.75A.75.75 0 013 4.75zM3 9.75A.75.75 0 013.75 9h12.5a.75.75 0 010 1.5H3.75A.75.75 0 013 9.75zM3.75 14a.75.75 0 000 1.5h12.5a.75.75 0 000-1.5H3.75z" />
                                    </svg>
                                    {{ __('Dashboard') }}
                                </a>
                                <button id="wallet-copy-address" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                     <svg class="w-5 h-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3.5A1.5 1.5 0 018.5 2h3.879a1.5 1.5 0 011.06.44l3.122 3.12A1.5 1.5 0 0117 6.622V12.5a1.5 1.5 0 01-1.5 1.5h-1v-3.379a3 3 0 00-.879-2.121L10.5 5.379A3 3 0 008.379 4.5H7v-1z" /><path d="M4.5 6A1.5 1.5 0 003 7.5v9A1.5 1.5 0 004.5 18h7a1.5 1.5 0 001.5-1.5v-5.879a1.5 1.5 0 00-.44-1.06L9.44 6.439A1.5 1.5 0 008.378 6H4.5z" /></svg>
                                     {{ __('Copy Address') }}
                                 </button>
                                <button id="wallet-disconnect" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M19 10a.75.75 0 00-.75-.75H8.704l1.048-.943a.75.75 0 10-1.004-1.114l-2.5 2.25a.75.75 0 000 1.114l2.5 2.25a.75.75 0 101.004-1.114l-1.048-.943h9.546A.75.75 0 0019 10z" clip-rule="evenodd" /></svg>
                                    {{ __('Disconnect') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="{{ $navLinkClasses }}">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Register') }}</a>
                </nav>

                {{-- Menu Mobile Button --}}
                <div class="-mr-2 flex md:hidden">
                     <button type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" id="hamburger-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                        <svg class="hidden h-6 w-6" id="close-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile (Contenuto) --}}
        <div class="md:hidden hidden" id="mobile-menu">
             @php $mobileNavLinkClasses = "text-gray-600 hover:bg-gray-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium"; @endphp
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                 @unless(request()->is('/')) <a href="{{ url('/') }}" class="{{ $mobileNavLinkClasses }}">Home</a> @endunless
                 <a href="{{ route('home.collections.index') }}" class="{{ $mobileNavLinkClasses }}">Collections</a>
                 <a href="{{ route('epps.index') }}" class="{{ $mobileNavLinkClasses }}">EPPs</a>
                 <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $mobileNavLinkClasses }} w-full text-left">{{ __('Create EGI') }}</button>
                 <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $mobileNavLinkClasses }} w-full text-left">{{ __('Create Collection') }}</button>
            </div>
             <div class="pt-4 pb-3 border-t border-gray-200 px-5 space-y-3">
                  {{-- Contenitore dinamico mobile --}}
                 <div id="wallet-cta-container-mobile">
                     <button id="connect-wallet-button-mobile" type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"> <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg> {{ __('Connect Wallet') }} </button>
                      {{-- Il dropdown NON viene replicato qui per semplicità, si usa il bottone standard --}}
                 </div>
                 <div class="flex justify-center gap-3">
                       <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">{{ __('Login') }}</a>
                       <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">{{ __('Register') }}</a>
                  </div>
             </div>
        </div>
    </header>

    {{-- ... (Hero, Main, Footer, Modals HTML come prima) ... --}}
    <section id="hero-section" class="relative min-h-screen flex items-center overflow-hidden">
        <div id="background-gradient" class="absolute inset-0 z-2"></div>
        <div id="background-image-layer" class="absolute inset-0 z-1"></div>
        <canvas id="backgroundCanvas" class="absolute inset-0 z-3 w-full h-full"></canvas>
        <div class="hero-content-overlay container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
             <div class="w-full mb-10 md:mb-12 z-10"> {{ $heroCarousel ?? '' }} </div>
             <div class="hero-text-content max-w-3xl mx-auto text-center text-white mb-12 md:mb-16 z-10"> {{ $heroContent ?? '' }} </div>
             <div class="below-hero-content w-full max-w-6xl mx-auto"> {{ $belowHeroContent ?? '' }} </div>
        </div>
    </section>
    <main id="main-content" role="main" class="flex-grow">
        {{ $slot }}
    </main>
    <footer class="bg-white border-t py-8 mt-auto" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-500 text-sm">© {{ date('Y') }} {{ __('Frangette APS') }}</p>
            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                <span class="text-sm text-gray-500">{{ __('Total CO₂ Offset') }}: <strong class="text-gray-700">123.456 Kg</strong></span>
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('Algorand Carbon-Negative') }}</div>
            </div>
        </div>
    </footer>
    <div id="upload-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
        <div class="relative bg-gray-800 rounded-lg shadow-xl max-w-4xl w-11/12 md:w-3/4 lg:w-1/2 max-h-[90vh] overflow-y-auto p-6 md:p-8">
            <button id="close-upload-modal" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl leading-none" aria-label="{{ __('Close upload modal') }}">×</button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>
    <div id="connect-wallet-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-labelledby="connect-wallet-title" aria-hidden="true" tabindex="-1">
        <div class="relative bg-white rounded-lg shadow-xl w-11/12 md:w-1/2 lg:w-1/3 max-h-[90vh] overflow-y-auto p-6 md:p-8 text-gray-800">
            <button id="close-connect-wallet-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-3xl leading-none" aria-label="{{ __('Close connect wallet modal') }}">×</button>
            <h2 id="connect-wallet-title" class="text-2xl font-semibold mb-6 text-center">{{ __('Connect Your Algorand Wallet') }}</h2>
            <form id="connect-wallet-form" method="POST" action="{{ route('wallet.connect') }}">
                @csrf
                <div class="mb-4">
                    <label for="wallet_address" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Paste your Algorand Address') }}</label>
                    <input type="text" name="wallet_address" id="wallet_address" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter your 58-character Algorand address">
                    <p id="wallet-error-message" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Connect') }}</button>
                <p class="text-xs text-gray-500 mt-3 text-center">{{ __('Connecting allows liking and weak reservations.') }} <a href="{{ route('register') }}" class="underline hover:text-indigo-600">{{ __('Register for full features.') }}</a></p>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
        @csrf
    </form>

    {{-- Script layout --}}
    @include('layouts.guest_script')

    {{-- Asset JS (Vite) --}}
    @vite([
        'resources/js/app.js', // Contiene ModalManager init e forse Alpine
        'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts' // Assicurati sia compilato
    ])

    @stack('scripts')


    <script>
        // {{-- 📜 Oracode-compliant script UNIFICATO per gestire modali e stato wallet in guest.blade.php --}}
        // {{-- Gestisce #connect-wallet-modal, interazione con #upload-modal via ModalManager, e UI dinamica navbar --}}

        // === VARIABILI DI STATO E HELPER (Scope globale dello script) ===
        let connectModalLastFocusedElement = null; // Elemento con focus prima di aprire connect modal
        let pendingActionAfterConnect = null; // Azione da eseguire dopo connessione ('create-egi', 'create-collection')
        let isDropdownOpen = false; // Stato del dropdown wallet

        // === FUNZIONI HELPER ===

        /**
         * Apre la modal di connessione wallet.
         * @param {string|null} pendingAction Azione da ricordare dopo la connessione.
         */
        function openConnectWalletModal(pendingAction = null) {
            const connectWalletModal = document.getElementById('connect-wallet-modal');
            if (!connectWalletModal) { console.error("Connect wallet modal (#connect-wallet-modal) not found"); return; }

            pendingActionAfterConnect = pendingAction;
            connectModalLastFocusedElement = document.activeElement; // Salva focus

            connectWalletModal.classList.remove('hidden');
            connectWalletModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden'; // Blocca scroll

            const input = connectWalletModal.querySelector('input[name="wallet_address"]');
            if (input instanceof HTMLElement) input.focus(); else connectWalletModal.focus();
            // Aggiungere trap focus specifico se necessario
            console.log('Connect Wallet Modal Opened. Pending Action:', pendingAction);
        }

        /**
         * Chiude la modal di connessione wallet.
         */
        function closeConnectWalletModal() {
            const connectWalletModal = document.getElementById('connect-wallet-modal');
            const uploadModal = document.getElementById('upload-modal'); // Serve per check scroll
            if (!connectWalletModal || connectWalletModal.classList.contains('hidden')) return;

            connectWalletModal.classList.add('hidden');
            connectWalletModal.setAttribute('aria-hidden', 'true');

            // Riabilita scroll SOLO SE uploadModal NON è visibile
            if (!uploadModal || uploadModal.classList.contains('hidden')) {
                 document.body.style.overflow = '';
            }

            if (connectModalLastFocusedElement instanceof HTMLElement) {
                connectModalLastFocusedElement.focus(); // Ripristina focus
            }
            connectModalLastFocusedElement = null;
            pendingActionAfterConnect = null; // Resetta azione
            console.log('Connect Wallet Modal Closed.');
        }

        /**
         * Controlla lo stato di autenticazione/connessione.
         * @returns {'logged-in' | 'connected' | 'disconnected'}
         */
        function getAuthStatus() {
            // Priorità allo stato backend (strong auth)
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            if (isAuthenticated) return 'logged-in';
            // Altrimenti, controlla localStorage (weak auth)
            if (localStorage.getItem('connected_wallet')) return 'connected';
            // Altrimenti disconnesso
            return 'disconnected';
        }

        /**
         * Ottiene l'indirizzo wallet corrente (da backend o localStorage).
         * @returns {string|null}
         */
        function getConnectedWalletAddress() {
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            const loggedInUserWallet = isAuthenticated ? '{{ auth()->user()?->wallet }}' : null;
            return loggedInUserWallet || localStorage.getItem('connected_wallet');
        }

        /**
         * Salva/Rimuove l'indirizzo wallet da localStorage (SOLO per weak auth).
         * @param {string|null} address Indirizzo da salvare o null per rimuovere.
         */
        function setWeakAuthWallet(address) {
            if (address) {
                localStorage.setItem('connected_wallet', address);
            } else {
                localStorage.removeItem('connected_wallet');
            }
            updateWalletUI(); // Aggiorna sempre dopo cambiamento
        }

        /**
         * Aggiorna l'interfaccia della Navbar in base allo stato.
         */
        function updateWalletUI() {
            const walletAddress = getConnectedWalletAddress();
            const authStatus = getAuthStatus();
            const shortAddress = walletAddress ? `${walletAddress.substring(0, 6)}...${walletAddress.substring(walletAddress.length - 4)}` : null;

            const connectWalletButtonStd = document.getElementById('connect-wallet-button');
            const walletDropdownContainer = document.getElementById('wallet-dropdown-container');
            const walletDisplayText = document.getElementById('wallet-display-text');
            const walletDropdownButton = document.getElementById('wallet-dropdown-button');
            const connectWalletButtonMobile = document.getElementById('connect-wallet-button-mobile');
            const loginLinkDesktop = document.querySelector('nav.hidden.md\\:flex a[href*="login"]');
            const registerLinkDesktop = document.querySelector('nav.hidden.md\\:flex a[href*="register"]');
            const mobileAuthButtonsContainer = document.querySelector('#mobile-menu .flex.justify-center.gap-3'); // Contenitore bottoni Login/Reg mobile

            // --- Logica UI Desktop ---
            if (connectWalletButtonStd && walletDropdownContainer && walletDisplayText && walletDropdownButton) {
                const showDropdown = authStatus === 'logged-in' || authStatus === 'connected';

                connectWalletButtonStd.classList.toggle('hidden', showDropdown);
                walletDropdownContainer.classList.toggle('hidden', !showDropdown);

                if (showDropdown) {
                    walletDisplayText.textContent = shortAddress;
                    if (authStatus === 'logged-in') {
                        walletDropdownButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                        walletDropdownButton.classList.add('bg-green-600', 'hover:bg-green-700');
                        walletDropdownButton.setAttribute('aria-label', `Wallet: ${shortAddress}. User logged in.`);
                    } else { // connected
                        walletDropdownButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                        walletDropdownButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                        walletDropdownButton.setAttribute('aria-label', `Wallet: ${shortAddress}. Connected (weak auth).`);
                    }
                }
                 // Gestione visibilità link Login/Register Desktop
                 if(loginLinkDesktop) loginLinkDesktop.style.display = authStatus === 'logged-in' ? 'none' : 'inline-flex';
                 if(registerLinkDesktop) registerLinkDesktop.style.display = authStatus === 'logged-in' ? 'none' : 'inline-flex';
            }

            // --- Logica UI Mobile ---
            if (connectWalletButtonMobile) {
                connectWalletButtonMobile.style.display = authStatus === 'disconnected' ? 'inline-flex' : 'none';
            }
             // Gestione visibilità bottoni Login/Register Mobile
             if (mobileAuthButtonsContainer) {
                 mobileAuthButtonsContainer.style.display = authStatus === 'logged-in' ? 'none' : 'flex';
             }
            console.log(`UI Updated. Status: ${authStatus}`);
        }

        // --- Funzioni Gestione Dropdown Wallet ---
        function closeWalletDropdownMenu() {
            const walletDropdownMenu = document.getElementById('wallet-dropdown-menu');
            const walletDropdownButton = document.getElementById('wallet-dropdown-button');
            if (!walletDropdownMenu || !walletDropdownButton || walletDropdownMenu.classList.contains('hidden')) return;

            walletDropdownMenu.classList.add('hidden');
            walletDropdownButton.setAttribute('aria-expanded', 'false');
            document.removeEventListener('click', handleOutsideDropdownClick);
            walletDropdownMenu.removeEventListener('keydown', handleDropdownKeydown);
            isDropdownOpen = false;
        }

        function handleOutsideDropdownClick(event) {
            const walletDropdownButton = document.getElementById('wallet-dropdown-button');
            const walletDropdownMenu = document.getElementById('wallet-dropdown-menu');
            if (walletDropdownButton && !walletDropdownButton.contains(event.target) &&
                walletDropdownMenu && !walletDropdownMenu.contains(event.target)) {
                closeWalletDropdownMenu();
            }
        }

        function toggleWalletDropdownMenu(event) {
            // Non serve event.stopPropagation() se usiamo il listener globale controllato
            const walletDropdownMenu = document.getElementById('wallet-dropdown-menu');
            const walletDropdownButton = document.getElementById('wallet-dropdown-button');
            if (!walletDropdownButton || !walletDropdownMenu) return;

            const isExpanded = !walletDropdownMenu.classList.contains('hidden'); // Controlla visibilità diretta

            if (isExpanded) {
                closeWalletDropdownMenu();
            } else {
                walletDropdownMenu.classList.remove('hidden');
                walletDropdownButton.setAttribute('aria-expanded', 'true');
                isDropdownOpen = true;
                // Aggiungi listener per chiudere cliccando fuori e per Escape
                // Rimuoviamo eventuali listener precedenti per sicurezza prima di aggiungerli
                document.removeEventListener('click', handleOutsideDropdownClick);
                walletDropdownMenu.removeEventListener('keydown', handleDropdownKeydown);
                // Aggiungi i listener
                document.addEventListener('click', handleOutsideDropdownClick);
                walletDropdownMenu.addEventListener('keydown', handleDropdownKeydown);
                // Focus sul primo elemento del menu
                const firstMenuItem = walletDropdownMenu.querySelector('button[role="menuitem"]');
                if(firstMenuItem instanceof HTMLElement) firstMenuItem.focus();
            }
        }

        function handleDropdownKeydown(e) {
            const walletDropdownButton = document.getElementById('wallet-dropdown-button');
            if (e.key === 'Escape') {
                closeWalletDropdownMenu();
                if(walletDropdownButton) walletDropdownButton.focus();
            }
            // Aggiungere logica navigazione frecce se desiderato
        }

        // --- Funzioni Azioni Dropdown ---
        function copyWalletAddress() {
            const walletAddress = getConnectedWalletAddress();
            const walletCopyAddressButton = document.getElementById('wallet-copy-address');
            if (!walletAddress || !(walletCopyAddressButton instanceof HTMLElement)) return;

            navigator.clipboard.writeText(walletAddress)
                .then(() => {
                    const originalHTML = walletCopyAddressButton.innerHTML;
                    walletCopyAddressButton.textContent = '✓ Copied!';
                    walletCopyAddressButton.disabled = true;
                    setTimeout(() => {
                        walletCopyAddressButton.innerHTML = originalHTML;
                        walletCopyAddressButton.disabled = false;
                    }, 1500); // Ridotto tempo feedback
                })
                .catch(err => { console.error('Failed to copy address:', err); alert('Failed to copy.'); });
            closeWalletDropdownMenu();
        }

        async function handleDisconnect() {
             const authStatus = getAuthStatus();
             closeWalletDropdownMenu(); // Chiudi menu prima di agire

             if (authStatus === 'logged-in') {
                 // Submit form logout Laravel
                  const logoutForm = document.getElementById('logout-form');
                  if (logoutForm && typeof logoutForm.submit === 'function') { logoutForm.submit(); }
                  else { console.error("Logout form not found!"); alert("Logout error."); }
             } else if (authStatus === 'connected') {
                  // Pulisci stato locale per weak auth
                 try { /* Chiamata API opzionale */ } catch (apiError) { /* Gestisci errore */ }
                 setWeakAuthWallet(null); // Rimuove da localStorage e aggiorna UI
                 console.log("Disconnected locally (weak auth).");
             }
         }

        // --- Funzione Submit Form Connect ---
        async function handleConnectWalletSubmit(e) {
            e.preventDefault();
            const connectWalletForm = document.getElementById('connect-wallet-form');
            const walletErrorMessage = document.getElementById('wallet-error-message');
            const connectSubmitButton = connectWalletForm?.querySelector('button[type="submit"]');
            if (!connectWalletForm || !walletErrorMessage || !connectSubmitButton) return;

            connectSubmitButton.disabled = true; connectSubmitButton.textContent = 'Connecting...';
            walletErrorMessage.classList.add('hidden');

            try {
                const formData = new FormData(connectWalletForm);
                const walletAddress = formData.get('wallet_address'); // Prendi wallet da form
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                const response = await fetch('{{ route("wallet.connect") }}', {
                     method: 'POST',
                     body: new URLSearchParams(formData).toString(),
                     headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded'}
                 });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Connection failed');

                // SUCCESSO!
                const action = window.pendingActionAfterConnect; // Leggi azione PENDENTE
                setWeakAuthWallet(walletAddress); // Salva in localStorage e aggiorna UI
                closeConnectWalletModal(); // Chiudi modal connect (resetta azione pendente)

                // Esegui azione post-connect
                if (action === 'create-egi') {
                     // Chiama ModalManager per aprire la modal upload
                    if (window.globalModalManager?.openModal) {
                         console.log('Wallet connesso, apro modal upload EGI...');
                        setTimeout(() => window.globalModalManager.openModal('egi'), 100); // Leggero ritardo
                    } else { console.error('globalModalManager non trovato!'); alert('Errore apertura form.'); window.location.reload();}
                } else if (action === 'create-collection') {
                     console.log('Wallet connesso, redirect a register per collection...');
                     alert('Wallet connesso! Completa la registrazione per creare una collezione.');
                     window.location.href = '{{ route("register") }}';
                } else {
                     console.log('Wallet connesso (nessuna azione pendente). UI aggiornata.');
                     // Non ricaricare per non perdere lo stato locale, l'UI è già aggiornata
                     // window.location.reload();
                }

            } catch (error) {
                console.error("Errore connessione:", error);
                walletErrorMessage.textContent = error.message || 'Errore.';
                walletErrorMessage.classList.remove('hidden');
            } finally {
                connectSubmitButton.disabled = false; connectSubmitButton.textContent = '{{ __("Connect") }}';
            }
        }


        // === CODICE ESEGUITO AL CARICAMENTO DEL DOM ===
        document.addEventListener('DOMContentLoaded', function () {
            // Riferimenti Elementi DOM (ottenuti qui)
            const connectWalletModal = document.getElementById('connect-wallet-modal');
            const closeConnectWalletButton = document.getElementById('close-connect-wallet-modal');
            const connectWalletForm = document.getElementById('connect-wallet-form');
            const openConnectWalletButtons = document.querySelectorAll('#connect-wallet-button, #connect-wallet-button-mobile');
            const createEgiGuestButtons = document.querySelectorAll('[data-action="open-connect-modal-or-create-egi"]');
            const createCollectionGuestButtons = document.querySelectorAll('[data-action="open-connect-modal-or-create-collection"]');
            const walletDropdownButton = document.getElementById('wallet-dropdown-button');
            const walletCopyAddressButton = document.getElementById('wallet-copy-address');
            const walletDisconnectButton = document.getElementById('wallet-disconnect');
            const closeUploadButton = document.getElementById('close-upload-modal');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            // ---- Associazione Event Listeners ----

            // Apertura Connect Modal (diretta)
            if (openConnectWalletButtons) {
                openConnectWalletButtons.forEach(btn => btn.addEventListener('click', () => openConnectWalletModal(null)));
            }

            // Apertura Connect Modal (da bottoni "Create")
            if (createEgiGuestButtons) {
                createEgiGuestButtons.forEach(btn => btn.addEventListener('click', () => {
                    const authStatus = getAuthStatus();
                    if (authStatus === 'logged-in' || authStatus === 'connected') {
                        if(window.globalModalManager?.openModal) window.globalModalManager.openModal('egi');
                        else console.error('ModalManager non trovato per Create EGI');
                    } else {
                        openConnectWalletModal('create-egi');
                    }
                }));
            }
             if (createCollectionGuestButtons) {
                 createCollectionGuestButtons.forEach(btn => btn.addEventListener('click', () => {
                     const authStatus = getAuthStatus();
                     if (authStatus === 'logged-in') {
                         window.location.href = '{{ route("collections.create") }}';
                     } else if (authStatus === 'connected') {
                         alert('Per creare una collezione è necessario completare la registrazione.');
                         window.location.href = '{{ route("register") }}';
                     } else {
                         openConnectWalletModal('create-collection');
                     }
                 }));
             }


            // Chiusura Connect Modal
            if (closeConnectWalletButton) closeConnectWalletButton.addEventListener('click', closeConnectWalletModal);
            if (connectWalletModal) connectWalletModal.addEventListener('click', (e) => { if (e.target === connectWalletModal) closeConnectWalletModal(); });

            // Chiusura Upload Modal (via ModalManager globale)
            if (closeUploadButton && window.globalModalManager) {
                 closeUploadButton.addEventListener('click', () => window.globalModalManager.closeModal());
            }

            // Submit Form Connect Wallet
            if (connectWalletForm) connectWalletForm.addEventListener('submit', handleConnectWalletSubmit);

            // Dropdown Wallet
            if (walletDropdownButton) walletDropdownButton.addEventListener('click', toggleWalletDropdownMenu);
            if(walletCopyAddressButton) walletCopyAddressButton.addEventListener('click', copyWalletAddress);
            if(walletDisconnectButton) walletDisconnectButton.addEventListener('click', handleDisconnect);

            // Menu Mobile
            if (mobileMenuButton && mobileMenu && hamburgerIcon && closeIcon) {
                mobileMenuButton.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    hamburgerIcon.classList.toggle('hidden');
                    closeIcon.classList.toggle('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', !mobileMenu.classList.contains('hidden'));
                });
            }

            // === INIZIALIZZAZIONE UI AL CARICAMENTO ===
            updateWalletUI(); // Imposta stato iniziale navbar

        }); // Fine DOMContentLoaded
    </script>

</body>
</html>
