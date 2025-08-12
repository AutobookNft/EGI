@props([
'collections' => collect(),
'autoplayInterval' => 8000,
'componentElementId' => null
])

@php
$instanceId = $attributes->get('id', $componentElementId ?: 'chb_'.uniqid());
$logo = "15.jpg";
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
return [
'id' => $c->id,
'name' => $c->collection_name ?? '',
'creator' => $creatorName ?: __('guest_home.unknown_artist'),
'banner' => $bannerPath ? asset($bannerPath) : asset("images/default/random_background/$logo"),
];
})->values()->all();
}
@endphp


<div class="relative w-full overflow-hidden hero-banner-container"
    style="height: 60vh; min-height: 450px; max-height: 700px;" id="heroBannerContainer_{{ $instanceId }}">

    {{-- ... (div hero-banner-background come prima) ... --}}
    <div class="absolute inset-0 transition-opacity duration-700 ease-in-out bg-center bg-cover hero-banner-background"
        id="heroBannerBackground_{{ $instanceId }}"
        style="background-image: url('{{ $hasCollections && $firstCollection && $firstCollection->image_banner ? asset($firstCollection->image_banner) : asset("
        images/default/random_background/$logo") }}')">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
        <div class="absolute inset-0 opacity-75 bg-gradient-to-r from-black/50 via-transparent to-transparent"></div>
    </div>

    {{-- CTA Ambientale - CENTRATA COMPLETAMENTE --}}
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <p
            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-green-300 font-bold max-w-4xl leading-tight text-center px-8">
            {{ __('guest_home.hero_banner_cta') }}
        </p>
    </div>

    <!-- Contenuto Hero -->
    <div class="absolute inset-0 flex flex-col justify-between p-4 text-white sm:p-6 md:p-8 lg:p-10"> {{-- Ridotto
        padding mobile p-4 sm:p-6 --}}
        {{-- Riga Superiore: Titolo, Creator, Indicatori --}}
        <div
            class="flex flex-col items-center w-full gap-4 text-center md:text-left md:flex-row md:items-start md:justify-between">
            {{-- ^ MODIFICHE PRINCIPALI QUI:
            Default (mobile): flex-col items-center text-center
            Da md in su: md:text-left md:flex-row md:items-start md:justify-between
            --}}

            <!-- Titolo e creator info -->
            <div class="max-w-xl"> {{-- max-w-xl è già restrittivo, va bene per il centro --}}
                <h1 class="text-2xl font-bold sm:text-3xl md:text-4xl lg:text-5xl font-display"
                    id="collectionName_{{ $instanceId }}"> {{-- Ridotta dimensione font base mobile --}}
                    {{ __('guest_home.hero_banner_title') }}
                </h1>
                <p class="mt-2 text-base opacity-90 sm:text-lg md:text-xl font-body"
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
            <div class="hidden md:flex items-center p-2 mt-4 space-x-2 rounded-full md:mt-0 md:self-start bg-black/30 backdrop-blur-sm"
                id="slideIndicators_{{ $instanceId }}">
                @foreach($collections as $index => $collection)
                <button data-index="{{ $index }}"
                    aria-label="{{ __('guest_home.go_to_slide', ['index' => $index + 1]) }}"
                    class="slide-indicator w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/75' }}">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Riga Inferiore: Navigazione, Pulsante Reserve --}}
        {{-- Le classi qui dovrebbero già centrare bene su mobile: flex-col items-center --}}
        <div class="flex flex-col items-center w-full gap-4 sm:gap-6 md:flex-row md:items-end md:justify-between">
            <!-- Pulsanti di navigazione (prev/next) -->
            @if($collections->count() > 1)
            <div class="flex order-2 space-x-3 md:order-1">
                {{-- ... (codice bottoni prev/next come prima) ... --}}
                <button id="prevSlide_{{ $instanceId }}" aria-label="{{ __('guest_home.previous_slide') }}"
                    class="p-2 text-white transition-colors duration-300 rounded-full sm:p-3 bg-black/40 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button id="nextSlide_{{ $instanceId }}" aria-label="{{ __('guest_home.next_slide') }}"
                    class="p-2 text-white transition-colors duration-300 rounded-full sm:p-3 bg-black/40 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            @else
            <div class="order-2 md:order-1"></div> {{-- Placeholder per mantenere layout --}}
            @endif

            <!-- Pulsante Reserve -->
            {{-- @include('partials.collection-hero-banner-reserve-button', [
            'instanceId' => $instanceId,
            'hasCollections' => $hasCollections,
            'firstCollection' => $firstCollection
            ]) --}}

        </div>
    </div>
