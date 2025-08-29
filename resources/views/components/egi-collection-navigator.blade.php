{{-- resources/views/components/egi-collection-navigator.blade.php --}}
{{-- 🎯 OpenSea-style EGI Collection Navigator - Images Only --}}
{{-- Horizontal carousel with thumbnails for navigating between EGIs in the same collection --}}

@props([
    'collectionEgis' => collect(),
    'currentEgi' => null
])

@if($collectionEgis->count() > 1)
<div class="w-full border-b bg-white/5 backdrop-blur-sm border-white/10">
    <div class="relative px-2 py-2">
        <!-- Navigation Title (Screen readers only) -->
        <h2 class="sr-only">{{ __('label.collection_navigation.navigate_collection') }}</h2>

        <!-- Carousel Container - Full Width -->
        <div class="relative w-full">
            <!-- Scrollable Container - Expanded to full width -->
            <div
                id="carousel-track"
                class="flex gap-2 py-1 overflow-x-auto scrollbar-hide scroll-smooth"
                style="scrollbar-width: none; -ms-overflow-style: none;"
            >
                @foreach($collectionEgis as $egi)
                    <a
                        href="{{ route('egis.show', $egi->id) }}"
                        class="carousel-item relative group flex-shrink-0 w-12 h-12 md:w-16 md:h-16 rounded-lg overflow-hidden transition-all duration-200 hover:scale-105 hover:shadow-lg {{ $currentEgi && $currentEgi->id === $egi->id ? 'ring-2 ring-blue-500 scale-105' : 'hover:ring-2 hover:ring-white/50' }}"
                        aria-label="Visualizza EGI {{ $egi->name ?? '#' . $egi->id }}"
                    >
                        @if($egi->main_image_url)
                            <img
                                src="{{ $egi->main_image_url }}"
                                alt="EGI {{ $egi->name ?? '#' . $egi->id }}"
                                class="w-full h-full object-cover transition-opacity duration-200 {{ $currentEgi && $currentEgi->id === $egi->id ? 'opacity-100' : 'opacity-80 group-hover:opacity-100' }}"
                                loading="lazy"
                            >
                        @else
                            <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-gray-700 to-gray-900">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        <!-- Current item indicator -->
                        @if($currentEgi && $currentEgi->id === $egi->id)
                            <div class="absolute inset-0 flex items-center justify-center bg-blue-500/20">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('carousel-track');

    if (!track) return;

    // Auto-scroll to current item on load
    const currentItem = track.querySelector('.ring-2.ring-blue-500');
    if (currentItem) {
        setTimeout(() => {
            currentItem.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });
        }, 100);
    }
});
</script>
@endif
