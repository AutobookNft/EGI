@props(['collection' => null])

@php
use App\Models\PaymentDistribution;
use App\Models\Egi;

// Se viene passata una collezione specifica, calcola le sue statistiche
if ($collection) {
// VOLUME - Solo distribuzioni di prenotazioni con sub_status = 'highest'
$totalVolume = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
->where('payment_distributions.collection_id', $collection->id)
->where('reservations.sub_status', 'highest')
->sum('payment_distributions.amount_eur');

// EPP - Solo distribuzioni EPP di prenotazioni con sub_status = 'highest'
$eppTotal = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
->where('payment_distributions.collection_id', $collection->id)
->where('reservations.sub_status', 'highest')
->where('payment_distributions.user_type', 'epp')
->sum('payment_distributions.amount_eur');

// EGIS - Numero EGI in questa collezione
$totalEgis = $collection->egis()->count();

// SELL EGIS - EGI con prenotazioni attive in questa collezione
$sellEgis = $collection->egis()
->whereHas('reservations', function($query) {
$query->where('is_current', true)->where('status', 'active');
})
->count();
} else {
// Fallback: statistiche globali se non specificata collezione
$distributionStats = PaymentDistribution::getDashboardStats();
$totalVolume = $distributionStats['overview']['total_amount_distributed'];
$eppTotal = collect($distributionStats['by_user_type'])
->firstWhere('user_type', 'epp')['total_amount'] ?? 0;
$totalEgis = Egi::count();
$sellEgis = Egi::whereHas('reservations', function($query) {
$query->where('is_current', true)->where('status', 'active');
})->count();
}

// ID univoco per evitare conflitti
$instanceId = uniqid();
@endphp

