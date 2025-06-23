<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Documento Legale | FlorenceEGI' }}</title>

    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #f1f5f9; /* Grigio più neutro per lo sfondo */
            color: #334155; /* Testo più scuro per il corpo */
        }
        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
        }
        .active-nav {
            background-color: #EAE7DC;
            color: #8C6A4A;
            font-weight: 600;
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
        }
        .accordion-button.open .accordion-arrow {
            transform: rotate(180deg);
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased">
    {{-- Il layout non impone più una struttura, ma lascia fare alla vista --}}
    {{ $slot }}
    @stack('scripts')
</body>
</html>
