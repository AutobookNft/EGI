{{-- resources/views/components/egi-card-carousel.blade.php --}}
{{-- 🎠 EGI Carousel Component --}}
{{-- Uses existing egi-card-list layout in horizontal scrollable carousel format --}}
{{-- Mobile-optimized carousel version --}}

@props([
'egis' => collect(),
'context' => 'collector',
'portfolioOwner' => null,
'showPurchasePrice' => true,
'showOwnershipBadge' => true,
'showBadge' => null
])

{{-- EGI Carousel Container --}}
<div class="egi-carousel-container">
    @if($egis->count() > 0)
        {{-- Carousel Track --}}
        <div class="flex space-x-4 overflow-x-auto pb-4 scrollbar-hide" id="egi-carousel-track">
            @foreach($egis as $egi)
                {{-- EGI Card Item usando il layout esistente --}}
                <div class="egi-carousel-item flex-shrink-0" style="min-width: 320px;">
                    <x-egi-card-list 
                        :egi="$egi" 
                        :context="$context"
                        :portfolioOwner="$portfolioOwner"
                        :showPurchasePrice="$showPurchasePrice"
                        :showOwnershipBadge="$showOwnershipBadge"
                        :showBadge="$showBadge" />
                </div>
            @endforeach
        </div>

        {{-- Gradient Fade Effect --}}
        <div class="absolute top-0 right-0 w-8 h-full bg-gradient-to-l from-gray-900 to-transparent pointer-events-none"></div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-8">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-gray-400">{{ __('egi.carousel.empty_state.no_egis') }}</p>
        </div>
    @endif
</div>

