{{--
    /partials/uploading_form_content.blade.php
    🎯 CONSERVATIVE MOBILE FIX - Mantiene tutto visibile
    📱 Miglioramenti mobile senza stravolgere la struttura
--}}
@vite(['vendor/ultra/ultra-upload-manager/resources/css/app.css'])


{{-- START: Schema.org Markup (JSON-LD) --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}",
  "description": "Form to upload and manage your EGI (Ecological Goods Invent) assets for minting on the FlorenceEGI platform, including features like secure storage, virus scan, and advanced validation. Part of the Frangette ecosystem.",
  "isPartOf": {
    "@type": "WebSite",
    "url": "https://florenceegi.com/"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Frangette Cultural Promotion Association",
    "url": "https://frangette.com/",
    "logo": {
      "@type": "ImageObject",
      "url": "https://frangette.com/images/logo-frangette.png"
    }
  }
}
</script>
{{-- END: Schema.org Markup --}}

{{-- Container principale - MOBILE-FIRST RESPONSIVE --}}
<div class="relative w-full p-4 border-0 rounded-none shadow-xl md:p-5 bg-gradient-to-br from-gray-800 via-purple-900 to-blue-900 md:rounded-xl md:border md:border-purple-500/30 nft-background max-w-none"
     id="upload-container"
     data-upload-type="egi"
     role="form"
     aria-label="{{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}">

    <!-- Title - dimensioni leggermente più piccole per mobile -->
    <h2 class="mb-4 text-xl font-extrabold tracking-wide text-center text-white md:text-2xl drop-shadow-md nft-title">
        💎 {{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}
    </h2>
    
    <!-- Enhanced drag & drop upload area - altezza ridotta su mobile -->
    <div class="flex flex-col items-center justify-center w-full p-5 mb-4 transition-all duration-300 border-dashed h-36 md:h-44 border-3 border-blue-400/50 rounded-xl bg-purple-800/20 hover:bg-purple-800/30 group"
         id="upload-drop-zone"
         role="group"
         aria-label="{{ trans('uploadmanager::uploadmanager.drag_files_here') }}">

        <!-- Drag & drop icon/illustration -->
        <div class="mb-3 text-2xl text-blue-400 transition-transform duration-300 md:text-3xl group-hover:scale-110">
            📤
        </div>

        <!-- Instructions with improved contrast -->
        <p class="mb-4 text-sm text-center text-white md:text-base">
            {{ trans('uploadmanager::uploadmanager.drag_files_here') }} <br>
            <span class="text-xs text-blue-200">{{ trans('uploadmanager::uploadmanager.or') }}</span>
        </p>

        <!-- Button styled with tooltip -->
        <label for="files" id="file-label" class="relative cursor-pointer rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-5 py-2.5 flex items-center justify-center text-base font-semibold text-white transition-all duration-300 ease-in-out hover:from-purple-500 hover:to-blue-500 hover:shadow-lg nft-button group" aria-label="{{ trans('uploadmanager::uploadmanager.select_files_aria') }}">
            {{ trans('uploadmanager::uploadmanager.select_files') }}
            <input type="file" id="files" multiple class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer">
            <!-- Tooltip - solo su desktop -->
            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-32 text-center hidden md:block">
                {{ trans('uploadmanager::uploadmanager.select_files_tooltip') }}
            </span>
        </label>
        {{-- <div class="upload-dropzone text-center text-gray-200 text-xs mt-1.5">
            <!-- About upload size -->
        </div> --}}
    </div>

    {{-- Metadata partial --}}
    @include('egimodule::partials.metadata')

    <!-- Progress bar and virus switch -->
    <div class="mt-4 space-y-4">
        <div class="w-full h-2 overflow-hidden bg-gray-700 rounded-full" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-describedby="progress-text">
            <div class="h-2 transition-all duration-500 rounded-full bg-gradient-to-r from-green-400 to-blue-500" id="progress-bar"></div>
        </div>
        <p class="text-xs text-center text-gray-200"><span id="progress-text"></span></p>

        <div class="flex items-center justify-center gap-2">
            <input class="me-1 h-3 w-6 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-3 before:w-3 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow-sm after:transition-all checked:bg-purple-600 checked:after:ms-3 checked:after:bg-purple-400 checked:after:shadow-sm hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                   type="checkbox"
                   role="switch"
                   id="scanvirus"
                   title="{{ trans('uploadmanager::uploadmanager.toggle_virus_scan') }}"
                   aria-checked="false"
                   aria-labelledby="scanvirus_label" />
            <label class="text-xs font-medium text-red-400 hover:pointer-events-none"
                   id="scanvirus_label"
                   for="scanvirus">{{ trans('uploadmanager::uploadmanager.virus_scan_disabled') }}</label>
        </div>
        <p class="text-xs text-center text-gray-200"><span id="virus-advise"></span></p>
    </div>

    <!-- Action buttons - affiancati anche su mobile con padding ridotto -->
    <div class="flex justify-center gap-2 mt-6 md:gap-4">
        <button type="button"
                id="uploadBtn"
                class="relative bg-green-500 text-white px-2 py-1.5 md:px-5 md:py-2.5 rounded-full font-semibold text-xs md:text-base nft-button opacity-50 cursor-not-allowed disabled:hover:bg-green-500 disabled:hover:shadow-none group flex-1 max-w-xs"
                aria-label="{{ trans('uploadmanager::uploadmanager.save_aria') }}"
                aria-disabled="true">
            💾 {{ trans('uploadmanager::uploadmanager.save_the_files') }}
            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-32 text-center pointer-events-none hidden md:block">
                {{ trans('uploadmanager::uploadmanager.save_tooltip') }}
            </span>
        </button>
        <button type="button" onclick="cancelUpload()" id="cancelUpload" class="relative bg-red-500 text-white px-2 py-1.5 md:px-5 md:py-2.5 rounded-full font-semibold text-xs md:text-base nft-button opacity-50 cursor-not-allowed disabled:hover:bg-red-500 disabled:hover:shadow-none group flex-1 max-w-xs" aria-label="{{ trans('uploadmanager::uploadmanager.cancel_aria') }}" aria-disabled="true">
            ❌ {{ trans('uploadmanager::uploadmanager.cancel') }}
            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-32 text-center pointer-events-none hidden md:block">
                {{ trans('uploadmanager::uploadmanager.cancel_tooltip') }}
            </span>
        </button>
    </div>

    <!-- Previews grid - quadratini piccoli su mobile -->
    <div id="collection" class="grid grid-cols-4 gap-2 mt-6 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 lg:gap-4" role="region" aria-label="Uploaded File Previews">
        <!-- Previews will be loaded dynamically via JS -->
    </div>

    <!-- Return to collection button with tooltip - dimensioni ridotte su mobile -->
    <div class="flex justify-center mt-6">
        <button type="button" onclick="redirectToCollection()" id="returnToCollection" class="relative px-4 py-2 md:px-8 md:py-4 text-base md:text-lg font-semibold text-white bg-gray-700 rounded-full nft-button hover:bg-gray-600 group" aria-label="{{ trans('uploadmanager::uploadmanager.return_aria') }}">
            🔙 {{ trans('uploadmanager::uploadmanager.return_to_collection') }}
            <span class="absolute hidden w-48 px-2 py-1 text-xs text-center text-white transition-opacity duration-300 transform -translate-x-1/2 bg-gray-800 rounded opacity-0 pointer-events-none -top-12 left-1/2 group-hover:opacity-100 md:block">
                {{ trans('uploadmanager::uploadmanager.return_tooltip') }}
            </span>
        </button>
    </div>

    <!-- Scan progress with improved contrast -->
    <div class="mt-6 text-center">
        <p class="text-xs text-gray-200"><span id="scan-progress-text" role="status"></span></p>
    </div>

    <!-- Status showEmoji-->
    <div id="status" class="w-32 p-2 mx-auto mt-4 text-xs text-center text-gray-200" role="status"></div>

    <!-- Upload status -->
    <div id="upload-status" class="mt-5 text-center text-gray-200">
        <p id="status-message" class="text-xs" role="status">{{ trans('uploadmanager::uploadmanager.preparing_to_mint') }}</p>
    </div>
</div>

{{-- CSS personalizzato per thumbnail più piccole su mobile --}}
<style>
/* Thumbnail piccole su mobile - sovrascrive gli stili del modulo upload */
@media (max-width: 640px) {
    #collection .upload-preview,
    #collection .file-preview,
    #collection .preview-item,
    #collection img {
        max-width: 60px !important;
        max-height: 60px !important;
        width: 60px !important;
        height: 60px !important;
        object-fit: cover !important;
    }
    
    #collection .upload-preview {
        min-height: 60px !important;
        padding: 4px !important;
    }
    
    /* Riduci anche il testo nelle preview se presente */
    #collection .preview-text,
    #collection .file-name {
        font-size: 8px !important;
        line-height: 1.2 !important;
    }
}
</style>

@vite(['resources/js/components/create-collection-modal.js'])
