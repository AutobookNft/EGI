@props([
    'collections' => collect(),
    'title' => 'Featured Collections'
])

@php
if (is_array($collections)) {
    $collections = collect($collections);
}
$hasCollections = $collections->isNotEmpty();
@endphp

@if($hasCollections)
<section class="py-8 bg-gray-900">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-white md:text-3xl">{{ $title }}</h2>
                <p class="mt-2 text-gray-400">{{ __('guest_home.hero_banner_subtitle') }}</p>
            </div>
        </div>

        {{-- Carousel Container --}}
        <div class="relative">
            {{-- Navigation Buttons --}}
            <button id="collection-hero-prev"
                class="absolute left-0 z-10 p-2 text-white transition-colors duration-300 transform -translate-y-1/2 bg-black rounded-full top-1/2 bg-opacity-60 hover:bg-opacity-80"
                aria-label="Previous">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <button id="collection-hero-next"
                class="absolute right-0 z-10 p-2 text-white transition-colors duration-300 transform -translate-y-1/2 bg-black rounded-full top-1/2 bg-opacity-60 hover:bg-opacity-80"
                aria-label="Next">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            {{-- Carousel Track - SAME AS WORKING CAROUSEL --}}
            <div class="flex pb-4 space-x-4 overflow-x-auto scrollbar-hide" id="collection-hero-track">
                @foreach($collections as $collection)
                <div class="flex-shrink-0" style="width: 320px;">
                    <div class="relative overflow-hidden transition-transform duration-300 bg-gray-800 rounded-lg hover:scale-105">
                        {{-- Collection Image --}}
                        <div class="relative h-48">
                            @php
                                $imageUrl = null;
                                if ($collection->image_banner) {
                                    $imageUrl = asset($collection->image_banner);
                                } elseif ($collection->image_card) {
                                    $imageUrl = asset($collection->image_card);
                                } elseif ($collection->image_cover) {
                                    $imageUrl = asset($collection->image_cover);
                                } elseif ($collection->image_avatar) {
                                    $imageUrl = asset($collection->image_avatar);
                                }
                            @endphp

                            @if($imageUrl)
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $collection->collection_name }}"
                                     class="object-cover w-full h-full">
                            @else
                                <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-purple-900 to-blue-900">
                                    <span class="text-lg font-bold text-white">{{ substr($collection->collection_name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>

                        {{-- Collection Info --}}
                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                            <h3 class="mb-1 text-lg font-bold">{{ $collection->collection_name }}</h3>
                            <p class="text-sm text-gray-300">
                                {{ __('guest_home.by') }} {{ $collection->creator?->name ?: __('guest_home.unknown_artist') }}
                            </p>
                            <div class="flex items-center mt-2 text-sm">
                                <span class="px-2 py-1 text-xs rounded bg-emerald-600">
                                    {{ $collection->egis_count ?? 0 }} {{ __('guest_home.items') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- JavaScript - COPY FROM WORKING CAROUSEL --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('collection-hero-track');
    const prevBtn = document.getElementById('collection-hero-prev');
    const nextBtn = document.getElementById('collection-hero-next');

    if (carousel && prevBtn && nextBtn) {
        const cardWidth = 320 + 16; // card width + gap
        let currentIndex = 0;

        function updateCarousel() {
            carousel.scrollTo({
                left: currentIndex * cardWidth,
                behavior: 'smooth'
            });
        }

        function updateButtonStates() {
            const maxIndex = Math.max(0, carousel.children.length - Math.floor(carousel.offsetWidth / cardWidth));
            prevBtn.style.opacity = currentIndex > 0 ? '1' : '0.5';
            nextBtn.style.opacity = currentIndex < maxIndex ? '1' : '0.5';
        }

        prevBtn.addEventListener('click', () => {
            currentIndex = Math.max(0, currentIndex - 1);
            updateCarousel();
            updateButtonStates();
        });

        nextBtn.addEventListener('click', () => {
            const visibleCards = Math.floor(carousel.offsetWidth / cardWidth);
            const maxIndex = Math.max(0, carousel.children.length - visibleCards);
            currentIndex = Math.min(maxIndex, currentIndex + 1);
            updateCarousel();
            updateButtonStates();
        });

        // Initial state
        updateButtonStates();

        // Handle window resize
        window.addEventListener('resize', updateButtonStates);
    }
});
</script>
@endif
