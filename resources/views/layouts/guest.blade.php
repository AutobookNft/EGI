<!DOCTYPE html>

@php(session(['natan_session_boot' => true]))

@include('layouts.partials.header')

{{-- @include('layouts.guest_script') --}}

<!-- Hero Section -->
@unless (isset($noHero) && $noHero)
    <section id="hero-section" class="relative flex min-h-[100vh] flex-col items-center overflow-hidden"
        aria-labelledby="hero-main-title">

        <h1 id="hero-main-title" class="sr-only">{{ $title ?? __('guest_layout.default_title') }}</h1>

        @isset($heroFullWidth)
            {{-- Layout a colonna intera --}}
            <div class="relative z-10 mx-auto mb-auto mt-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                {{ $heroFullWidth }}
            </div>

            <div class="natan-assistant fixed bottom-6 right-6 z-[100000] flex flex-col items-end" role="region"
                aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                {{ $heroNatanAssistant ?? '' }}
            </div>
        @else
            {{-- Layout a colonne esistente (NON USATO DALLA HOME ATTUALE) --}}
            <div
                class="hero-content-overlay container relative z-10 mx-auto flex w-full flex-col items-center px-4 sm:px-6 lg:px-8">
                <div class="mb-10 grid w-full grid-cols-1 gap-8 md:mb-12 md:grid-cols-12 lg:gap-6">
                    {{-- Colonna mobile: sinistra+destra in un contenitore --}}
                    <div class="flex flex-col items-center gap-8 md:hidden">
                        {{-- Colonna sinistra (Natan) --}}
                        <div class="w-full" role="region" aria-label="{{ __('guest_layout.hero_left_content_aria_label') }}">
                            {{ $heroContentLeft ?? '' }}
                        </div>

                        {{-- Colonna destra (Badge impatto) --}}
                        <div class="relative z-10 w-full" role="region"
                            aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                            {{ $heroContentRight ?? '' }}
                        </div>
                    </div>

                    {{-- Colonna sinistra (25%) - Visibile solo su tablet+ --}}
                    <div class="hidden md:col-span-3 md:block" role="region"
                        aria-label="{{ __('guest_layout.hero_left_content_aria_label') }}">
                        {{ $heroContentLeft ?? '' }}
                    </div>

                    {{-- Colonna centrale: Carousel (50% su desktop, 100% su tablet) --}}
                    <div class="flex w-full flex-col items-center justify-center md:col-span-12 lg:col-span-6" role="region"
                        aria-label="{{ __('guest_layout.hero_carousel_aria_label') }}">
                        {{ $heroCarousel ?? '' }}
                    </div>

                    {{-- Colonna destra (25%) - Visibile solo su tablet+ --}}
                    <div class="relative z-10 w-full" role="region"
                        aria-label="{{ __('guest_layout.hero_right_content_aria_label') }}">
                        {{ $heroContentRight ?? '' }}
                    </div>

                    {{-- Layout alternativo tablet: due colonne laterali affiancate sotto il carousel --}}
                    <div class="hidden md:col-span-12 md:grid md:grid-cols-2 md:gap-6 lg:hidden">
                        {{-- Colonna sinistra (Natan) - Ripetuta per layout tablet --}}
                        <div class="flex items-center" role="region"
                            aria-label="{{ __('guest_layout.hero_left_content_tablet_aria_label') }}">
                            {{ $heroContentLeft ?? '' }}
                        </div>

                        {{-- Colonna destra (Badge impatto) - Ripetuta per layout tablet --}}
                        <div class="flex items-center" role="region"
                            aria-label="{{ __('guest_layout.hero_right_content_tablet_aria_label') }}">
                            {{ $heroContentRight ?? '' }}
                        </div>
                    </div>
                </div>
            @endisset

            {{-- Contenuto sotto l'hero --}}
            <div class="below-hero-content relative z-10 mb-12 ml-10 mr-10 mt-12 w-11/12" role="region"
                aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10 e w-full --}}
                {{ $belowHeroContent ?? '' }}
            </div>

            {{-- Contenuto sotto l'hero --}}
            <div class="below-hero-content relative z-10 mb-12 ml-10 mr-10 mt-12 w-11/12" role="region"
                aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10 e w-full --}}
                {{ $belowHeroContent_1 ?? '' }}
            </div>

            <div class="below-hero-content relative z-10 mb-12 ml-10 mr-10 mt-12 w-full" role="region"
                aria-label="{{ __('guest_layout.hero_featured_content_aria_label') }}"> {{-- Aggiunto z-10, w-full e mt-12 --}}
                {{ $belowHeroContent_2 ?? '' }}
            </div>

            <div class="animate-bounce-slow absolute bottom-6 left-1/2 z-20 -translate-x-1/2 transform md:hidden">
                <button type="button" aria-label="{{ __('guest_layout.scroll_down_aria_label') }}"
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-black bg-opacity-30 text-white hover:bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    onclick="document.getElementById('main-content').scrollIntoView({behavior: 'smooth'});">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
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
<footer class="mt-auto border-t border-gray-800 bg-gray-900 py-6 md:py-8" role="contentinfo"
    aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">{{ __('guest_layout.footer_sr_heading') }}</h2>
    <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
        <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('guest_layout.copyright_holder') }}.
            {{ __('guest_layout.all_rights_reserved') }}</p>
        <div
            class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-x-4 md:space-y-0">
            <x-environmental-stats format="footer" />
            <div class="rounded-full border border-green-800 bg-green-900/50 px-2 py-0.5 text-xs text-green-400">
                {{ __('guest_layout.algorand_blue_mission') }}</div>
        </div>
    </div>
