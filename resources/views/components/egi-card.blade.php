{{-- components/egi-card.blade.php --}}
{{-- Questa è la vista del singolo EGI card --}}

{{-- resources/views/components/egi-card.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Card --}}
{{-- Displays a single EGI card, typically within a collection grid. --}}
{{-- Expects an $egi object (App\Models\Egi) and optionally $collection (for creator comparison). --}}
{{-- Uses Tailwind CSS for a modern, responsive design. --}}

{{-- 🧱 Card Container --}}
{{-- @style: Background, rounded corners, shadow, overflow hidden, flex column layout, transition for hover effects. --}}
{{-- @interactivity: `data-egi-id` for potential JS interactions. --}}
<article class="egi-card group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 ease-in-out" data-egi-id="{{ $egi->id }}">

    {{-- 🖼️ Sezione Immagine --}}
    {{-- @style: Relative positioning for badges, aspect ratio, background for placeholder. --}}
    <figure class="relative aspect-square w-full overflow-hidden bg-gray-100"> {{-- aspect-square è comune, aggiusta se serve --}}
        @php
            // 🛠️ Costruzione Path Immagine (Oracode: Esplicitamente Intenzionale)
            // Costruisce il path relativo allo storage/app/public
            $imageRelativePath = $egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension
                ? sprintf(
                    'users_files/collections_%d/creator_%d/%d.%s',
                    $egi->collection_id,
                    $egi->user_id, // Assumiamo user_id sia l'ID del creator dell'EGI specifico
                    $egi->key_file,
                    $egi->extension
                  )
                : null;

            // 🔗 Generazione URL Pubblico (Oracode: Robusto)
            // Usa Storage::url() per ottenere l'URL corretto, gestendo il disco public.
            $imageUrl = $imageRelativePath ? Storage::disk('public')->url($imageRelativePath) : null;

        @endphp

        {{-- 🎯 Immagine Principale o Placeholder --}}
        @if($imageUrl)
            {{-- @style: Cover the container, center the image, group-hover effect. --}}
            {{-- @accessibility-trait: Alt text descrittivo. loading=lazy per performance. --}}
            <img src="{{ $imageUrl }}"
                 {{-- @accessibility-trait: Alt text descrittivo. --}}
                 {{-- @interactivity: Group hover effect for scaling. --}}
                 alt="{{ $egi->title ?? 'EGI Image' }}"
                 class="h-full w-full object-cover object-center transition-transform duration-300 ease-in-out group-hover:scale-105"
                 loading="lazy" />
        @else
            {{-- @style: Placeholder styling, centered icon. --}}
            <div class="flex h-full w-full items-center justify-center bg-gray-200">
                <svg class="h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                   <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                 </svg>
            </div>
        @endif

        {{-- Overlay leggero su hover (opzionale) --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        {{-- 🔢 Badge Posizione (se presente) --}}
        @if($egi->position)
            {{-- @style: Posizionato top-left, stile badge piccolo. --}}
            <span class="absolute top-2 left-2 inline-block rounded-full bg-black/50 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                #{{ $egi->position }}
            </span>
        @endif

         {{-- ▶️ Badge Media Type (se $egi->media è true) --}}
         @if($egi->media)
            {{-- @style: Posizionato top-right, stile badge icona. --}}
            <span class="absolute top-2 right-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/50 text-white backdrop-blur-sm" title="{{ __('Media Content') }}">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.84Z" />
                </svg>
            </span>
        @endif

    </figure>

    {{-- ℹ️ Sezione Informazioni EGI --}}
    {{-- @style: Padding, layout flessibile verticale, occupa spazio rimanente. --}}
    <div class="flex flex-1 flex-col justify-between p-4">
        {{-- Blocco Superiore Info (Titolo, Creator se diverso) --}}
        <div>
            {{-- 👑 Titolo EGI --}}
            {{-- @style: Dimensione, peso, colore, troncamento, spaziatura. --}}
            <h3 class="text-base font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors duration-200">
                {{-- Link rimosso per ora, la card intera è cliccabile implicitamente --}}
                {{ Str::limit($egi->title ?? __('Untitled EGI'), 45) }} {{-- Aumentato limite caratteri --}}
            </h3>

             {{-- 👤 Creator EGI (mostra solo se DIVERSO dal creator della collezione) --}}
             {{-- @logic: Verifica se $collection è passato e se gli ID sono diversi. --}}
             @if(isset($collection) && $egi->user_id && $egi->user_id != $collection->creator_id && $egi->user)
                 <div class="mt-1.5 flex items-center text-xs text-gray-500">
                     {{-- Avatar/Placeholder Creator EGI --}}
                     @if ($egi->user->profile_photo_url)
                         <img src="{{ $egi->user->profile_photo_url }}" alt="{{ $egi->user->name }}" class="h-4 w-4 rounded-full mr-1 object-cover">
                     @else
                         <span class="inline-block h-4 w-4 rounded-full overflow-hidden bg-gray-200 mr-1 align-middle">
                            <svg class="h-full w-full text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </span>
                     @endif
                     {{-- Nome Creator EGI --}}
                     <span class="truncate">{{ __('Created by:') }} {{ $egi->user->name }}</span>
                 </div>
             @endif
        </div>

        {{-- 💰 Prezzo EGI --}}
        {{-- @style: Margine superiore, dimensione testo, peso font, colori. --}}
        <div class="mt-3">
            @if($egi->price && $egi->price > 0)
                <p class="text-sm font-medium text-gray-900">
                    {{ number_format($egi->price, 2) }} <span class="text-xs text-gray-500">ALGO</span>
                </p>
            @elseif($egi->floorDropPrice && $egi->floorDropPrice > 0)
                <p class="text-sm text-gray-500">
                    {{ __('Floor:') }} <span class="font-medium text-gray-700">{{ number_format($egi->floorDropPrice, 2) }}</span> <span class="text-xs">ALGO</span>
                </p>
            @else
                 {{-- Placeholder se non c'è prezzo --}}
                 <p class="text-sm text-gray-400 italic">{{ __('Not for sale') }}</p>
            @endif
        </div>
    </div>

    {{-- 🎬 Footer Card (Azioni) --}}
    {{-- @style: Bordo superiore, padding, layout flex, spaziatura tra elementi. --}}
    <div class="border-t border-gray-100 px-4 py-3">
        <div class="flex items-center justify-between gap-2">
            {{-- Pulsante/Link Visualizza Dettaglio --}}
            <a href="{{ route('egis.show', $egi->id) }}"
               class="inline-flex items-center justify-center rounded-md px-2.5 py-1.5 text-xs font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 transition-colors duration-150 ease-in-out flex-shrink-0"
               aria-label="{{ __('View EGI details') }}">
               <svg class="h-4 w-4 mr-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                 <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                 <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.18l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 0 2.336 0l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 0 2.336 0l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 1 0 1.18l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879a1.651 1.651 0 0 0-2.336 0l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879a1.651 1.651 0 0 0-2.336 0l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879Zm16.473-4.425a.823.823 0 0 1 0 1.166l-1.888 1.888a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0L.664 7.33a.823.823 0 0 1 0-1.166l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l1.888 1.888a.823.823 0 0 1 0 1.166Z" clip-rule="evenodd" />
               </svg>
               {{ __('View') }}
            </a>

            {{-- 🔖 Pulsante Riserva (Condizionale) --}}
            {{-- @logic: Mostra solo se EGI non è mintato E (è pubblicato O l'utente auth è il creator) --}}
            @php
                $canReserve = !$egi->mint && ($egi->is_published || (isset($collection) && auth()->id() === $collection->creator_id));
            @endphp
            @if($canReserve)
                 {{-- @style: Stile bottone primario piccolo. --}}
                 {{-- @interactivity: Richiede JS per gestire la logica di prenotazione (probabilmente apre una modal o invia una richiesta API). --}}
                <button class="inline-flex items-center justify-center rounded-md bg-green-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-colors duration-150 ease-in-out flex-shrink-0 reserve-button"
                        data-egi-id="{{ $egi->id }}">
                        {{-- data-reserve-url="{{ route('api.egis.reserve', $egi->id) }}"> Assicurati che esista rotta API> --}}
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Reserve') }}
                </button>
            @else
                {{-- Placeholder o stato disabilitato se non prenotabile --}}
                {{-- <span class="text-xs text-gray-400 italic">{{ __('Minted') }}</span> --}}
            @endif
        </div>
    </div>

</article> {{-- Fine Card Container --}}
