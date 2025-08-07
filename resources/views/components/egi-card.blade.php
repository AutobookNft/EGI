{{-- resources/views/components/egi-card.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Card --}}
{{-- Displays a single EGI card, typically within a collection grid. --}}
{{-- Expects an $egi object (App\Models\Egi) and optionally $collection (for creator comparison). --}}
{{-- Uses Tailwind CSS for a modern, responsive design. --}}

{{-- Props: Definisci l'oggetto egi come richiesto --}}
@props(['egi', 'collection' => null]) {{-- $collection è opzionale ma utile --}}

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

        {{-- Badges (Posizione, Media Type) --}}
        @if ($egi->position)
            <span
                class="absolute left-2 top-2 inline-block rounded-full bg-black/50 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                #{{ $egi->position }}
            </span>
        @endif
        @if ($egi->media)
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
    <div class="flex flex-1 flex-col justify-between p-4">
        <div>
            {{-- Titolo EGI --}}
            <h3
                class="text-base font-semibold text-gray-800 transition-colors duration-200 group-hover:text-indigo-600">
                {{ Str::limit($egi->title ?? __('Untitled EGI'), 45) }}
            </h3>

            {{-- Creator EGI (se diverso da collection creator) --}}
            @if (isset($collection) && $egi->user_id && $egi->user_id != $collection->creator_id && $egi->user)
                <div class="mt-1.5 flex items-center text-xs text-gray-500">
                    @if ($egi->user->profile_photo_url)
                        {{-- Assumendo Jetstream per _url --}}
                        <img src="{{ $egi->user->profile_photo_url }}" alt="{{ $egi->user->name }}"
                            class="mr-1 h-4 w-4 rounded-full object-cover">
                    @elseif ($egi->user->profile_photo_path)
                        {{-- Fallback a path --}}
                        <img src="{{ asset('storage/' . $egi->user->profile_photo_path) }}" alt="{{ $egi->user->name }}"
                            class="mr-1 h-4 w-4 rounded-full object-cover">
                    @else
                        <span
                            class="mr-1 inline-block h-4 w-4 overflow-hidden rounded-full bg-gray-200 align-middle"><svg
                                class="h-full w-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M..." />
                            </svg></span>
                    @endif
                    <span class="truncate">{{ __('Created by:') }} {{ $egi->user->name }}</span>
                </div>
            @endif
        </div>

        {{-- Prezzo EGI --}}
        <div class="mt-3">
            @if ($egi->price && $egi->price > 0)
                <p class="text-sm font-medium text-gray-900">
                    {{ number_format($egi->price, 2) }} <span class="text-xs text-gray-500">ALGO</span>
                </p>
            @elseif($egi->floorDropPrice && $egi->floorDropPrice > 0)
                <p class="text-sm text-gray-500">
                    {{ __('Floor:') }} <span
                        class="font-medium text-gray-700">{{ number_format($egi->floorDropPrice, 2) }}</span> <span
                        class="text-xs">ALGO</span>
                </p>
            @else
                <p class="text-sm italic text-gray-400">{{ __('Not for sale') }}</p>
            @endif
        </div>
    </div>

    {{-- 🎬 Footer Card (Azioni) --}}
    <div class="border-t border-gray-100 px-4 py-3">
        <div class="flex items-center justify-between gap-2">
            {{-- Link Visualizza Dettaglio --}}
            {{-- Usa la rotta corretta per il dettaglio EGI --}}
            <a href="{{ route('egis.show', $egi->id) }}"
                class="inline-flex flex-shrink-0 items-center justify-center rounded-md px-2.5 py-1.5 text-xs font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 transition-colors duration-150 ease-in-out hover:bg-gray-100"
                aria-label="{{ __('View EGI details') }}">
                <svg class="mr-1 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                    <path fill-rule="evenodd"
                        d="M.664 10.59a1.651 1.651 0 0 1 0-1.18l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 0 2.336 0l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 0 2.336 0l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 1 0 1.18l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879a1.651 1.651 0 0 0-2.336 0l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879a1.651 1.651 0 0 0-2.336 0l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879Zm16.473-4.425a.823.823 0 0 1 0 1.166l-1.888 1.888a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0L.664 7.33a.823.823 0 0 1 0-1.166l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l1.888 1.888a.823.823 0 0 1 0 1.166Z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('View') }}
            </a>

            {{-- Pulsante Riserva (Condizionale) --}}
            @php
                // Determina se $collection è disponibile (potrebbe non essere passato in alcuni contesti)
                $creatorId = isset($collection) ? $collection->creator_id : $egi->collection->creator_id ?? null;
                $isPublished = isset($egi->is_published)
                    ? $egi->is_published
                    : $egi->status === 'local' || $egi->status === 'published';
                $canReserve = !$egi->mint && ($isPublished || (auth()->check() && auth()->id() === $creatorId));
            @endphp
            @if ($canReserve)
                <button
                    class="reserve-button inline-flex flex-shrink-0 items-center justify-center rounded-md bg-green-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm transition-colors duration-150 ease-in-out hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
                    data-egi-id="{{ $egi->id }}">
                    {{-- data-reserve-url="{{ route('api.egis.reserve', $egi->id) }}"> --}}
                    <svg class="mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ __('Reserve') }}
                </button>
            @endif
        </div>
    </div>

</article>