{{-- Carousel Styles --}}
<style>
.egi-carousel-container {
    position: relative;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.egi-carousel-item {
    min-width: 320px;
}
</style>

@props([
'egis' => collect(),
'context' => 'collector',
'portfolioOwner' => null,
'showPurchasePrice' => true,
'showOwnershipBadge' => true,
'showBadge' => null
])

@php
// Context-specific configurations
$contextConfig = [
    'collector' => [
        'badge_color' => 'bg-green-500',
        'badge_icon' => 'check',
        'badge_title' => __('collector.portfolio.owned'),
        'show_purchase' => true,
        'show_creator' => true
    ],
    'creator' => [
        'badge_color' => 'bg-blue-500',
        'badge_icon' => 'palette',
        'badge_title' => __('creator.portfolio.created'),
        'show_purchase' => false,
        'show_creator' => false
    ],
    'patron' => [
        'badge_color' => 'bg-purple-500',
        'badge_icon' => 'heart',
        'badge_title' => __('patron.portfolio.supported'),
        'show_purchase' => true,
        'show_creator' => true
    ],
    'collection' => [
        'badge_color' => 'bg-indigo-500',
        'badge_icon' => 'collections',
        'badge_title' => __('collection.show.from_collection'),
        'show_purchase' => false,
        'show_creator' => true
    ]
];

$config = $contextConfig[$context] ?? $contextConfig['collector'];
@endphp

{{-- EGI Carousel Container --}}
<div class="egi-carousel-container">
    @if($egis->count() > 0)
        {{-- Carousel Track --}}
        <div class="flex pb-4 space-x-4 overflow-x-auto scrollbar-hide" id="egi-carousel-track">
            @foreach($egis as $egi)
                @php
                    // 🔥 HYPER MODE: Leggiamo direttamente dal database il campo hyper dell'EGI
                    $isHyper = $egi->hyper ?? false;

                    // Controllo se l'utente loggato è il creator dell'EGI
                    $isCreator = auth()->check() && auth()->id() === $egi->user_id;

                    // Badge logic - può essere sovrascritto dal parametro showBadge
                    $showBadgeItem = $showBadge ?? $showOwnershipBadge;
                @endphp

                {{-- Include CSS hyper se necessario --}}
                @if($isHyper)
                    @once
                        <link rel="stylesheet" href="{{ asset('css/egi-hyper.css') }}">
                        <style>
                            .egi-hyper-badge-small {
                                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
                                color: white;
                                font-size: 0.625rem;
                                font-weight: bold;
                                padding: 2px 6px;
                                border-radius: 6px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                                animation: hyperPulse 2s infinite;
                                white-space: nowrap;
                            }

                            @keyframes hyperPulse {
                                0%, 100% { transform: scale(1); }
                                50% { transform: scale(1.05); }
                            }
                        </style>
                    @endonce
                @endif

                {{-- EGI Card Item --}}
                <article class="egi-carousel-item {{ $isHyper ? 'egi-card--hiper' : '' }} flex-shrink-0 w-72 group relative bg-gray-800/50 rounded-xl p-4 border border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70 transition-all duration-300"
                         data-egi-id="{{ $egi->id }}"
                         data-hyper="{{ $isHyper ? '1' : '0' }}"
                         style="{{ $isHyper ? '--energy:0.95; --foilHue:265; --edge:#9b5cf6; --accent:#a78bfa;' : '' }}">

                    @if($isHyper)
                        {{-- Sparkles Effect per HYPER --}}
                        <div class="egi-sparkles" aria-hidden="true"></div>
                    @endif

                    {{-- Header con Badge e Prezzo --}}
                    <div class="flex items-start justify-between mb-3">
                        {{-- Badge Ownership/Context --}}
                        @if($showBadgeItem)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-white rounded-full {{ $config['badge_color'] }}">
                                @if($config['badge_icon'] === 'check')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @elseif($config['badge_icon'] === 'palette')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm3 2h6v2H7V4zm0 4h6v1H7V8zm0 3h6v1H7v-1z" clip-rule="evenodd" />
                                    </svg>
                                @elseif($config['badge_icon'] === 'heart')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                {{ $config['badge_title'] }}
                            </span>
                        @endif

                        {{-- HYPER Badge --}}
                        @if($isHyper)
                            <span class="ml-2 egi-hyper-badge-small">
                                🔥 HYPER
                            </span>
                        @endif
                    </div>

                    {{-- EGI Image --}}
                    <div class="relative mb-3">
                        <a href="{{ route('egis.show', $egi->id) }}"
                           class="block transition-transform duration-300 group-hover:scale-105">
                            @if($egi->image_url)
                                <img src="{{ $egi->image_url }}"
                                     alt="{{ $egi->title }}"
                                     class="object-cover w-full h-40 rounded-lg shadow-md"
                                     loading="lazy">
                            @else
                                <div class="flex items-center justify-center w-full h-40 rounded-lg bg-gradient-to-br from-gray-700 to-gray-900">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </a>

                        {{-- Prezzo sovrapposto --}}
                        @if($config['show_purchase'] && $showPurchasePrice && $egi->current_price_eur)
                            <div class="absolute px-2 py-1 text-sm font-semibold text-white rounded-lg top-2 right-2 bg-black/80">
                                €{{ number_format($egi->current_price_eur, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>

                    {{-- EGI Info --}}
                    <div class="space-y-2">
                        {{-- Titolo --}}
                        <h3 class="text-sm font-semibold text-white transition-colors duration-200 group-hover:text-purple-300 line-clamp-2">
                            <a href="{{ route('egis.show', $egi->id) }}">
                                {{ $egi->title }}
                            </a>
                        </h3>

                        {{-- Creator Info --}}
                        @if($config['show_creator'] && $egi->user)
                            <div class="flex items-center space-x-2">
                                <div class="flex-shrink-0 w-6 h-6 overflow-hidden bg-gray-700 rounded-full">
                                    @if($egi->user->profile_image)
                                        <img src="{{ $egi->user->profile_image }}"
                                             alt="{{ $egi->user->name }}"
                                             class="object-cover w-full h-full">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-purple-500 to-blue-500">
                                            <span class="text-xs font-bold text-white">
                                                {{ strtoupper(substr($egi->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('creator.show', $egi->user->id) }}"
                                   class="text-sm text-gray-400 truncate transition-colors duration-200 hover:text-white">
                                    {{ $egi->user->name }}
                                </a>
                            </div>
                        @endif

                        {{-- Stats Footer --}}
                        <div class="flex items-center justify-between pt-2 text-xs text-gray-400 border-t border-gray-700/50">
                            <span>EGI #{{ $egi->id }}</span>
                            @if($egi->created_at)
                                <span>{{ $egi->created_at->format('M Y') }}</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Gradient Fade Effect --}}
        <div class="absolute top-0 right-0 w-8 h-full pointer-events-none bg-gradient-to-l from-gray-900 to-transparent"></div>
    @else
        {{-- Empty State --}}
        <div class="py-8 text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-700 rounded-full">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-gray-400">{{ __('egi.carousel.empty_state.no_egis') }}</p>
        </div>
    @endif
</div>

{{-- Carousel Styles --}}
<style>
.egi-carousel-container {
    position: relative;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.egi-carousel-item {
    min-width: 288px; /* w-72 = 18rem = 288px */
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* HYPER Effects mantengono compatibilità con egi-hyper.css */
.egi-card--hiper {
    position: relative;
    overflow: hidden;
}

.egi-sparkles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
}

.egi-carousel-item > * {
    position: relative;
    z-index: 2;
}
</style>
