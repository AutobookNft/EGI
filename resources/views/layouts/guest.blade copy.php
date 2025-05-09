{{-- resources/views/layouts/guest.blade.php --}}
{{-- 📜 Oracode Layout: Guest Layout (Homepage Focus) --}}
{{-- Struttura base HTML, asset, navbar, hero dinamico con slot per contenuto sovrapposto, modal upload, footer. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO & Semantica --}}
    <title>{{ $title ?? 'Frangette | Ecological Goods Invent' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Esplora e crea asset digitali ecologici (EGI) sulla piattaforma FlorenceEGI di Frangette, sostenendo progetti di protezione ambientale.' }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}

    {{-- Faviicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Asset CSS (Vite) --}}
    @vite([
        'resources/css/app.css',
        'vendor/ultra/ultra-upload-manager/resources/css/app.css',
    ])
    <script>console.log('resources/views/layouts/guest.blade.php');</script>

    {{-- Icone e Font esterni --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>

    {{-- Stili specifici del layout --}}
    <style>
        /* Stile per l'immagine di sfondo (rimane uguale) */
        #background-image-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/default/random_background/15.jpg') }}'); /* Usa asset() per l'URL */
            background-size: cover;
            background-position: center;
            opacity: 0.35; /* O il valore desiderato */
            z-index: 1; /* Dietro il canvas e il contenuto */
        }

    </style>

    {{-- Schema.org Markup per il WebSite (Generale) --}}
    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "url": "{{ url('/') }}", // Usa url() per l'URL corrente
        "name": "{{ __('FlorenceEGI | Frangette') }}",
        "description": "{{ $metaDescription ?? 'Esplora e crea asset digitali ecologici (EGI) sulla piattaforma FlorenceEGI di Frangette, sostenendo progetti di protezione ambientale.' }}",
        "publisher": {
            "@type": "Organization",
            "name": "{{ __('Frangette Cultural Promotion Association') }}",
            "url": "https://frangette.com/", // Assumi questo sia l'URL corretto
            "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo/Frangette_Logo_1000x1000_transparent.png') }}" // Usa asset()
            }
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ route('home.collections.index') }}?search={search_term_string}", // Esempio URL ricerca
            "query-input": "required name=search_term_string"
        }
        }
    </script>
    {{ $schemaMarkup ?? '' }} {{-- Per Schema.org specifico della pagina --}}
    {{ $headExtra ?? '' }}    {{-- Per meta tag specifici della pagina --}}
    @stack('styles')          {{-- Per stili specifici della pagina --}}

