{{--
|--------------------------------------------------------------------------
| Layout: Creator Layout (Versione con Struttura Corretta)
|--------------------------------------------------------------------------
|
| Copia di guest.blade.php dove la complessa area <main> e gli slot
| dell'hero sono stati sostituiti da un unico <main> slot subito dopo
| l'header, per permettere alle pagine di definire il proprio layout.
|
--}}
<!DOCTYPE html>

    @include('layouts.partials.header')


    <main id="main-content" role="main" class="flex-grow">
        {{ $slot }}
    </main>

    <footer class="py-6 mt-auto bg-gray-900 border-t border-gray-800 md:py-8" role="contentinfo" aria-labelledby="footer-heading">
        {{-- IL FOOTER È IDENTICO AL 100% A GUEST.BLADE.PHP --}}
        <h2 id="footer-heading" class="sr-only">{{ __('guest_layout.footer_sr_heading') }}</h2>
        <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8 md:flex md:justify-between md:items-center">
            <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('guest_layout.copyright_holder') }}. {{ __('guest_layout.all_rights_reserved') }}</p>
            <div class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-y-0 md:space-x-4">
                <x-environmental-stats format="footer" />
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-900/50 text-green-400 border border-green-800">{{ __('guest_layout.algorand_blue_mission') }}</div>
            </div>
        </div>
    </footer>

    {{-- I MODAL E GLI SCRIPT SONO IDENTICI AL 100% A GUEST.BLADE.PHP --}}
    <div id="upload-modal" class="hidden modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1" aria-labelledby="upload-modal-title">
        <div role="document">
            <button id="close-upload-modal" type="button" aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">
                <span aria-hidden="true">&times;</span>
            </button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>
    <x-wallet-connect-modal />
    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
        @csrf
        <button type="submit" class="sr-only">{{ __('guest_layout.logout_sr_button') }}</button>
    </form>
    @include('components.create-collection-modal')
    @vite([
        'resources/js/guest.js',
        'resources/js/polyfills.js',
        'resources/ts/main.ts',
        'resources/js/app.js',
    ])
    @stack('scripts')
</body>
</html>