</div>


{{-- Script --}}
<script>
    // Log per indicare che il blocco script è stato parsato
    // console.log('HERO BANNER SCRIPT BLOCK PARSED - Instance ID: {{ $instanceId }}');

    document.addEventListener('DOMContentLoaded', function() {
        // Log per indicare che DOMContentLoaded si è attivato per questo script
        // console.log('HERO BANNER DOMCONTENTLOADED FIRED - Instance ID: {{ $instanceId }}');

        const componentId = "{{ $instanceId }}";
        // console.log('Component Initializing with JS componentId:', componentId);

        const heroBannerContainer = document.getElementById('heroBannerContainer_' + componentId);

        if (!heroBannerContainer) {
            console.error('CRITICAL: Hero Banner Container (heroBannerContainer_' + componentId + ') not found.');
            return;
        }
        // console.log('Hero Banner Container FOUND:', heroBannerContainer);

        const collectionsData = @json($jsCollectionsData);

        if (!collectionsData || !Array.isArray(collectionsData)) { // Controllo più robusto
            console.warn('Collections data is not a valid array or is missing for component ID:', componentId);
            // Nascondi controlli se non ci sono dati validi
            const elToHideOnError = ['prevSlide_', 'nextSlide_', 'slideIndicators_'];
            elToHideOnError.forEach(prefix => {
                const el = document.getElementById(prefix + componentId);
                if (el) el.style.display = 'none';
            });
            // Potresti anche voler aggiornare il banner a uno stato di "nessuna collezione"
             const bannerBgErr = document.getElementById('heroBannerBackground_' + componentId);
             const subTextErr = document.getElementById('collectionSubText_' + componentId);
             const reserveBtnErr = document.getElementById('reserveButton_' + componentId);
             if(bannerBgErr) bannerBgErr.style.backgroundImage = `url('{{ asset('images/default/banner_placeholder.jpg') }}')`;
             if(subTextErr) subTextErr.textContent = '{{ __('guest_home.no_collections_available') }}';
             if(reserveBtnErr) reserveBtnErr.disabled = true;
            return;
        }
        // console.log('Collections Data Loaded:', collectionsData.length, collectionsData);

        let currentIndex = 0;
        let autoScrollInterval;
        const autoplayInterval = {{ $autoplayInterval }}; // Preso dalle props
        const totalCollections = collectionsData.length;
        // console.log('Total Collections:', totalCollections);

        const bannerBackground = document.getElementById('heroBannerBackground_' + componentId);
        const collectionSubTextElement = document.getElementById('collectionSubText_' + componentId);
        const reserveButton = document.getElementById('reserveButton_' + componentId);
        const slideIndicatorsContainer = document.getElementById('slideIndicators_' + componentId);
        const slideIndicators = slideIndicatorsContainer ? slideIndicatorsContainer.querySelectorAll('.slide-indicator') : [];
        const prevButton = document.getElementById('prevSlide_' + componentId);
        const nextButton = document.getElementById('nextSlide_' + componentId);

        // === INIZIO DEFINIZIONE FUNZIONI HELPER ===
        function updateBannerContent() {
            // console.log('updateBannerContent called. Current index:', currentIndex);
            if (!collectionsData[currentIndex]) {
                console.warn('updateBannerContent: No data for current index', currentIndex);
                if(bannerBackground) bannerBackground.style.backgroundImage = `url('{{ asset('images/default/banner_placeholder.jpg') }}')`;
                if(collectionSubTextElement) collectionSubTextElement.textContent = '{{ __('guest_home.no_collections_available') }}';
                if(reserveButton) reserveButton.disabled = true;
                return;
            }
            const currentCollection = collectionsData[currentIndex];

            if (bannerBackground) {
                bannerBackground.style.opacity = '0.7'; // Inizia transizione
                setTimeout(() => {
                    bannerBackground.style.backgroundImage = `url('${currentCollection.banner}')`;
                    bannerBackground.style.opacity = '1'; // Fine transizione
                }, 350); // Metà della durata css transition-opacity (che è 700ms)
            }

            if (collectionSubTextElement) {
                collectionSubTextElement.textContent = `${currentCollection.name} {{ __('guest_home.by') }} ${currentCollection.creator}`;
            }

            if (reserveButton) {
                reserveButton.dataset.egiId = currentCollection.id;
                reserveButton.dataset.collectionName = currentCollection.name;
                reserveButton.disabled = false;
            }

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
            // console.log('goToSlide called with index:', index);
            if (isNaN(index) || index < 0 || index >= totalCollections) {
                console.warn('goToSlide: Invalid index received:', index);
                return;
            }
            currentIndex = index;
            updateBannerContent();
            if (totalCollections > 1) resetAutoScroll();
        }

        function cycleSlide(direction) {
            // console.log('cycleSlide called with direction:', direction);
            if (totalCollections <= 1) return;
            let newIndex = currentIndex + direction;
            if (newIndex < 0) newIndex = totalCollections - 1;
            else if (newIndex >= totalCollections) newIndex = 0;
            goToSlide(newIndex);
        }

        function startAutoScroll() {
            // console.log('startAutoScroll called. Autoplay interval:', autoplayInterval);
            clearInterval(autoScrollInterval);
            if (totalCollections > 1 && autoplayInterval > 0) {
                autoScrollInterval = setInterval(() => cycleSlide(1), autoplayInterval);
            }
        }

        function resetAutoScroll() {
            // console.log('resetAutoScroll called');
            clearInterval(autoScrollInterval);
            startAutoScroll();
        }
        // === FINE DEFINIZIONE FUNZIONI HELPER ===

        // Aggancio event listener
        if (prevButton && nextButton && totalCollections > 1) {
            // console.log('Attaching listeners to Prev/Next buttons.');
            prevButton.addEventListener('click', () => { /* console.log('Prev button clicked'); */ cycleSlide(-1); });
            nextButton.addEventListener('click', () => { /* console.log('Next button clicked'); */ cycleSlide(1); });
        } else {
            if (totalCollections <= 1) { // Nascondi se non servono
                if(prevButton) prevButton.style.display = 'none';
                if(nextButton) nextButton.style.display = 'none';
                if(slideIndicatorsContainer) slideIndicatorsContainer.style.display = 'none';
            }
        }

        if (slideIndicators.length > 0 && totalCollections > 1) {
            // console.log('Attaching listeners to Slide Indicators.');
            slideIndicators.forEach((indicator) => {
                indicator.addEventListener('click', () => {
                    const indexVal = indicator.dataset.index;
                    const parsedIndex = parseInt(indexVal);
                    // console.log('Indicator clicked. Data-index:', indexVal, 'Parsed index:', parsedIndex);
                    goToSlide(parsedIndex);
                });
            });
        } else {
             if(slideIndicatorsContainer) slideIndicatorsContainer.style.display = 'none';
        }

        if (totalCollections > 1 && heroBannerContainer) {
            heroBannerContainer.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') cycleSlide(-1);
                else if (e.key === 'ArrowRight') cycleSlide(1);
            });
            heroBannerContainer.addEventListener('mouseenter', () => { /* console.log('Mouse entered, clearing interval'); */ clearInterval(autoScrollInterval); });
            heroBannerContainer.addEventListener('mouseleave', () => { /* console.log('Mouse left, restarting autoscroll'); */ startAutoScroll(); });
        }

        // Chiamata iniziale e autoscroll
        if (collectionsData.length > 0) { // Modificato da totalCollections a collectionsData.length per coerenza
            // console.log('Initial call to updateBannerContent for index:', currentIndex);
            updateBannerContent();
            if (totalCollections > 1) {
                startAutoScroll();
            }
        } else {
            // Questo blocco è ridondante se il controllo all'inizio dello script nasconde già tutto
            // console.warn("No collections to display, banner content not updated (final check).");
        }
    });
</script>
