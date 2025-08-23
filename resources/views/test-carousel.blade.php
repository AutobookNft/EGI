<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Test Carousel</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-violet-800 py-8">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Collections Carousel Demo</h1>
                <p class="text-gray-300">Carousel simile a OpenSea con VOLUME e EPP al posto di ETH e %</p>
            </div>
            
            {{-- Componente Carousel Collections --}}
            <x-carousel-coll-list />
            
            {{-- Statistiche globali esistenti per confronto --}}
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-white mb-6">Global Statistics</h2>
                <x-payment-distribution-stats />
            </div>
            
            {{-- Note tecniche --}}
            <div class="mt-12 p-6 bg-black/30 rounded-lg">
                <h3 class="text-xl font-bold text-white mb-4">Note Tecniche</h3>
                <ul class="text-gray-300 space-y-2 text-sm">
                    <li>• <strong>VOLUME:</strong> Sostituisce ETH, mostra il totale distribuito per collezione</li>
                    <li>• <strong>EPP:</strong> Sostituisce la %, mostra la percentuale distribuita agli EPP</li>
                    <li>• <strong>Dati:</strong> Utilizzano <code class="bg-gray-700 px-2 py-1 rounded">PaymentDistribution::getDashboardStats()</code></li>
                    <li>• <strong>Responsive:</strong> Adattivo per desktop, tablet e mobile</li>
                    <li>• <strong>Auto-scroll:</strong> Carousel automatico ogni 5 secondi (pausa su hover)</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