{{-- Statistiche Hero Banner - Per la collezione corrente --}}
<div class="p-4 bg-black border rounded-lg backdrop-blur-sm border-white/10 opacity-70"
    id="heroBannerStatsContainer_{{ $instanceId }}">
    <div class="flex divide-x divide-white/20">
        {{-- VOLUME - Totale valore prenotazioni per questa collezione --}}
        <div class="pr-6">
            <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.volume') }}</div>
            <div class="font-bold text-white" style="font-size: 14px; color: #ffffff;"
                id="statVolume_{{ $instanceId }}">
                @if($totalVolume > 0)
                {{-- Responsive formatting: desktop standard, mobile abbreviated --}}
                <span class="hidden md:inline">€{{ number_format($totalVolume, 2) }}</span>
                <span class="md:hidden">{{ formatPriceAbbreviated($totalVolume) }}</span>
                @else
                €0.00
                @endif
            </div>
        </div>

        {{-- EPP - Distribuzioni EPP per questa collezione - VERDE --}}
        <div class="px-6">
            <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.epp') }}</div>
            <div class="font-bold text-green-400" style="font-size: 14px; color: #4ade80;"
                id="statEpp_{{ $instanceId }}">
                @if($eppTotal > 0)
                {{-- Responsive formatting: desktop standard, mobile abbreviated --}}
                <span class="hidden md:inline">€{{ number_format($eppTotal, 2) }}</span>
                <span class="md:hidden">{{ formatPriceAbbreviated($eppTotal) }}</span>
                @else
                €0.00
                @endif
            </div>
        </div>

        {{-- EGIS - Numero EGI in questa collezione --}}
        <div class="px-6">
            <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.egis') }}</div>
            <div class="font-bold text-white" style="font-size: 14px; color: #ffffff;"
                id="statTotalEgis_{{ $instanceId }}">
                {{-- Responsive formatting: desktop standard, mobile abbreviated for large numbers --}}
                <span class="hidden md:inline">{{ number_format($totalEgis) }}</span>
                <span class="md:hidden">{{ $totalEgis >= 1000 ? formatNumberAbbreviated($totalEgis) : number_format($totalEgis) }}</span>
            </div>
        </div>

        {{-- SELL EGIS - EGI con prenotazioni attive in questa collezione --}}
        <div class="pl-6">
            <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.sell_egis') }}
            </div>
            <div class="font-bold text-white" style="font-size: 14px; color: #ffffff;"
                id="statSellEgis_{{ $instanceId }}">
                {{-- Responsive formatting: desktop standard, mobile abbreviated for large numbers --}}
                <span class="hidden md:inline">{{ number_format($sellEgis) }}</span>
                <span class="md:hidden">{{ $sellEgis >= 1000 ? formatNumberAbbreviated($sellEgis) : number_format($sellEgis) }}</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const instanceId = "{{ $instanceId }}";
    const heroBannerStatsContainer = document.getElementById('heroBannerStatsContainer_' + instanceId);

    if (!heroBannerStatsContainer) return;

    // Aggiorna le statistiche del hero banner
    function updateHeroBannerStats() {
        fetch('/api/stats/global')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Elementi delle statistiche con formatting responsivo
                    const volumeElement = document.getElementById('statVolume_' + instanceId);
                    const eppElement = document.getElementById('statEpp_' + instanceId);
                    const totalEgisElement = document.getElementById('statTotalEgis_' + instanceId);
                    const sellEgisElement = document.getElementById('statSellEgis_' + instanceId);

                    // Funzione per aggiornare con effetto brillamento e formattazione responsive
                    function updateStatWithEffect(element, rawValue, formattedValue) {
                        if (!element) return;

                        let currentValue = element.textContent.trim();
                        let newValue = formattedValue;

                        // Se l'elemento ha formattazione responsive
                        const desktopSpan = element.querySelector('.hidden.md\\:inline');
                        const mobileSpan = element.querySelector('.md\\:hidden');

                        if (desktopSpan && mobileSpan) {
                            // Formattazione responsive: desktop standard, mobile abbreviated
                            let desktopValue = formattedValue;
                            let mobileValue = formattedValue;

                            // Per i numeri, usa la formattazione abbreviata su mobile
                            if (typeof rawValue === 'number' && rawValue >= 1000) {
                                mobileValue = formatNumberAbbreviated(rawValue);
                            }

                            // Controlla se ci sono cambiamenti
                            if (desktopSpan.textContent.trim() !== desktopValue ||
                                mobileSpan.textContent.trim() !== mobileValue) {

                                element.style.transition = 'all 0.3s ease';
                                element.style.transform = 'scale(1.05)';
                                element.style.textShadow = '0 0 8px rgba(255, 255, 255, 0.6)';

                                desktopSpan.textContent = desktopValue;
                                mobileSpan.textContent = mobileValue;

                                // Reset dell'effetto dopo 300ms
                                setTimeout(() => {
                                    element.style.transform = 'scale(1)';
                                    element.style.textShadow = 'none';
                                }, 300);
                            }
                        } else {
                            // Formattazione semplice
                            if (currentValue !== newValue) {
                                element.style.transition = 'all 0.3s ease';
                                element.style.transform = 'scale(1.05)';
                                element.style.textShadow = '0 0 8px rgba(255, 255, 255, 0.6)';
                                element.textContent = newValue;

                                setTimeout(() => {
                                    element.style.transform = 'scale(1)';
                                    element.style.textShadow = 'none';
                                }, 300);
                            }
                        }
                    }

                    // Funzione helper per formattazione abbreviata (replica della PHP)
                    function formatNumberAbbreviated(number, decimals = 0) {
                        if (number === null || number === undefined) return '0';

                        const num = Math.abs(number);
                        const suffixes = [
                            { threshold: 1000000000000, suffix: 'T' },
                            { threshold: 1000000000, suffix: 'B' },
                            { threshold: 1000000, suffix: 'M' },
                            { threshold: 1000, suffix: 'K' }
                        ];

                        for (const { threshold, suffix } of suffixes) {
                            if (num >= threshold) {
                                const value = num / threshold;
                                if (value >= 100) {
                                    return Math.round(value) + suffix;
                                } else {
                                    return value.toFixed(decimals) + suffix;
                                }
                            }
                        }

                        return number.toLocaleString('it-IT');
                    }

                    // Aggiorna i valori
                    if (volumeElement) {
                        updateStatWithEffect(volumeElement, data.data.volume, data.formatted.volume);
                    }

                    if (eppElement) {
                        updateStatWithEffect(eppElement, data.data.epp, data.formatted.epp);
                    }

                    if (totalEgisElement) {
                        updateStatWithEffect(totalEgisElement, data.data.total_egis, data.formatted.total_egis);
                    }

                    if (sellEgisElement) {
                        updateStatWithEffect(sellEgisElement, data.data.sell_egis, data.formatted.sell_egis);
                    }
                }
            })
            .catch(error => {
                console.warn('Errore nell\'aggiornamento statistiche hero banner:', error);
            });
    }

    // Prima chiamata dopo 2 secondi per evitare conflitti con il caricamento
    setTimeout(updateHeroBannerStats, 2000);

    // Aggiorna ogni 30 secondi
    const updateInterval = setInterval(updateHeroBannerStats, 30000);

    // Cleanup quando l'elemento viene rimosso dal DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.removedNodes.forEach(function(node) {
                if (node === heroBannerStatsContainer || (node.contains && node.contains(heroBannerStatsContainer))) {
                    clearInterval(updateInterval);
                    observer.disconnect();
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
</script>
