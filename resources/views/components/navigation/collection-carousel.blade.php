{{-- Collection Carousel Component for Menu --}}
@props(['user' => null])

@php
// Ottieni le collezioni dell'utente ordinati per position
$collections = $user
    ? $user->ownedCollections()
        ->orderBy('position', 'asc')
        ->orderBy('created_at', 'desc')
        ->get()
    : collect();
@endphp

<div class="collection-carousel-container relative">
    @if($collections->count() > 0)
        <!-- Carousel Header -->
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                {{ __('collection.my_galleries') }}
            </h4>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ $collections->count() }} {{ trans_choice('label.collection|label.collections', $collections->count()) }}
            </div>
        </div>

        <!-- Carousel Container -->
        <div class="relative overflow-hidden rounded-xl bg-gray-50/50 dark:bg-gray-800/50">
            <div class="carousel-track flex transition-transform duration-300 ease-out"
                 data-carousel-track
                 data-total-slides="{{ $collections->count() }}">

                @foreach($collections as $index => $collection)
                    <div class="carousel-slide flex-none w-full" data-slide="{{ $index }}">
                        <div class="p-4">
                            <!-- Collection Card -->
                            <div class="flex items-center space-x-3 group">
                                <!-- Collection Image/Avatar -->
                                <div class="relative flex-shrink-0">
                                    @if($collection->getFirstMediaUrl('head'))
                                        <img src="{{ $collection->getFirstMediaUrl('head') }}"
                                             alt="{{ $collection->collection_name }}"
                                             class="w-12 h-12 rounded-lg object-cover ring-2 ring-purple-500/20 group-hover:ring-purple-500/40 transition-all duration-300">
                                    @elseif($collection->image_card)
                                        <img src="{{ $collection->image_card->url ?? $collection->image_card }}"
                                             alt="{{ $collection->collection_name }}"
                                             class="w-12 h-12 rounded-lg object-cover ring-2 ring-purple-500/20 group-hover:ring-purple-500/40 transition-all duration-300">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center ring-2 ring-purple-500/20 group-hover:ring-purple-500/40 transition-all duration-300">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <!-- Status indicator -->
                                    @if($collection->is_published)
                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                    @endif
                                </div>

                                <!-- Collection Info -->
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('collections.show', $collection->id) }}"
                                       class="block hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200">
                                        <h5 class="font-medium text-gray-900 dark:text-gray-100 truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-200">
                                            {{ $collection->collection_name }}
                                        </h5>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <!-- EGI Count -->
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $collection->egis()->count() }} EGI</span>
                                            </span>

                                            <!-- Status -->
                                            <span class="flex items-center space-x-1">
                                                <div class="w-2 h-2 rounded-full {{ $collection->is_published ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                                                <span>{{ $collection->is_published ? __('label.published') : __('label.draft') }}</span>
                                            </span>
                                        </div>
                                    </a>
                                </div>

                                <!-- Quick Actions -->
                                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <a href="{{ route('collections.show', $collection->id) }}"
                                       class="p-1.5 text-gray-400 hover:text-purple-600 transition-colors duration-200"
                                       title="{{ __('label.view') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('update', $collection)
                                        <a href="{{ route('collections.edit', $collection->id) }}"
                                           class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors duration-200"
                                           title="{{ __('label.edit') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Navigation Controls (only if more than 1 collection) -->
            @if($collections->count() > 1)
                <button class="carousel-nav carousel-prev absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/90 dark:bg-gray-800/90 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 transition-all duration-200 opacity-0 group-hover:opacity-100"
                        data-carousel-prev>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <button class="carousel-nav carousel-next absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/90 dark:bg-gray-800/90 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 transition-all duration-200 opacity-0 group-hover:opacity-100"
                        data-carousel-next>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- Dots Indicator -->
                <div class="flex justify-center space-x-1 mt-3">
                    @for($i = 0; $i < $collections->count(); $i++)
                        <button class="carousel-dot w-2 h-2 rounded-full transition-all duration-200 {{ $i === 0 ? 'bg-purple-500' : 'bg-gray-300 dark:bg-gray-600' }}"
                                data-carousel-dot="{{ $i }}"></button>
                    @endfor
                </div>
            @endif
        </div>

        <!-- Quick Action Link -->
        <div class="mt-3 text-center">
            <a href="{{ route('collections.index') }}"
               class="text-xs text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors duration-200">
                {{ __('collection.my_galleries') }} →
            </a>
        </div>

    @else
        <!-- Empty State -->
        <div class="text-center py-6">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                {{ __('collection.no_collections_found') }}
            </h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ __('collection.create_modal_subtitle') }}
            </p>
            <button type="button" data-action="open-create-collection-modal"
               class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('collection.create_new_collection') }}
            </a>
        </div>
    @endif
</div>

{{-- Carousel JavaScript --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.collection-carousel-container');

    carousels.forEach(carousel => {
        const track = carousel.querySelector('[data-carousel-track]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');
        const dots = carousel.querySelectorAll('[data-carousel-dot]');

        if (!track) return;

        const totalSlides = parseInt(track.dataset.totalSlides);
        let currentSlide = 0;

        function updateCarousel() {
            // Move track
            track.style.transform = `translateX(-${currentSlide * 100}%)`;

            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-purple-500', index === currentSlide);
                dot.classList.toggle('bg-gray-300', index !== currentSlide);
                dot.classList.toggle('dark:bg-gray-600', index !== currentSlide);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        // Event listeners
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateCarousel();
            });
        });

        // Touch/swipe support for mobile
        let startX = 0;
        let isDragging = false;

        track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });

        track.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
        });

        track.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            isDragging = false;

            const endX = e.changedTouches[0].clientX;
            const diffX = startX - endX;

            if (Math.abs(diffX) > 50) { // Minimum swipe distance
                if (diffX > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        });

        // Auto-play on hover (optional)
        // Uncomment if you want auto-scroll
        /*
        let autoPlayInterval;

        carousel.addEventListener('mouseenter', () => {
            clearInterval(autoPlayInterval);
        });

        carousel.addEventListener('mouseleave', () => {
            if (totalSlides > 1) {
                autoPlayInterval = setInterval(nextSlide, 4000);
            }
        });

        // Start auto-play
        if (totalSlides > 1) {
            autoPlayInterval = setInterval(nextSlide, 4000);
        }
        */
    });
});
</script>
@endpush
