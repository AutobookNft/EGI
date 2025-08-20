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
                €{{ number_format($totalVolume, 2) }}
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
                €{{ number_format($eppTotal, 2) }}
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
                {{ number_format($totalEgis) }}
            </div>
        </div>

        {{-- SELL EGIS - EGI con prenotazioni attive in questa collezione --}}
        <div class="pl-6">
            <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">{{ __('statistics.sell_egis') }}
            </div>
            <div class="font-bold text-white" style="font-size: 14px; color: #ffffff;"
                id="statSellEgis_{{ $instanceId }}">
                {{ number_format($sellEgis) }}
            </div>
        </div>
    </div>
</div>
