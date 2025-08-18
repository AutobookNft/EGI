{{-- resources/views/components/creator-card-carousel.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Creator Card Carousel)
* @date 2025-08-18
* @purpose Carousel version of creator card for mobile-optimized horizontal scrolling
--}}

@props([
'creators' => collect(),
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
'show_collections' => true,
'badge_color' => 'bg-purple-500',
'badge_icon' => 'palette',
'badge_title' => __('creator.creator_badge')
],
'featured' => [
'show_stats' => true,
'show_collections' => true,
'badge_color' => 'bg-yellow-500',
'badge_icon' => 'star',
'badge_title' => __('creator.featured')
],
'following' => [
'show_stats' => false,
'show_collections' => true,
'badge_color' => 'bg-green-500',
'badge_icon' => 'check',
'badge_title' => __('creator.following')
]
];

$config = $contextConfig[$context] ?? $contextConfig['default'];

// Badge logic - può essere sovrascritto dal parametro showBadge
$showBadgeItem = $showBadge ?? $showOwnershipBadge;

// Determina l'immagine da utilizzare
$logo = config('app.logo');
@endphp

{{-- Creator Carousel Container --}}
<div class="creator-carousel-container">
    @if($creators->count() > 0)
    {{-- Carousel Track --}}
    <div class="flex space-x-4 overflow-x-auto pb-4 scrollbar-hide" id="creator-carousel-track">
        @foreach($creators as $index => $creator)
        @php
        $imageUrl = '';
        if ($creator) {
        if ($creator->profile_photo_url) {
        $imageUrl = $creator->profile_photo_url;
        } else {
        $imageUrl = asset("images/logo/$logo");
        }
        } else {
        $imageUrl = asset("images/logo/$logo");
        }

        $rank = $showRanks ? ($index + 1) : null;
        @endphp

        {{-- Creator Card Item --}}
        <article
            class="creator-carousel-item flex-shrink-0 w-72 group relative bg-gray-800/50 rounded-xl p-4 border border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70 transition-all duration-300"
            data-creator-id="{{ $creator->id }}">

            {{-- Header con Badge --}}
            @if($showBadgeItem)
            <div class="flex justify-between items-start mb-3">
                <span
                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-white rounded-full {{ $config['badge_color'] }}">
                    @if($config['badge_icon'] === 'palette')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 2a2 2 0 00-2 2v11a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm3 2h6v2H7V4zm0 4h6v1H7V8zm0 3h6v1H7v-1z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif($config['badge_icon'] === 'star')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @elseif($config['badge_icon'] === 'check')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
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

            {{-- Creator Avatar and Info --}}
            <div class="text-center mb-4">
                <a href="{{ route('creator.home', ['id' => $creator->id]) }}"
                    class="block relative mx-auto w-20 h-20 rounded-full overflow-hidden bg-gradient-to-br from-gray-700 to-gray-800 group-hover:ring-2 group-hover:ring-purple-400 transition-all duration-300">

                    <img src="{{ $imageUrl }}" alt="{{ $creator->first_name }} {{ $creator->last_name }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                        loading="lazy">

                    {{-- Hover overlay --}}
                    <div
                        class="absolute inset-0 bg-purple-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                        </svg>
                    </div>
                </a>

                {{-- Nome Creator --}}
                <h3
                    class="mt-3 font-semibold text-white text-sm group-hover:text-purple-300 transition-colors duration-200 line-clamp-2">
                    <a href="{{ route('creator.home', ['id' => $creator->id]) }}">
                        {{ $creator->first_name }} {{ $creator->last_name }}
                    </a>
                </h3>

                {{-- Username --}}
                @if($creator->username)
                <p class="text-gray-400 text-xs mt-1">@{{ $creator->username }}</p>
                @endif
            </div>

            {{-- Stats Section --}}
            @if($config['show_stats'])
            <div class="space-y-3">
                {{-- EGI Count --}}
                @php
                $egisCount = $creator->createdEgis()->count();
                $collectionsCount = $creator->ownedCollections()->count();
                // TODO: Implementare followers relationship
                $followersCount = 0; // Placeholder until followers relationship is implemented
                @endphp

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-gray-700/50 rounded-lg p-2">
                        <div class="text-lg font-bold text-white">{{ $egisCount }}</div>
                        <div class="text-xs text-gray-400">EGI</div>
                    </div>
                    @if($config['show_collections'])
                    <div class="bg-gray-700/50 rounded-lg p-2">
                        <div class="text-lg font-bold text-white">{{ $collectionsCount }}</div>
                        <div class="text-xs text-gray-400">{{ __('creator.collections') }}</div>
                    </div>
                    @endif
                    <div class="bg-gray-700/50 rounded-lg p-2">
                        <div class="text-lg font-bold text-white">{{ $followersCount }}</div>
                        <div class="text-xs text-gray-400">{{ __('creator.followers') }}</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Bio Section --}}
            @if($creator->bio)
            <div class="mt-3 pt-3 border-t border-gray-700/50">
                <p class="text-gray-400 text-xs line-clamp-2">
                    {{ $creator->bio }}
                </p>
            </div>
            @endif

            {{-- Footer --}}
            <div class="flex items-center justify-between text-xs text-gray-400 pt-3 mt-3 border-t border-gray-700/50">
                <span>{{ __('creator.since') }} {{ $creator->created_at->format('M Y') }}</span>
                @if($creator->is_verified)
                <div class="flex items-center text-blue-400">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('creator.verified') }}</span>
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
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
        </div>
        <p class="text-gray-400">{{ __('egi.carousel.empty_state.no_creators') }}</p>
    </div>
    @endif
</div>

{{-- Carousel Styles --}}
<style>
    .creator-carousel-container {
        position: relative;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .creator-carousel-item {
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
