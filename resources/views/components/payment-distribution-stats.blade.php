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
<div class="flex flex-col items-center justify-center w-full gap-4 sm:gap-6">
    <div class="p-4 border rounded-lg backdrop-blur-sm border-white/10" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex divide-x divide-white/20">
            {{-- VOLUME - Totale importo distribuito (€) --}}
            <div class="pr-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">VOLUME</div>
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
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">EPP</div>
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
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">COLLECTIONS</div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statCollections_{{ $instanceId }}">
                    {{ number_format($totalCollections) }}
                </div>
            </div>

            {{-- SELL COLLECTIONS - Collections con distribuzioni attive --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">SELL COLLECTIONS</div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statSellCollections_{{ $instanceId }}">
                    {{ number_format($sellCollections) }}
                </div>
            </div>

            {{-- EGIS - Numero totale degli EGI presenti --}}
            <div class="px-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">EGIS</div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statTotalEgis_{{ $instanceId }}">
                    {{ number_format($totalEgis) }}
                </div>
            </div>

            {{-- SELL_EGIS - Numero totale degli EGI che hanno in corso una prenotazione valida --}}
            <div class="pl-6">
                <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">SELL EGIS</div>
                <div class="text-white" style="font-size: 12px; color: #ffffff; font-weight: 700;"
                    id="statSellEgis_{{ $instanceId }}">
                    {{ number_format($sellEgis) }}
                </div>
            </div>
        </div>
    </div>
</div>
