@php
use App\Models\PaymentDistribution;
use App\Models\Egi;
use App\Models\Collection;

// Calcola le statistiche
$totalEgis = Egi::count();
$sellEgis = Egi::whereHas('reservations', function($query) {
$query->where('is_current', true)->where('status', 'active');
})->count();

$distributionStats = PaymentDistribution::getDashboardStats();
$totalVolume = $distributionStats['overview']['total_amount_distributed'];

// COLLECTIONS totali (come EGIS)
$totalCollections = Collection::count();

// SELL COLLECTIONS - quelle con distribuzioni (come SELL EGIS)
$sellCollections = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=',
'reservations.id')
->join('egis', 'reservations.egi_id', '=', 'egis.id')
->distinct('egis.collection_id')
->count('egis.collection_id');

$eppTotal = collect($distributionStats['by_user_type'])
->firstWhere('user_type', 'epp')['total_amount'] ?? 0;

// ID univoco per evitare conflitti
$instanceId = uniqid();

// Array delle statistiche per il carousel
$stats = [
[
'label' => 'VOLUME',
'value' => $totalVolume > 0 ? '€' . number_format($totalVolume, 2) : '€0.00',
'id' => 'statVolume_' . $instanceId
],
[
'label' => 'EPP',
'value' => $eppTotal > 0 ? '€' . number_format($eppTotal, 2) : '€0.00',
'id' => 'statEpp_' . $instanceId,
'color' => '#4ade80' // Verde environment
],
[
'label' => 'COLLECTIONS',
'value' => number_format($totalCollections),
'id' => 'statCollections_' . $instanceId
],
[
'label' => 'SELL COLLECTIONS',
'value' => number_format($sellCollections),
'id' => 'statSellCollections_' . $instanceId
],
[
'label' => 'EGIS',
'value' => number_format($totalEgis),
'id' => 'statTotalEgis_' . $instanceId
],
[
'label' => 'SELL EGIS',
'value' => number_format($sellEgis),
'id' => 'statSellEgis_' . $instanceId
]
];
@endphp

{{-- Statistiche Payment Distribution MOBILE - Formato Carousel --}}
<div class="w-full">
    {{-- Contenitore per il carousel fluido --}}
    <div class="relative overflow-hidden">
        <div id="mobile-stats-carousel-{{ $instanceId }}"
            class="flex gap-3 transition-transform duration-1000 ease-in-out" style="transform: translateX(0px);">
            @foreach($stats as $stat)
            <div class="flex-shrink-0">
                {{-- Card singola statistica --}}
                <div class="p-2 border rounded-lg backdrop-blur-sm border-white/10 min-w-[120px]"
                    style="background-color: rgba(0, 0, 0, 0.5);">
                    {{-- Layout inline: label e valore sulla stessa riga --}}
                    <div class="flex items-center justify-between gap-2">
                        {{-- Label --}}
                        <div class="flex-shrink-0 text-xs font-medium tracking-wider text-gray-300 uppercase">
                            {{ $stat['label'] }}
                        </div>

                        {{-- Valore --}}
                        <div class="text-sm font-semibold text-white" id="{{ $stat['id'] }}"
                            style="color: {{ $stat['color'] ?? '#ffffff' }}; font-weight: 700;">
                            {{ $stat['value'] }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            {{-- Duplica le prime 3 card per loop infinito --}}
            @foreach(array_slice($stats, 0, 3) as $stat)
            <div class="flex-shrink-0">
                <div class="p-2 border rounded-lg backdrop-blur-sm border-white/10 min-w-[120px]"
                    style="background-color: rgba(0, 0, 0, 0.5);">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex-shrink-0 text-xs font-medium tracking-wider text-gray-300 uppercase">
                            {{ $stat['label'] }}
                        </div>
                        <div class="text-sm font-semibold text-white"
                            style="color: {{ $stat['color'] ?? '#ffffff' }}; font-weight: 700;">
                            {{ $stat['value'] }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Indicatori di scroll attivi - COMMENTATI per nasconderli --}}
    {{--
    <div class="flex justify-center mt-2 space-x-1">
        @foreach($stats as $index => $stat)
        <div class="w-1.5 h-1.5 bg-white/20 rounded-full transition-all duration-500"
            id="indicator-{{ $instanceId }}-{{ $index }}"></div>
        @endforeach
    </div>
    --}}
</div>

{{-- CSS per animazione fluida --}}
<style>
    .indicator-active {
        background-color: rgba(255, 255, 255, 0.6) !important;
        transform: scale(1.2);
    }

    @keyframes smooth-slide {
        0% {
            transform: translateX(0px);
        }

        100% {
            transform: translateX(-{{ (count($stats) * 132) }}px);
            /* 120px + 12px gap per ogni card */
        }
    }

    .carousel-animation {
        animation: smooth-slide {{ count($stats) * 4 }}s linear infinite;
    }
</style>

{{-- JavaScript per animazione fluida --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('mobile-stats-carousel-{{ $instanceId }}');
    const totalPanels = {{ count($stats) }};
    let currentIndex = 0;
    let isPaused = false;
    let animationDuration = 4000; // 4 secondi per pannello

    if (!carousel) return;

    // Avvia l'animazione CSS continua
    function startAnimation() {
        if (isPaused) return;
        carousel.style.animation = `smooth-slide ${totalPanels * 4}s linear infinite`;
    }

    // Ferma l'animazione
    function pauseAnimation() {
        carousel.style.animationPlayState = 'paused';
    }

    // Riprendi l'animazione
    function resumeAnimation() {
        carousel.style.animationPlayState = 'running';
    }

    // Aggiorna indicatori ogni 100ms - COMMENTATO perché indicatori nascosti
    /*
    function updateIndicators() {
        const animationTime = carousel.style.animationDelay || '0s';
        const progress = (performance.now() % (totalPanels * animationDuration)) / animationDuration;
        const activeIndex = Math.floor(progress) % totalPanels;

        for (let i = 0; i < totalPanels; i++) {
            const indicator = document.getElementById('indicator-{{ $instanceId }}-' + i);
            if (indicator) {
                if (i === activeIndex) {
                    indicator.classList.add('indicator-active');
                } else {
                    indicator.classList.remove('indicator-active');
                }
            }
        }
    }

    setInterval(updateIndicators, 100);
    */

    // Controlli per pausa/ripresa
    carousel.addEventListener('mouseenter', function() {
        isPaused = true;
        pauseAnimation();
    });

    carousel.addEventListener('mouseleave', function() {
        isPaused = false;
        resumeAnimation();
    });

    carousel.addEventListener('touchstart', function() {
        isPaused = true;
        pauseAnimation();
    });

    carousel.addEventListener('touchend', function() {
        setTimeout(function() {
            isPaused = false;
            resumeAnimation();
        }, 2000);
    });

    // Avvia l'animazione
    startAnimation();
    // updateIndicators(); // Commentato perché indicatori nascosti
});
</script>
