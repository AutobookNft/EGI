{{--
PREVIEW DEL LAYOUT CON STATISTICHE PAYMENT DISTRIBUTION
======================================================

Questo mostra come apparirà la homepage con le nuove statistiche:
--}}

<!DOCTYPE html>
<html>

<head>
    <title>FlorenceEGI - Preview Statistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    {{-- SIMULAZIONE LAYOUT HOMEPAGE --}}
    <div class="min-h-screen">

        {{-- SLOT PLATFORM STATS (NUOVO) --}}
        <div class="flex justify-center py-6 bg-gray-900/50 backdrop-blur-sm">
            <div class="p-4 bg-black border rounded-lg backdrop-blur-sm border-white/10 opacity-70">
                <div class="flex divide-x divide-white/20">
                    {{-- EGIS --}}
                    <div class="pr-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">EGIS</div>
                        <div class="text-white" style="font-size: 8px;">47</div>
                    </div>
                    {{-- SELL EGIS --}}
                    <div class="px-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">SELL EGIS</div>
                        <div class="text-white" style="font-size: 8px;">7</div>
                    </div>
                    {{-- VOLUME --}}
                    <div class="px-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">VOLUME</div>
                        <div class="text-white" style="font-size: 8px;">€1,180.00</div>
                    </div>
                    {{-- COLLECTIONS --}}
                    <div class="px-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">COLLECTIONS</div>
                        <div class="text-white" style="font-size: 8px;">1</div>
                    </div>
                    {{-- EPP --}}
                    <div class="pl-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">EPP</div>
                        <div class="text-white" style="font-size: 8px;">€236.00</div>
                        <div class="text-xs text-gray-400" style="font-size: 6px;">di cui EPP</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SLOT PLATFORM INFO BUTTONS (ESISTENTE) --}}
        <div class="py-4 bg-blue-900 text-white text-center">
            <p>Platform Info Buttons (esistente)</p>
        </div>

        {{-- HERO SECTION (ESISTENTE) --}}
        <div class="min-h-screen bg-gradient-to-br from-purple-900 to-blue-900 flex items-center justify-center">
            <div class="text-center text-white">
                <h1 class="text-6xl font-bold mb-4">FlorenceEGI</h1>
                <p class="text-xl">Hero Section con Collection Carousel</p>
            </div>
        </div>

    </div>

</body>

</html>

{{--
STRUTTURA FINALE HOMEPAGE:
========================

1. 📊 Platform Statistics (NUOVO)
└── Payment Distribution Stats Component
└── Sfondo grigio semi-trasparente
└── 5 statistiche in riga: EGIS | SELL EGIS | VOLUME | COLLECTIONS | EPP

2. 📋 Platform Info Buttons (esistente)
└── Componente esistente

3. 🎨 Hero Section (esistente)
└── Collection Hero Banner
└── Carousel collections

4. 📱 Mobile/Desktop Content (esistente)
└── EGI Carousels
└── Creator/Collector Carousels

STATISTICHE MOSTRATE:
- EGIS: 47 (totale EGI)
- SELL EGIS: 7 (EGI con prenotazioni attive)
- VOLUME: €1,180.00 (totale distribuito)
- COLLECTIONS: 1 (collections con distribuzioni)
- EPP: €236.00 (totale distribuito agli EPP)
--}}
