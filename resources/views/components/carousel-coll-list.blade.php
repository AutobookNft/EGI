@php
use App\Models\PaymentDistribution;
use App\Models\Collection;

// Ottieni le statistiche delle collection più profittevoli
$distributionStats = PaymentDistribution::getDashboardStats();
$topCollections = $distributionStats['top_collections'] ?? [];

// Recupera gli oggetti collection completi per avere le immagini
$collectionsWithImages = [];
foreach ($topCollections as $collectionData) {
    $collection = Collection::find($collectionData['collection_id']);
    if ($collection) {
        $collectionsWithImages[] = [
            'collection' => $collection,
            'stats' => $collectionData
        ];
    }
}

// ID univoco per evitare conflitti
$instanceId = uniqid();
@endphp

{{-- Carousel Collections Container come OpenSea --}}
<div class="w-full" id="carousel_{{ $instanceId }}">
    {{-- Header semplice --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-white">Trending collections</h3>
        <div class="flex space-x-2">
            <button id="prevBtn_{{ $instanceId }}" class="p-1.5 text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button id="nextBtn_{{ $instanceId }}" class="p-1.5 text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Container del carousel con overflow nascosto --}}
    <div class="overflow-hidden">
        <div id="carouselContainer_{{ $instanceId }}" 
             class="flex transition-transform duration-300 ease-in-out space-x-3">
            @if(count($collectionsWithImages) > 0)
                @foreach($collectionsWithImages as $item)
                    <div class="flex-shrink-0 w-64">
                        <x-coll-card-list-small 
                            :collection="$item['collection']"
                            :stats="$item['stats']"
                        />
                    </div>
                @endforeach
            @else
                <div class="w-full text-center py-8 text-gray-400">
                    <p>No collections available</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- JavaScript per il carousel --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const instanceId = "{{ $instanceId }}";
    const container = document.getElementById('carouselContainer_' + instanceId);
    const prevBtn = document.getElementById('prevBtn_' + instanceId);
    const nextBtn = document.getElementById('nextBtn_' + instanceId);
    
    if (!container) return;
    
    const cards = container.children;
    const cardWidth = 256 + 12; // 256px width + 12px gap (w-64 + space-x-3)
    let currentPosition = 0;
    const maxPosition = Math.max(0, (cards.length * cardWidth) - container.parentElement.offsetWidth);
    
    function updateCarousel() {
        container.style.transform = `translateX(-${currentPosition}px)`;
        
        // Update button states
        prevBtn.style.opacity = currentPosition === 0 ? '0.5' : '1';
        nextBtn.style.opacity = currentPosition >= maxPosition ? '0.5' : '1';
    }
    
    function moveNext() {
        if (currentPosition < maxPosition) {
            currentPosition = Math.min(currentPosition + cardWidth, maxPosition);
            updateCarousel();
        }
    }
    
    function movePrev() {
        if (currentPosition > 0) {
            currentPosition = Math.max(currentPosition - cardWidth, 0);
            updateCarousel();
        }
    }
    
    // Event listeners
    nextBtn.addEventListener('click', moveNext);
    prevBtn.addEventListener('click', movePrev);
    
    // Initial state
    updateCarousel();
});
</script>

<style>
#carouselContainer_{{ $instanceId }} {
    transition: transform 0.3s ease;
}
</style>

