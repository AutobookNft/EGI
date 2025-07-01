{{-- resources/views/components/creators-carousel.blade.php --}}
@props([
    'creators' => [],
    'title' => __('guest_home.featured_creators_title'),
    'titleClass' => '',
    'bgClass' => 'bg-gray-900',
    'marginClass' => 'mb-12'
])

<div class="w-full py-8 {{ $marginClass }} {{ $bgClass }} md:py-10 lg:py-12">
    <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if($title)
            <h2 class="mb-6 text-2xl font-bold text-white md:text-3xl lg:text-4xl {{ $titleClass }} font-display">
                {{ $title }}
            </h2>
        @endif

        <div class="relative overflow-hidden featured-creators-carousel">
            <div class="flex gap-4 pb-4 overflow-x-auto md:gap-6 snap-x snap-mandatory scrollbar-hide">
                @forelse($creators as $index => $creator)
                    {{-- VERSIONE MOBILE: Avatar compatto (come per le collezioni) --}}
                    <div class="flex-shrink-0 w-32 snap-start md:hidden creator-card-mobile" data-creator-id="{{ $creator->id ?? $index }}">
                        <x-creator-card
                            :creator="$creator"
                            imageType="avatar" {{-- Puoi decidere se la creator-card avrà tipi di immagine diversi --}}
                            displayType="avatar"
                        />
                    </div>

                    {{-- VERSIONE DESKTOP: Card completa --}}
                    <div class="flex-shrink-0 hidden w-72 md:w-80 lg:w-96 snap-start md:block creator-card-desktop" data-creator-id="{{ $creator->id ?? $index }}">
                        <div class="h-full group">
                            <x-creator-card
                                :creator="$creator"
                                imageType="card"
                                displayType="default"
                            />
                        </div>
                    </div>
                @empty
                    <div class="w-full py-8 text-center text-gray-400">
                        {{ __('guest_home.no_creators_available') }}
                    </div>
                @endforelse
            </div>

            {{-- Controlli Carousel (solo se ci sono creator multipli) --}}
            @if(count($creators) > 1)
                <button type="button"
                        class="absolute left-0 z-10 items-center justify-center hidden w-10 h-10 -ml-5 text-white transition-all transform -translate-y-1/2 bg-black rounded-full opacity-70 md:flex hover:opacity-100 top-1/2 carousel-prev"
                        aria-label="{{ __('guest_home.previous_creators') }}">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button type="button"
                        class="absolute right-0 z-10 items-center justify-center hidden w-10 h-10 -mr-5 text-white transition-all transform -translate-y-1/2 bg-black rounded-full opacity-70 md:flex hover:opacity-100 top-1/2 carousel-next"
                        aria-label="{{ __('guest_home.next_creators') }}">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Script carousel con sicurezza proattiva --}}
@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('.featured-creators-carousel'); // Selettore aggiornato

            carousels.forEach(carousel => {
                const container = carousel.querySelector('.snap-x');
                const prevButton = carousel.querySelector('.carousel-prev');
                const nextButton = carousel.querySelector('.carousel-next');

                if (!container || !prevButton || !nextButton) {
                    console.warn('FlorenceEGI: Carousel elements for Creators missing');
                    return;
                }

                // Calcolo dinamico scroll amount basato su viewport
                function getScrollAmount() {
                    const isMobile = window.innerWidth < 768;
                    if (isMobile) {
                        // Mobile: w-32 (128px) + gap-4 (16px)
                        return 144;
                    } else {
                        // Desktop: calcola basandosi sulla prima card visibile
                        const firstCard = container.querySelector('.creator-card-desktop'); // Selettore aggiornato
                        if (firstCard) {
                            return firstCard.offsetWidth + 24; // + gap-6 (24px)
                        }
                        return 320; // Fallback
                    }
                }

                let scrollAmount = getScrollAmount();

                // Aggiorna scroll amount su resize
                window.addEventListener('resize', function() {
                    scrollAmount = getScrollAmount();
                });

                // Event listeners con throttling per performance
                let scrollTimeout;

                prevButton.addEventListener('click', () => {
                    if (scrollTimeout) return;

                    container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });

                    scrollTimeout = setTimeout(() => {
                        scrollTimeout = null;
                    }, 300);
                });

                nextButton.addEventListener('click', () => {
                    if (scrollTimeout) return;

                    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });

                    scrollTimeout = setTimeout(() => {
                        scrollTimeout = null;
                    }, 300);
                });
            });
        });
    </script>
    @endpush
@endonce
