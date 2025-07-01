{{-- resources/views/creator/under-construction.blade.php --}}
{{-- 📜 Oracode View: Under Construction Page - Standalone Edition --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('creator.under_construction.meta_description', ['section' => $section, 'name' => $creator->name]) }}</title>
    <meta name="description" content="{{ __('creator.under_construction.meta_description', ['section' => $section, 'name' => $creator->name]) }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/scss/app.scss'])
</head>
<body class="h-full font-sans antialiased text-white bg-gray-900">

    <div class="relative flex flex-col items-center justify-center min-h-screen px-4 py-16 bg-gray-900">

        {{-- Il messaggio GRANDE e DORATO in cima, usando 'message' --}}
        {{-- Questo div gestirà la larghezza massima del testo grande --}}
        <div class="w-full max-w-5xl mb-12 text-center"> {{-- Aumentato max-w e aggiunto margin-bottom --}}
            <p class="text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl font-playfair"
               style="color: #D4A574;"> {{-- Applicazione colore oro-fiorentino forzata con style in linea --}}
                {{ __('creator.under_construction.message') }}
            </p>
        </div>

        {{-- Contenitore centrale per gli altri elementi --}}
        <div class="w-full max-w-md mx-auto text-center"> {{-- Questo div contiene l'icona, il titolo e il messaggio di dettaglio --}}

            {{-- Renaissance Pattern Animation --}}
            <div class="relative mb-8">
                <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-oro-fiorentino to-verde-rinascita animate-pulse opacity-20"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-16 h-16 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
            </div>

            {{-- Titolo "In Costruzione" --}}
            <h1 class="mt-8 mb-4 text-2xl font-bold text-white font-playfair">
                {{ __('creator.under_construction.title') }}
            </h1>

            {{-- Messaggio di dettaglio, usando 'message_details' --}}
            <p class="mb-8 text-lg text-gray-300">
                {{ __('creator.under_construction.message_details', ['section' => $section, 'name' => $creator->name]) }}
            </p>

            {{-- Back Button --}}
            <a href="{{ route('creator.home', ['id' => $creator->id]) }}"
                class="inline-flex items-center px-6 py-3 font-semibold text-white transition-all duration-300 bg-blue-600 rounded-full shadow-lg hover:bg-blue-700 hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('creator.under_construction.back_to_profile') }}
            </a>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>
