{{-- resources/views/components/egi-card.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Card --}}
{{-- Displays a single EGI card, typically within a collection grid. --}}
{{-- Expects an $egi object (App\Models\Egi) and optionally $collection (for creator comparison). --}}
{{-- Uses Tailwind CSS for a modern, responsive design. --}}

{{-- Props: Definisci l'oggetto egi come richiesto --}}
@props(['egi', 'collection' => null, 'showPurchasePrice' => false, 'hideReserveButton' => false]) {{-- Nuovo prop per nascondere reserve --}}

{{-- 🧱 Card Container --}}
<article
    class="egi-card group relative overflow-hidden rounded-2xl border-2 border-purple-500/30 bg-gray-900 transition-all duration-300 hover:border-purple-400 hover:shadow-2xl hover:shadow-purple-500/20"
    data-egi-id="{{ $egi->id }}">

    {{-- 🖼️ Sezione Immagine --}}
    <figure class="relative aspect-[4/5] w-full overflow-hidden bg-black">
        @php
            // 🛠️ Costruzione Path Immagine Relativo (Oracode: Esplicitamente Intenzionale)
            // Ricostruisce il path RELATIVO a storage/app/public/ come definito dalla logica di upload.
            $imageRelativePath =
                $egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension
                    ? sprintf(
                        'users_files/collections_%d/creator_%d/%d.%s',
                        $egi->collection_id,
                        $egi->user_id,
                        $egi->key_file,
                        $egi->extension,
                    )
                    : null;

            // 🔗 Generazione URL Pubblico usando asset() (Oracode: Pragmatico)
            // Usa l'helper asset() che correttamente include la porta se necessario
// e presuppone che il link simbolico 'public/storage' esista e punti a 'storage/app/public'.
$imageUrl = $imageRelativePath ? asset('storage/' . $imageRelativePath) : null;
        @endphp

        {{-- 🎯 Immagine Principale o Placeholder --}}
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" {{-- Usa l'URL generato con asset() --}} alt="{{ $egi->title ?? 'EGI Image' }}"
                class="h-full w-full object-cover object-center transition-transform duration-300 ease-in-out group-hover:scale-105"
                loading="lazy" />
        @else
            {{-- Placeholder --}}
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                <svg class="h-16 w-16 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        {{-- Overlay leggero su hover --}}
        <div class="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
        </div>

        {{-- Badges (Posizione, Media Type, Owned) --}}
        @if ($egi->position)
            <span
                class="absolute left-2 top-2 inline-block rounded-full bg-black/50 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                #{{ $egi->position }}
            </span>
        @endif
        
        {{-- Badge Owned se siamo nel portfolio collector --}}
        @if ($showPurchasePrice)
            <span
                class="absolute right-2 top-2 inline-flex items-center gap-1 rounded-full bg-green-500/90 px-2 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                OWNED
            </span>
        @elseif ($egi->media)
            <span
                class="absolute right-2 top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/50 text-white backdrop-blur-sm"
                title="{{ __('Media Content') }}">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path
                        d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.84Z" />
                </svg>
            </span>
        @endif
    </figure>

    {{-- ℹ️ Sezione Informazioni EGI --}}
    <div class="flex flex-1 flex-col justify-between p-4 bg-gradient-to-b from-gray-900/50 to-gray-900">
        <div>
            {{-- Titolo EGI con icona --}}
            <div class="flex items-center gap-2 mb-2">
                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white leading-tight group-hover:text-purple-300 transition-colors duration-200">
                    {{ Str::limit($egi->title ?? __('✨ Untitled EGI'), 45) }}
                </h3>
            </div>

            {{-- Creator EGI con badge stilizzato --}}
            @if (isset($collection) && $egi->user_id && $egi->user_id != $collection->creator_id && $egi->user)
                <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/50 backdrop-blur-sm">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1">
                            <span class="text-xs font-medium text-gray-300">👨‍🎨 Created by:</span>
                        </div>
                        <span class="text-xs font-semibold text-white truncate">{{ $egi->user->name }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Prezzo con simboli e design migliorato --}}
        <div class="mt-4">
            {{-- Mostra prezzo di acquisto se siamo nel portfolio del collector --}}
            @if ($showPurchasePrice && $egi->pivot && $egi->pivot->offer_amount_eur)
                <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-500/30">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-blue-300">💳 Purchased for</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-white">€{{ number_format($egi->pivot->offer_amount_eur, 2) }}</span>
                    </div>
                </div>
            {{-- Prezzi originali per altri contesti --}}
            @elseif ($egi->price && $egi->price > 0)
                <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-500/30">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-green-300">💰 Price</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-white">{{ number_format($egi->price, 2) }}</span>
                        <span class="text-xs text-green-300 ml-1">ALGO</span>
                    </div>
                </div>
            @elseif($egi->floorDropPrice && $egi->floorDropPrice > 0)
                <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-blue-500/20 to-indigo-500/20 border border-blue-500/30">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-blue-300">📊 Floor</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-white">{{ number_format($egi->floorDropPrice, 2) }}</span>
                        <span class="text-xs text-blue-300 ml-1">ALGO</span>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center p-3 rounded-xl bg-gradient-to-r from-gray-600/20 to-gray-500/20 border border-gray-500/30">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-gray-500 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM4 10a6 6 0 1112 0A6 6 0 014 10z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-300">🚫 Not for sale</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 🎬 Footer Card con design migliorato --}}
    <div class="border-t border-gray-700/50 bg-gray-900/80 backdrop-blur-sm px-4 py-3">
        <div class="flex items-center justify-between gap-2">
            {{-- Link Visualizza Dettaglio con stile migliorato --}}
            <a href="{{ route('egis.show', $egi->id) }}"
                class="inline-flex flex-shrink-0 items-center justify-center rounded-lg px-3 py-2 text-xs font-semibold text-gray-300 bg-gray-800 border border-gray-600 hover:bg-gray-700 hover:text-white hover:border-gray-500 transition-all duration-200 shadow-sm hover:shadow-md"
                aria-label="{{ __('View EGI details') }}">
                <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                    <path fill-rule="evenodd"
                        d="M.664 10.59a1.651 1.651 0 0 1 0-1.18C3.6 8.229 6.614 6.61 10 6.61s6.4 1.619 9.336 3.8a1.651 1.651 0 0 1 0 1.18C16.4 13.771 13.386 15.39 10 15.39s-6.4-1.619-9.336-3.8ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('View') }}
            </a>

            {{-- Pulsante Riserva stilizzato (solo se non è nascosto) --}}
            @if (!$hideReserveButton)
                @php
                    // Determina se $collection è disponibile (potrebbe non essere passato in alcuni contesti)
                    $creatorId = isset($collection) ? $collection->creator_id : $egi->collection->creator_id ?? null;
                    // Usa solo il campo booleano is_published per determinare se l'EGI è pubblicato
                    $isPublished = (bool) $egi->is_published;
                    $canReserve = !$egi->mint && ($isPublished || (auth()->check() && auth()->id() === $creatorId));
                @endphp
                @if ($canReserve)
                    <button
                        class="reserve-button inline-flex flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 px-3 py-2 text-xs font-semibold text-white shadow-lg hover:from-green-600 hover:to-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-all duration-200 hover:shadow-xl hover:scale-105"
                        data-egi-id="{{ $egi->id }}">
                        <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Reserve') }}
                    </button>
                @endif
            @endif
        </div>
    </div>

</article>
