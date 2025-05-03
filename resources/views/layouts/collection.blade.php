{{-- resources/views/layouts/collection.blade.php --}}
{{-- 📜 Oracode Layout: Collection Detail --}}
{{-- Layout specific for viewing a single collection's details. --}}
{{-- Excludes the main hero section for better focus on collection content. --}}
{{-- Includes Navbar, Footer, Upload Modal structure. --}}
{{-- Expects $title, $metaDescription, $headExtra, $headMetaExtra, $schemaMarkup slots/variables. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO & Semantica --}}
    <title>{{ $title ?? 'Collection Detail | FlorenceEGI' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Details for this EGI collection on FlorenceEGI.' }}">
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!}

    {{-- Faviicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Asset CSS (Vite) --}}
    @vite([
        'resources/css/app.css',
        'vendor/ultra/ultra-upload-manager/resources/css/app.css', // Se necessario anche qui
    ])
    <script>console.log('resources/views/layouts/collection.blade.php');</script>

    {{-- Icone e Font esterni --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>

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
    {{-- Slot per Schema.org specifico della pagina (da inserire nella vista show.blade.php) --}}
    {{ $schemaMarkup ?? '' }}

    {{-- Slot per meta tag aggiuntivi specifici della pagina --}}
    {{ $headExtra ?? '' }}

    {{-- Stack per stili specifici della pagina (se necessario) --}}
    @stack('styles')

</head>

<body class="bg-gray-100 text-gray-800"> {{-- Cambiato sfondo leggermente per distinguerlo --}}

    <!-- Navbar - Ruolo ARIA banner -->
    <header class="bg-white shadow-md sticky top-0 z-50" role="banner"> {{-- Resa sticky --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {{-- Padding aggiornato per coerenza --}}
             <div class="flex justify-between items-center h-16">
                {{-- Logo & Branding --}}
                <div class="flex items-center">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group" aria-label="{{ __('Frangette Home') }}">
                        {{-- <div class="h-8 w-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold group-hover:scale-110 transition">F</div> --}}
                        <img src="{{ asset('images/default/logo_1.webp') }}" alt="Frangette Logo" class="h-8 w-auto">
                        <span class="font-semibold text-lg text-gray-800 group-hover:text-green-600 transition hidden sm:inline">{{ __('Frangette') }}</span>
                    </a>
                </div>
                {{-- Navigazione principale - Ruolo ARIA navigation --}}
                <nav class="hidden md:flex items-center space-x-6" role="navigation" aria-label="{{ __('Main navigation') }}">
                    <a href="{{ route('home.collections.index') }}" class="text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium">{{ __('Collections') }}</a>
                    <a href="{{ route('epps.index') }}" class="text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium">{{ __('EPPs') }}</a>
                     {{-- Aggiungi altri link pubblici se necessario --}}

                     {{-- Pulsante "Crea EGI" --}}
                     {{-- @permission TODO: Mostrare solo se l'utente può creare? O link a login? --}}
                    <button id="open-upload-modal" data-upload-type="egi" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" aria-haspopup="dialog" aria-label="{{ __('uploadmanager::uploadmanager.create_egi') }}">
                        {{ __('uploadmanager::uploadmanager.create_egi') }}
                    </button>

                     {{-- Link al Dashboard --}}
                    <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-green-600 transition px-3 py-2 rounded-md text-sm font-medium">{{ __('Enter Dashboard') }}</a>
                </nav>
                 {{-- TODO: Hamburger menu per mobile --}}
                 <div class="-mr-2 flex md:hidden">
                    <button type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        {{-- Icona Hamburger --}}
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        {{-- Icona Close (X) --}}
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        {{-- TODO: Menu mobile (da implementare con JS/Alpine) --}}
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

    {{-- SEZIONE HERO RIMOSSA --}}

    {{-- Contenuto specifico della pagina - Ruolo ARIA main --}}
    <main id="main-content" role="main" class="flex-grow"> {{-- flex-grow per occupare spazio disponibile --}}
        {{ $slot }}
    </main>

    <!-- Footer - Ruolo ARIA contentinfo -->
    <footer class="bg-white border-t py-8 mt-auto" role="contentinfo"> {{-- mt-auto per spingerlo in fondo se il contenuto è corto --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-500 text-sm">© {{ date('Y') }} {{ __('Frangette APS') }}</p> {{-- Aggiornato nome e font size --}}
            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                {{-- Placeholder per CO2 - da rendere dinamico --}}
                <span class="text-sm text-gray-500">{{ __('Total CO₂ Offset') }}: <strong class="text-gray-700"> -- Kg</strong></span>
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('Algorand Carbon-Negative') }}</div>
            </div>
        </div>
    </footer>

    {{-- Modal di Upload (Struttura base come in guest layout) --}}
    <div id="upload-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
        <div class="relative bg-gray-800 rounded-lg shadow-xl max-w-4xl w-11/12 md:w-3/4 lg:w-1/2 max-h-[90vh] overflow-y-auto p-6 md:p-8">
            <button id="close-upload-modal" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl leading-none" aria-label="{{ __('Close upload modal') }}">
                ×
            </button>
            {{-- Il contenuto effettivo del form di upload --}}
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>

    {{-- Script globali per il layout (se differenti da guest_script) --}}
    {{-- @include('layouts.partials.collection_scripts') Crea questo file se servono script specifici --}}

    {{-- Asset JS (Vite) --}}
    @vite([
        'resources/js/app.js',
        'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts', // Se necessario
    ])

    {{-- Stack per script specifici della pagina --}}
    @stack('scripts')

</body>
</html>
