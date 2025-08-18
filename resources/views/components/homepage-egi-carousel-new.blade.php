{{-- resources/views/components/homepage-egi-carousel.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 3.0.0 (FlorenceEGI - Multi-Carousel Stack)
* @date 2025-01-18
* @purpose Multiple carousels stacked vertically - no selector buttons
--}}

@props([
'egis' => collect(),
'creators' => collect(),
'collections' => collect(),
'collectors' => collect()
])

@php
// 🧮 Contatori dinamici per il database
$creatorsCount = \App\Models\User::where('usertype', 'creator')->count();
$collectionsCount = \App\Models\Collection::count();
$egisCount = \App\Models\Egi::count();

// 🎯 Attivatori: User che hanno attualmente almeno 1 EGI con miglior offerta attiva
$activatorsCount = \DB::table('users')
->join('reservations', 'users.id', '=', 'reservations.user_id')
->where('reservations.is_current', true)
->where('reservations.status', 'active')
->whereNull('reservations.superseded_by_id')
->distinct('users.id')
->count('users.id');
@endphp

<section class="py-8 bg-gradient-to-br from-gray-900 via-gray-800 to-black">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-8 text-center">
            <h2 class="mb-3 text-2xl font-bold text-white md:text-3xl">
                🎠 <span class="text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                    {{ __('egi.carousel.title') }}
                </span>
            </h2>
            <p class="max-w-2xl mx-auto text-gray-300">
                {{ __('egi.carousel.subtitle') }}
            </p>
        </div>

        {{-- Multi-Carousel Stack --}}
        <div class="space-y-12">

            {{-- EGI Carousel Section --}}
            @if($egis->count() > 0)
            <div class="carousel-section">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-white">
                            🎨 {{ __('egi.carousel.sections.egis') }}
                        </h3>
                        <span
                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-purple-300 border rounded-full bg-purple-900/30 border-purple-500/20">
                            {{ $egisCount }}
                        </span>
                    </div>
                    <a href="#" class="text-sm text-purple-300 hover:text-purple-200 transition-colors">
                        {{ __('egi.carousel.view_all') }} →
                    </a>
                </div>

                <div class="flex pb-4 space-x-4 overflow-x-auto scrollbar-hide">
                    @foreach($egis as $egi)
                    <div class="flex-shrink-0" style="min-width: 320px;">
                        <x-egi-card-list :egi="$egi" context="creator" :showPurchasePrice="false"
                            :showOwnershipBadge="false" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Creator Carousel Section --}}
            @if($creators->count() > 0)
            <div class="carousel-section">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-white">
                            👨‍🎨 {{ __('egi.carousel.sections.creators') }}
                        </h3>
                        <span
                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-purple-300 border rounded-full bg-purple-900/30 border-purple-500/20">
                            {{ $creatorsCount }}
                        </span>
                    </div>
                    <a href="{{ route('creator.index') }}"
                        class="text-sm text-purple-300 hover:text-purple-200 transition-colors">
                        {{ __('egi.carousel.view_all') }} →
                    </a>
                </div>

                <div class="flex pb-4 space-x-4 overflow-x-auto scrollbar-hide">
                    @foreach($creators as $creator)
                    <div class="flex-shrink-0" style="min-width: 320px;">
                        <x-creator-card-list :creator="$creator" :context="'carousel'" :showBadge="true" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Collection Carousel Section --}}
            @if($collections->count() > 0)
            <div class="carousel-section">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-white">
                            📚 {{ __('egi.carousel.sections.collections') }}
                        </h3>
                        <span
                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-purple-300 border rounded-full bg-purple-900/30 border-purple-500/20">
                            {{ $collectionsCount }}
                        </span>
                    </div>
                    <a href="{{ route('collections.index') }}"
                        class="text-sm text-purple-300 hover:text-purple-200 transition-colors">
                        {{ __('egi.carousel.view_all') }} →
                    </a>
                </div>

                <div class="flex pb-4 space-x-4 overflow-x-auto scrollbar-hide">
                    @foreach($collections as $collection)
                    <div class="flex-shrink-0" style="min-width: 320px;">
                        <x-collection-card-list :collection="$collection" :context="'carousel'" :showBadge="true" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Collector Carousel Section --}}
            @if($collectors->count() > 0)
            <div class="carousel-section">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-white">
                            ⚡ {{ __('egi.carousel.sections.collectors') }}
                        </h3>
                        <span
                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-purple-300 border rounded-full bg-purple-900/30 border-purple-500/20">
                            {{ $activatorsCount }}
                        </span>
                    </div>
                    <a href="{{ route('collector.index') }}"
                        class="text-sm text-purple-300 hover:text-purple-200 transition-colors">
                        {{ __('egi.carousel.view_all') }} →
                    </a>
                </div>

                <div class="flex pb-4 space-x-4 overflow-x-auto scrollbar-hide">
                    @foreach($collectors as $collector)
                    <div class="flex-shrink-0" style="min-width: 320px;">
                        <x-collector-card-list :collector="$collector" :context="'carousel'" :showBadge="true" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Empty State --}}
        @if($egis->count() === 0 && $creators->count() === 0 && $collections->count() === 0 && $collectors->count() ===
        0)
        <div class="py-12 text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-gray-700 rounded-full">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-white">{{ __('egi.carousel.empty_state.title') }}</h3>
            <p class="max-w-md mx-auto text-gray-400">
                {{ __('egi.carousel.empty_state.subtitle') }}
            </p>
        </div>
        @endif
    </div>
</section>

{{-- Custom Styles --}}
<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .carousel-section {
        transition: all 0.3s ease;
    }

    .carousel-section:hover {
        transform: translateY(-2px);
    }
</style>
