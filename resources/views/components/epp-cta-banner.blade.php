{{-- resources/views/components/epp-cta-banner.blade.php --}}
@props([
    'title' => __('guest_home.epp_banner_title'), // "Il Nostro Impegno per il Pianeta"
    'subtitle' => __('guest_home.epp_banner_subtitle'), // "Ogni opera su FlorenceEGI contribuisce attivamente alla protezione e al ripristino ambientale."
    'message' => __('guest_home.epp_banner_message'), // "Collaboriamo con Programmi di Protezione Ambientale verificati per garantire che una parte significativa di ogni transazione generi un cambiamento positivo e misurabile. La trasparenza è al centro del nostro operato: segui il flusso dei contributi e osserva i risultati."
    'ctaText' => __('guest_home.epp_banner_cta'), // "Scopri i Programmi Supportati"
    'ctaLink' => null, // Verrà passato da home.blade.php (route('epps.index'))
    'backgroundImage' => asset('images/default/epp_banner_background.png'), // Immagine di sfondo a tema ambientale/speranza
    'heightClass' => 'min-h-[50vh] md:min-h-[60vh]', // Altezza del banner
    'overlayColor' => 'bg-black/60' // Overlay per leggibilità testo
])

<section class="relative w-full overflow-hidden {{ $heightClass }}" aria-labelledby="epp-banner-title-{{ Str::slug($title) }}" style="height: 50vh; min-height: 450px; max-height: 700px;">
    <!-- Banner Background -->
    <div class="absolute inset-0 bg-center bg-cover"
         style="background-image: url('{{ $backgroundImage }}');">
        <div class="absolute inset-0 {{ $overlayColor }}"></div> {{-- Overlay scuro --}}
    </div>

    <!-- Contenuto del Banner -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full px-4 py-12 text-center text-white sm:px-6 lg:px-8">
        <span class="mb-4 text-6xl material-symbols-outlined text-verde-rinascita" aria-hidden="true">
            eco {{-- O "public", "verified_user", "savings" --}}
        </span>

        <h2 id="epp-banner-title-{{ Str::slug($title) }}" class="mb-4 text-3xl font-bold md:text-4xl lg:text-5xl font-display">
            {{ $title }}
        </h2>
        <p class="max-w-3xl mb-6 text-lg opacity-90 md:text-xl font-body">
            {{ $subtitle }}
        </p>
        <p class="max-w-2xl mb-10 text-base opacity-80 font-body">
            {{ $message }}
        </p>

        @if($ctaLink)
        <a href="{{ $ctaLink }}"
           class="inline-flex items-center justify-center px-10 py-3 text-base font-bold transition-colors duration-300 border-2 rounded-md shadow-sm border-verde-rinascita focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-verde-rinascita text-verde-rinascita hover:bg-verde-rinascita hover:text-verde-rinascita-text">
            <span class="mr-2 -ml-1 material-symbols-outlined" aria-hidden="true">forest</span>
            <span>{{ $ctaText }}</span>
        </a>
        @endif
    </div>
</section>
