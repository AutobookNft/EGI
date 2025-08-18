{{-- resources/views/components/hero-egi-coverflow.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Hero Coverflow Carousel)
* @date 2025-01-18
* @purpose Hero section with 3D coverflow effect for featured EGIs
--}}

@props([
'egis' => collect(),
'id' => 'hero-egi-coverflow'
])

<section id="{{ $id }}" class="relative min-h-[70vh] bg-gradient-to-br from-gray-900 via-gray-800 to-black overflow-hidden" data-coverflow>
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, #8b5cf6 0%, transparent 50%), radial-gradient(circle at 75% 75%, #3b82f6 0%, transparent 50%);"></div>
    </div>

    <div class="relative z-10 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8 py-12">
        
        {{-- Header Section --}}
        <div class="mb-12 text-center">
            <h1 class="mb-4 text-4xl font-bold text-white md:text-5xl lg:text-6xl">
                🎨 <span class="text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                    {{ __('egi.hero_coverflow.title') }}
                </span>
            </h1>
            <p class="max-w-3xl mx-auto text-lg text-gray-300 md:text-xl">
                {{ __('egi.hero_coverflow.subtitle') }}
            </p>

            {{-- View Toggle --}}
            <div class="inline-flex mt-6 rounded-xl bg-gray-800/60 p-1 gap-1 border border-gray-700">
                <button class="px-4 py-2 text-sm font-medium rounded-lg transition-all data-[active=true]:bg-purple-600 data-[active=true]:text-white text-gray-300 hover:text-white"
                        data-action="set-view" data-view="carousel" aria-label="{{ __('egi.hero_coverflow.carousel_mode') }}">
                    🎠 {{ __('egi.hero_coverflow.carousel_mode') }}
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg transition-all data-[active=true]:bg-purple-600 data-[active=true]:text-white text-gray-300 hover:text-white"
                        data-action="set-view" data-view="list" aria-label="{{ __('egi.hero_coverflow.list_mode') }}">
                    📋 {{ __('egi.hero_coverflow.list_mode') }}
                </button>
            </div>
        </div>

        {{-- Coverflow Carousel --}}
        <div class="relative" data-carousel-container>
            <div class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-pl-6 pr-6 pl-6 will-change-transform pb-8"
                 data-track tabindex="0" aria-label="{{ __('egi.hero_coverflow.carousel_label') }}">
                
                @if($egis->count() > 0)
                    @foreach($egis as $egi)
                        <div class="snap-center shrink-0 w-[85%] sm:w-[70%] md:w-[50%] lg:w-[40%] xl:w-[35%]
                                    transition-all duration-200 ease-out cursor-pointer"
                             data-slide data-url="{{ route('egis.show', $egi) }}">
                            
                            <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                            
                        </div>
                    @endforeach
                @else
                    {{-- Empty State --}}
                    <div class="w-full text-center py-12">
                        <div class="text-gray-400 text-lg">
                            {{ __('egi.hero_coverflow.no_egis') }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Navigation Arrows --}}
            @if($egis->count() > 1)
            <div class="pointer-events-none absolute inset-y-0 left-0 right-0 flex justify-between items-center px-4">
                <button class="pointer-events-auto rounded-full p-3 bg-gray-900/80 backdrop-blur border border-gray-700 text-white hover:bg-gray-800 hover:border-purple-500 transition-all transform hover:scale-110" 
                        data-arrow="-1" aria-label="{{ __('egi.hero_coverflow.navigation.previous') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button class="pointer-events-auto rounded-full p-3 bg-gray-900/80 backdrop-blur border border-gray-700 text-white hover:bg-gray-800 hover:border-purple-500 transition-all transform hover:scale-110" 
                        data-arrow="1" aria-label="{{ __('egi.hero_coverflow.navigation.next') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            @endif

            {{-- Edge Fade Effects --}}
            <div class="pointer-events-none absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-gray-900 to-transparent"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-gray-900 to-transparent"></div>
        </div>

        {{-- List View (Fallback) --}}
        <div class="hidden px-4" data-list>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($egis as $egi)
                    <x-egi-card-list :egi="$egi" :showPurchasePrice="true" />
                @endforeach
            </div>
        </div>

    </div>
</section>

