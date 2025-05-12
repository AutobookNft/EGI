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
    <title>{{ $title ?? __('FlorenceEGI | Frangette - Ecological Goods Invent') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('Esplora, crea e colleziona asset digitali ecologici (EGI) unici su FlorenceEGI. Ogni opera supporta progetti concreti di protezione ambientale. Unisciti al Rinascimento Digitale dell\'arte e della sostenibilità.') }}">
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
        "name": "{{ __('FlorenceEGI | Frangette') }}",
        "url": "{{ url('/') }}",
        "description": "{{ $metaDescription ?? __('Piattaforma per la creazione e lo scambio di Ecological Goods Invent (EGI) che finanziano progetti ambientali.') }}",
        "publisher": {
            "@type": "Organization",
            "name": "{{ __('Frangette Cultural Promotion Association') }}",
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

    {{-- ⚙️ INIEZIONE CONFIGURAZIONE PER TYPESCRIPT (PRIMA di @vite per main.ts) --}}
    <script id="app-config" type="application/json">
    @php
        // Determine authentication status
        $isAuthenticatedByBackend = auth()->check();
        $loggedInUser = $isAuthenticatedByBackend ? auth()->user() : null;
        // Assume user model has a 'wallet_address' attribute for logged-in users
        $loggedInUserWallet = $loggedInUser?->wallet_address;

        // Get initial user collection data
        // This requires backend logic, assuming a method like getCurrentCollectionDetails on the User model
        $initialUserData = [
            'current_collection_id' => null,
            'current_collection_name' => null,
            'can_edit_current_collection' => false,
        ];
        // If user is logged in, try to get their current collection details
        if ($isAuthenticatedByBackend && method_exists($loggedInUser, 'getCurrentCollectionDetails')) {
            // Ensure the returned data structure matches the InitialUserData interface
            $initialUserData = (array) $loggedInUser->getCurrentCollectionDetails();
             // Provide defaults in case the backend method doesn't return all keys
            $initialUserData = array_merge([
                'current_collection_id' => null,
                'current_collection_name' => null,
                'can_edit_current_collection' => false,
            ], $initialUserData); // Ensure it's an array for array_merge
        }


        // Collect frontend translation keys used by TypeScript modules
        // This is a manual collection based on the provided TS files.
        // A more robust approach would be needed for larger applications.
        $frontendTranslationKeys = [
             'padminGreeting', // main.ts
             'padminReady', // main.ts
             // walletConnect.ts
             'errorModalNotFoundConnectWallet', 'connecting', 'walletAddressRequired',
             'errorConnectionFailed', 'errorConnectionGeneric', 'registrationRequiredTitle',
             'registrationRequiredTextCollections', 'registerNowButton', 'laterButton',
             'errorEgiFormOpen', 'errorUnexpected', 'walletConnectedTitle',
             // walletDropdown.ts
             'errorWalletDropdownMissing', 'errorNoWalletToCopy', 'copied',
             'errorCopyAddress', 'disconnectedTitle', 'disconnectedTextWeak',
             'errorLogoutFormMissing', 'errorApiDisconnect', 'walletDefaultText',
             'walletAriaLabelLoggedIn', 'walletAriaLabelConnected', 'loggedInStatus', 'connectedStatusWeak',
             // collectionUI.ts
             'errorGalleriesListUIDOM', 'errorFetchCollections', 'errorFetchCollectionsHttp', 'errorFetchCollectionsGeneric',
             'errorSetCurrentCollectionHttp', 'errorSetCurrentCollectionGeneric',
             'errorLoadingGalleries', 'byCreator', 'switchingGallery', 'gallerySwitchedTitle',
             'gallerySwitchedText', 'pageWillReload', 'myGalleriesOwned', 'myGalleriesCollaborations',
             'editCurrentGalleryTitle', 'viewCurrentGalleryTitle', 'Loading galleries...', 'No galleries found.', 'Error loading galleries.', 'switchingGallery',
             // mobileMenu.ts
             'errorMobileMenuElementsMissing', // corrected duplicate key
             // Add any other keys used directly by appTranslate in other modules
        ];

        // Fetch the actual translated strings using Laravel's translation system
        $translations = [];
        foreach($frontendTranslationKeys as $key) {
            // Assuming the keys in TS directly map to keys in Laravel translation files
            // e.g., 'walletConnectedTitle' in TS corresponds to 'walletConnectedTitle' in a Laravel lang file
             $translations[$key] = __($key);
        }

         // Include some fallback/generic error messages used by UEM_Client_TS_Placeholder
         // These might be redundant if all UI messages are explicitly listed above,
         // but good for safety if UEM placeholder uses a string literal not in the list.
         $translations['An error occurred.'] = __('An error occurred.'); // Default UEM Placeholder fallback
         $translations['Client error: :errorCode. See console.'] = __('Client error: :errorCode. See console.'); // UEM Placeholder generic client error
         $translations['Authorization check failed.'] = __('Authorization check failed.'); // Used in UploadModalManager (deprecated method)
         $translations['You are not authorized to perform this action.'] = __('You are not authorized to perform this action.'); // Used in UploadModalManager (deprecated method)
         $translations['Could not verify authorization. Please try again.'] = __('Could not verify authorization. Please try again.'); // Used in UploadModalManager (deprecated method)


        // Build the application configuration object for the frontend
        $appConfig = [
            'isAuthenticatedByBackend' => $isAuthenticatedByBackend,
            'loggedInUserWallet' => $loggedInUserWallet,
            'initialUserData' => $initialUserData,
            'routes' => [
                'baseUrl' => url('/'), // Base URL of the application
                'walletConnect' => route('wallet.connect'), // Route for wallet connection API (POST /wallet/connect)
                'collectionsCreate' => route('collections.create'), // Route to create a new collection (GET /home/collections/create)
                'register' => route('register'), // Route to registration page (GET /register)
                'logout' => route('logout'), // Route for logout POST request (POST /logout)
                'homeCollectionsIndex' => route('home.collections.index'), // Route to the public collections list page (GET /home/collections)
                // Routes with parameters: Use :id placeholder as expected by the TS route() helper
                // Parameter name is {collection} in web.php routes
                'viewCollectionBase' => route('home.collections.show', ['collection' => ':id']), // GET /home/collections/{collection}
                // 'editCollectionBase' => route('collections.edit', ['collection' => ':id']), // GET /collections/{collection}/edit
                'api' => [
                    'baseUrl' => url('/api'), // Base URL for API calls (prefix /api)
                    'accessibleCollections' => route('api.user.accessibleCollections'), // API route to fetch user's collections (GET /api/user/accessible-collections)
                    'setCurrentCollectionBase' => route('api.user.setCurrentCollection', ['collection' => ':id']), // API route to set current collection (POST /api/user/set-current-collection/{collection})
                    // Include API routes used by TS
                    'checkUploadAuth' => route('upload.authorization'), // API route for upload authorization check (GET /api/check-upload-authorization), used by UploadModalManager (note: TS interface name 'checkUploadAuth' matches backend route name 'upload.authorization' in web.php's API group)
                    // 'walletDisconnect' => route('api.wallet.disconnect'), // API route for weak wallet disconnect (POST /api/wallet/disconnect)
                    // 'uemConfigEndpoint' => route('api.error-definitions'), // API endpoint for UEM config (as per TS analysis) - THIS ROUTE IS NOT DEFINED IN THE PROVIDED web.php. Omitting it.
                ],
            ],
            'translations' => $translations, // Frontend translations
        ];

        // Add specific translations used within guest.blade.php itself if needed by TS for fallback/init
         $appConfig['translations']['Open main menu'] = __('Open main menu'); // Example, if toggleMobileMenu needed this explicitly via appTranslate
         $appConfig['translations']['Close main menu'] = __('Close main menu');


    @endphp
    {{-- Inject the generated config JSON --}}
    @json($appConfig)
</script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen antialiased">

    <!-- Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50" role="banner" aria-label="{{ __('Site Header') }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group" aria-label="{{ __('Navigate to Frangette Homepage') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="{{ __('Frangette Platform Logo') }}" class="h-8 w-auto">
                        <span class="font-semibold text-lg text-gray-800 group-hover:text-green-600 transition hidden sm:inline">{{ __('Frangette') }}</span>
                    </a>
                </div>

                {{-- Navigazione Desktop --}}
                <nav class="hidden md:flex items-center space-x-1" role="navigation" aria-label="{{ __('Main desktop navigation') }}">
                    @php $navLinkClasses = "text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium"; @endphp
                    @unless(request()->routeIs('home') || request()->is('/')) <a href="{{ url('/') }}" class="{{ $navLinkClasses }}" aria-label="{{ __('Navigate to Homepage') }}">{{ __('Home') }}</a> @endunless {{-- Mostra Home se non siamo sulla home --}}

                    {{-- Link "Collections" Generico (nascosto se utente loggato, gestito da TS) --}}
                    <a href="{{ route('home.collections.index') }}" id="generic-collections-link-desktop" class="{{ $navLinkClasses }} @if(request()->routeIs('home.collections.*')) text-green-600 font-semibold @endif" aria-label="{{ __('View all public collections') }}">{{ __('Collections') }}</a>

                    {{-- NUOVO: Dropdown "My Galleries" (HTML presente, visibilità gestita da TS) --}}
                    <div id="collection-list-dropdown-container" class="relative hidden"> {{-- Nascosto di default, TS lo mostra per utenti loggati --}}
                        <button id="collection-list-dropdown-button" type="button"
                                class="{{ $navLinkClasses }} inline-flex items-center"
                                aria-expanded="false" aria-haspopup="true" aria-controls="collection-list-dropdown-menu" aria-label="{{ __('Open my galleries menu') }}">
                            <span class="material-symbols-outlined mr-1 text-base" aria-hidden="true">view_carousel</span>
                            <span id="collection-list-button-text">{{ __('My Galleries') }}</span> {{-- Testo che potrebbe cambiare? O fisso? --}}
                            <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                        <div id="collection-list-dropdown-menu"
                             class="absolute right-0 z-20 mt-2 w-72 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                             role="menu" aria-orientation="vertical" aria-labelledby="collection-list-dropdown-button" tabindex="-1">
                            <div id="collection-list-loading" class="px-4 py-3 text-sm text-gray-500 text-center" role="status">{{ __('Loading galleries...') }}</div>
                            <div id="collection-list-empty" class="px-4 py-3 text-sm text-gray-500 text-center hidden">{{ __('No galleries found.') }} <a href="{{ route('collections.create') }}" class="underline hover:text-green-600">{{ __('Create one?') }}</a></div>
                            <div id="collection-list-error" class="px-4 py-3 text-sm text-red-600 text-center hidden" role="alert">{{ __('Error loading galleries.') }}</div>
                            {{-- Voci menu popolate da TS --}}
                        </div>
                    </div>

                    <a href="{{ route('epps.index') }}" class="{{ $navLinkClasses }} @if(request()->routeIs('epps.*')) text-green-600 font-semibold @endif" aria-label="{{ __('View Environmental Protection Projects') }}">{{ __('EPPs') }}</a>

                    <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $navLinkClasses }} flex items-center gap-1" aria-label="{{ __('Create new EGI') }}"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('Create EGI') }} </button>
                    <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $navLinkClasses }} flex items-center gap-1" aria-label="{{ __('Create new gallery') }}"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg> {{ __('Create Collection') }} </button>

                    <span class="border-l border-gray-300 h-6 mx-2" aria-hidden="true"></span>

                    {{-- NUOVO: Badge Collection Corrente (HTML presente, visibilità gestita da TS) --}}
                    <div id="current-collection-badge-container" class="ml-2 hidden items-center"> {{-- Nascosto di default, TS lo mostra --}}
                        <a href="#" id="current-collection-badge-link" {{-- href e title impostati da TS --}}
                           class="flex items-center px-3 py-1.5 border border-sky-300 bg-sky-50 text-sky-700 text-xs font-semibold rounded-full hover:bg-sky-100 hover:border-sky-400 transition"
                           aria-label="{{ __('Current active gallery') }}"> {{-- aria-label più specifico verrà aggiunto da TS se necessario --}}
                            <span class="material-symbols-outlined mr-1.5 text-sm leading-none" aria-hidden="true">folder_managed</span>
                            <span id="current-collection-badge-name"></span>
                        </a>
                    </div>

                    <div id="wallet-cta-container" class="ml-2">
                        <button id="connect-wallet-button" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('Connect your Algorand wallet') }}"> <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg> {{ __('Connect Wallet') }} </button>
                        <div id="wallet-dropdown-container" class="relative hidden">
                            <button id="wallet-dropdown-button" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" aria-expanded="false" aria-haspopup="true" aria-controls="wallet-dropdown-menu">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg>
                                <span id="wallet-display-text">Wallet</span>
                                <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                            </button>
                            <div id="wallet-dropdown-menu" class="absolute right-0 z-20 mt-2 w-56 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="wallet-dropdown-button" tabindex="-1">
                                <a href="{{ route('dashboard') }}" id="wallet-dashboard-link" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" aria-label="{{ __('Go to your dashboard') }}">
                                    <span class="material-symbols-outlined w-5 h-5 mr-2 text-gray-500" aria-hidden="true">dashboard</span>
                                    {{ __('Dashboard') }}
                                </a>
                                <button id="wallet-copy-address" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" aria-label="{{ __('Copy your wallet address') }}">
                                     <span class="material-symbols-outlined w-5 h-5 mr-2 text-gray-500" aria-hidden="true">content_copy</span>
                                     {{ __('Copy Address') }}
                                 </button>
                                <button id="wallet-disconnect" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" aria-label="{{ __('Disconnect your wallet or logout') }}">
                                    <span class="material-symbols-outlined w-5 h-5 mr-2 text-gray-500" aria-hidden="true">logout</span>
                                    {{ __('Disconnect') }} {{-- Testo cambierà in Logout se utente è 'logged-in' --}}
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}" aria-label="{{ __('Login to your account') }}">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" id="register-link-desktop" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('Register a new account') }}">{{ __('Register') }}</a>
                </nav>

                {{-- Menu Mobile Button --}}
                <div class="-mr-2 flex md:hidden">
                     <button type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">{{ __('Open main menu') }}</span>
                        <svg class="block h-6 w-6" id="hamburger-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg class="hidden h-6 w-6" id="close-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile (Contenuto) --}}
        <div class="md:hidden hidden" id="mobile-menu" role="navigation" aria-label="{{ __('Main mobile navigation') }}">
            @php $mobileNavLinkClasses = "text-gray-600 hover:bg-gray-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium"; @endphp
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                 @unless(request()->routeIs('home') || request()->is('/')) <a href="{{ url('/') }}" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('Navigate to Homepage') }}">{{ __('Home') }}</a> @endunless
                 <a href="{{ route('home.collections.index') }}" id="generic-collections-link-mobile" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('View all public collections') }}">{{ __('Collections') }}</a> {{-- Visibilità gestita da TS --}}
                 {{-- Qui TS potrebbe inserire "My Galleries" se utente loggato --}}
                 <a href="{{ route('epps.index') }}" class="{{ $mobileNavLinkClasses }}" aria-label="{{ __('View Environmental Protection Projects') }}">{{ __('EPPs') }}</a>
                 <button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $mobileNavLinkClasses }} w-full text-left" aria-label="{{ __('Create new EGI') }}">{{ __('Create EGI') }}</button>
                 <button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $mobileNavLinkClasses }} w-full text-left" aria-label="{{ __('Create new gallery') }}">{{ __('Create Collection') }}</button>
            </div>
             <div class="pt-4 pb-3 border-t border-gray-200 px-5 space-y-3" id="mobile-auth-section">
                 <div id="wallet-cta-container-mobile">
                     <button id="connect-wallet-button-mobile" type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('Connect your Algorand wallet') }}"> <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" /></svg> {{ __('Connect Wallet') }} </button>
                     {{-- Per utenti loggati/connessi, TS potrebbe mostrare qui info wallet e link dashboard/disconnect --}}
                 </div>
                 <div class="flex justify-center gap-3" id="mobile-login-register-buttons">
                       <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" aria-label="{{ __('Login to your account') }}">{{ __('Login') }}</a>
                       <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700" aria-label="{{ __('Register a new account') }}">{{ __('Register') }}</a>
                  </div>
             </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section id="hero-section" class="relative min-h-screen flex items-center overflow-hidden" aria-labelledby="hero-main-title">
        {{-- Titolo H1 nascosto per screen reader ma presente per SEO e struttura --}}
        <h1 id="hero-main-title" class="sr-only">{{ $title ?? __('FlorenceEGI | Frangette - Ecological Goods Invent') }}</h1>
        <div id="background-gradient" class="absolute inset-0 z-2" aria-hidden="true"></div>
        <div id="background-image-layer" class="absolute inset-0 z-1" aria-hidden="true"></div>
        <canvas id="backgroundCanvas" class="absolute inset-0 z-3 w-full h-full" aria-hidden="true"></canvas>
        <div class="hero-content-overlay container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
             <div class="w-full mb-10 md:mb-12 z-10" role="region" aria-label="{{ __('Featured EGI Carousel') }}"> {{ $heroCarousel ?? '' }} </div>
             <div class="hero-text-content max-w-3xl mx-auto text-center text-white mb-12 md:mb-16 z-10" role="region" aria-label="{{ __('Hero Introduction') }}"> {{ $heroContent ?? '' }} </div>
             <div class="below-hero-content w-full max-w-6xl mx-auto" role="region" aria-label="{{ __('Featured Content Below Hero') }}"> {{ $belowHeroContent ?? '' }} </div>
        </div>
    </section>

    {{-- Main Content Area --}}
    <main id="main-content" role="main" class="flex-grow">
        {{ $slot }} {{-- Contenuto specifico della pagina (es. home.blade.php) --}}
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t py-8 mt-auto" role="contentinfo" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">{{ __('Footer content and legal links') }}</h2>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:flex md:justify-between md:items-center">
            <p class="text-gray-500 text-sm mb-4 md:mb-0">© {{ date('Y') }} {{ __('Frangette APS') }}. {{ __('All rights reserved.') }}</p>
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-end space-y-2 md:space-y-0 md:space-x-4">
                {{-- Link Privacy e Cookie (DA IMPLEMENTARE) --}}
                {{-- <a href="{{ route('privacy.policy') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Privacy Policy') }}</a>
                <a href="{{ route('cookie.settings') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Cookie Settings') }}</a> --}}
                <span class="text-sm text-gray-500">{{ __('Total CO₂ Offset') }}: <strong class="text-gray-700">123.456 Kg</strong></span>
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('Algorand Carbon-Negative') }}</div>
            </div>
        </div>
    </footer>

    {{-- Modali --}}
    {{-- Modale Upload EGI (contenuto da egimodule) --}}
    <div id="upload-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1" aria-labelledby="upload-modal-title">
        <div class="relative bg-gray-800 rounded-lg shadow-xl max-w-4xl w-11/12 md:w-3/4 lg:w-1/2 max-h-[90vh] overflow-y-auto p-6 md:p-8" role="document">
            {{-- Il titolo dovrebbe essere dentro uploading_form_content o aggiunto qui se manca --}}
            {{-- <h2 id="upload-modal-title" class="sr-only">{{ __('Upload EGI Modal') }}</h2> --}}
            <button id="close-upload-modal" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl leading-none" aria-label="{{ __('Close EGI upload modal') }}">×</button>
            @include('egimodule::partials.uploading_form_content') {{-- Assicurati che questo parziale abbia un titolo h2 appropriato per aria-labelledby --}}
        </div>
    </div>

    {{-- Modale Connessione Wallet --}}
    <div id="connect-wallet-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-labelledby="connect-wallet-title" aria-hidden="true" tabindex="-1">
        <div class="relative bg-white rounded-lg shadow-xl w-11/12 md:w-1/2 lg:w-1/3 max-h-[90vh] overflow-y-auto p-6 md:p-8 text-gray-800" role="document">
            <button id="close-connect-wallet-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-3xl leading-none" aria-label="{{ __('Close connect wallet modal') }}">×</button>
            <h2 id="connect-wallet-title" class="text-2xl font-semibold mb-6 text-center">{{ __('Connect Your Algorand Wallet') }}</h2>
            <form id="connect-wallet-form" method="POST" action="{{ route('wallet.connect') }}">
                @csrf
                <div class="mb-4">
                    <label for="wallet_address" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Paste your Algorand Address') }}</label>
                    <input type="text" name="wallet_address" id="wallet_address" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ __('Enter your 58-character Algorand address') }}" aria-describedby="wallet-error-message">
                    <p id="wallet-error-message" class="text-red-500 text-xs mt-1 hidden" role="alert"></p>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Connect') }}</button>
                <p class="text-xs text-gray-500 mt-3 text-center">{{ __('Connecting allows liking and weak reservations.') }} <a href="{{ route('register') }}" class="underline hover:text-indigo-600">{{ __('Register for full features.') }}</a></p>
            </form>
        </div>
    </div>

    {{-- Form di Logout (nascosto) --}}
    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
        @csrf
        <button type="submit" class="sr-only">{{ __('Logout') }}</button>
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