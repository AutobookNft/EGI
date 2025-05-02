{{--
    /partials/uploading_form_content.blade.php
    Questo partial contiene SOLO il contenuto centrale della form di upload EGI.
    È pensato per essere incluso all'interno di una modale o di un altro contenitore
    nella Home page o in un'altra vista.

    NON include:
    - HTML/HEAD/BODY tags
    - Layout a colonne
    - Animazione Matrix/sfondi animati (gestiti dalla vista contenitore)
    - Script di setup globale (config loading, DOMContentLoaded listeners, Vite)
    - Componenti Livewire esterni (Navbar, Sidebar)

    Richiede che gli asset (CSS, JS di UUM, Livewire JS, global config script, Alpine.js)
    siano caricati nella pagina che lo include.
--}}

{{-- START: Schema.org Markup (JSON-LD) --}}
{{-- Aggiunto in base ai principi Oracode per l'interpretabilità delle macchine e la coerenza semantica. --}}
{{-- Descrive la natura di questo contenuto come una WebPage relativa al servizio di caricamento EGI. --}}
{{-- Non modifica i tag HTML esistenti, è un blocco separato. --}}
<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "name": "{{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}", {{-- Utilizza il titolo H2 come nome della pagina --}}
      "description": "Form to upload and manage your EGI (Ecological Goods Invent) assets for minting on the FlorenceEGI platform, including features like secure storage, virus scan, and advanced validation. Part of the Frangette ecosystem.", {{-- Descrizione statica dello scopo del form --}}
      {{-- "url": "https://florenceegi.com/upload-form", --}} {{-- URL Placeholder - Potrebbe necessitare di adattamento nella vista parent --}}
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
          "url": "https://frangette.com/images/logo-frangette.png" {{-- Utilizza il logo Frangette come logo del publisher --}}
        }
      }
    }
    </script>
    {{-- END: Schema.org Markup --}}


    {{-- Il div centrale con tutto il contenuto del form --}}
    {{-- Aggiunti role="form" e aria-label per l'accessibilità (ARIA). --}}
    {{-- Descrive questo div come una regione/form per le tecnologie assistive. --}}
    <div class="p-5 bg-gradient-to-br from-gray-800 via-purple-900 to-blue-900 rounded-xl shadow-xl border border-purple-500/30 relative nft-background" id="upload-container" data-upload-type="egi" role="form" aria-label="{{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}">
        <!-- Title with EGI style -->
        {{-- H2 ha già una buona semantica per il titolo principale. --}}
        <h2 class="text-2xl font-extrabold text-white mb-4 text-center tracking-wide drop-shadow-md nft-title">
            💎 {{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}
        </h2>

        <!-- Enterprise Features Badges -->
        {{-- Questi badge sono elementi decorativi/informativi. Non richiedono ruoli ARIA sui contenitori. I title sono sufficienti per l'hover visivo. --}}
        <div class="flex flex-wrap justify-center gap-2 mb-4">
            <div class="bg-blue-900/60 text-blue-200 px-2 py-1 rounded-md text-xs font-medium flex items-center shadow-sm" title="{{ trans('uploadmanager::uploadmanager.secure_storage_tooltip') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                {{ trans('uploadmanager::uploadmanager.secure_storage') }}
            </div>
            <div class="bg-purple-900/60 text-purple-200 px-2 py-1 rounded-md text-xs font-medium flex items-center shadow-sm" title="{{ trans('uploadmanager::uploadmanager.virus_scan_tooltip') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                {{ trans('uploadmanager::uploadmanager.virus_scan_feature') }}
            </div>
            {{-- Correzione: Rimosso '<' iniziale, era un errore di sintassi nel partial originale. --}}
            <div class="bg-green-900/60 text-green-200 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center shadow-md" title="{{ trans('uploadmanager::uploadmanager.advanced_validation_tooltip') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                {{ trans('uploadmanager::uploadmanager.advanced_validation') }}
            </div>
            <div class="bg-indigo-900/60 text-indigo-200 px-2 py-1 rounded-md text-xs font-medium flex items-center shadow-sm" title="{{ trans('uploadmanager::uploadmanager.storage_space_tooltip') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>
                    <span id="storage-used">2.4</span>/<span id="storage-total">50</span> {{ trans('uploadmanager::uploadmanager.storage_space_unit') }}
                </span>
            </div>
        </div>

        <!-- Enhanced drag & drop upload area -->
        {{-- Aggiunti role="group" e aria-label per l'accessibilità (ARIA). --}}
        {{-- Definisce quest'area come un gruppo per le tecnologie assistive. --}}
        <div
            class="w-full h-44 border-3 border-dashed border-blue-400/50 rounded-xl mb-4 flex flex-col items-center justify-center p-5 transition-all duration-300 bg-purple-800/20 hover:bg-purple-800/30 group"
            id="upload-drop-zone"
            role="group"
            aria-label="{{ trans('uploadmanager::uploadmanager.drag_files_here') }}" {{-- Usa il testo principale come etichetta per l'intero gruppo --}}
        >
            <!-- Drag & drop icon/illustration -->
            {{-- Elemento decorativo, non necessita di ARIA. --}}
            <div class="text-3xl mb-3 text-blue-400 group-hover:scale-110 transition-transform duration-300">
                📤
            </div>
            <!-- Instructions with improved contrast -->
            {{-- Testo informativo, non necessita di ruoli ARIA. Potrebbe essere collegato via aria-describedby, ma richiederebbe l'aggiunta di ID ai paragrafi, violando i vincoli. --}}
            <p class="text-base text-center text-white mb-4">
                {{ trans('uploadmanager::uploadmanager.drag_files_here') }} <br>
                <span class="text-blue-200 text-xs">{{ trans('uploadmanager::uploadmanager.or') }}</span>
            </p>
            <!-- Button styled with tooltip -->
            {{-- Label e input sono collegati, la label ha aria-label. Questo è sufficiente per l'accessibilità dell'elemento interattivo. --}}
            <label for="files" id="file-label" class="relative cursor-pointer rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-5 py-2.5 flex items-center justify-center text-base font-semibold text-white transition-all duration-300 ease-in-out hover:from-purple-500 hover:to-blue-500 hover:shadow-lg nft-button group" aria-label="{{ trans('uploadmanager::uploadmanager.select_files_aria') }}">
                {{ trans('uploadmanager::uploadmanager.select_files') }}
                <input type="file" id="files" multiple class="absolute left-0 top-0 h-full w-full cursor-pointer opacity-0">
                <!-- Tooltip -->
                {{-- Il tooltip è un miglioramento visivo, non un elemento interattivo fondamentale. Non necessita di ARIA. --}}
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-32 text-center">
                    {{ trans('uploadmanager::uploadmanager.select_files_tooltip') }}
                </span>
            </label>
            <div class="upload-dropzone text-center text-gray-200 text-xs mt-1.5">
                <!-- About upload size -->
                {{-- Testo informativo caricato via JS, non necessita di ruolo ARIA sul contenitore. --}}
            </div>
        </div>

        {{-- Metadata partial - ASSICURATI CHE QUESTO PARTIAL ESISTA E CONTENGA LA FORM PER I METADATI --}}
        {{-- Non è possibile aggiungere markup ARIA/Schema all'interno di questo partial. Si assume che sia gestito altrove se necessario. --}}
        @include('egimodule::partials.metadata')

        <!-- Progress bar and virus switch -->
        <div class="mt-4 space-y-4">
            {{-- Contenitore della barra di progresso. Aggiunti role="progressbar" e attributi aria per l'accessibilità (ARIA).
                 Lo stato di aria-valuenow dovrebbe essere aggiornato dal JavaScript che interagisce con lo stile #progress-bar. --}}
            <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-describedby="progress-text">
                {{-- Il div interno è l'indicatore visivo, non necessita di ruolo ARIA su se stesso. --}}
                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full transition-all duration-500" id="progress-bar"></div>
            </div>
            {{-- Elemento di testo che descrive la barra di progresso. Il suo ID è referenziato da aria-describedby sopra. --}}
            <p class="text-gray-200 text-xs text-center"><span id="progress-text"></span></p>

            <div class="flex items-center justify-center gap-2">
                {{-- Switch virus scan. Ha già role="switch". Aggiunti aria-checked e aria-labelledby per l'accessibilità (ARIA).
                     Lo stato di aria-checked dovrebbe essere aggiornato dal JS in base allo stato del checkbox. --}}
                <input
                    class="me-1 h-3 w-6 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-3 before:w-3 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow-sm after:transition-all checked:bg-purple-600 checked:after:ms-3 checked:after:bg-purple-400 checked:after:shadow-sm hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                    type="checkbox"
                    role="switch"
                    id="scanvirus"
                    title="{{ trans('uploadmanager::uploadmanager.toggle_virus_scan') }}"
                    aria-checked="false" {{-- Stato iniziale basato sull'etichetta visiva --}}
                    aria-labelledby="scanvirus_label" {{-- Collegamento all'etichetta che potrebbe cambiare dinamicamente --}}
                />
                {{-- Etichetta per lo switch virus scan. Il suo ID è referenziato da aria-labelledby sull'input. --}}
                <label
                    class="text-red-400 font-medium hover:pointer-events-none text-xs"
                    id="scanvirus_label"
                    for="scanvirus"
                >{{ trans('uploadmanager::uploadmanager.virus_scan_disabled') }}</label>
            </div>
            {{-- Elemento di testo per consigli sul virus scan. Non necessita di ruolo ARIA. --}}
            <p class="text-gray-200 text-xs text-center"><span id="virus-advise"></span></p>
        </div>

        <!-- Action buttons with EGI style and tooltips -->
        <div class="mt-6 flex justify-center space-x-4">
            {{-- Pulsante Save. Aggiunto aria-disabled per l'accessibilità (ARIA). Lo stato dovrebbe essere aggiornato dal JS. --}}
            <button type="button" id="uploadBtn" class="relative bg-green-500 text-white px-5 py-2.5 rounded-full font-semibold text-base nft-button opacity-50 cursor-not-allowed disabled:hover:bg-green-500 disabled:hover:shadow-none group" aria-label="{{ trans('uploadmanager::uploadmanager.save_aria') }}" aria-disabled="true">
                💾 {{ trans('uploadmanager::uploadmanager.save_the_files') }}
                {{-- Il tooltip è un miglioramento visivo, non necessita di ARIA. --}}
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-32 text-center pointer-events-none">
                    {{ trans('uploadmanager::uploadmanager.save_tooltip') }}
                </span>
            </button>
            {{-- Pulsante Cancel. Aggiunto aria-disabled per l'accessibilità (ARIA). Lo stato dovrebbe essere aggiornato dal JS. --}}
            <button type="button" onclick="cancelUpload()" id="cancelUpload" class="relative bg-red-500 text-white px-5 py-2.5 rounded-full font-semibold text-base nft-button opacity-50 cursor-not-allowed disabled:hover:bg-red-500 disabled:hover:shadow-none group" aria-label="{{ trans('uploadmanager::uploadmanager.cancel_aria') }}" aria-disabled="true">
                ❌ {{ trans('uploadmanager::uploadmanager.cancel') }}
                {{-- Il tooltip è un miglioramento visivo, non necessita di ARIA. --}}
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-32 text-center pointer-events-none">
                    {{ trans('uploadmanager::uploadmanager.cancel_tooltip') }}
                </span>
            </button>
        </div>

        <!-- Previews grid -->
        {{-- Contenitore per elementi di anteprima caricati dinamicamente. Aggiunti role="region" e aria-label per l'accessibilità (ARIA). --}}
        {{-- Questo aiuta le tecnologie assistive a identificare quest'area. L'ARIA per i singoli elementi di anteprima (se complessi) dovrebbe essere gestito dal JS che li crea. --}}
        <div id="collection" class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" role="region" aria-label="Uploaded File Previews">
            <!-- Previews will be loaded dynamically via JS -->
        </div>

        <!-- Return to collection button with tooltip -->
        <div class="mt-6 flex justify-center">
            {{-- Pulsante Return. Ha già aria-label. Non necessita di ulteriore ARIA in questo contesto statico. --}}
            <button type="button" onclick="redirectToCollection()" id="returnToCollection" class="relative bg-gray-700 text-white px-8 py-4 rounded-full font-semibold text-lg nft-button hover:bg-gray-600 group" aria-label="{{ trans('uploadmanager::uploadmanager.return_aria') }}">
                🔙 {{ trans('uploadmanager::uploadmanager.return_to_collection') }}
                {{-- Il tooltip è un miglioramento visivo, non necessita di ARIA. --}}
                <span class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-48 text-center pointer-events-none">
                    {{ trans('uploadmanager::uploadmanager.return_tooltip') }}
                </span>
            </button>
        </div>

        <!-- Scan progress with improved contrast -->
        <div class="mt-6 text-center">
            {{-- Testo di stato. Aggiunto role="status" per l'accessibilità (ARIA). Indica che questo elemento può ricevere aggiornamenti live importanti per l'utente. --}}
            <p class="text-gray-200 text-xs"><span id="scan-progress-text" role="status"></span></p>
        </div>

        <!-- Status showEmoji-->
        {{-- Contenitore di stato. Aggiunto role="status" per l'accessibilità (ARIA). Indica che questo elemento può ricevere aggiornamenti live importanti per l'utente. --}}
        <div id="status" class="mt-4 text-center text-gray-200 text-xs mx-auto w-32 p-2" role="status"></div>

        <!-- Upload status -->
        <div id="upload-status" class="mt-5 text-center text-gray-200">
            {{-- Messaggio di stato. Aggiunto role="status" per l'accessibilità (ARIA). Indica che questo elemento può ricevere aggiornamenti live importanti per l'utente. --}}
            <p id="status-message" class="text-xs" role="status">{{ trans('uploadmanager::uploadmanager.preparing_to_mint') }}</p>
        </div>
    </div>

    {{-- NOTA: Questo partial non contiene script.
         Tutto il codice JS che interagisce con questi elementi (id=...)
         deve essere caricato e inizializzato nella pagina che include questo partial.
         Il JS dovrà anche aggiornare gli attributi ARIA dinamici come aria-valuenow, aria-checked e aria-disabled
         in base allo stato dell'interfaccia. --}}
