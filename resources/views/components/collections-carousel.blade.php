{{-- resources/views/components/collections-carousel.blade.php --}}
{{--
    A reusable carousel component for displaying collections

    Props:
    - collections: Collection[] - The collections to display
    - title: string - The title for the carousel section (optional)
    - titleClass: string - Additional classes for the title (optional)
    - bgClass: string - Background class for the container (optional)
    - marginClass: string - Margin class for the container (optional)
--}}

@props([
    'collections' => [],
    'title' => __('guest_home.featured_collections_title'),
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

        <div class="relative overflow-hidden featured-collections-carousel">
            <div class="flex gap-4 pb-4 overflow-x-auto md:gap-6 snap-x snap-mandatory scrollbar-hide">
                @forelse($collections as $collection)
                    {{-- Versione AVATAR per Mobile (nascosta da md in su) --}}
                    <div class="flex-shrink-0 w-32 snap-start md:hidden"> {{-- Larghezza per avatar, es. w-32 --}}
                        <x-home-collection-card :collection="$collection" :key="'carousel-avatar-' . $collection->id" imageType="avatar" displayType="avatar" />
                    </div>

                    {{-- Versione CARD per Desktop/Tablet (nascosta sotto md) --}}
                    <div class="flex-shrink-0 hidden w-72 md:w-80 lg:w-96 snap-start md:block">
                        <div class="h-full group">
                            <x-home-collection-card :collection="$collection" :key="'carousel-card-' . $collection->id" imageType="card" displayType="default" />
                        </div>
                    </div>
                @empty
                    <div class="w-full py-8 text-center text-gray-400">
                        {{ __('guest_home.no_collections_available') }}
                    </div>
                @endforelse
            </div>

            {{-- Controlli Carousel (mostrati solo se ci sono collezioni e più di un tipo di item visibile per lo scroll) --}}
            @if(count($collections) > 0)
                {{-- ... (codice controlli carousel come prima, ma assicurati che lo scrollAmount nello JS sia appropriato per entrambe le visualizzazioni o adattalo dinamicamente) ... --}}
                {{-- Potrebbe essere necessario nascondere i controlli su mobile se la visualizzazione avatar non è pensata per lo scroll orizzontale con frecce --}}
                <button type="button" class="absolute left-0 z-10 items-center justify-center hidden w-10 h-10 -ml-5 text-white transition-all transform -translate-y-1/2 bg-black rounded-full opacity-70 md:flex hover:opacity-100 top-1/2 carousel-prev" aria-label="{{ __('guest_home.previous_collections') }}">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button type="button" class="absolute right-0 z-10 items-center justify-center hidden w-10 h-10 -mr-5 text-white transition-all transform -translate-y-1/2 bg-black rounded-full opacity-70 md:flex hover:opacity-100 top-1/2 carousel-next" aria-label="{{ __('guest_home.next_collections') }}">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Script per inizializzazione carousel (opzionale) --}}
@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('.featured-collections-carousel');
            carousels.forEach(carousel => {
                const container = carousel.querySelector('.snap-x');
                const prevButton = carousel.querySelector('.carousel-prev');
                const nextButton = carousel.querySelector('.carousel-next');

                if (container && prevButton && nextButton) {
                    // Scroll amount - deve adattarsi alla larghezza dell'item visibile
                    // Su mobile, l'item è più stretto (es. w-32 + gap)
                    // Su desktop, è più largo (es. w-72 + gap)
                    // Per l'MVP, potremmo usare uno scroll fisso e vedere l'effetto,
                    // o calcolarlo dinamicamente in base alla larghezza del primo figlio visibile.
                    let scrollAmount = 300; // Default per desktop
                    if (window.innerWidth < 768) { // Breakpoint md di Tailwind
                         const firstMobileItem = container.querySelector('.md\\:hidden');
                         if(firstMobileItem) {
                            scrollAmount = firstMobileItem.offsetWidth + 16; // 16px per il gap-4
                         } else {
                            scrollAmount = 128 + 16; // w-32 (128px) + gap-4 (16px)
                         }
                    } else {
                        const firstDesktopItem = container.querySelector('.md\\:block');
                         if(firstDesktopItem) {
                            scrollAmount = firstDesktopItem.offsetWidth + 24; // 24px per il md:gap-6
                         }
                    }
                    // console.log('Scroll amount set to:', scrollAmount);

                    prevButton.addEventListener('click', () => {
                        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    });
                    nextButton.addEventListener('click', () => {
                        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    });
                }
            });
        });
    </script>
    @endpush
@endonce
