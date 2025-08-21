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
@endphp

{{-- Statistiche Payment Distribution GLOBALI --}}
<div class="flex flex-col items-center justify-center w-full gap-4 sm:gap-6"
    id="globalStatsContainer_{{ $instanceId }}">
    <div class="p-4 border rounded-lg backdrop-blur-sm border-white/10" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex divide-x divide-white/20">
            {{-- VOLUME - Totale importo distribuito (€) --}}
            <div class="pr-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.volume') }}
                </div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statVolume_{{ $instanceId }}">
                    @if($totalVolume > 0)
                    €{{ number_format($totalVolume, 2) }}
                    @else
                    €0.00
                    @endif
                </div>
            </div>

            {{-- EPP - Totale distribuito agli EPP (€) --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.epp') }}</div>
                <div class="text-green-400" style="font-size: 12px; color: #4ade80; font-weight: 700;"
                    id="statEpp_{{ $instanceId }}">
                    @if($eppTotal > 0)
                    €{{ number_format($eppTotal, 2) }}
                    @else
                    €0.00
                    @endif
                </div>
            </div>

            {{-- COLLECTIONS - Numero totale delle collections (come EGIS) --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.collections')
                    }}</div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statCollections_{{ $instanceId }}">
                    {{ number_format($totalCollections) }}
                </div>
            </div>

            {{-- SELL COLLECTIONS - Collections con distribuzioni attive --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{
                    __('statistics.sell_collections') }}</div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statSellCollections_{{ $instanceId }}">
                    {{ number_format($sellCollections) }}
                </div>
            </div>

            {{-- EGIS - Numero totale degli EGI presenti --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.egis') }}
                </div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statTotalEgis_{{ $instanceId }}">
                    {{ number_format($totalEgis) }}
                </div>
            </div>

            {{-- SELL_EGIS - Numero totale degli EGI che hanno in corso una prenotazione valida --}}
            <div class="pl-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.sell_egis') }}
                </div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statSellEgis_{{ $instanceId }}">
                    {{ number_format($sellEgis) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript per aggiornamento automatico delle statistiche globali --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const instanceId = "{{ $instanceId }}";
    const globalStatsContainer = document.getElementById('globalStatsContainer_' + instanceId);

    if (!globalStatsContainer) return;

    // Aggiorna le statistiche globali
    function updateGlobalStats() {
        // console.log('Aggiornamento statistiche globali desktop...'); // Debug
        fetch('/api/stats/global')
            .then(response => response.json())
            .then(data => {
                // console.log('Dati ricevuti:', data); // Debug
                if (data.success && data.formatted) {
                    // Aggiorna i valori con i dati formattati e aggiungi effetto brillamento
                    const volumeElement = document.getElementById('statVolume_' + instanceId);
                    const eppElement = document.getElementById('statEpp_' + instanceId);
                    const collectionsElement = document.getElementById('statCollections_' + instanceId);
                    const sellCollectionsElement = document.getElementById('statSellCollections_' + instanceId);
                    const totalEgisElement = document.getElementById('statTotalEgis_' + instanceId);
                    const sellEgisElement = document.getElementById('statSellEgis_' + instanceId);

                    // Funzione per aggiungere effetto brillamento
                    function addShineEffect(element, newValue) {
                        if (element && element.textContent !== newValue) {
                            // console.log('Aggiornamento valore:', element.id, 'da', element.textContent, 'a', newValue); // Debug
                            element.textContent = newValue;
                            element.style.transition = 'all 0.3s ease';
                            element.style.transform = 'scale(1.05)';
                            element.style.textShadow = '0 0 10px rgba(255, 255, 255, 0.8)';

                            setTimeout(() => {
                                element.style.transform = 'scale(1)';
                                element.style.textShadow = 'none';
                            }, 300);
                        } else if (element) {
                            element.textContent = newValue;
                        }
                    }

                    addShineEffect(volumeElement, data.formatted.volume);
                    addShineEffect(eppElement, data.formatted.epp);
                    addShineEffect(collectionsElement, data.formatted.collections);
                    addShineEffect(sellCollectionsElement, data.formatted.sell_collections);
                    addShineEffect(totalEgisElement, data.formatted.total_egis);
                    addShineEffect(sellEgisElement, data.formatted.sell_egis);
                }
            })
            .catch(error => {
                console.error('Errore nel recupero delle statistiche globali:', error);
            });
    }

    // Prima chiamata immediata per testare
    setTimeout(updateGlobalStats, 1000);

    // Aggiorna ogni 5 secondi (temporaneo per test, poi ripristinare a 30000)
    const updateInterval = setInterval(updateGlobalStats, 5000);

    // Cleanup quando l'elemento viene rimosso dal DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.removedNodes.forEach(function(node) {
                if (node === globalStatsContainer || (node.contains && node.contains(globalStatsContainer))) {
                    clearInterval(updateInterval);
                    observer.disconnect();
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
</script>
