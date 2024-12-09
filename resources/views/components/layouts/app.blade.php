<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ config('app.name', 'Florence EGI') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>console.log('resources/views/components/layouts/app.blade.php');</script>

        {{-- Icone di MaterialIcons --}}
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

        <!-- Styles -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>
    </head>
    <body class="font-sans antialiased bg-base-200">
        <div class="drawer lg:drawer-open">
            <input id="main-drawer" type="checkbox" class="drawer-toggle" />

            <!-- Page Content -->
            <div class="flex flex-col min-h-screen drawer-content">
                <!-- Navbar -->
                <livewire:navigation-menu />

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="shadow bg-base-100">
                        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Main Content -->
                <main class="flex-1 p-4 lg:p-8">
                    {{ $slot }}
                </main>
            </div>

            <!-- Sidebar -->
            <livewire:sidebar />

            @stack('modals')

            @if(app()->environment('local'))
                {!! $debugInfo ?? '' !!}
            @endif
        </div>
    </body>
</html>