</footer>


<!-- Modals -->
<div id="upload-modal" class="modal hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1"
    aria-labelledby="upload-modal-title">
    <div role="document">
        <button id="close-upload-modal" type="button"
            aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">
            <span aria-hidden="true">&times;</span>
        </button>
        @include('egimodule::partials.uploading_form_content')
    </div>
</div>

<x-wallet-connect-modal />

<!-- Logout Form -->
<form method="POST" action="{{ route('custom.logout') }}" id="logout-form" style="display: none;">
    @csrf
    <button type="submit" class="sr-only">{{ __('guest_layout.logout_sr_button') }}</button>
</form>

<!-- Create Collection Modal (OS1 Integration) -->
@include('components.create-collection-modal')

<!-- Scripts -->


@vite(['resources/js/polyfills.js', 'resources/js/app.js', 'resources/js/guest.js', 'resources/ts/main.ts'])

{{-- 🎯 HEIC Detection Integration - Embedded in Layout --}}
<script>
    // 🎯 HEIC Detection Function - Provides window.showHEICMessage for UUM
    window.showHEICMessage = function() {
        // Check if SweetAlert2 is available
        if (typeof Swal === 'undefined') {
            console.warn('⚠️ SweetAlert2 not available, using alert fallback');
            alert(
                '� HEIC Format Detected\n\nThe selected file is in HEIC/HEIF format.\nWeb browsers don\'t support this format.\n\nSuggestion: Convert the file to JPG or PNG.');
            return;
        }

        // Use UTM translation system if available
        if (typeof window.getTranslation === 'function') {
            console.log('🌍 Using UTM translation system for HEIC message');

            // Get translations via UTM
            const title = window.getTranslation('heic_detection_title');
            const greeting = window.getTranslation('heic_detection_greeting');
            const explanation = window.getTranslation('heic_detection_explanation');
            const solutionsTitle = window.getTranslation('heic_detection_solutions_title');
            const solutionIos = window.getTranslation('heic_detection_solution_ios');
            const solutionShare = window.getTranslation('heic_detection_solution_share');
            const solutionComputer = window.getTranslation('heic_detection_solution_computer');
            const thanks = window.getTranslation('heic_detection_thanks');
            const button = window.getTranslation('heic_detection_understand_button');

            const htmlContent = `
            <div style="text-align: left; line-height: 1.6;">
                <p style="margin-bottom: 15px;">${greeting}</p>
                <p style="margin-bottom: 20px;">${explanation}</p>
                
                <div style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; color: #333;">${solutionsTitle}</h4>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li style="margin-bottom: 8px;">${solutionIos}</li>
                        <li style="margin-bottom: 8px;">${solutionShare}</li>
                        <li style="margin-bottom: 8px;">${solutionComputer}</li>
                    </ul>
                </div>
                
                <p style="margin-bottom: 0; text-align: center; font-style: italic;">${thanks}</p>
            </div>
        `;

            Swal.fire({
                title: title,
                html: htmlContent,
                icon: 'info',
                confirmButtonText: button,
                width: '600px',
                showCancelButton: false,
                allowOutsideClick: true,
                allowEscapeKey: true
            });

        } else {
            console.warn('⚠️ UTM translation system not available, using fallback');
            // Fallback if UTM is not available
            Swal.fire({
                title: '📸 HEIC Format Detected',
                html: `
                <div style="text-align: left; line-height: 1.6;">
                    <p>We noticed you're trying to upload <strong>HEIC/HEIF</strong> format files.</p>
                    <p>These are great for quality and storage space, but unfortunately web browsers don't fully support them yet.</p>
                    
                    <div style="margin: 20px 0;">
                        <h4 style="margin-bottom: 10px; color: #333;">💡 What you can do:</h4>
                        <ul style="margin: 0; padding-left: 20px;">
                            <li style="margin-bottom: 8px;"><strong>📱 iPhone/iPad:</strong> Settings → Camera → Formats → "Most Compatible"</li>
                            <li style="margin-bottom: 8px;"><strong>🔄 Quick conversion:</strong> Share the photo from Photos app (it will convert automatically)</li>
                            <li style="margin-bottom: 8px;"><strong>💻 On computer:</strong> Open with Preview (Mac) or online converters</li>
                        </ul>
                    </div>
                    
                    <p style="margin-bottom: 0; text-align: center; font-style: italic;">Thanks for your patience! 💚</p>
                </div>
            `,
                icon: 'info',
                confirmButtonText: '✨ I Understand'
            });
        }
    };

    console.log('✅ HEIC Detection function loaded and available globally');
</script>

@stack('scripts')
</body>

</html>
