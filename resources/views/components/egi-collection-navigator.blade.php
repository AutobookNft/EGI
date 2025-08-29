{{-- resources/views/components/egi-collection-navigator.blade.php --}}
{{-- 🎯 OpenSea-style EGI Collection Navigator - Images Only --}}
{{-- Horizontal carousel with thumbnails for navigating between EGIs in the same collection --}}

@props([
    'collectionEgis' => collect(),
    'currentEgi' => null
])

@if($collectionEgis->count() > 1)
<div class="w-full bg-white/5 backdrop-blur-sm border-b border-white/10">
    <div class="relative px-4 py-3">
        <!-- Navigation Title (Screen readers only) -->
        <h2 class="sr-only">{{ __('label.collection_navigation.navigate_collection') }}</h2>
        
        <!-- Carousel Container -->
        <div class="relative">
            <!-- Left Arrow -->
            <button 
                id="carousel-prev" 
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-black/50 hover:bg-black/70 flex items-center justify-center transition-all duration-200 backdrop-blur-sm"
                aria-label="{{ __('label.collection_navigation.previous_egi') }}"
            >
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Right Arrow -->
            <button 
                id="carousel-next" 
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-black/50 hover:bg-black/70 flex items-center justify-center transition-all duration-200 backdrop-blur-sm"
                aria-label="{{ __('label.collection_navigation.next_egi') }}"
            >
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Scrollable Container -->
            <div 
                id="carousel-track" 
                class="flex gap-2 overflow-x-auto scrollbar-hide mx-10 py-2 scroll-smooth"
                style="scrollbar-width: none; -ms-overflow-style: none;"
            >
                @foreach($collectionEgis as $egi)
                    <a 
                        href="{{ route('egis.show', $egi->id) }}" 
                        class="carousel-item relative group flex-shrink-0 w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden transition-all duration-200 hover:scale-105 hover:shadow-lg {{ $currentEgi && $currentEgi->id === $egi->id ? 'ring-2 ring-blue-500 scale-105' : 'hover:ring-2 hover:ring-white/50' }}"
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
                            <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Current item indicator -->
                        @if($currentEgi && $currentEgi->id === $egi->id)
                            <div class="absolute inset-0 bg-blue-500/20 flex items-center justify-center">
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
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    
    if (!track || !prevBtn || !nextBtn) return;
    
    const scrollAmount = 160; // Width of 2 items
    
    prevBtn.addEventListener('click', () => {
        track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });
    
    nextBtn.addEventListener('click', () => {
        track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
    
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
    
    // Update button states based on scroll position
    function updateButtonStates() {
        const isAtStart = track.scrollLeft <= 0;
        const isAtEnd = track.scrollLeft >= track.scrollWidth - track.clientWidth;
        
        prevBtn.style.opacity = isAtStart ? '0.5' : '1';
        nextBtn.style.opacity = isAtEnd ? '0.5' : '1';
        prevBtn.disabled = isAtStart;
        nextBtn.disabled = isAtEnd;
    }
    
    track.addEventListener('scroll', updateButtonStates);
    updateButtonStates(); // Initial state
});
</script>
@endif
