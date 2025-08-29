@props([
    'user',
    'userType' => 'creator',
    'stats' => []
])

@php
// Estrai i dati dalle stats (se esistono) basati sul user_type
$totalEarnings = $stats['total_amount'] ?? 0;
$distributionsCount = $stats['count'] ?? 0;
$avgAmount = $stats['avg_amount'] ?? 0;

// Determina l'immagine da usare - ora usa la logica unificata di privacy
$imageUrl = null;
if ($user) {
    // Usa profile_photo_url che ora gestisce automaticamente la privacy
    $imageUrl = $user->profile_photo_url;
}

// Determina il nome da mostrare in base alla privacy
$displayName = '';
if ($user) {
    // Nel model User c'è la gestione del name
    $displayName = $user->name;

}

// Iniziali per fallback avatar (basate sul nome appropriato)
$initials = '';
if ($displayName) {
    $initials = strtoupper(substr($displayName, 0, 2));
}

// Configura i colori e le etichette basati sul user_type
$typeConfig = match($userType) {
    'creator' => [
        'badge_color' => 'bg-purple-500',
        'badge_icon' => 'palette',
        'earnings_label' => __('statistics.creator_earnings'),
        'count_label' => __('statistics.creator_count_label'),
        'route' => 'creator.home'
    ],
    'collector' => [
        'badge_color' => 'bg-blue-500',
        'badge_icon' => 'shopping_bag',
        'earnings_label' => __('statistics.collector_earnings'),
        'count_label' => __('statistics.collector_count_label'),
        'route' => 'collector.home'
    ],
    'epp' => [
        'badge_color' => 'bg-green-500',
        'badge_icon' => 'eco',
        'earnings_label' => __('statistics.epp_earnings'),
        'count_label' => __('statistics.epp_count_label'),
        'route' => 'epp.home'
    ],
    'activator' => [
        'badge_color' => 'bg-blue-500',
        'badge_icon' => 'flash_on',
        'earnings_label' => __('statistics.activator_earnings'),
        'count_label' => __('statistics.activator_count_label'),
        'route' => 'collector.home'
    ],
    default => [
        'badge_color' => 'bg-gray-500',
        'badge_icon' => 'person',
        'earnings_label' => 'USER',
        'route' => 'creator.home'
    ]
};

// URL del profilo se esiste
$profileUrl = $user ? route($typeConfig['route'], ['id' => $user->id]) : '#';
@endphp

{{-- User Card IDENTICA a OpenSea style --}}
<a href="{{ $profileUrl }}">
    <div class="px-2 py-1 transition-colors rounded-lg cursor-pointer hover:bg-gray-800/30">
        {{-- Layout come OpenSea: immagine piccola tonda a sinistra --}}
        <div class="flex items-center justify-between">
            {{-- Parte sinistra: immagine + nome --}}
            <div class="flex items-center space-x-3">
                {{-- Immagine User PICCOLA E QUADRATA con angoli stondati --}}
                <div class="flex-shrink-0 w-10 h-10 overflow-hidden bg-gray-700 rounded-lg">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $displayName }}" class="object-cover w-full h-full">
                    @else
                        <div class="flex items-center justify-center w-full h-full text-sm font-bold text-white {{ $typeConfig['badge_color'] }}">
                            {{ $initials }}
                        </div>
                    @endif
                </div>

                {{-- Nome User e Badge - ora usa il nome rispettoso della privacy --}}
                <div class="flex items-center">
                    <h3 class="mr-2 text-sm font-medium text-white">
                        {{ $displayName ?: __('statistics.unknown_user') }}
                    </h3>
                    <div class="flex items-center">
                        @if($user && $user->usertype === 'verified')
                            <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Parte destra: earnings e conteggio distribuzioni --}}
            <div class="text-right">
                <div class="text-lg font-bold text-white">
                    {{-- Desktop: formato standard, Mobile: formato abbreviato --}}
                    <span class="hidden md:inline">€{{ number_format($totalEarnings, 2) }}</span>
                    <span class="md:hidden">{{ formatPriceAbbreviated($totalEarnings, 1) }}</span>
                    <span class="text-sm text-gray-400">{{ $typeConfig['earnings_label'] }}</span>
                </div>
                <div class="text-sm font-medium text-green-400">
                    {{ $distributionsCount }} {{ $typeConfig['count_label'] }}
                </div>
            </div>
        </div>
    </div>
</a>
