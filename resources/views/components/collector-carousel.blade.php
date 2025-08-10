{{-- resources/views/components/collector-carousel.blade.php --}}
{{--
 * @package App\View\Components  
 * @author AI Assistant for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Top Collectors Carousel)
 * @date 2025-08-10
 * @purpose Marketing carousel showcasing top spending collectors for guest homepage
 --}}

@props(['collectors' => collect()])

<section class="py-12 bg-gradient-to-br from-gray-900 via-gray-800 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                🏆 <span class="bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">{{ __('collector.carousel.title') }}</span>
            </h2>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                {{ __('collector.carousel.subtitle') }}
            </p>
        </div>

        @if($collectors->count() > 0)
            {{-- Stats Bar --}}
            <div class="flex justify-center space-x-8 mt-6 mb-10">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400">{{ $collectors->count() }}</div>
                    <div class="text-sm text-gray-400">{{ __('collector.carousel.stats.top_collectors') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-400">
                        €{{ number_format($collectors->sum('total_spending'), 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-400">{{ __('collector.carousel.stats.total_investment') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-400">{{ $collectors->sum('activated_egis_count') }}</div>
                    <div class="text-sm text-gray-400">{{ __('collector.carousel.stats.activated_egis') }}</div>
                </div>
            </div>

            {{-- Carousel Container --}}
            <div class="relative">
                {{-- Carousel Track --}}
                <div id="collectors-carousel" class="overflow-x-auto scrollbar-hide">
                    <div class="flex space-x-6 pb-6" style="width: max-content;">
                        @foreach($collectors as $index => $collector)
                            <div class="flex-shrink-0" style="width: 280px; max-width: 280px;">
                                <x-collector-card 
                                    :collector="$collector" 
                                    :rank="$index + 1"
                                    displayType="carousel" 
                                />
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <button 
                    id="prev-btn"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-12 h-12 bg-gray-800 hover:bg-gray-700 text-white rounded-full shadow-lg transition-all duration-300 flex items-center justify-center group border border-gray-600 hover:border-gray-400"
                    aria-label="{{ __('collector.carousel.navigation.previous') }}">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button 
                    id="next-btn"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-12 h-12 bg-gray-800 hover:bg-gray-700 text-white rounded-full shadow-lg transition-all duration-300 flex items-center justify-center group border border-gray-600 hover:border-gray-400"
                    aria-label="{{ __('collector.carousel.navigation.next') }}">
                    <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            {{-- Leaderboard Indicator --}}
            <div class="flex justify-center mt-8 space-x-2">
                @for($i = 0; $i < min(5, ceil($collectors->count() / 3)); $i++)
                    <button class="w-3 h-3 rounded-full bg-gray-600 hover:bg-blue-500 transition-colors duration-300 carousel-indicator" 
                            data-slide="{{ $i }}"
                            aria-label="{{ __('collector.carousel.navigation.slide', ['number' => $i + 1]) }}"></button>
                @endfor
            </div>

            {{-- Call to Action --}}
            <div class="text-center mt-10">
                <p class="text-gray-300 mb-4">
                    {!! __('collector.carousel.cta.message') !!}
                </p>
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    {{ __('collector.carousel.cta.button') }}
                </a>
            </div>
        @else
            {{-- No Collectors State --}}
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">{{ __('collector.carousel.empty_state.title') }}</h3>
                <p class="text-gray-400 mb-6 max-w-md mx-auto">
                    {{ __('collector.carousel.empty_state.subtitle') }}
                </p>
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    {{ __('collector.carousel.empty_state.cta') }}
                </a>
            </div>
        @endif
    </div>
</section>

{{-- Carousel JavaScript - Solo se ci sono collectors --}}
@if($collectors->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('collectors-carousel');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const indicators = document.querySelectorAll('.carousel-indicator');
        
        if (!carousel || !prevBtn || !nextBtn) return;
        
        const cardWidth = 300; // 280px + 20px space-x-6
        const visibleCards = Math.floor(carousel.offsetWidth / cardWidth);
        const totalCards = {{ $collectors->count() }};
        const maxScroll = Math.max(0, (totalCards - visibleCards) * cardWidth);
        
        let currentPosition = 0;
        let isScrolling = false;
        
        function updateCarousel() {
            carousel.scrollTo({
                left: currentPosition,
                behavior: 'smooth'
            });
            
            // Update indicators
            const currentSlide = Math.floor(currentPosition / (cardWidth * visibleCards));
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('bg-blue-500', index === currentSlide);
                indicator.classList.toggle('bg-gray-600', index !== currentSlide);
            });
            
            // Update button states
            prevBtn.style.opacity = currentPosition > 0 ? '1' : '0.5';
            nextBtn.style.opacity = currentPosition < maxScroll ? '1' : '0.5';
        }
        
        prevBtn.addEventListener('click', function() {
            if (isScrolling || currentPosition <= 0) return;
            isScrolling = true;
            currentPosition = Math.max(0, currentPosition - cardWidth * visibleCards);
            updateCarousel();
            setTimeout(() => isScrolling = false, 500);
        });
        
        nextBtn.addEventListener('click', function() {
            if (isScrolling || currentPosition >= maxScroll) return;
            isScrolling = true;
            currentPosition = Math.min(maxScroll, currentPosition + cardWidth * visibleCards);
            updateCarousel();
            setTimeout(() => isScrolling = false, 500);
        });
        
        // Indicator clicks
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', function() {
                if (isScrolling) return;
                isScrolling = true;
                currentPosition = index * cardWidth * visibleCards;
                updateCarousel();
                setTimeout(() => isScrolling = false, 500);
            });
        });
        
        // Auto-scroll (optional)
        let autoScrollInterval;
        function startAutoScroll() {
            autoScrollInterval = setInterval(() => {
                if (!isScrolling && currentPosition < maxScroll) {
                    currentPosition = Math.min(maxScroll, currentPosition + cardWidth);
                    updateCarousel();
                } else if (!isScrolling && currentPosition >= maxScroll) {
                    currentPosition = 0;
                    updateCarousel();
                }
            }, 5000);
        }
        
        // Start auto-scroll after initial load
        setTimeout(startAutoScroll, 2000);
        
        // Pause auto-scroll on hover
        carousel.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
        carousel.addEventListener('mouseleave', startAutoScroll);
        
        // Initialize
        updateCarousel();
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
</style>
@endif
