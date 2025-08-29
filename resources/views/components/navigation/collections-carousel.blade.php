{{-- Collections Carousel Component --}}
@props(['user'])

@php
// Ottieni le collezioni dell'utente ordinate per position
$userCollections = $user->ownedCollections()
    ->orderBy('position', 'asc')
    ->orderBy('created_at', 'asc')
    ->get();
@endphp

<!-- Collections Carousel Card -->
<div class="p-4 border bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl border-purple-200/30 dark:border-purple-800/30 mega-card">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.my_collections') }}</h4>
        </div>

        @if($userCollections->count() > 1)
            <!-- Navigation Arrows -->
            <div class="flex space-x-1">
                <button id="carousel-prev"
                        class="flex items-center justify-center w-8 h-8 text-purple-600 transition-all duration-200 bg-white rounded-lg shadow-sm hover:bg-purple-50 hover:text-purple-700 dark:bg-gray-800 dark:text-purple-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button id="carousel-next"
                        class="flex items-center justify-center w-8 h-8 text-purple-600 transition-all duration-200 bg-white rounded-lg shadow-sm hover:bg-purple-50 hover:text-purple-700 dark:bg-gray-800 dark:text-purple-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>

    @if($userCollections->count() > 0)
        <!-- Carousel Container -->
        <div class="relative overflow-hidden">
            <div id="collections-carousel" class="flex transition-transform duration-300 ease-in-out">
                @foreach($userCollections as $collection)
                    <div class="flex-shrink-0 w-full px-1">
                        <!-- Collection Card -->
                        <a href="{{ route('collections.show', $collection->id) }}"
                           class="block p-3 transition-all duration-200 bg-white rounded-lg shadow-sm hover:shadow-md hover:scale-105 dark:bg-gray-800 hover:bg-purple-50 dark:hover:bg-gray-700">

                            <div class="flex items-center space-x-3">
                                <!-- Collection Image -->
                                <div class="flex-shrink-0">
                                    @if($collection->image_card && $collection->image_card->url)
                                        <img src="{{ $collection->image_card->url }}"
                                             alt="{{ $collection->collection_name }}"
                                             class="object-cover w-12 h-12 rounded-lg border border-purple-200/30 dark:border-purple-700/30">
                                    @else
                                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-800/30 dark:to-pink-800/30 border border-purple-200/30 dark:border-purple-700/30">
                                            <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Collection Info -->
                                <div class="flex-1 min-w-0">
                                    <h5 class="font-medium text-gray-900 truncate dark:text-gray-100">
                                        {{ $collection->collection_name }}
                                    </h5>
                                    <div class="flex items-center mt-1 space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                        <!-- EGI Count -->
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 3v10a2 2 0 002 2h6a2 2 0 002-2V7M9 7h6M9 11h6m-3 4h3"/>
                                            </svg>
                                            <span>{{ $collection->egis()->count() }} EGI</span>
                                        </span>

                                        <!-- Status -->
                                        @if($collection->is_published)
                                            <span class="px-2 py-0.5 bg-green-100 text-green-600 rounded-full text-xs font-medium dark:bg-green-900/30 dark:text-green-400">
                                                {{ __('common.published') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium dark:bg-yellow-900/30 dark:text-yellow-400">
                                                {{ __('common.draft') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Hover Arrow -->
                                <div class="flex-shrink-0 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        @if($userCollections->count() > 1)
            <!-- Dots Indicator -->
            <div class="flex justify-center mt-3 space-x-1">
                @foreach($userCollections as $index => $collection)
                    <button class="carousel-dot w-2 h-2 rounded-full bg-purple-300 dark:bg-purple-600 transition-all duration-200 hover:bg-purple-400 dark:hover:bg-purple-500 {{ $index === 0 ? 'bg-purple-500 dark:bg-purple-400' : '' }}"
                            data-slide="{{ $index }}"></button>
                @endforeach
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="flex justify-between mt-3 pt-3 border-t border-purple-200/30 dark:border-purple-700/30">
            <a href="{{ route('collections.index') }}"
               class="text-xs text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200">
                {{ __('menu.view_all') }}
            </a>
            @can('create_collection')
                <button type="button" data-action="open-create-collection-modal"
                   class="text-xs text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200 font-medium">
                    + {{ __('menu.new_collection') }}
                </button>
            @endcan
        </div>

    @else
        <!-- Empty State -->
        <div class="text-center py-6">
            <div class="flex justify-center mb-3">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-800/30 dark:to-pink-800/30">
                    <svg class="w-8 h-8 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-1">
                {{ __('collections.empty.title') }}
            </h5>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ __('collections.empty.description') }}
            </p>
            @can('create_collection')
                <button type="button" data-action="open-create-collection-modal"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-200 hover:scale-105 hover:shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    {{ __('collections.create.title') }}
                </button>
            @endcan
        </div>
    @endif
</div>

{{-- JavaScript per il Carousel --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('collections-carousel');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    const dots = document.querySelectorAll('.carousel-dot');

    if (!carousel) return;

    let currentSlide = 0;
    const totalSlides = carousel.children.length;

    function updateCarousel() {
        const translateX = -currentSlide * 100;
        carousel.style.transform = `translateX(${translateX}%)`;

        // Update dots
        dots.forEach((dot, index) => {
            if (index === currentSlide) {
                dot.classList.add('bg-purple-500', 'dark:bg-purple-400');
                dot.classList.remove('bg-purple-300', 'dark:bg-purple-600');
            } else {
                dot.classList.remove('bg-purple-500', 'dark:bg-purple-400');
                dot.classList.add('bg-purple-300', 'dark:bg-purple-600');
            }
        });

        // Update button states
        if (prevBtn) prevBtn.disabled = currentSlide === 0;
        if (nextBtn) nextBtn.disabled = currentSlide === totalSlides - 1;
    }

    function nextSlide() {
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateCarousel();
        }
    }

    function prevSlide() {
        if (currentSlide > 0) {
            currentSlide--;
            updateCarousel();
        }
    }

    function goToSlide(index) {
        currentSlide = index;
        updateCarousel();
    }

    // Event listeners
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => goToSlide(index));
    });

    // Touch/Swipe support
    let startX = 0;
    let isDragging = false;

    carousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
    });

    carousel.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
    });

    carousel.addEventListener('touchend', (e) => {
        if (!isDragging) return;

        const endX = e.changedTouches[0].clientX;
        const diffX = startX - endX;

        if (Math.abs(diffX) > 50) { // Minimum swipe distance
            if (diffX > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        }

        isDragging = false;
    });

    // Auto-play (optional, uncomment to enable)
    // setInterval(() => {
    //     if (currentSlide < totalSlides - 1) {
    //         nextSlide();
    //     } else {
    //         currentSlide = 0;
    //         updateCarousel();
    //     }
    // }, 5000);

    // Initialize
    updateCarousel();
});
</script>
@endpush
