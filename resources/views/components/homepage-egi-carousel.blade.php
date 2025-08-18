{{-- resources/views/components/homepage-egi-carousel.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 2.0.0 (FlorenceEGI - Multi-Content Mobile Carousel)
* @date 2025-08-11
* @purpose Multi-content carousel for mobile with EGI, Creator, Collection, and Collector cards
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

// 📊 EGI Count: Conta tutti gli EGI nel database
$egisCount = \App\Models\Egi::count();

// 🎯 Attivatori: User che hanno attualmente almeno 1 EGI con miglior offerta attiva
// Query per trovare utenti con prenotazioni attive che sono la miglior offerta
$activatorsCount = \DB::table('users')
->join('reservations', 'users.id', '=', 'reservations.user_id')
->where('reservations.is_current', true)
->where('reservations.status', 'active')
->whereNull('reservations.superseded_by_id')
->distinct('users.id')
->count('users.id');
@endphp

<section class="py-8 bg-gradient-to-br from-gray-900 via-gray-800 to-black lg:py-12">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-8 text-center">
            <h2 class="mb-3 text-2xl font-bold text-white md:text-3xl">
                🎨 <span class="text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                    {{ __('egi.carousel.title') }}
                </span>
            </h2>
            <p class="max-w-2xl mx-auto text-gray-300">
                {{ __('egi.carousel.subtitle') }}
            </p>
        </div>

        {{-- Mobile Content Type Selector --}}
        <div class="flex flex-col gap-4 mb-6 lg:hidden">
            {{-- Content Type Selector --}}
            <div class="flex justify-center">
                <div class="flex flex-wrap gap-1 p-1 bg-gray-800 border border-gray-700 rounded-lg">
                    {{-- EGI List Button --}}
                    <button
                        class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn active"
                        data-content="egi-list" aria-label="{{ __('egi.carousel.content_types.egi_list') }}">
                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z" />
                        </svg>
                        <span class="hidden sm:inline">EGI</span>
                    </button>

                {{-- EGI Card Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="egi-card" aria-label="{{ __('egi.carousel.content_types.egi_card') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4h16v16H4V4zm2 2v12h12V6H6z" />
                    </svg>
                    <span class="hidden sm:inline">EGI+</span>
                </button>

                {{-- Creator Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="creator" aria-label="{{ __('egi.carousel.content_types.creators') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.carousel.creators') }}</span>
                </button>

                {{-- Collector Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="collector" aria-label="{{ __('egi.carousel.content_types.collectors') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.carousel.collectors') }}</span>
                </button>

                {{-- Collection Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="collection" aria-label="{{ __('egi.carousel.content_types.collections') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-4h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zm-3-3h2c.55 0 1-.45 1-1s-.45-1-1-1H8c-.55 0-1 .45-1 1s.45 1 1 1zm6 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zm-3-3h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.carousel.collections') }}</span>
                </button>

            </div>
            
            {{-- View Mode Toggle --}}
            <div class="flex justify-center">
                <div class="flex gap-1 p-1 bg-gray-800 border border-gray-700 rounded-lg">
                    {{-- Carousel Mode Button (Default) --}}
                    <button
                        class="px-4 py-2 text-xs font-medium transition-all duration-200 rounded view-mode-btn active"
                        data-view="carousel" aria-label="{{ __('egi.carousel.view_modes.carousel') }}">
                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        🎠 {{ __('egi.carousel.carousel_mode') }}
                    </button>

                    {{-- List Mode Button --}}
                    <button
                        class="px-4 py-2 text-xs font-medium transition-all duration-200 rounded view-mode-btn"
                        data-view="list" aria-label="{{ __('egi.carousel.view_modes.list') }}">
                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        📋 {{ __('egi.carousel.list_mode') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Dynamic Content Header for Mobile --}}
        <div class="mb-6 text-center lg:hidden">
            @php
            // 🔗 Mapping route per ogni tipo di contenuto
            $routeMapping = [
            'egi-list' => null, // EGI non ha una pagina index specifica
            'egi-card' => null,
            'creator' => route('creator.index'),
            'collection' => route('collections.index'),
            'collector' => route('collector.index')
            ];

            // 📊 Mapping contatori per ogni tipo
            $countMapping = [
            'egi-list' => $egisCount,
            'egi-card' => $egisCount,
            'creator' => $creatorsCount,
            'collection' => $collectionsCount,
            'collector' => $activatorsCount
            ];
            @endphp

            <div id="content-type-header-container" class="inline-flex items-center gap-2">
                <h3 id="content-type-header"
                    class="text-xl font-bold text-white transition-all duration-300 cursor-pointer hover:text-purple-300"
                    data-route="" onclick="navigateToContent(this)">
                    {{ __('egi.carousel.headers.egi_list') }}
                </h3>
                <span id="content-type-count"
                    class="inline-flex items-center px-2 py-1 text-sm font-medium text-purple-300 border rounded-full bg-purple-900/30 border-purple-500/20">
                    {{ $egisCount }}
                </span>
            </div>
        </div>

        {{-- Carousel Container --}}
        <div class="relative">
            {{-- Desktop Navigation Buttons --}}
            <button id="prev-btn"
                class="absolute left-0 z-10 items-center justify-center hidden w-10 h-10 text-white transition-all duration-300 -translate-x-4 -translate-y-1/2 bg-gray-800 border border-gray-600 rounded-full shadow-lg top-1/2 hover:bg-gray-700 lg:flex group hover:border-gray-400"
                aria-label="{{ __('egi.carousel.navigation.previous') }}">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button id="next-btn"
                class="absolute right-0 z-10 items-center justify-center hidden w-10 h-10 text-white transition-all duration-300 translate-x-4 -translate-y-1/2 bg-gray-800 border border-gray-600 rounded-full shadow-lg top-1/2 hover:bg-gray-700 lg:flex group hover:border-gray-400"
                aria-label="{{ __('egi.carousel.navigation.next') }}">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Carousel Track --}}
            <div id="homepage-multi-carousel" class="overflow-hidden">

                {{-- EGI List Cards (List Mode) --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-egi-list view-list"
                    data-content="egi-list" data-view="list">
                    @if($egis->count() > 0)
                    <div class="space-y-3">
                        @foreach($egis as $egi)
                        <x-egi-card-list :egi="$egi" context="creator" :showPurchasePrice="false"
                            :showOwnershipBadge="false" />
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_egis') }}
                    </div>
                    @endif
                </div>

                {{-- EGI Carousel (Carousel Mode - Default) --}}
                <div class="grid grid-cols-1 gap-4 transition-all duration-300 mobile-content content-egi-list view-carousel"
                    data-content="egi-list" data-view="carousel">
                    @if($egis->count() > 0)
                        <x-egi-card-carousel :egis="$egis" context="creator" :showPurchasePrice="false" :showOwnershipBadge="false" />
                    @else
                        <div class="py-8 text-center text-gray-400">
                            {{ __('egi.carousel.empty_state.no_egis') }}
                        </div>
                    @endif
                </div>

                {{-- EGI Cards (Carousel Mode) --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-egi-card lg:hidden"
                    data-content="egi-card">
                    @if($egis->count() > 0)
                        {{-- Carousel Track per EGI Cards --}}
                        <div class="flex space-x-4 overflow-x-auto pb-4 scrollbar-hide" id="egi-card-mobile-carousel">
                            @foreach($egis as $egi)
                            <div class="flex-shrink-0" style="width: 280px;">
                                <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                            </div>
                            @endforeach
                        </div>
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_egis') }}
                    </div>
                    @endif
                </div>

                {{-- Creator List Cards (List Mode) --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-creator view-list lg:hidden"
                    data-content="creator" data-view="list">
                    @if($creators->count() > 0)
                    @foreach($creators as $creator)
                    <div class="mobile-item">
                        <x-creator-card-list :creator="$creator" :context="'carousel'" :showBadge="true" />
                    </div>
                    @endforeach
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_creators') }}
                    </div>
                    @endif
                </div>

                {{-- Creator Carousel (Carousel Mode) --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-creator view-carousel lg:hidden"
                    data-content="creator" data-view="carousel">
                    @if($creators->count() > 0)
                        <x-creator-card-carousel :creators="$creators" context="default" :showBadge="true" />
                    @else
                        <div class="py-8 text-center text-gray-400">
                            {{ __('egi.carousel.empty_state.no_creators') }}
                        </div>
                    @endif
                </div>

                {{-- Collection List Cards --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-collection lg:hidden"
                    data-content="collection">
                    @if($collections->count() > 0)
                    @foreach($collections as $collection)
                    <div class="mobile-item">
                        <x-collection-card-list :collection="$collection" :context="'default'" :showBadge="true" />
                    </div>
                    @endforeach
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_collections') }}
                    </div>
                    @endif
                </div>

                {{-- Collector List Cards (List Mode) --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-collector view-list lg:hidden"
                    data-content="collector" data-view="list">
                    @if($collectors->count() > 0)
                    @foreach($collectors as $collector)
                    <div class="mobile-item">
                        <x-collector-card-list :collector="$collector" :context="'default'" :showBadge="true" />
                    </div>
                    @endforeach
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_collectors') }}
                    </div>
                    @endif
                </div>

                {{-- Collector Carousel (Carousel Mode) --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-collector view-carousel lg:hidden"
                    data-content="collector" data-view="carousel">
                    @if($collectors->count() > 0)
                        <x-collector-card-carousel :collectors="$collectors" context="default" :showBadge="true" />
                    @else
                        <div class="py-8 text-center text-gray-400">
                            {{ __('egi.carousel.empty_state.no_collectors') }}
                        </div>
                    @endif
                </div>

                {{-- Desktop Carousel (remains unchanged for EGI only) --}}
                <div class="hidden pb-4 space-x-4 overflow-x-auto desktop-carousel lg:flex scrollbar-hide"
                    id="desktop-carousel-track">
                    @if($egis->count() > 0)
                    @foreach($egis as $egi)
                    <div class="flex-shrink-0" style="width: 280px;">
                        <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Indicators for Desktop --}}
        @if($egis->count() > 0)
        <div class="justify-center hidden mt-6 space-x-2 lg:flex">
            @for($i = 0; $i < min(5, ceil($egis->count() / 4)); $i++)
                <button
                    class="w-2 h-2 transition-colors duration-300 bg-gray-600 rounded-full hover:bg-purple-500 carousel-indicator"
                    data-slide="{{ $i }}" aria-label="{{ __('egi.carousel.navigation.slide', ['number' => $i + 1]) }}">
                </button>
                @endfor
        </div>
        @endif
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

{{-- Multi-Content Carousel JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Mobile Content Type Switcher
    const contentTypeBtns = document.querySelectorAll('.content-type-btn');
    const viewModeBtns = document.querySelectorAll('.view-mode-btn');
    const mobileContents = document.querySelectorAll('.mobile-content');
    const contentHeader = document.getElementById('content-type-header');
    const contentCount = document.getElementById('content-type-count');

    // Current state
    let currentContentType = 'egi-list';
    let currentViewMode = 'carousel'; // Default to carousel

    // Header text mapping
    const headerTexts = {
        'egi-list': '{{ __('egi.carousel.headers.egi_list') }}',
        'egi-card': '{{ __('egi.carousel.headers.egi_card') }}',
        'creator': '{{ __('egi.carousel.headers.creators') }}',
        'collection': '{{ __('egi.carousel.headers.collections') }}',
        'collector': '{{ __('egi.carousel.headers.collectors') }}'
    };

    // 🔗 Route mapping per navigazione
    const routeMapping = {
        'egi-list': null,
        'egi-card': null,
        'creator': '{{ route('creator.index') }}',
        'collection': '{{ route('collections.index') }}',
        'collector': '{{ route('collector.index') }}'
    };

    // 📊 Count mapping per contatori
    const countMapping = {
        'egi-list': {{ $egisCount }},
        'egi-card': {{ $egisCount }},
        'creator': {{ $creatorsCount }},
        'collection': {{ $collectionsCount }},
        'collector': {{ $activatorsCount }}
    };

    // Helper function to show content based on type and view mode
    function showContent(contentType, viewMode) {
        // Hide all content containers
        mobileContents.forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('grid');
        });

        // For content types that have view modes (egi-list, creator, collector)
        if (['egi-list', 'creator', 'collector'].includes(contentType)) {
            const targetContent = document.querySelector(`.content-${contentType}.view-${viewMode}`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('grid');
            }
        } else {
            // For content types without view modes (egi-card, collection)
            const targetContent = document.querySelector(`.content-${contentType}`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('grid');
            }
        }
    }

    // Content Type Button Logic
    contentTypeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentContentType = this.dataset.content;

            // Update active button
            contentTypeBtns.forEach(b => {
                b.classList.remove('active', 'bg-purple-600', 'text-white');
                b.classList.add('text-gray-400');
            });
            this.classList.add('active', 'bg-purple-600', 'text-white');
            this.classList.remove('text-gray-400');

            // Show/hide view mode toggle based on content type
            const viewModeContainer = document.querySelector('.view-mode-btn').parentElement.parentElement;
            if (['egi-list', 'creator', 'collector'].includes(currentContentType)) {
                viewModeContainer.style.display = 'flex';
            } else {
                viewModeContainer.style.display = 'none';
            }

            // Update header text and route with smooth transition
            updateHeader(currentContentType);
            
            // Show content with current view mode (or default for types without view modes)
            showContent(currentContentType, currentViewMode);
        });
    });

    // View Mode Button Logic
    viewModeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentViewMode = this.dataset.view;

            // Update active button
            viewModeBtns.forEach(b => {
                b.classList.remove('active', 'bg-purple-600', 'text-white');
                b.classList.add('text-gray-400');
            });
            this.classList.add('active', 'bg-purple-600', 'text-white');
            this.classList.remove('text-gray-400');

            // Show content with current content type
            showContent(currentContentType, currentViewMode);
        });
    });

    // Update header function
    function updateHeader(contentType) {
        if (contentHeader && contentCount) {
            contentHeader.style.opacity = '0.5';
            contentCount.style.opacity = '0.5';

            setTimeout(() => {
                // Update header text
                contentHeader.textContent = headerTexts[contentType] || headerTexts['egi-list'];

                // Update count
                contentCount.textContent = countMapping[contentType] || 0;

                // Update route data attribute
                const route = routeMapping[contentType];
                contentHeader.setAttribute('data-route', route || '');

                // Update cursor style based on route availability
                if (route) {
                    contentHeader.classList.add('cursor-pointer', 'hover:text-purple-300');
                    contentHeader.classList.remove('cursor-default');
                } else {
                    contentHeader.classList.add('cursor-default');
                    contentHeader.classList.remove('cursor-pointer', 'hover:text-purple-300');
                }

                contentHeader.style.opacity = '1';
                contentCount.style.opacity = '1';
            }, 150);
        }
    }

    // Initialize active buttons and default state
    const activeContentBtn = document.querySelector('.content-type-btn.active');
    if (activeContentBtn) {
        activeContentBtn.classList.add('bg-purple-600', 'text-white');
        activeContentBtn.classList.remove('text-gray-400');
    }

    const activeViewBtn = document.querySelector('.view-mode-btn.active');
    if (activeViewBtn) {
        activeViewBtn.classList.add('bg-purple-600', 'text-white');
        activeViewBtn.classList.remove('text-gray-400');
    }

    // Show/hide view mode toggle based on initial content type
    const viewModeContainer = document.querySelector('.view-mode-btn').parentElement.parentElement;
    if (['egi-list', 'creator', 'collector'].includes(currentContentType)) {
        viewModeContainer.style.display = 'flex';
    } else {
        viewModeContainer.style.display = 'none';
    }

    // Show initial content (EGI carousel by default)
    showContent(currentContentType, currentViewMode);

    // Desktop Carousel Logic (unchanged, for EGI only)
    const carousel = document.getElementById('desktop-carousel-track');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const indicators = document.querySelectorAll('.carousel-indicator');

    @if($egis->count() > 0)
    if (carousel && prevBtn && nextBtn) {
        const cardWidth = 296; // 280px + 16px gap
        let currentPosition = 0;
        let isScrolling = false;

        function updateCarousel() {
            carousel.scrollTo({
                left: currentPosition,
                behavior: 'smooth'
            });

            // Update indicators
            const totalCards = {{ $egis->count() }};
            const visibleCards = Math.floor(carousel.offsetWidth / cardWidth);
            const currentSlide = Math.floor(currentPosition / cardWidth);
            const maxSlides = Math.max(1, totalCards - visibleCards + 1);

            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('bg-purple-500', index === currentSlide);
                indicator.classList.toggle('bg-gray-600', index !== currentSlide);
            });

            // Update button states
            const maxScroll = Math.max(0, (totalCards - visibleCards) * cardWidth);
            prevBtn.style.opacity = currentPosition > 0 ? '1' : '0.5';
            nextBtn.style.opacity = currentPosition < maxScroll ? '1' : '0.5';
        }

        prevBtn.addEventListener('click', function() {
            if (isScrolling) return;
            isScrolling = true;
            currentPosition = Math.max(0, currentPosition - cardWidth);
            updateCarousel();
            setTimeout(() => isScrolling = false, 300);
        });

        nextBtn.addEventListener('click', function() {
            if (isScrolling) return;
            isScrolling = true;
            const totalCards = {{ $egis->count() }};
            const visibleCards = Math.floor(carousel.offsetWidth / cardWidth);
            const maxScroll = Math.max(0, (totalCards - visibleCards) * cardWidth);
            currentPosition = Math.min(maxScroll, currentPosition + cardWidth);
            updateCarousel();
            setTimeout(() => isScrolling = false, 300);
        });

        // Indicator clicks
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', function() {
                if (isScrolling) return;
                isScrolling = true;
                currentPosition = index * cardWidth;
                updateCarousel();
                setTimeout(() => isScrolling = false, 300);
            });
        });

        // Initialize
        updateCarousel();

        // Handle window resize
        window.addEventListener('resize', function() {
            updateCarousel();
        });
    }
    @endif
});

// 🔗 Funzione globale per navigazione header
function navigateToContent(element) {
    const route = element.getAttribute('data-route');
    if (route && route !== '') {
        window.location.href = route;
    }
}
</script>

{{-- Custom Styles --}}
<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .carousel-indicator {
        transition: all 0.3s ease;
    }

    .carousel-indicator:hover {
        transform: scale(1.2);
    }

    .content-type-btn.active,
    .view-mode-btn.active {
        background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
    }

    .content-type-btn,
    .view-mode-btn {
        min-width: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-content {
        max-height: 70vh;
        overflow-y: auto;
    }

    @media (max-width: 1023px) {
        .mobile-content {
            scrollbar-width: thin;
        }
    }

    @media (min-width: 1024px) {
        .mobile-content {
            display: none !important;
        }
    }
</style>
