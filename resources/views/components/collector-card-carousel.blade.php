{{-- resources/views/components/collector-card-carousel.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Collector Card Carousel)
* @date 2025-08-18
* @purpose Carousel version of collector card for mobile-optimized horizontal scrolling
--}}

@props([
'collectors' => collect(),
'context' => 'default',
'showOwnershipBadge' => false,
'showBadge' => null,
'showRanks' => false
])

@php
// Context configurations for different usage scenarios
$contextConfig = [
'default' => [
'show_stats' => true,
'show_activity' => true,
'badge_color' => 'bg-emerald-500',
'badge_icon' => 'heart',
'badge_title' => __('collector.collector_badge')
],
'leaderboard' => [
'show_stats' => true,
'show_activity' => false,
'badge_color' => 'bg-yellow-500',
'badge_icon' => 'star',
'badge_title' => __('collector.top_collector')
],
'patron' => [
'show_stats' => false,
'show_activity' => true,
'badge_color' => 'bg-gold-500',
'badge_icon' => 'crown',
'badge_title' => __('collector.patron')
]
];

$config = $contextConfig[$context] ?? $contextConfig['default'];

// Badge logic - può essere sovrascritto dal parametro showBadge
$showBadgeItem = $showBadge ?? $showOwnershipBadge;

// Determina l'immagine da utilizzare
$logo = config('app.logo');
@endphp

{{-- Collector Carousel Container --}}
<div class="collector-carousel-container">
    @if($collectors->count() > 0)
    {{-- Carousel Track --}}
    <div class="flex space-x-4 overflow-x-auto pb-4 scrollbar-hide" id="collector-carousel-track">
        @foreach($collectors as $index => $collector)
        @php
        $imageUrl = '';
        if ($collector) {
        if ($collector->profile_photo_url) {
        $imageUrl = $collector->profile_photo_url;
        } else {
        $imageUrl = asset("images/logo/$logo");
        }
        } else {
        $imageUrl = asset("images/logo/$logo");
        }

        $rank = $showRanks ? ($index + 1) : null;

        // Get collector stats if available
        $stats = [];
        if ($collector && method_exists($collector, 'getCollectorStats')) {
        $stats = $collector->getCollectorStats();
        }
        @endphp

        {{-- Collector Card Item --}}
        <article
            class="collector-carousel-item flex-shrink-0 w-72 group relative bg-gray-800/50 rounded-xl p-4 border border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70 transition-all duration-300"
            data-collector-id="{{ $collector->id }}">

            {{-- Header con Badge --}}
            @if($showBadgeItem)
            <div class="flex justify-between items-start mb-3">
                <span
                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-white rounded-full {{ $config['badge_color'] }}">
                    @if($config['badge_icon'] === 'heart')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif($config['badge_icon'] === 'star')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @elseif($config['badge_icon'] === 'crown')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    @endif
                    {{ $config['badge_title'] }}
                </span>

                {{-- Rank Badge --}}
                @if($rank)
                <div
                    class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 ring-2 ring-gray-800">
                    #{{ $rank }}
                </div>
                @endif
            </div>
            @endif

            {{-- Collector Avatar and Info --}}
            <div class="text-center mb-4">
                <a href="{{ route('collector.home', ['id' => $collector->id]) }}"
                    class="block relative mx-auto w-20 h-20 rounded-full overflow-hidden bg-gradient-to-br from-gray-700 to-gray-800 group-hover:ring-2 group-hover:ring-emerald-400 transition-all duration-300">

                    <img src="{{ $imageUrl }}" alt="{{ $collector->first_name }} {{ $collector->last_name }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                        loading="lazy">

                    {{-- Hover overlay --}}
                    <div
                        class="absolute inset-0 bg-emerald-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                        </svg>
                    </div>
                </a>

                {{-- Nome Collector --}}
                <h3
                    class="mt-3 font-semibold text-white text-sm group-hover:text-emerald-300 transition-colors duration-200 line-clamp-2">
                    <a href="{{ route('collector.home', ['id' => $collector->id]) }}">
                        {{ $collector->first_name }} {{ $collector->last_name }}
                    </a>
                </h3>

                {{-- Username --}}
                @if($collector->username)
                <p class="text-gray-400 text-xs mt-1">@{{ $collector->username }}</p>
                @endif
            </div>

            {{-- Stats Section --}}
            @if($config['show_stats'])
            <div class="space-y-3">
                {{-- Collection Stats --}}
                @php
                $ownedEgisCount = $collector->ownedEgis()->count();
                $reservationsCount = $collector->reservations()->where('status', 'active')->count();
                $totalSpent = $collector->reservations()->where('status', 'completed')->sum('amount_eur');
                @endphp

                <div class="grid grid-cols-2 gap-2 text-center">
                    <div class="bg-gray-700/50 rounded-lg p-2">
                        <div class="text-lg font-bold text-white">{{ $ownedEgisCount }}</div>
                        <div class="text-xs text-gray-400">{{ __('collector.owned_egis') }}</div>
                    </div>
                    <div class="bg-gray-700/50 rounded-lg p-2">
                        <div class="text-lg font-bold text-white">{{ $reservationsCount }}</div>
                        <div class="text-xs text-gray-400">{{ __('collector.active_bids') }}</div>
                    </div>
                </div>

                {{-- Total Spent --}}
                @if($totalSpent > 0)
                <div class="bg-emerald-900/30 border border-emerald-500/20 rounded-lg p-2 text-center">
                    <div class="text-sm font-bold text-emerald-300">€{{ number_format($totalSpent, 0, ',', '.') }}</div>
                    <div class="text-xs text-emerald-400">{{ __('collector.total_invested') }}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Activity Section --}}
            @if($config['show_activity'])
            <div class="mt-3 pt-3 border-t border-gray-700/50">
                {{-- Last Activity --}}
                @if($collector->last_activity_at)
                <div class="flex items-center text-xs text-gray-400">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('collector.last_active') }} {{ $collector->last_activity_at->diffForHumans() }}
                </div>
                @endif

                {{-- Favorite Genres --}}
                @if(isset($stats['favorite_genres']) && count($stats['favorite_genres']) > 0)
                <div class="mt-2">
                    <div class="flex flex-wrap gap-1">
                        @foreach(array_slice($stats['favorite_genres'], 0, 2) as $genre)
                        <span class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full">
                            {{ $genre }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Footer --}}
            <div class="flex items-center justify-between text-xs text-gray-400 pt-3 mt-3 border-t border-gray-700/50">
                <span>{{ __('collector.since') }} {{ $collector->created_at->format('M Y') }}</span>
                @if($collector->is_verified)
                <div class="flex items-center text-emerald-400">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('collector.verified') }}</span>
                </div>
                @endif
            </div>
        </article>
        @endforeach
    </div>

    {{-- Gradient Fade Effect --}}
    <div class="absolute top-0 right-0 w-8 h-full bg-gradient-to-l from-gray-900 to-transparent pointer-events-none">
    </div>
    @else
    {{-- Empty State --}}
    <div class="text-center py-8">
        <div class="w-16 h-16 mx-auto mb-4 bg-gray-700 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <p class="text-gray-400">{{ __('egi.carousel.empty_state.no_collectors') }}</p>
    </div>
    @endif
</div>

{{-- Carousel Styles --}}
<style>
    .collector-carousel-container {
        position: relative;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .collector-carousel-item {
        min-width: 288px;
        /* w-72 = 18rem = 288px */
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