{{-- Coverflow JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initCoverflow(root) {
        const track = root.querySelector('[data-track]');
        const list = root.querySelector('[data-list]');
        const slides = Array.from(track?.querySelectorAll('[data-slide]') || []);
        
        if (!track || !list) return;

        // View toggle functions
        function setView(view) {
            if (view === 'carousel') {
                track.classList.remove('hidden');
                list.classList.add('hidden');
            } else {
                list.classList.remove('hidden');
                track.classList.add('hidden');
            }
            
            root.querySelectorAll('[data-action="set-view"]').forEach(btn => {
                btn.dataset.active = (btn.dataset.view === view).toString();
            });
            
            localStorage.setItem(`coverflow:view:${root.id}`, view);
        }

        // Initialize view
        const savedView = localStorage.getItem(`coverflow:view:${root.id}`) || 'carousel';
        setView(savedView);

        // View toggle buttons
        root.querySelectorAll('[data-action="set-view"]').forEach(btn => {
            btn.addEventListener('click', () => {
                setView(btn.dataset.view || 'carousel');
            });
        });

        // Arrow navigation
        root.querySelectorAll('[data-arrow]').forEach(btn => {
            btn.addEventListener('click', () => {
                const direction = parseInt(btn.dataset.arrow || '1', 10);
                const slideWidth = slides[0]?.getBoundingClientRect().width || 320;
                const scrollAmount = (slideWidth + 24) * direction; // 24px = gap
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        });

        // Coverflow 3D effect
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        function updateCoverflowEffect() {
            if (!track) return;
            
            const trackRect = track.getBoundingClientRect();
            const center = trackRect.left + trackRect.width / 2;

            slides.forEach((slide) => {
                const slideRect = slide.getBoundingClientRect();
                const slideCenter = slideRect.left + slideRect.width / 2;
                const distance = Math.abs(center - slideCenter);
                const normalizedDistance = Math.min(1, distance / (trackRect.width * 0.6));
                
                // Scale: 1 (center) to 0.85 (edges)
                const scale = 1 - normalizedDistance * 0.15;
                
                // Opacity: 1 (center) to 0.6 (edges)
                const opacity = 0.6 + (1 - normalizedDistance) * 0.4;
                
                // Rotation: -6deg to +6deg
                const rotation = ((center - slideCenter) / (trackRect.width / 2)) * 6;
                
                // Z-index: higher for center elements
                const zIndex = 1000 - Math.round(normalizedDistance * 1000);

                slide.style.zIndex = String(zIndex);
                slide.style.opacity = String(opacity);
                slide.style.transform = prefersReduced
                    ? `scale(${scale})`
                    : `perspective(1000px) rotateY(${rotation}deg) scale(${scale})`;
                slide.style.transition = 'transform 150ms ease-out, opacity 150ms ease-out';
            });

            requestAnimationFrame(updateCoverflowEffect);
        }

        updateCoverflowEffect();

        // Center on click for non-centered slides
        slides.forEach(slide => {
            slide.addEventListener('click', (e) => {
                const trackRect = track.getBoundingClientRect();
                const slideRect = slide.getBoundingClientRect();
                const slideCenter = slideRect.left + slideRect.width / 2;
                const trackCenter = trackRect.left + trackRect.width / 2;
                const isCentered = Math.abs(slideCenter - trackCenter) < 10;
                
                if (!isCentered) {
                    e.preventDefault();
                    const scrollAmount = slideCenter - trackCenter;
                    track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            });
        });

        // Keyboard navigation
        track.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                e.preventDefault();
                track.scrollBy({ left: 350, behavior: 'smooth' });
            }
            if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                e.preventDefault();
                track.scrollBy({ left: -350, behavior: 'smooth' });
            }
        });
    }

    // Initialize all coverflow carousels
    document.querySelectorAll('[data-coverflow]').forEach(initCoverflow);
});
</script>

{{-- Custom Styles --}}
<style>
/* Smooth scrolling for carousel */
[data-track] {
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

[data-track]::-webkit-scrollbar {
    display: none;
}

/* Ensure slides maintain their transform origin */
[data-slide] {
    transform-origin: center center;
    will-change: transform, opacity;
}

/* Enhance focus styles for accessibility */
[data-track]:focus {
    outline: 2px solid #8b5cf6;
    outline-offset: 2px;
}

/* Button hover animations */
button[data-arrow] {
    transition: all 0.2s ease;
}

button[data-arrow]:hover {
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
}
</style>
