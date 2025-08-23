@props([
    'collection',
    'stats' => []
])

@php
// Estrai i dati dalle stats (se esistono)
$totalDistributed = $stats['total_distributed'] ?? 0;
$totalToEpp = $stats['total_to_epp'] ?? 0;

// Determina l'immagine da usare
$imageUrl = null;
if ($collection->image_card) {
    $imageUrl = $collection->image_card;
} elseif ($collection->image_avatar) {
    $imageUrl = $collection->image_avatar;
} elseif ($collection->image_cover) {
    $imageUrl = $collection->image_cover;
}
@endphp

{{-- Card Collection IDENTICA a OpenSea --}}
<div class="p-2 hover:bg-gray-800/30 transition-colors cursor-pointer rounded-lg">
    {{-- Layout come OpenSea: immagine piccola tonda a sinistra --}}
    <div class="flex items-center justify-between">
        {{-- Parte sinistra: immagine + nome --}}
        <div class="flex items-center space-x-3">
            {{-- Immagine Collection PICCOLA E TONDA (come OpenSea) --}}
            <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-700 flex-shrink-0">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $collection->collection_name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-purple-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($collection->collection_name, 0, 2)) }}
                    </div>
                @endif
            </div>
            
            {{-- Nome Collection e Badge --}}
            <div class="flex items-center">
                <h3 class="text-white font-medium text-sm mr-2">{{ $collection->collection_name }}</h3>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        {{-- Parte destra: volume e EPP volume --}}
        <div class="text-right">
            <div class="text-white text-lg font-bold">{{ number_format($totalDistributed, 2) }} <span class="text-sm text-gray-400">VOLUME</span></div>
            <div class="text-green-400 font-medium text-sm">
                €{{ number_format($totalToEpp, 2) }} EPP
            </div>
        </div>
    </div>
</div>
