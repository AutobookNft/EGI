{{-- resources/views/components/collection-card.blade.php --}}
{{-- 📜 Oracode Blade Component: Collection Card (Overlay Style - v3 - Usa Attributi Castati per Immagini) --}}
{{-- Displays a Collection card with info overlaying the main image. --}}
{{-- Expects $collection object, $imageType prop ('card' default). Assumes image attributes are casted to URL/path. --}}

@props([
    'collection',
    'imageType' => 'card'
])

{{-- 🧱 Card Container (Link) --}}
<a href="{{ route('home.collections.show', $collection->id) }}" class="relative block w-full overflow-hidden transition-shadow duration-300 ease-in-out bg-gray-800 rounded-lg shadow-md collection-card group hover:shadow-xl">
    <div class="pb-[125%] relative">
        {{-- 🖼️ Immagine di Sfondo --}}
        @php
            // Determina QUALE attributo usare in base a imageType
            $imagePathAttribute = match ($imageType) {
                'banner' => 'image_banner',
                'avatar' => 'image_avatar',
                default => 'image_card',
            };
            // ACCEDI DIRETTAMENTE all'attributo castato. Laravel eseguirà il Cast (EGIImageCast)
            // restituendo l'URL/path corretto come definito nel Cast.
            $imageUrl = $collection->{$imagePathAttribute};
        @endphp

        @if ($imageUrl)
            {{-- Usa direttamente $imageUrl ottenuto dal Cast --}}
            <img src="{{ $imageUrl }}"
                alt="Cover image for {{ $collection->collection_name }}"
                class="absolute inset-0 object-cover object-center w-full h-full transition-transform duration-300 ease-in-out group-hover:scale-105"
                loading="lazy">
        @else
            {{-- Placeholder --}}
            <div class="absolute inset-0 flex items-center justify-center w-full h-full bg-gradient-to-br from-gray-700 via-gray-800 to-gray-900">
                <svg class="w-16 h-16 text-gray-500 opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm16.5-1.5H3.75" />
                </svg>
        </div>
        @endif

        {{-- Overlay Gradiente --}}
        <div class="absolute inset-0 transition-opacity duration-300 bg-gradient-to-t from-black/80 via-black/50 to-transparent opacity-90 group-hover:opacity-100"></div>

        {{-- ℹ️ Contenuto Informativo Sovrapposto --}}
        <div class="absolute bottom-0 left-0 right-0 flex flex-col p-4 text-white">
            {{-- Nome Collezione --}}
            <h3 class="mb-1 text-lg font-semibold truncate" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.8);" title="{{ $collection->collection_name }}">
                {{ $collection->collection_name }}
            </h3>
            {{-- Riga Creator --}}
            <div class="flex items-center mb-2 text-xs opacity-90" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.7);">
                @if ($collection->creator)
                    {{-- Avatar (Usa l'attributo castato se profile_photo_url è gestito così, altrimenti accedi a profile_photo_path e usa Storage::url) --}}
                    @if ($collection->creator->profile_photo_url)
                        <img src="{{ $collection->creator->profile_photo_url }}" alt="{{ $collection->creator->name }}" class="w-4 h-4 rounded-full mr-1.5 border border-white/30 object-cover flex-shrink-0">
                    {{-- Fallback se profile_photo_url non c'è ma c'è il path --}}
                    @elseif ($collection->creator->profile_photo_path)
                        <img src="{{ Storage::disk('public')->url($collection->creator->profile_photo_path) }}" alt="{{ $collection->creator->name }}" class="w-4 h-4 rounded-full mr-1.5 border border-white/30 object-cover flex-shrink-0">
                    @else
                        <span class="inline-block h-4 w-4 rounded-full bg-gray-400 mr-1.5 align-middle border border-white/30 flex-shrink-0"></span>
                    @endif
                    {{-- Nome --}}
                    <span class="truncate">{{ $collection->creator->name }}</span>
                    {{-- Badge Verificato --}}
                    @if ($collection->creator->usertype === 'verified')
                        <svg class="flex-shrink-0 w-3 h-3 ml-1 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.06 0l4-5.5Z" clip-rule="evenodd" /></svg>
                    @endif
                @else
                    <span class="italic text-gray-300">{{ __('Creator Unknown') }}</span>
                @endif
            </div>
            {{-- Riga Statistiche --}}
            <div class="flex items-center justify-between text-[11px] text-gray-200 opacity-80 mt-1">
                <div class="flex items-center" title="{{ __('Items') }}">
                    <svg class="w-3 h-3 mr-1 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                    <span>{{ $collection->EGI_number ?? $collection->egis_count ?? 0 }} {{ __('Items') }}</span>
                </div>
                @if ($collection->floor_price && $collection->floor_price > 0)
                    <div title="{{ __('Floor Price') }}">
                        <span class="font-medium">{{ number_format($collection->floor_price, 2) }}</span>
                        <span class="opacity-70"> ALGO</span>
                    </div>
                @else
                    <div></div>
                @endif
            </div>
        </div>

        {{-- Badge Status (Opzionale) --}}
        @if ($collection->status !== 'published')
            {{-- ... classi badge status ... --}}
        @endif

        {{-- Badge EPP (Opzionale) --}}
        @if ($collection->epp)
            {{-- ... classi badge EPP ... --}}
        @endif
    </div>
</a>