{{-- JavaScript per il carousel --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const instanceId = "{{ $instanceId }}";
    const totalItems = {{ count($topCollections) }};
    let itemsPerSlide = getItemsPerSlide();
    let totalSlides = Math.ceil(totalItems / itemsPerSlide);
    let currentSlide = 0;
    
    const container = document.getElementById('carouselContainer_' + instanceId);
    const prevBtn = document.getElementById('prevBtn_' + instanceId);
    const nextBtn = document.getElementById('nextBtn_' + instanceId);
    const indicators = document.querySelectorAll('#indicators_' + instanceId + ' .indicator');
    
    if (!container || totalItems <= itemsPerSlide) return;
    
    // Calcola items per slide in base alla larghezza schermo
    function getItemsPerSlide() {
        if (window.innerWidth >= 1024) return 3; // Desktop
        if (window.innerWidth >= 768) return 2;  // Tablet
        return 1; // Mobile
    }
    
    // Aggiorna il carousel
    function updateCarousel() {
        const itemWidth = container.children[0]?.offsetWidth || 300;
        const gap = 16; // 1rem = 16px
        const translateX = -(currentSlide * (itemWidth + gap) * itemsPerSlide);
        container.style.transform = `translateX(${translateX}px)`;
        
        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('bg-blue-500', index === currentSlide);
            indicator.classList.toggle('bg-gray-600', index !== currentSlide);
        });
        
        // Update button states
        if (prevBtn) {
            prevBtn.classList.toggle('opacity-50', currentSlide === 0);
            prevBtn.disabled = currentSlide === 0;
        }
        if (nextBtn) {
            nextBtn.classList.toggle('opacity-50', currentSlide >= totalSlides - 1);
            nextBtn.disabled = currentSlide >= totalSlides - 1;
        }
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
    
    // Ricalcola al resize
    function handleResize() {
        const newItemsPerSlide = getItemsPerSlide();
        if (newItemsPerSlide !== itemsPerSlide) {
            itemsPerSlide = newItemsPerSlide;
            totalSlides = Math.ceil(totalItems / itemsPerSlide);
            currentSlide = Math.min(currentSlide, totalSlides - 1);
            updateCarousel();
        }
    }
    
    // Global function for indicators
    window['goToSlide' + instanceId] = function(slideIndex) {
        currentSlide = Math.min(slideIndex, totalSlides - 1);
        updateCarousel();
    };
    
    // Event listeners
    prevBtn?.addEventListener('click', prevSlide);
    nextBtn?.addEventListener('click', nextSlide);
    window.addEventListener('resize', handleResize);
    
    // Auto-play carousel (solo se ci sono abbastanza elementi)
    if (totalSlides > 1) {
        let autoPlayInterval = setInterval(function() {
            if (currentSlide >= totalSlides - 1) {
                currentSlide = 0;
            } else {
                nextSlide();
            }
        }, 5000);
        
        // Pause on hover
        const carouselElement = document.getElementById('carousel_' + instanceId);
        carouselElement.addEventListener('mouseenter', () => {
            clearInterval(autoPlayInterval);
        });
        
        carouselElement.addEventListener('mouseleave', () => {
            autoPlayInterval = setInterval(function() {
                if (currentSlide >= totalSlides - 1) {
                    currentSlide = 0;
                    updateCarousel();
                } else {
                    nextSlide();
                }
            }, 5000);
        });
    }
    
    // Initial update
    updateCarousel();
});
</script>

{{-- Stili CSS per responsive design --}}
<style>
/* Desktop: 3 cards */
@media (min-width: 1024px) {
    #carousel_{{ $instanceId }} .carousel-item {
        width: calc(33.333% - 1rem) !important;
    }
}

/* Tablet: 2 cards */
@media (max-width: 1023px) and (min-width: 768px) {
    #carousel_{{ $instanceId }} .carousel-item {
        width: calc(50% - 0.5rem) !important;
    }
}

/* Mobile: 1 card */
@media (max-width: 767px) {
    #carousel_{{ $instanceId }} .carousel-item {
        width: 100% !important;
    }
    
    #carousel_{{ $instanceId }} #carouselContainer_{{ $instanceId }} {
        gap: 0.5rem !important;
    }
}

/* Smooth transitions */
#carouselContainer_{{ $instanceId }} {
    transition: transform 0.3s ease-in-out;
}

/* Button hover effects */
#carousel_{{ $instanceId }} button:hover:not(:disabled) {
    transform: translateY(-1px);
}
</style>
