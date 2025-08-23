@php
use App\Models\Collection;
use App\Models\PaymentDistribution;

// Prendi TUTTE le collection dal database con i loro dati EPP reali
$allCollections = Collection::orderBy('created_at', 'desc')->get()->map(function($collection) {
    // Calcola i dati reali per ogni collection
    $totalDistributed = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
        ->join('egis', 'reservations.egi_id', '=', 'egis.id')
        ->where('egis.collection_id', $collection->id)
        ->sum('payment_distributions.amount_eur') ?? 0;
    
    $totalToEpp = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
        ->join('egis', 'reservations.egi_id', '=', 'egis.id')
        ->where('egis.collection_id', $collection->id)
        ->where('payment_distributions.user_type', 'epp')
        ->sum('payment_distributions.amount_eur') ?? 0;
    
    return [
        'collection' => $collection,
        'stats' => [
            'total_distributed' => $totalDistributed,
            'total_to_epp' => $totalToEpp
        ]
    ];
});

// ID univoco per evitare conflitti
$instanceId = uniqid();
@endphp

{{-- Collection List Container --}}
<div class="w-full mt-4 mb-4" id="collectionList_{{ $instanceId }}">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 ml-2">
        <h3 class="text-lg font-semibold text-white">{{ __('statistics.all_collections') }}</h3>
        <div class="mr-2 text-sm text-gray-400">
            {{ count($allCollections) }} {{ strtolower(__('statistics.collections')) }}
        </div>
    </div>

    {{-- Lista delle Collection con scroll --}}
    @if(count($allCollections) > 0)
        <div class="pr-2 space-y-1 overflow-y-auto max-h-96">
            @foreach($allCollections as $item)
                <x-coll-card-list-small 
                    :collection="$item['collection']"
                    :stats="$item['stats']"
                />
            @endforeach
        </div>
    @else
        <div class="py-12 text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <p class="text-lg">{{ __('statistics.no_collections_data') }}</p>
            <p class="text-sm">{{ __('statistics.collections_coming_soon') }}</p>
        </div>
    @endif
</div>

{{-- Stili CSS per scroll personalizzato --}}
<style>
/* Custom scrollbar per la lista */
#collectionList_{{ $instanceId }} .max-h-96 {
    scrollbar-width: thin;
    scrollbar-color: #4B5563 transparent;
}

#collectionList_{{ $instanceId }} .max-h-96::-webkit-scrollbar {
    width: 6px;
}

#collectionList_{{ $instanceId }} .max-h-96::-webkit-scrollbar-track {
    background: transparent;
}

#collectionList_{{ $instanceId }} .max-h-96::-webkit-scrollbar-thumb {
    background-color: #4B5563;
    border-radius: 3px;
}

#collectionList_{{ $instanceId }} .max-h-96::-webkit-scrollbar-thumb:hover {
    background-color: #6B7280;
}

/* Smooth scroll */
#collectionList_{{ $instanceId }} .max-h-96 {
    scroll-behavior: smooth;
}

/* Hover effects per le card */
#collectionList_{{ $instanceId }} .space-y-1 > * {
    transition: transform 0.2s ease;
}

#collectionList_{{ $instanceId }} .space-y-1 > *:hover {
    transform: translateX(4px);
}
</style>
