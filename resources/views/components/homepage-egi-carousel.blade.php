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
        <div class="flex justify-center mb-6 lg:hidden">
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

                {{-- Collection Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="collection" aria-label="{{ __('egi.carousel.content_types.collections') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.carousel.collections') }}</span>
                </button>

                {{-- Collector Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="collector" aria-label="{{ __('egi.carousel.content_types.collectors') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.carousel.collectors') }}</span>
                </button>
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

                {{-- EGI List Cards (Default Active) --}}
                <div class="grid grid-cols-1 gap-4 transition-all duration-300 mobile-content content-egi-list lg:hidden"
                    data-content="egi-list">
                    @if($egis->count() > 0)
                    @foreach($egis as $egi)
                    <div class="mobile-item">
                        <x-egi-card-list :egi="$egi" :context="'carousel'" :showBadge="true" />
                    </div>
                    @endforeach
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_egis') }}
                    </div>
                    @endif
                </div>

                {{-- EGI Cards --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-egi-card lg:hidden"
                    data-content="egi-card">
                    @if($egis->count() > 0)
                    @foreach($egis as $egi)
                    <div class="mobile-item">
                        <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                    </div>
                    @endforeach
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_egis') }}
                    </div>
                    @endif
                </div>

                {{-- Creator Cards --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-creator lg:hidden"
                    data-content="creator">
                    @if($creators->count() > 0)
                    @foreach($creators as $creator)
                    <div class="mobile-item">
                        <x-creator-card :creator="$creator" />
                    </div>
                    @endforeach
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_creators') }}
                    </div>
                    @endif
                </div>

                {{-- Collection Cards --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-collection lg:hidden"
                    data-content="collection">
                    @if($collections->count() > 0)
                    @foreach($collections as $collection)
                    <div class="mobile-item">
                        <x-collection-card :id="$collection->id" />
                    </div>
                    @endforeach
                    @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.carousel.empty_state.no_collections') }}
                    </div>
                    @endif
                </div>

                {{-- Collector Cards --}}
                <div class="hidden grid-cols-1 gap-4 transition-all duration-300 mobile-content content-collector lg:hidden"
                    data-content="collector">
                    @if($collectors->count() > 0)
                    @foreach($collectors as $collector)
                    <div class="mobile-item">
                        <x-collector-card :collector="$collector" />
                    </div>
                    @endforeach
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
    const mobileContents = document.querySelectorAll('.mobile-content');

    contentTypeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const contentType = this.dataset.content;

            // Update active button
            contentTypeBtns.forEach(b => {
                b.classList.remove('active', 'bg-purple-600', 'text-white');
                b.classList.add('text-gray-400');
            });
            this.classList.add('active', 'bg-purple-600', 'text-white');
            this.classList.remove('text-gray-400');

            // Hide all content containers
            mobileContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('grid');
            });

            // Show selected content container
            const targetContent = document.querySelector(`.content-${contentType}`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('grid');
            }
        });
    });

    // Initialize active button (EGI List by default)
    const activeBtn = document.querySelector('.content-type-btn.active');
    if (activeBtn) {
        activeBtn.classList.add('bg-purple-600', 'text-white');
        activeBtn.classList.remove('text-gray-400');
    }

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

    .content-type-btn.active {
        background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
    }

    .content-type-btn {
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
