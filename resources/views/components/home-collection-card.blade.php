{{-- resources/views/components/collection-card.blade.php --}}
{{-- 📜 Oracode Blade Component: Collection Card (Overlay Style - v3 - Usa Attributi Castati per Immagini) --}}
{{-- Displays a Collection card with info overlaying the main image. --}}
{{-- Expects $collection object, $imageType prop ('card' default). Assumes image attributes are casted to URL/path. --}}

@props([
    'collection',
    'imageType' => 'card', // 'card', 'avatar', 'cover' etc.
    'displayType' => 'default' // Aggiungiamo 'default' o 'compact' per mobile
])

@php
    $isAvatarDisplay = ($displayType === 'avatar'); // Per la forma tonda
    $imageUrl = '';
    if ($collection) {
        if ($imageType === 'avatar' && $collection->image_avatar) {
            $imageUrl = asset($collection->image_avatar);
        } elseif (($imageType === 'card' || $imageType === 'cover') && $collection->image_card) { // image_card o image_cover
            $imageUrl = asset($collection->image_card);
        } elseif ($collection->image_cover) { // Fallback a image_cover se image_card non c'è
             $imageUrl = asset($collection->image_cover);
        } else {
            $imageUrl = asset('images/default/collection_placeholder.jpg'); // Fallback generale
        }
    } else {
        $imageUrl = asset('images/default/collection_placeholder.jpg');
    }
@endphp

@if($collection)
    <a href="{{ route('home.collections.show', $collection->id) }}" {{-- Assumendo una route e un campo slug_or_id --}}
       class="block w-full h-full group focus:outline-none focus:ring-2 focus:ring-florence-gold focus:ring-offset-2 focus:ring-offset-gray-800 {{ $isAvatarDisplay ? 'p-2' : 'overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 bg-gray-800' }}"
       aria-label="{{ sprintf(__('View collection %s by %s'), $collection->collection_name, $collection->creator?->name) }}">

        @if($isAvatarDisplay)
            {{-- Visualizzazione AVATAR (Mobile) --}}
            <div class="flex flex-col items-center text-center">
                <div class="relative w-24 h-24 mb-3 md:w-28 md:h-28">
                    <img src="{{ $imageUrl }}"
                         alt="{{ $collection->collection_name }}"
                         class="object-cover w-full h-full transition-colors border-2 border-gray-700 rounded-full shadow-md group-hover:border-florence-gold"
                         loading="lazy" decoding="async"
                         width="96" height="96">
                    @if($collection->is_verified) {{-- Esempio di badge verifica --}}
                        <span class="absolute bottom-0 right-0 block p-0.5 bg-blu-algoritmo border-2 border-gray-800 rounded-full" title="{{ __('Verified Collection') }}">
                            <span class="text-sm text-white material-symbols-outlined">verified</span>
                        </span>
                    @endif
                </div>
                <h3 class="text-sm font-semibold text-white truncate font-body group-hover:text-florence-gold" title="{{ $collection->collection_name }}">
                    {{ Str::limit($collection->collection_name, 25) }}
                </h3>
                @if($collection->creator)
                    <p class="text-xs text-gray-400 truncate font-body group-hover:text-gray-300" title="{{ $collection->creator->name }}">
                        {{ __('by') }} {{ Str::limit($collection->creator->name, 20) }}
                    </p>
                @endif
                 {{-- Potresti aggiungere un piccolo contatore di item o EGI qui --}}
            </div>
        @else
            {{-- Visualizzazione CARD (Desktop/Tablet) --}}
            <div class="relative w-full {{ $imageType === 'cover' ? 'aspect-[3/4]' : 'aspect-square' }} overflow-hidden"> {{-- aspect-square o aspect-[3/4] per cover --}}
                <img src="{{ $imageUrl }}"
                     alt="{{ $collection->collection_name }}"
                     class="object-cover w-full h-full transition-transform duration-300 ease-in-out group-hover:scale-105"
                     loading="lazy" decoding="async">
                <div class="absolute inset-0 transition-opacity bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-80 group-hover:opacity-90"></div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-5">
                <h3 class="text-lg font-bold text-white truncate transition-colors md:text-xl font-display group-hover:text-florence-gold" title="{{ $collection->collection_name }}">
                    {{ $collection->collection_name }}
                </h3>
                @if($collection->creator)
                    <p class="mt-1 text-sm text-gray-300 truncate transition-colors font-body group-hover:text-gray-100">
                        {{ __('by') }} {{ $collection->creator->name }}
                    </p>
                @endif
                {{-- Qui potresti aggiungere altre info come il numero di EGI, il floor price, ecc. --}}
                 <div class="mt-3 text-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-florence-gold/20 text-florence-gold font-semibold">
                        {{ $collection->egis_count ?? 0 }} {{ trans_choice('EGI|EGIs', $collection->egis_count ?? 0) }}
                    </span>
                </div>
            </div>
        @endif
    </a>
@else
    {{-- Fallback se $collection non è passato o è null --}}
    <div class="flex items-center justify-center w-full h-full p-4 text-center text-gray-500 bg-gray-800 rounded-xl">
        {{ __('Collection data not available.') }}
    </div>
@endif
