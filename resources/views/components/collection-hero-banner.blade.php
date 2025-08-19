@props([
'collections' => collect(),
'autoplayInterval' => 8000,
'componentElementId' => null
])

@php
$instanceId = $attributes->get('id', $componentElementId ?: 'chb_'.uniqid());
$logo = "15.jpg";
$defaultBannerUrl = asset("images/default/random_background/$logo");

if (is_array($collections)) {
$collections = collect($collections);
}
$hasCollections = $collections->isNotEmpty();
$firstCollection = $hasCollections ? $collections->first() : null;

// ... ($jsCollectionsData come definito precedentemente) ...
$jsCollectionsData = [];
if ($hasCollections) {
$jsCollectionsData = $collections->map(function($c) use($logo) {
$creatorName = $c->creator ? $c->creator->name : null;
$bannerPath = $c->image_banner;

// Calcola le statistiche per ogni collezione DINAMICAMENTE
$egisCount = $c->egis()->count();
$likesCount = $c->likes()->count();
$reservationsCount = $c->egis()
->whereHas('reservations', function($query) {
$query->where('is_current', true)->where('status', 'active');
})
->count();

$totalReservationsValue = $c->egis()
->whereHas('reservations', function($query) {
$query->where('is_current', true)->where('status', 'active');
})
->with(['reservations' => function($query) {
$query->where('is_current', true)->where('status', 'active');
}])
->get()
->sum(function($egi) {
return $egi->reservations->sum('offer_amount_fiat');
});

return [
'id' => $c->id,
'name' => $c->collection_name ?? '',
'creator' => $creatorName ?: __('guest_home.unknown_artist'),
'banner' => $bannerPath ? asset($bannerPath) : asset("images/default/random_background/$logo"),
'stats' => [
'egis' => $egisCount,
'likes' => $likesCount,
'reserved' => $reservationsCount,
'volume' => $totalReservationsValue
]
];
})->values()->all();
}
@endphp

