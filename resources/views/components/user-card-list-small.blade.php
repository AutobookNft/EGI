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

// Determina l'immagine da usare
$imageUrl = null;
if ($user && $user->avatar_url) {
    $imageUrl = $user->avatar_url;
} elseif ($user && $user->profile_photo_url) {
    $imageUrl = $user->profile_photo_url;
}

// Se non ha immagine, usa le iniziali
$initials = '';
if ($user) {
    $name = $user->name ?? ($user->first_name . ' ' . $user->last_name);
    $initials = strtoupper(substr($name, 0, 2));
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
    <div class="p-2 transition-colors rounded-lg cursor-pointer hover:bg-gray-800/30">
        {{-- Layout come OpenSea: immagine piccola tonda a sinistra --}}
        <div class="flex items-center justify-between">
            {{-- Parte sinistra: immagine + nome --}}
            <div class="flex items-center space-x-3">
                {{-- Immagine User PICCOLA E TONDA (come OpenSea) --}}
                <div class="flex-shrink-0 w-10 h-10 overflow-hidden bg-gray-700 rounded-full">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $user->name ?? $user->first_name . ' ' . $user->last_name }}" class="object-cover w-full h-full">
                    @else
                        <div class="flex items-center justify-center w-full h-full text-sm font-bold text-white {{ $typeConfig['badge_color'] }}">
                            {{ $initials }}
                        </div>
                    @endif
                </div>

                {{-- Nome User e Badge --}}
                <div class="flex items-center">
                    <h3 class="mr-2 text-sm font-medium text-white">
                        {{ $user->name ?? ($user->first_name . ' ' . $user->last_name) ?? __('statistics.unknown_user') }}
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
                <div class="text-lg font-bold text-white">€{{ number_format($totalEarnings, 2) }} <span class="text-sm text-gray-400">{{ $typeConfig['earnings_label'] }}</span></div>
                <div class="text-sm font-medium text-green-400">
                    {{ $distributionsCount }} {{ $typeConfig['count_label'] }}
                </div>
            </div>
        </div>
    </div>
</a>