</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    {{-- @style: Ombra, sticky per mantenerla visibile --}}
    <header class="bg-white shadow-md sticky top-0 z-50" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group" aria-label="{{ __('Frangette Home') }}">
                         <img src="{{ asset('images/logo/Frangette_Logo_1000x1000_transparent.png') }}" alt="Frangette Logo" class="h-8 w-auto">
                        <span class="font-semibold text-lg text-gray-800 group-hover:text-green-600 transition hidden sm:inline">{{ __('Frangette') }}</span>
                    </a>
                </div>
                {{-- Navigazione Desktop --}}
                <nav class="hidden md:flex items-center space-x-6" role="navigation" aria-label="{{ __('Main navigation') }}">
                    <a href="{{ route('home.collections.index') }}" class="text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium">{{ __('Collections') }}</a>
                    <a href="{{ route('epps.index') }}" class="text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium">{{ __('EPPs') }}</a>
                    {{-- Pulsante Crea EGI --}}
                    <button class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        id="open-upload-modal"
                        data-upload-type="egi"
                        aria-haspopup="dialog"
                        aria-label="{{ __('uploadmanager::uploadmanager.create_egi') }}">
                        {{ __('uploadmanager::uploadmanager.create_egi') }}
                    </button>
                    {{-- Link Dashboard --}}
                    <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium">{{ __('Enter Dashboard') }}</a>
                </nav>
                 {{-- Pulsante Hamburger Menu Mobile --}}
                <div class="-mr-2 flex md:hidden">
                    <button type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" id="hamburger-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" id="close-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        {{-- Menu Mobile (Contenuto) --}}
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                 <a href="{{ route('home.collections.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium">Collections</a>
                 <a href="{{ route('epps.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium">EPPs</a>
                 <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:bg-gray-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium">Enter Dashboard</a>
            </div>
             <div class="pt-4 pb-3 border-t border-gray-200 px-5">
                 <button id="open-upload-modal-mobile" data-upload-type="egi" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('uploadmanager::uploadmanager.create_egi') }}
                 </button>
             </div>
        </div>
    </header>

    <!-- Hero Section con Animazione e Contenuto Sovrapposto -->
    <section id="hero-section" class="relative min-h-screen flex items-center overflow-hidden">
        {{-- Layers di sfondo --}}
        <div id="background-gradient" class="absolute inset-0 z-2"></div>
        <div id="background-image-layer" class="absolute inset-0 z-1"></div>
        <canvas id="backgroundCanvas" class="absolute inset-0 z-3 w-full h-full"></canvas>

        {{-- Contenitore per il contenuto SOVRAPPOSTO --}}
        <div class="hero-content-overlay container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center"> {{-- Aggiunto flex-col --}}

             {{-- 🎠 NUOVO: Carousel EGI Casuali --}}
             {{-- Lo inseriamo qui, sopra il contenuto testuale (che è commentato) e le collezioni in evidenza --}}
             {{-- Passiamo la variabile $randomEgis recuperata dal controller --}}
             {{-- Lo slot 'heroCarousel' verrà popolato da home.blade.php --}}
             <div class="w-full mb-10 md:mb-12 z-10 opacity-60"> {{-- Spazio sotto il carousel --}}
                 {{ $heroCarousel ?? '' }}
             </div>

             {{-- Slot per contenuto Testuale Hero (Mantenuto vuoto per ora) --}}
             <div class="hero-text-content max-w-3xl mx-auto text-center text-white mb-12 md:mb-16 z-30 opacity-60">
                 {{ $heroContent ?? '' }}
             </div>

             {{-- Slot per contenuto SOTTO il testo Hero (Collezioni Evidenza) --}}
             <div class="below-hero-content w-full max-w-6xl mx-auto">
                 {{ $belowHeroContent ?? '' }}
             </div>

        </div>
    </section>

    {{-- Contenuto Principale della Pagina (sotto l'Hero) --}}
    <main id="main-content" role="main" class="flex-grow">
        {{ $slot }} {{-- Qui verranno iniettate le sezioni "Ultime Gallerie", "EPP", "CTA" --}}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t py-8 mt-auto" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-500 text-sm">© {{ date('Y') }} {{ __('Frangette APS') }}</p>
            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                <span class="text-sm text-gray-500">{{ __('Total CO₂ Offset') }}: <strong class="text-gray-700">123.456 Kg</strong></span> {{-- Reso statico come prima --}}
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('Algorand Carbon-Negative') }}</div>
            </div>
        </div>
    </footer>

    {{-- Modal di Upload --}}
    <div id="upload-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
        <div class="relative bg-gray-800 rounded-lg shadow-xl max-w-4xl w-11/12 md:w-3/4 lg:w-1/2 max-h-[90vh] overflow-y-auto p-6 md:p-8">
            <button id="close-upload-modal" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl leading-none" aria-label="{{ __('Close upload modal') }}">
                ×
            </button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>

    {{-- Script per animazione e UI (incluso menu mobile) --}}
    @include('layouts.guest_script')

    {{-- Asset JS (Vite) --}}
    @vite([
        'resources/js/app.js',
        'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts'
    ])

    @stack('scripts') {{-- Per script specifici della pagina --}}

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
