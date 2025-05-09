{{-- resources/views/components/egi-carousel.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Carousel --}}
{{-- Displays a single-item auto-playing carousel of EGIs. --}}
{{-- Expects $validEgis (Eloquent Collection) passed from the component class. --}}
{{-- Uses Alpine.js for interactivity and Tailwind for styling. --}}

{{-- 🎠 Contenitore Principale del Carousel --}}
{{-- @interactivity: Alpine.js gestisce lo stato (slide corrente, autoplay). --}}
{{-- @style: Posizionamento relativo, overflow nascosto per le slide. --}}
<div x-data="{
    activeSlide: 1,
    interval: null,
    totalSlides: {{ $validEgis->count() }},
    autoplayDuration: 5000, // 5 secondi per slide
    nextSlide() {
        this.activeSlide = (this.activeSlide % this.totalSlides) + 1;
    },
    prevSlide() {
        this.activeSlide = (this.activeSlide === 1) ? this.totalSlides : this.activeSlide - 1;
    },
    startAutoplay() {
        this.interval = setInterval(() => {
            this.nextSlide();
        }, this.autoplayDuration);
    },
    stopAutoplay() {
        clearInterval(this.interval);
        this.interval = null;
    },
    init() {
        if (this.totalSlides > 1) {
            this.startAutoplay();
        }
    }
}"
x-init="init()"
@mouseenter="stopAutoplay()"
@mouseleave="startAutoplay()"
class="relative w-full max-w-4xl mx-auto overflow-hidden rounded-xl shadow-2xl border-2 border-white/10 backdrop-blur-lg bg-black/30"
aria-roledescription="carousel"
aria-label="Random EGIs Showcase">

{{-- 🖼️ Contenitore Slide --}}
{{-- @style: Flex container per le slide affiancate. La transizione è gestita da Alpine. --}}
<div class="flex transition-transform duration-500 ease-in-out"
     :style="`transform: translateX(-${(activeSlide - 1) * 100}%)`">

    {{-- 🔄 Loop sulle slide (EGI Validi) --}}
    @foreach ($validEgis as $index => $egi)
        {{-- @style: Ogni slide occupa il 100% della larghezza, non si restringe. --}}
        <div class="w-full flex-shrink-0"
             role="group"
             aria-label="Slide {{ $index + 1 }} of {{ $validEgis->count() }}"
             aria-roledescription="slide">

             {{-- Layout interno della slide (Es: Immagine + Info sotto o sovrapposte) --}}
             <div class="relative aspect-[4/3] bg-black"> {{-- aspect-[4/3] o altro --}}
                 @php
                     $imageUrl = asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s', $egi->collection_id, $egi->user_id, $egi->key_file, $egi->extension));
                 @endphp
                <img src="{{ $imageUrl }}"
                     alt="{{ $egi->title ?? 'EGI Image' }}"
                     class="absolute inset-0 w-full h-full object-contain" loading="lazy">

                {{-- Overlay con informazioni (Opzionale, in basso) --}}
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 via-black/40 to-transparent text-white">
                    <h3 class="text-lg font-semibold truncate mb-0.5" title="{{ $egi->title ?? 'Untitled EGI' }}">
                        <a href="{{ route('egis.show', $egi->id) }}" class="hover:underline">
                            {{ $egi->title ?? 'Untitled EGI' }}
                        </a>
                    </h3>
                    <p class="text-xs text-gray-300 truncate">
                        {{ __('From collection:') }}
                        <a href="{{ route('home.collections.show', $egi->collection->id) }}" class="hover:underline font-medium">
                            {{ $egi->collection->collection_name ?? 'Unnamed Collection' }}
                        </a>
                    </p>
                </div>
             </div>
        </div>
    @endforeach
</div>

{{-- 🕹️ Controlli Carousel (Opzionali: Frecce e/o Pallini) --}}
@if($validEgis->count() > 1)
    {{-- Frecce Precedente/Successivo --}}
    {{-- @style: Posizionate assolutamente, stile bottone semi-trasparente. --}}
    <button @click="prevSlide(); stopAutoplay(); startAutoplay();"
            class="absolute top-1/2 left-2 md:left-4 transform -translate-y-1/2 z-20 p-2 rounded-full bg-black/40 hover:bg-black/60 text-white transition focus:outline-none focus:ring-2 focus:ring-white"
            aria-label="Previous Slide">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
    </button>
    <button @click="nextSlide(); stopAutoplay(); startAutoplay();"
            class="absolute top-1/2 right-2 md:right-4 transform -translate-y-1/2 z-20 p-2 rounded-full bg-black/40 hover:bg-black/60 text-white transition focus:outline-none focus:ring-2 focus:ring-white"
            aria-label="Next Slide">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    </button>

    {{-- Pallini di Navigazione (Indicatori) --}}
    {{-- @style: Posizionati assolutamente in basso al centro. --}}
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-30 flex space-x-2">
        @foreach ($validEgis as $index => $egi)
            <button @click="activeSlide = {{ $index + 1 }}; stopAutoplay(); startAutoplay();"
                    :class="{'bg-white': activeSlide === {{ $index + 1 }}, 'bg-white/40 hover:bg-white/70': activeSlide !== {{ $index + 1 }} }"
                    class="h-2 w-2 rounded-full transition duration-150 ease-in-out"
                    :aria-label="`Go to slide {{ $index + 1 }}`"
                    :aria-current="activeSlide === {{ $index + 1 }} ? 'true' : 'false'">
            </button>
        @endforeach
    </div>
@endif

</div>

{{-- Assicurati di includere Alpine.js nel tuo layout principale o app.js --}}
{{-- Se non lo hai già: <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