<div class="relative w-full overflow-hidden hero-banner-container" style="height: 35vh; max-height: 700px;"
    id="heroBannerContainer_{{ $instanceId }}">

    {{-- Desktop: Banner con background-image --}}
    <div class="absolute inset-0 hidden transition-opacity duration-700 ease-in-out bg-center bg-cover hero-banner-background md:block"
        id="heroBannerBackground_{{ $instanceId }}"
        style="background-image: url('{{ $hasCollections && $firstCollection && $firstCollection->image_banner ? asset($firstCollection->image_banner) : $defaultBannerUrl }}')">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
        <div class="absolute inset-0 opacity-75 bg-gradient-to-r from-black/50 via-transparent to-transparent"></div>
    </div>

    {{-- Mobile: Carousel con immagini scrollabili --}}
    <div class="absolute inset-0 z-[60] md:hidden" id="mobileImageCarousel_{{ $instanceId }}">
        <div class="flex w-full h-full overflow-x-auto snap-x snap-mandatory scrollbar-hide"
            id="mobileCarouselTrack_{{ $instanceId }}">
            @if($hasCollections)
            @foreach($collections as $index => $collection)
            <div class="relative flex-shrink-0 w-full h-full snap-start">
                <img src="{{ $collection->image_banner ? asset($collection->image_banner) : $defaultBannerUrl }}"
                    alt="{{ $collection->collection_name ?? '' }}" class="object-cover w-full h-full">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
                <div class="absolute inset-0 opacity-75 bg-gradient-to-r from-black/50 via-transparent to-transparent">
                </div>
            </div>
            @endforeach
            @else
            <div class="relative flex-shrink-0 w-full h-full snap-start">
                <img src="{{ $defaultBannerUrl }}" alt="Default background" class="object-cover w-full h-full">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
                <div class="absolute inset-0 opacity-75 bg-gradient-to-r from-black/50 via-transparent to-transparent">
                </div>\
            </div>
            @endif
        </div>
    </div>

    {{-- CTA Ambientale - CENTRATA COMPLETAMENTE --}}
    <div class="absolute inset-0 z-[70] flex items-center justify-center pointer-events-none">
        <p
            class="max-w-2xl px-8 text-lg font-bold leading-tight text-center text-green-300 pointer-events-none sm:text-xl md:text-4xl lg:text-5xl">
            {{ __('guest_home.hero_banner_cta') }}
        </p>
    </div>

    <!-- Contenuto Hero -->
    <div
        class="absolute inset-0 z-[70] flex flex-col justify-between p-4 text-white sm:p-6 md:p-8 lg:p-10 md:z-auto pointer-events-none md:pointer-events-auto">
        {{-- z-20 su mobile, z-auto su desktop padding mobile p-4 sm:p-6 --}}
        {{-- Riga Superiore: Titolo, Creator, Indicatori --}}
        <div
            class="flex flex-col items-center w-full gap-4 text-center md:text-left md:flex-row md:items-start md:justify-between">
            {{-- ^ MODIFICHE PRINCIPALI QUI:
            Default (mobile): flex-col items-center text-center
            Da md in su: md:text-left md:flex-row md:items-start md:justify-between
            --}}

            <!-- Titolo e creator info -->
            <div class="max-w-xl pointer-events-none md:pointer-events-auto"> {{-- Trasparente ai touch su mobile --}}
                <h1 class="text-sm font-bold sm:text-3xl md:text-4xl lg:text-5xl font-display"
                    id="collectionName_{{ $instanceId }}"> {{-- Ridotta molto dimensione font mobile per banner 30vh
                    --}}
                    {{ __('guest_home.hero_banner_title') }}
                </h1>
                <p class="mt-1 text-xs opacity-90 sm:text-lg md:text-xl font-body"
                    id="collectionSubText_{{ $instanceId }}"> {{-- Ridotta dimensione font base mobile --}}
                    @if($hasCollections && $firstCollection)
                    {{ $firstCollection->collection_name }} {{ __('guest_home.by') }} {{
                    $firstCollection->creator?->name ?: __('guest_home.unknown_artist') }}
                    @else
                    {{ __('guest_home.hero_banner_subtitle') }}
                    @endif
                </p>
            </div>

            {{-- Indicatori di scorrimento (pallini) - NASCOSTI su mobile --}}
            @if($collections->count() > 1)
            {{-- Su mobile, questo div sarà NASCOSTO.
            Su md+, md:self-start lo allinea all'inizio del contenitore flex laterale (a destra).
            Aggiunto md:mt-0 per resettare il margine su schermi più grandi se il titolo è corto.
            --}}
            <div class="items-center hidden p-2 mt-4 space-x-2 rounded-full pointer-events-auto md:flex md:mt-0 md:self-start bg-black/30 backdrop-blur-sm"
                id="slideIndicators_{{ $instanceId }}">
                @foreach($collections as $index => $collection)
                <button data-index="{{ $index }}"
                    aria-label="{{ __('guest_home.go_to_slide', ['index' => $index + 1]) }}"
                    class="slide-indicator w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/75' }} pointer-events-auto">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Riga Inferiore: Statistiche stile OpenSea centrate --}}
        <div class="flex flex-col items-center justify-center w-full gap-4 sm:gap-6">
            {{-- Statistiche OpenSea SOVRAPPOSTE nel banner - 4 NOSTRE STATISTICHE --}}
            @if($hasCollections && $firstCollection)
            <div class="p-4 bg-black border rounded-lg backdrop-blur-sm border-white/10 opacity-70">
                <div class="flex divide-x divide-white/20">
                    <div class="pr-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">EGIS</div>
                        <div class="text-white" style="font-size: 8px;" id="statEgis_{{ $instanceId }}">{{
                            $firstCollection->egis()->count() }}</div>
                    </div>
                    <div class="px-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">LIKES</div>
                        <div class="text-white" style="font-size: 8px;" id="statLikes_{{ $instanceId }}">{{
                            $firstCollection->likes()->count() }}</div>
                    </div>
                    <div class="px-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">RESERVED</div>
                        <div class="text-white" style="font-size: 8px;" id="statReserved_{{ $instanceId }}">{{
                            $firstCollection->egis()->whereHas('reservations', function($query) {
                            $query->where('is_current', true)->where('status', 'active');
                            })->count() }}</div>
                    </div>
                    <div class="pl-6">
                        <div class="text-xs font-medium tracking-wider text-gray-300 uppercase">VOLUME</div>
                        @php

                        $totalReservationsValue = $firstCollection->egis()
                        ->whereHas('reservations', function($query) {
                        $query->where('is_current', true)->where('status', 'active');
                        })
                        ->with(['reservations' => function($query) {
                        $query->where('is_current', true)->where('status', 'active');
                        }])
                        ->get()
                        ->sum(function($egi) {
                        return $egi->reservations->sum('offer_amount_fiat');
                        });
                        @endphp
                        @if($totalReservationsValue > 0)
                        <div class="text-white" style="font-size: 8px;" id="statVolume_{{ $instanceId }}">€ {{
                            number_format($totalReservationsValue, 2) }}</div>
                        @else
                        <div class="text-white" style="font-size: 8px;" id="statVolume_{{ $instanceId }}">€ 0.00</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            <!-- Pulsanti di navigazione (prev/next) - COMMENTATI -->
            {{-- @if($collections->count() > 1)
            <div class="flex order-2 space-x-3 pointer-events-auto md:order-1">
                <button id="prevSlide_{{ $instanceId }}" aria-label="{{ __('guest_home.previous_slide') }}"
                    class="p-2 text-white transition-colors duration-300 rounded-full pointer-events-auto sm:p-3 bg-black/40 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button id="nextSlide_{{ $instanceId }}" aria-label="{{ __('guest_home.next_slide') }}"
                    class="p-2 text-white transition-colors duration-300 rounded-full pointer-events-auto sm:p-3 bg-black/40 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            @else
            <div class="order-2 md:order-1"></div>
            @endif --}}


        </div>
    </div>
