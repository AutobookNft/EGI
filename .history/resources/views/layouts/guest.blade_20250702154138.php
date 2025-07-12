<!DOCTYPE html>

    @include('layouts.partials.header')

    {{-- @include('layouts.guest_script') --}}

    <!-- Hero Section -->
    @unless(isset($noHero) && $noHero)
        <section id="hero-section" class="relative flex flex-col items-center overflow-hidden min-h-[100vh]" aria-labelledby="hero-main-title">

            <h1 id="hero-main-title" class="sr-only">{{ $title ?? __('guest_layout.default_title') }}</h1>

            @isset($heroFullWidth)
                {{-- Layout a colonna intera --}}
                <div class="relative z-10 w-full px-4 mx-auto mt-auto mb-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $heroFullWidth }}
                </div>

                <div class="natan-assistant fixed bottom-6 right-6 z-[100000] flex flex-col items-end" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                    {{ $heroNatanAssistant ?? '' }}
                </div>

            @else
                {{-- Layout a colonne esistente (NON USATO DALLA HOME ATTUALE) --}}
                <div class="container relative z-10 flex flex-col items-center w-full px-4 mx-auto hero-content-overlay sm:px-6 lg:px-8">
                    <div class="grid w-full grid-cols-1 gap-8 mb-10 md:grid-cols-12 md:mb-12 lg:gap-6">
                    {{-- Colonna mobile: sinistra+destra in un contenitore--}}
                    <div class="flex flex-col items-center gap-8 md:hidden">
                        {{-- Colonna sinistra (Natan) --}}
                        <div class="w-full" role="region" aria-label="{{ __('guest_layout.hero_left_content_aria_label') }}">
                            {{ $heroContentLeft ?? '' }}
                        </div>

                        {{-- Colonna destra (Badge impatto) --}}
                        <div class="relative z-10 w-full" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                            {{ $heroContentRight ?? '' }}
                        </div>
                    </div>

                    {{-- Colonna sinistra (25%) - Visibile solo su tablet+ --}}
                    <div class="hidden md:block md:col-span-3" role="region" aria-label="{{ __('guest_layout.hero_left_content_aria_label') }}">
                        {{ $heroContentLeft ?? '' }}
                    </div>

                    {{-- Colonna centrale: Carousel (50% su desktop, 100% su tablet) --}}
                    <div class="flex flex-col items-center justify-center w-full md:col-span-12 lg:col-span-6" role="region" aria-label="{{ __('guest_layout.hero_carousel_aria_label') }}">
                        {{ $heroCarousel ?? '' }}
                    </div>

                    {{-- Colonna destra (25%) - Visibile solo su tablet+ --}}
                    <div class="relative z-10 w-full" role="region" aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                        {{ $heroContentRight ?? '' }}
                    </div>

                    {{-- Layout alternativo tablet: due colonne laterali affiancate sotto il carousel --}}
                    <div class="hidden md:grid md:grid-cols-2 md:gap-6 md:col-span-12 lg:hidden">
                        {{-- Colonna sinistra (Natan) - Ripetuta per layout tablet --}}
                        <div class="flex items-center" role="region" aria-label="{{ __('guest_layout.hero_left_content_tablet_aria_label') }}">
                            {{ $heroContentLeft ?? '' }}
                        </div>

                        {{-- Colonna destra (Badge impatto) - Ripetuta per layout tablet --}}
                        <div class="flex items-center" role="region" aria-label="{{ __('guest_layout.hero_right_content_tablet_aria_label') }}">
                            {{ $heroContentRight ?? '' }}
                        </div>
                    </div>
                </div>
            @endisset

            {{-- Contenuto sotto l'hero --}}
            <div class="relative z-10 w-11/12 mt-12 mb-12 ml-10 mr-10 below-hero-content" role="region" aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10 e w-full --}}
                {{ $belowHeroContent ?? '' }}
            </div>

            {{-- Contenuto sotto l'hero --}}
            <div class="relative z-10 w-11/12 mt-12 mb-12 ml-10 mr-10 below-hero-content" role="region" aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10 e w-full --}}
                {{ $belowHeroContent_1 ?? '' }}
            </div>

             <div class="relative z-10 w-full mt-12 mb-12 ml-10 mr-10 below-hero-content" role="region" aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10, w-full e mt-12 --}}
                {{ $belowHeroContent_2 ?? '' }}
            </div>

            <div class="absolute z-20 transform -translate-x-1/2 bottom-6 left-1/2 md:hidden animate-bounce-slow">
                <button type="button" aria-label="{{ __('guest_layout.scroll_down_aria_label') }}" class="flex items-center justify-center w-10 h-10 text-white bg-black rounded-full bg-opacity-30 hover:bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="document.getElementById('main-content').scrollIntoView({behavior: 'smooth'});">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </button>
            </div>
        </section>
    @endunless

    {{-- SLOT PER IL CONTENUTO DEGLI ATTORI --}}
    @isset($actorContent)
        <div id="guest-layout-actors-section-wrapper" class="relative z-10 w-full"> {{-- Wrapper opzionale per stili globali se necessario --}}
            {{ $actorContent }}
        </div>
    @endisset

    <!-- Main Content -->
    <main id="main-content" role="main" class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="py-6 mt-auto bg-gray-900 border-t border-gray-800 md:py-8" role="contentinfo" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">{{ __('guest_layout.footer_sr_heading') }}</h2>
        <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8 md:flex md:justify-between md:items-center">
            <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('guest_layout.copyright_holder') }}. {{ __('guest_layout.all_rights_reserved') }}</p>
            <div class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-y-0 md:space-x-4">
                <x-environmental-stats format="footer" />
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-900/50 text-green-400 border border-green-800">{{ __('guest_layout.algorand_blue_mission') }}</div>
            </div>
        </div>
    </footer>


    <!-- Modals -->
    <div id="upload-modal"
        class="hidden modal"
        role="dialog"
        aria-modal="true"
        aria-hidden="true"
        tabindex="-1"
        aria-labelledby="upload-modal-title">
        <div role="document">
            <button id="close-upload-modal"
                    type="button"
                    aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">
                <span aria-hidden="true">&times;</span>
            </button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>

    <x-wallet-connect-modal />

    <!-- Logout Form -->
    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
        @csrf
        <button type="submit" class="sr-only">{{ __('guest_layout.logout_sr_button') }}</button>
    </form>

    <!-- Create Collection Modal (OS1 Integration) -->
    @include('components.create-collection-modal')

    <!-- Scripts -->


    @vite([
        'resources/ts/main.ts',
        'resources/js/app.js',
        'resources/js/guest.js',
        'resources/js/polyfills.js',

        ])

    @stack('scripts')
</body>
</html>


