{{-- resources/views/layouts/guest.blade.php --}}
{{--
    Vista Blade per il componente di layout guest <x-guest-layout>.
    Include la struttura HTML di base, gli asset globali, la navbar,
    la struttura della sezione hero con animazione, la modal di upload
    e il footer.
    Il contenuto specifico della pagina viene iniettato tramite le slot.
    Include ottimizzazioni ARIA e Schema.org per migliorare accessibilità e SEO.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO & Semantica --}}
    {{-- Slot per il titolo della pagina. Essenziale per SEO. --}}
    <title>{{ $title ?? 'Frangette | Ecological Goods Invent' }}</title>

    {{-- Slot per la meta description. Importante per SEO. --}}
    <meta name="description" content="{{ $metaDescription ?? 'Esplora e crea asset digitali ecologici (EGI) sulla piattaforma FlorenceEGI di Frangette, sostenendo progetti di protezione ambientale.' }}">

    {{-- **CORREZIONE APPLICATA:** Uso di {{!! !!}} per renderizzare l'HTML grezzo della slot. --}}
    {{-- Questo assicura che il meta tag venga interpretato come HTML e non visualizzato come testo. --}}
    {!! $headMetaExtra ?? '<meta name="robots" content="index, follow">' !!} {{-- Slot per meta tag extra (es. robots, canonical, ecc.) --}}


    {{-- Faviicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Asset CSS (Vite) --}}
    @vite([
        'resources/css/app.css',
        'vendor/ultra/ultra-upload-manager/resources/css/app.css',
    ])

    {{-- Icone e Font esterni --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>

    {{-- Tutti gli script attualmente sono in layouts/guest_script.blade.php --}}


    {{-- Stili specifici del layout (animazione Hero, bottoni, ecc.) --}}
    <style>

        #background-image-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-image: url('/images/default/random_background/15.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.35;
            z-index: 1;
        }

    </style>

    {{-- Schema.org Markup per il WebSite (Generale per il sito) --}}
    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "url": "https://florenceegi.com/",
        "name": "{{ __('FlorenceEGI | Frangette') }}",
        "description": "{{ $metaDescription ?? 'Esplora e crea asset digitali ecologici (EGI) sulla piattaforma FlorenceEGI di Frangette, sostenendo progetti di protezione ambientale.' }}",
        "publisher": {
            "@type": "Organization",
            "name": "{{ __('Frangette Cultural Promotion Association') }}",
            "url": "https://frangette.com/",
            "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/frangette-logo.png') }}"
            }
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://florenceegi.com/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
        }
    </script>

    {{-- **CORREZIONE:** Slot per meta tag aggiuntivi specifici della pagina (es. Open Graph, Twitter Cards, ecc.) --}}
    {{ $headExtra ?? '' }}

</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Navbar - Ruolo ARIA banner -->
    <header class="bg-white shadow-md" role="banner">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Logo/Link Home - Aggiunta ARIA per il link -->
            <a href="{{ url('/') }}" class="flex items-center gap-4 group" aria-label="{{ __('Frangette Home') }}">
                <div class="h-8 w-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold group-hover:scale-110 transition">F</div>
                <span class="font-semibold text-lg text-gray-800 group-hover:text-green-600 transition">{{ __('Frangette') }}</span>
            </a>
        </div>
        {{-- Navigazione principale - Ruolo ARIA navigation --}}
        <nav class="flex items-center gap-6" role="navigation" aria-label="{{ __('Main navigation') }}">
            {{-- Pulsante "Crea EGI" - Aggiunta ARIA per il dialog --}}
            <button id="open-upload-modal" data-upload-type="egi" class="btn btn-primary" aria-haspopup="dialog" aria-label="{{ __('uploadmanager::uploadmanager.create_egi') }}">
                {{ __('uploadmanager::uploadmanager.create_egi') }}
            </button>
            <a href="{{ url('/upload/egi') }}" class="hover:text-green-600 transition">{{ __('Egi') }}</a>
            <a href="{{ url('/collections/open') }}" class="hover:text-green-600 transition">{{ __('Collections') }}</a>
            <a href="{{ url('/dashboard') }}" class="hover:text-green-600 transition">{{ __('Enter Dashboard') }}</a>
        </nav>
        </div>
    </header>

    <!-- Hero Section con animazione e strati di sfondo -->
    <section id="hero-section">
        <!-- Layer 1: Gradiente di sfondo -->
        <div id="background-gradient"></div>

        <!-- Layer 2: Immagine di sfondo opaca -->
        <div id="background-image-layer"></div>

        <!-- Layer 3: Canvas per l'animazione -->
        <canvas id="backgroundCanvas"></canvas>

        <!-- Layer 4: Contenuto sovrapposto (qui va la slot del contenuto specifico della Hero) -->
        <div class="hero-content">
            <div class="max-w-3xl mx-auto px-6 text-center text-white space-y-6">
                {{ $heroContent ?? '' }}
            </div>
        </div>
    </section>

    {{-- Contenuto specifico della pagina - Ruolo ARIA main --}}
    <main id="main-content" role="main">
        {{ $slot }}
    </main>

    <!-- Footer - Ruolo ARIA contentinfo -->
    <footer class="bg-white border-t py-8" role="contentinfo">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-600">© {{ date('Y') }} {{ __('Frangette') }}</p>
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <span>{{ __('KG CO₂ compensated') }}: <strong>123.456</strong></span>
            <div class="bg-green-600 text-white text-xs px-3 py-1 rounded-full">{{ __('Algorand Carbon-Negative') }}</div>
        </div>
        </div>
    </footer>

    {{-- Modal di Upload - Struttura base. Il contenuto è incluso via partial. --}}
    <div id="upload-modal" class="modal hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
        <div class="relative max-w-4xl w-full mx-4">
            <button id="close-upload-modal" class="absolute top-4 right-4 text-white text-2xl" aria-label="{{ __('Close upload modal') }}">
            ×
            </button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>

    {{-- Script per l'animazione della Hero Section --}}

    @include('layouts.guest_script')

        {{-- Asset JS (Vite) --}}
        @vite([
            'resources/js/app.js',
            'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts'
        ])

        {{-- NOTA IMPORTANTE per la Modal: --}}
        {{-- Il JavaScript che gestisce l'apertura/chiusura della modal --}}
        {{-- DEVE aggiornare l'attributo `aria-hidden` sulla modal (#upload-modal) --}}
        {{-- per riflettere il suo stato visibile (aria-hidden="false" quando visibile, aria-hidden="true" quando nascosta). --}}
        {{-- Deve anche gestire il focus, spostandolo all'interno della modal quando si apre --}}
        {{-- e ripristinandolo sull'elemento che l'ha aperta quando si chiude. --}}

    </body>
    </html>