</div>


{{-- Script --}}
<script>
    // Log per indicare che il blocco script è stato parsato
    // console.log('HERO BANNER SCRIPT BLOCK PARSED - Instance ID: {{ $instanceId }}');

    document.addEventListener('DOMContentLoaded', function() {
        const componentId = "{{ $instanceId }}";
        const heroBannerContainer = document.getElementById('heroBannerContainer_' + componentId);

        if (!heroBannerContainer) {
            console.error('CRITICAL: Hero Banner Container (heroBannerContainer_' + componentId + ') not found.');
            return;
        }

        const collectionsData = @json($jsCollectionsData);

        if (!collectionsData || !Array.isArray(collectionsData)) {
            console.warn('Collections data is not a valid array or is missing for component ID:', componentId);
            return;
        }

        // Elementi del DOM
        const bannerBackground = document.getElementById('heroBannerBackground_' + componentId); // Desktop only
        const mobileCarouselTrack = document.getElementById('mobileCarouselTrack_' + componentId); // Mobile only
        const collectionSubTextElement = document.getElementById('collectionSubText_' + componentId);
        const reserveButton = document.getElementById('reserveButton_' + componentId);
        const prevButton = document.getElementById('prevSlide_' + componentId);
        const nextButton = document.getElementById('nextSlide_' + componentId);
        const slideIndicatorsContainer = document.querySelector('#heroBannerContainer_' + componentId + ' .slide-indicators');
        const slideIndicators = slideIndicatorsContainer ? slideIndicatorsContainer.querySelectorAll('[data-index]') : [];

        const totalCollections = collectionsData.length;
        const autoplayInterval = {{ $autoplayInterval }};
        let currentIndex = 0;
        let autoScrollInterval = null;
        let isMobile = window.innerWidth < 768; // md breakpoint

        // Funzione per rilevare se siamo su mobile
        function checkIfMobile() {
            isMobile = window.innerWidth < 768;
        }

        // Update in base a desktop/mobile
        function updateBannerContent() {
            if (totalCollections === 0) return;

            const currentCollection = collectionsData[currentIndex];
            if (!currentCollection) return;

            if (isMobile && mobileCarouselTrack) {
                // Mobile: scroll nativo
                const slideWidth = mobileCarouselTrack.offsetWidth;
                mobileCarouselTrack.scrollTo({
                    left: currentIndex * slideWidth,
                    behavior: 'smooth'
                });
            } else if (!isMobile && bannerBackground) {
                // Desktop: background image transition
                bannerBackground.style.opacity = '0.3';
                setTimeout(() => {
                    bannerBackground.style.backgroundImage = `url('${currentCollection.banner}')`;
                    bannerBackground.style.opacity = '1';
                }, 350);
            }

            // Aggiorna contenuto testuale
            if (collectionSubTextElement) {
                collectionSubTextElement.textContent = `${currentCollection.name} {{ __('guest_home.by') }} ${currentCollection.creator}`;
            }

            // Aggiorna statistiche dinamicamente
            const statEgis = document.getElementById('statEgis_' + componentId);
            const statLikes = document.getElementById('statLikes_' + componentId);
            const statReserved = document.getElementById('statReserved_' + componentId);
            const statVolume = document.getElementById('statVolume_' + componentId);

            if (currentCollection.stats) {
                if (statEgis) statEgis.textContent = currentCollection.stats.egis || 0;
                if (statLikes) statLikes.textContent = currentCollection.stats.likes || 0;
                if (statReserved) statReserved.textContent = currentCollection.stats.reserved || 0;
                if (statVolume) {
                    const volume = currentCollection.stats.volume || 0;
                    statVolume.textContent = volume > 0 ?
                        '€ ' + new Intl.NumberFormat('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(volume) :
                        '€ 0.00';
                }
            }

            if (reserveButton) {
                reserveButton.dataset.egiId = currentCollection.id;
                reserveButton.dataset.collectionName = currentCollection.name;
                reserveButton.disabled = false;
            }

            // Aggiorna indicatori
            if (slideIndicators && slideIndicators.length > 0) {
                slideIndicators.forEach((indicator, index) => {
                    indicator.classList.toggle('bg-white', index === currentIndex);
                    indicator.classList.toggle('scale-125', index === currentIndex);
                    indicator.classList.toggle('bg-white/50', index !== currentIndex);
                    indicator.classList.toggle('hover:bg-white/75', index !== currentIndex);
                });
            }
        }

        function goToSlide(index) {
            if (isNaN(index) || index < 0 || index >= totalCollections) {
                console.warn('goToSlide: Invalid index received:', index);
                return;
            }
            currentIndex = index;
            updateBannerContent();
            if (totalCollections > 1) resetAutoScroll();
        }

        function cycleSlide(direction) {
            if (totalCollections <= 1) return;
            let newIndex = currentIndex + direction;
            if (newIndex < 0) newIndex = totalCollections - 1;
            else if (newIndex >= totalCollections) newIndex = 0;
            goToSlide(newIndex);
        }

        function startAutoScroll() {
            clearInterval(autoScrollInterval);
            if (totalCollections > 1 && autoplayInterval > 0) {
                autoScrollInterval = setInterval(() => cycleSlide(1), autoplayInterval);
            }
        }

        function resetAutoScroll() {
            clearInterval(autoScrollInterval);
            startAutoScroll();
        }

        // Event listeners per i bottoni
        if (prevButton && nextButton && totalCollections > 1) {
            prevButton.addEventListener('click', () => cycleSlide(-1));
            nextButton.addEventListener('click', () => cycleSlide(1));
        } else {
            if (totalCollections <= 1) {
                if(prevButton) prevButton.style.display = 'none';
                if(nextButton) nextButton.style.display = 'none';
                if(slideIndicatorsContainer) slideIndicatorsContainer.style.display = 'none';
            }
        }

        // Event listeners per gli indicatori
        if (slideIndicators.length > 0 && totalCollections > 1) {
            slideIndicators.forEach((indicator) => {
                indicator.addEventListener('click', () => {
                    const indexVal = indicator.dataset.index;
                    const parsedIndex = parseInt(indexVal);
                    goToSlide(parsedIndex);
                });
            });
        } else {
             if(slideIndicatorsContainer) slideIndicatorsContainer.style.display = 'none';
        }

        // Gestione del mobile swipe su scroll nativo
        if (isMobile && mobileCarouselTrack && totalCollections > 1) {
            let isScrolling = false;

            mobileCarouselTrack.addEventListener('scroll', () => {
                if (isScrolling) return;
                isScrolling = true;

                requestAnimationFrame(() => {
                    const slideWidth = mobileCarouselTrack.offsetWidth;
                    const scrollLeft = mobileCarouselTrack.scrollLeft;
                    const newIndex = Math.round(scrollLeft / slideWidth);

                    if (newIndex !== currentIndex && newIndex >= 0 && newIndex < totalCollections) {
                        currentIndex = newIndex;
                        // Aggiorna solo il testo e gli indicatori, non lo scroll (già fatto dall'utente)
                        const currentCollection = collectionsData[currentIndex];
                        if (collectionSubTextElement && currentCollection) {
                            collectionSubTextElement.textContent = `${currentCollection.name} {{ __('guest_home.by') }} ${currentCollection.creator}`;
                        }

                        // Aggiorna statistiche anche per lo swipe manuale
                        const statEgis = document.getElementById('statEgis_' + componentId);
                        const statLikes = document.getElementById('statLikes_' + componentId);
                        const statReserved = document.getElementById('statReserved_' + componentId);
                        const statVolume = document.getElementById('statVolume_' + componentId);

                        if (currentCollection.stats) {
                            if (statEgis) statEgis.textContent = currentCollection.stats.egis || 0;
                            if (statLikes) statLikes.textContent = currentCollection.stats.likes || 0;
                            if (statReserved) statReserved.textContent = currentCollection.stats.reserved || 0;
                            if (statVolume) {
                                const volume = currentCollection.stats.volume || 0;
                                statVolume.textContent = volume > 0 ?
                                    '€ ' + new Intl.NumberFormat('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(volume) :
                                    '€ 0.00';
                            }
                        }

                        if (reserveButton && currentCollection) {
                            reserveButton.dataset.egiId = currentCollection.id;
                            reserveButton.dataset.collectionName = currentCollection.name;
                        }
                        // Aggiorna indicatori
                        if (slideIndicators && slideIndicators.length > 0) {
                            slideIndicators.forEach((indicator, index) => {
                                indicator.classList.toggle('bg-white', index === currentIndex);
                                indicator.classList.toggle('scale-125', index === currentIndex);
                                indicator.classList.toggle('bg-white/50', index !== currentIndex);
                                indicator.classList.toggle('hover:bg-white/75', index !== currentIndex);
                            });
                        }
                        resetAutoScroll();
                    }
                    isScrolling = false;
                });
            });
        }

        // Gestione resize per rilevare cambio desktop/mobile
        window.addEventListener('resize', () => {
            checkIfMobile();
            updateBannerContent();
        });

        // Gestione keyboard e mouse events (solo se non mobile o per contenuto generale)
        if (totalCollections > 1 && heroBannerContainer) {
            heroBannerContainer.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') cycleSlide(-1);
                else if (e.key === 'ArrowRight') cycleSlide(1);
            });
            heroBannerContainer.addEventListener('mouseenter', () => {
                clearInterval(autoScrollInterval);
            });
            heroBannerContainer.addEventListener('mouseleave', () => {
                startAutoScroll();
            });
        }

        // Inizializzazione
        if (collectionsData.length > 0) {
            updateBannerContent();
            if (totalCollections > 1) {
                startAutoScroll();
            }
        }
    });
</script>
