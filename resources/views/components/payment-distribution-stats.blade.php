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

$collectionsWithDistributions = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
    ->join('egis', 'reservations.egi_id', '=', 'egis.id')
    ->distinct('egis.collection_id')
    ->count('egis.collection_id');

$eppTotal = collect($distributionStats['by_user_type'])
    ->firstWhere('user_type', 'epp')['total_amount'] ?? 0;

// ID univoco per evitare conflitti
$instanceId = uniqid();
@endphp

{{-- Statistiche Payment Distribution GLOBALI --}}
<div class="flex flex-col items-center justify-center w-full gap-4 sm:gap-6">
    <div class="p-4 bg-black border rounded-lg backdrop-blur-sm border-white/10 opacity-70">
        <div class="flex divide-x divide-white/20">
            {{-- EGIS - Numero totale degli EGI presenti --}}
            <div class="pr-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">EGIS</div>
                <div class="text-white" style="font-size: 8px;" id="statTotalEgis_{{ $instanceId }}">
                    {{ number_format($totalEgis) }}
                </div>
            </div>

            {{-- SELL_EGIS - Numero totale degli EGI che hanno in corso una prenotazione valida --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">SELL EGIS</div>
                <div class="text-white" style="font-size: 8px;" id="statSellEgis_{{ $instanceId }}">
                    {{ number_format($sellEgis) }}
                </div>
            </div>

            {{-- VOLUME - Totale importo distribuito (€) --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">VOLUME</div>
                <div class="text-white" style="font-size: 8px;" id="statVolume_{{ $instanceId }}">
                    @if($totalVolume > 0)
                        €{{ number_format($totalVolume, 2) }}
                    @else
                        €0.00
                    @endif
                </div>
            </div>

            {{-- COLLECTIONS - Numero collections con distribuzioni --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">COLLECTIONS</div>
                <div class="text-white" style="font-size: 8px;" id="statCollections_{{ $instanceId }}">
                    {{ number_format($collectionsWithDistributions) }}
                </div>
            </div>

            {{-- EPP - Totale distribuito agli EPP (€) - "di cui" --}}
            <div class="pl-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">EPP</div>
                <div class="text-white" style="font-size: 8px;" id="statEpp_{{ $instanceId }}">
                    @if($eppTotal > 0)
                        €{{ number_format($eppTotal, 2) }}
                    @else
                        €0.00
                    @endif
                </div>
                {{-- Indicatore "di cui" --}}
                <div class="text-xs text-gray-400" style="font-size: 6px;">
                    di cui EPP
                </div>
            </div>
        </div>
    </div>
</div>
