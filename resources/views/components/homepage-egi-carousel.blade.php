{{-- resources/views/components/homepage-egi-carousel.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Homepage EGI Carousel)
* @date 2025-08-11
* @purpose Featured EGI carousel for homepage with mobile-first responsive design
--}}

@props(['egis' => collect()])

<section class="py-8 bg-gradient-to-br from-gray-900 via-gray-800 to-black lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-3">
                🎨 <span class="bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text text-transparent">
                    {{ __('egi.carousel.title') }}
                </span>
            </h2>
            <p class="text-gray-300 max-w-2xl mx-auto">
                {{ __('egi.carousel.subtitle') }}
            </p>
        </div>

        @if($egis->count() > 0)
        {{-- Mobile Layout Controls --}}
        <div class="flex justify-center mb-6 lg:hidden">
            <div class="flex p-1 bg-gray-800 rounded-lg border border-gray-700">
                <button class="layout-btn px-3 py-2 text-sm font-medium rounded transition-all duration-200 active"
                    data-layout="2" aria-label="{{ __('egi.carousel.two_columns') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z" />
                    </svg>
                </button>
                <button class="layout-btn px-3 py-2 text-sm font-medium rounded transition-all duration-200 ml-1"
                    data-layout="3" aria-label="{{ __('egi.carousel.three_columns') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4h16v16H4V4zm2 2v12h12V6H6z" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Carousel Container --}}
        <div class="relative">
            {{-- Desktop Navigation Buttons --}}
            <button id="prev-btn"
                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-10 h-10 bg-gray-800 hover:bg-gray-700 text-white rounded-full shadow-lg transition-all duration-300 z-10 hidden lg:flex items-center justify-center group border border-gray-600 hover:border-gray-400"
                aria-label="{{ __('egi.carousel.navigation.previous') }}">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button id="next-btn"
                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-10 h-10 bg-gray-800 hover:bg-gray-700 text-white rounded-full shadow-lg transition-all duration-300 z-10 hidden lg:flex items-center justify-center group border border-gray-600 hover:border-gray-400"
                aria-label="{{ __('egi.carousel.navigation.next') }}">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Carousel Track --}}
            <div id="homepage-egi-carousel" class="overflow-hidden">
                {{-- Mobile Grid Vista Lista (card-list) --}}
                <div class="mobile-grid-2 grid grid-cols-1 gap-4 lg:hidden transition-all duration-300"
                    id="mobile-grid-2">
                    @foreach($egis as $egi)
                    <div class="mobile-item">
                        <x-egi-card-list :egi="$egi" :context="'carousel'" :showBadge="true" />
                    </div>
                    @endforeach
                </div>

                {{-- Mobile Grid Vista Card (card) --}}
                <div class="mobile-grid-3 hidden grid-cols-1 gap-4 lg:hidden transition-all duration-300"
                    id="mobile-grid-3">
                    @foreach($egis as $egi)
                    <div class="mobile-item">
                        <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                    </div>
                    @endforeach
                </div>

                {{-- Desktop Carousel --}}
                <div class="desktop-carousel hidden lg:flex space-x-4 pb-4 overflow-x-auto scrollbar-hide"
                    id="desktop-carousel-track">
                    @foreach($egis as $egi)
                    <div class="flex-shrink-0" style="width: 280px;">
                        <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Indicators for Desktop --}}
        <div class="hidden lg:flex justify-center mt-6 space-x-2">
            @for($i = 0; $i < min(5, ceil($egis->count() / 4)); $i++)
                <button
                    class="w-2 h-2 rounded-full bg-gray-600 hover:bg-purple-500 transition-colors duration-300 carousel-indicator"
                    data-slide="{{ $i }}" aria-label="{{ __('egi.carousel.navigation.slide', ['number' => $i + 1]) }}">
                </button>
                @endfor
        </div>

        @else
        {{-- No EGIs State --}}
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-6 bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">{{ __('egi.carousel.empty_state.title') }}</h3>
            <p class="text-gray-400 max-w-md mx-auto">
                {{ __('egi.carousel.empty_state.subtitle') }}
            </p>
        </div>
        @endif
    </div>
</section>

{{-- Carousel JavaScript --}}
@if($egis->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Mobile Layout Switcher
    const layoutBtns = document.querySelectorAll('.layout-btn');
    const mobileGrid2 = document.getElementById('mobile-grid-2');
    const mobileGrid3 = document.getElementById('mobile-grid-3');

    layoutBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const layout = this.dataset.layout;

            // Update active button
            layoutBtns.forEach(b => {
                b.classList.remove('active', 'bg-purple-600', 'text-white');
                b.classList.add('text-gray-400');
            });
            this.classList.add('active', 'bg-purple-600', 'text-white');
            this.classList.remove('text-gray-400');

            // Toggle between grids
            if (layout === '2') {
                mobileGrid2.classList.remove('hidden');
                mobileGrid2.classList.add('grid');
                mobileGrid3.classList.add('hidden');
                mobileGrid3.classList.remove('grid');
            } else {
                mobileGrid2.classList.add('hidden');
                mobileGrid2.classList.remove('grid');
                mobileGrid3.classList.remove('hidden');
                mobileGrid3.classList.add('grid');
            }
        });
    });

    // Initialize active button
    document.querySelector('.layout-btn.active').classList.add('bg-purple-600', 'text-white');
    document.querySelector('.layout-btn.active').classList.remove('text-gray-400');

    // Desktop Carousel Logic
    const carousel = document.getElementById('desktop-carousel-track');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const indicators = document.querySelectorAll('.carousel-indicator');

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

    .layout-btn.active {
        background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
    }

    @media (max-width: 1023px) {

        .mobile-grid-2,
        .mobile-grid-3 {
            max-height: 70vh;
            overflow-y: auto;
        }
    }

    @media (min-width: 1024px) {

        .mobile-grid-2,
        .mobile-grid-3 {
            display: none !important;
        }
    }
</style>
@endif
