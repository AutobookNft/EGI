{{-- resources/views/egis/show.blade.php --}}
<x-guest-layout
    :title="$egi->title . ' | ' . $collection->collection_name"
    :metaDescription="Str::limit($egi->description, 155) ?? __('egi.meta_description_default', ['title' => $egi->title])">

{{-- Schema.org nel head --}}
<x-slot name="schemaMarkup">
    @php
    $egiImageUrl = $egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension
        ? asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s',
            $egi->collection_id, $egi->user_id, $egi->key_file, $egi->extension))
        : asset('images/default_egi_placeholder.jpg');
    @endphp
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VisualArtwork",
        "name": "{{ $egi->title }}",
        "description": "{{ $egi->description }}",
        "image": "{{ $egiImageUrl }}",
        "isPartOf": {
            "@type": "CollectionPage",
            "name": "{{ $collection->collection_name }}",
            "url": "{{ route('home.collections.show', $collection->id) }}"
        },
        "author": {
            "@type": "Person",
            "name": "{{ $egi->user->name ?? $collection->creator->name ?? 'Unknown Creator' }}"
        }
    }
    </script>
</x-slot>

{{-- Slot personalizzato per disabilitare la hero section --}}
<x-slot name="noHero">true</x-slot>

{{-- Contenuto principale --}}
<x-slot name="slot">
    {{-- Gallery Layout - Cinema Style con 3 Colonne --}}
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">

        {{-- Cinematic Artwork Display --}}
        <div class="relative w-full">

            {{-- Main Gallery Grid - MODIFICATO per 3 colonne --}}
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-12">

                {{-- Left: Artwork Area (Ridotta da 8-9 a 6-7) --}}
                <div class="relative flex items-center justify-center p-4 lg:col-span-6 xl:col-span-7 lg:p-8">

                    {{-- Artwork Container --}}
                    <div class="relative w-full max-w-5xl">

                        {{-- Main Image Display --}}
                        <div class="relative w-full max-w-5xl mx-auto">
                            @if($imageUrl = (
                                $egi->collection_id
                                && $egi->user_id
                                && $egi->key_file
                                && $egi->extension
                            )
                                ? asset(sprintf(
                                    'storage/users_files/collections_%d/creator_%d/%d.%s',
                                    $egi->collection_id,
                                    $egi->user_id,
                                    $egi->key_file,
                                    $egi->extension
                                ))
                                : null
                            )
                                {{-- Trigger per lo zoom --}}
                                <div id="zoom-container" class="overflow-hidden">
                                    <img
                                        id="zoom-image-trigger"
                                        src="{{ $imageUrl }}"
                                        alt="{{ $egi->title ?? __('egi.image_alt_default') }}"
                                        class="w-full h-auto cursor-zoom-in"
                                        {{-- OPZIONALE: aggiungi data-zoom-src per immagine high-res --}}
                                        {{-- data-zoom-src="{{ $imageUrl }}" --}}
                                    />
                                </div>
                            @else
                                {{-- Placeholder quando non c'è immagine --}}
                                <div
                                    class="flex items-center justify-center w-full shadow-2xl h-96 lg:h-auto bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl"
                                >
                                    <div class="text-center">
                                        <svg class="w-24 h-24 mb-4" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M21 3L3 21" />
                                        </svg>
                                        <p class="text-lg text-gray-400">{{ __('egi.artwork_loading') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Zoom Functionality --}}

                        {{-- Floating Title Card - Elegantly Positioned --}}
                        <div class="absolute bottom-6 left-6 right-6 lg:bottom-8 lg:left-8 lg:right-8">
                            <div class="p-6 border shadow-2xl bg-black/80 backdrop-blur-xl rounded-xl border-white/10">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h1 class="mb-2 text-2xl font-bold tracking-tight text-white lg:text-4xl">{{ $egi->title }}</h1>
                                        <div class="flex items-center space-x-3 text-sm text-gray-300">
                                            <a href="{{ route('home.collections.show', $collection->id) }}"
                                               class="font-medium transition-colors duration-200 hover:text-white">
                                                {{ $collection->collection_name }}
                                            </a>
                                            <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                                            <span>{{ __('egi.by_author', ['name' => $egi->user->name ?? $collection->creator->name ?? __('egi.unknown_creator')]) }}</span>
                                        </div>
                                    </div>

                                    {{-- Quick Actions in Title Area --}}
                                    <div class="flex items-center ml-4 space-x-2">
                                        {{-- Like Button - Compact Version --}}
                                        <button class="p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full transition-all duration-200 border border-white/20 like-button {{ $egi->is_liked ?? false ? 'is-liked bg-pink-500/20 border-pink-400/50' : '' }}"
                                                data-resource-type="egi"
                                                data-resource-id="{{ $egi->id }}"
                                                title="{{ __('egi.like_button_title') }}">
                                            <svg class="w-5 h-5 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-400' : 'text-white' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        {{-- Share Button --}}
                                        <button class="p-3 transition-all duration-200 border rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border-white/20"
                                                title="{{ __('egi.share_button_title') }}">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Center: CRUD Box (CORREZIONE SOLO PERMESSI) --}}
                @php
                    // CORREZIONE: Sostituito auth() con App\Helpers\FegiAuth::
                    $canUpdateEgi = App\Helpers\FegiAuth::check() &&
                                    App\Helpers\FegiAuth::user()->can('update_EGI') &&
                                    $collection->users()->where('user_id', App\Helpers\FegiAuth::id())->whereIn('role', ['admin', 'editor', 'creator'])->exists();

                    $canDeleteEgi = App\Helpers\FegiAuth::check() &&
                                    App\Helpers\FegiAuth::user()->can('delete_EGI') &&
                                    $collection->users()->where('user_id', App\Helpers\FegiAuth::id())->whereIn('role', ['admin', 'creator'])->exists();
                @endphp

                @if($canUpdateEgi)
                <div class="overflow-y-auto border-l border-r lg:col-span-3 xl:col-span-2 bg-gradient-to-b from-emerald-900/20 to-emerald-900/10 backdrop-blur-xl border-emerald-700/30">
                    {{-- CRUD Box Content --}}
                    <div class="sticky top-0 p-4 lg:p-6">
                        <div class="p-6 border bg-gradient-to-br from-emerald-800/20 to-emerald-900/20 rounded-xl border-emerald-700/30">

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-emerald-400">
                                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    {{ __('egi.crud.edit_egi') }}
                                </h3>
                                <button id="egi-edit-toggle"
                                        class="p-2 transition-colors duration-200 rounded-full text-emerald-400 hover:bg-emerald-800/30"
                                        title="{{ __('egi.crud.toggle_edit_mode') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- Edit Form --}}
                            <form id="egi-edit-form"
                                  action="{{ route('egis.update', $egi->id) }}"
                                  method="POST"
                                  class="space-y-4"
                                  style="display: none;">
                                @csrf
                                @method('PUT')

                                {{-- Title Field --}}
                                <div>
                                    <label for="title" class="block mb-2 text-sm font-medium text-emerald-300">
                                        {{ __('egi.crud.title') }}
                                    </label>
                                    <input type="text"
                                           id="title"
                                           name="title"
                                           value="{{ old('title', $egi->title) }}"
                                           class="w-full px-3 py-2 text-white placeholder-gray-400 border rounded-lg bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                           placeholder="{{ __('egi.crud.title_placeholder') }}"
                                           maxlength="60"
                                           required>
                                    <div class="mt-1 text-xs text-gray-400">{{ __('egi.crud.title_hint') }}</div>
                                </div>

                                {{-- Description Field --}}
                                <div>
                                    <label for="description" class="block mb-2 text-sm font-medium text-emerald-300">
                                        {{ __('egi.crud.description') }}
                                    </label>
                                    <textarea id="description"
                                              name="description"
                                              rows="4"
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 border rounded-lg resize-none bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                              placeholder="{{ __('egi.crud.description_placeholder') }}">{{ old('description', $egi->description) }}</textarea>
                                    <div class="mt-1 text-xs text-gray-400">{{ __('egi.crud.description_hint') }}</div>
                                </div>

                                {{-- Price Field --}}
                                <div>
                                    <label for="price" class="block mb-2 text-sm font-medium text-emerald-300">
                                        {{ __('egi.crud.price') }}
                                    </label>
                                    <div class="relative">
                                        <input type="number"
                                               id="price"
                                               name="price"
                                               value="{{ old('price', $egi->price) }}"
                                               step="0.01"
                                               min="0"
                                               class="w-full px-3 py-2 text-white placeholder-gray-400 border rounded-lg bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                               placeholder="{{ __('egi.crud.price_placeholder') }}">
                                        <span class="absolute text-sm text-gray-400 right-3 top-2">ALGO</span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-400">{{ __('egi.crud.price_hint') }}</div>
                                </div>

                                {{-- Creation Date Field --}}
                                <div>
                                    <label for="creation_date" class="block mb-2 text-sm font-medium text-emerald-300">
                                        {{ __('egi.crud.creation_date') }}
                                    </label>
                                    <input type="date"
                                           id="creation_date"
                                           name="creation_date"
                                           value="{{ old('creation_date', $egi->creation_date?->format('Y-m-d')) }}"
                                           class="w-full px-3 py-2 text-white border rounded-lg bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <div class="mt-1 text-xs text-gray-400">{{ __('egi.crud.creation_date_hint') }}</div>
                                </div>

                                {{-- Published Toggle --}}
                                <div>
                                    <label class="flex items-center">
                                        <input type="hidden" name="is_published" value="0">
                                        <input type="checkbox"
                                               id="is_published"
                                               name="is_published"
                                               value="1"
                                               {{ old('is_published', $egi->is_published) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-emerald-600 bg-black/30 border-emerald-700/50 focus:ring-emerald-500 focus:ring-2">
                                        <span class="ml-3 text-sm font-medium text-emerald-300">
                                            {{ __('egi.crud.is_published') }}
                                        </span>
                                    </label>
                                    <div class="mt-1 text-xs text-gray-400 ml-7">{{ __('egi.crud.is_published_hint') }}</div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex gap-3 pt-4">
                                    <button type="submit"
                                            class="inline-flex items-center justify-center flex-1 px-4 py-2 font-medium text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        {{ __('egi.crud.save_changes') }}
                                    </button>

                                    @if($canDeleteEgi)
                                    <button type="button"
                                            id="egi-delete-btn"
                                            class="px-4 py-2 font-medium text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                                            title="{{ __('egi.crud.delete_egi') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </form>

                            {{-- View Mode (Default) --}}
                            <div id="egi-view-mode" class="space-y-4">
                                <div class="p-4 rounded-lg bg-black/20">
                                    <div class="mb-1 text-sm text-emerald-300">{{ __('egi.crud.current_title') }}</div>
                                    <div class="font-medium text-white">{{ $egi->title ?: __('egi.crud.no_title') }}</div>
                                </div>

                                <div class="p-4 rounded-lg bg-black/20">
                                    <div class="mb-1 text-sm text-emerald-300">
                                        {{ $highestPriorityReservation ? __('egi.price.highest_bid') : __('egi.crud.current_price') }}
                                    </div>
                                    <div class="font-medium text-white">
                                        @if($displayPrice)
                                            {{ number_format($displayPrice, 2) }} ALGO
                                        @else
                                            {{ __('egi.crud.price_not_set') }}
                                        @endif
                                    </div>
                                    @if($displayUser || $highestPriorityReservation)
                                    @php
                                    $isWeakReservation = $highestPriorityReservation && $highestPriorityReservation->type === 'weak';
                                    $textColor = $isWeakReservation ? 'text-amber-400' : 'text-emerald-400';
                                    @endphp
                                    
                                    <div class="flex items-center gap-1 mt-2 text-xs {{ $textColor }}">
                                        @if ($isWeakReservation)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" clip-rule="evenodd" />
                                        </svg>
                                        @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        @endif
                                        
                                        @if ($isWeakReservation)
                                            {{ __('egi.reservation.by') }}: {{ $highestPriorityReservation->fegi_code ?? 'FG#******' }}
                                        @else
                                            {{ __('egi.reservation.by') }}: {{ $displayUser->name }}
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                <div class="p-4 rounded-lg bg-black/20">
                                    <div class="mb-1 text-sm text-emerald-300">{{ __('egi.crud.current_status') }}</div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 rounded-full mr-2 {{ $egi->is_published ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                                        <span class="font-medium text-white">
                                            {{ $egi->is_published ? __('egi.crud.status_published') : __('egi.crud.status_draft') }}
                                        </span>
                                    </div>
                                </div>

                                <button id="egi-edit-start"
                                        class="inline-flex items-center justify-center w-full px-4 py-3 font-medium text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-emerald-600/80 to-emerald-700/80 hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    {{ __('egi.crud.start_editing') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Right: Sidebar Esistente (Ridotta da 4-3 a 3) --}}
                <div class="overflow-y-auto border-l lg:col-span-3 bg-gray-900/95 backdrop-blur-xl border-gray-700/50">

                    {{-- Sidebar Content (Invariato) --}}
                    <div class="sticky top-0 p-6 space-y-8 lg:p-8">

                        {{-- Price & Purchase Section --}}
                        @php
                            // Ottengo la prenotazione con priorità più alta per questo EGI
                            $reservationService = app('App\Services\ReservationService');
                            $highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);
                            
                            // Determino il prezzo da mostrare
                            $displayPrice = $egi->price; // Prezzo base di default
                            $displayUser = null;
                            $priceLabel = __('egi.current_price');
                            
                            // Se c'è una prenotazione attiva, uso il suo prezzo e utente
                            if ($highestPriorityReservation && $highestPriorityReservation->status === 'active') {
                                $displayPrice = $highestPriorityReservation->offer_amount_algo ?? $egi->price;
                                $displayUser = $highestPriorityReservation->user;
                                
                                // Label diversa per STRONG vs WEAK
                                if ($highestPriorityReservation->type === 'weak') {
                                    $priceLabel = __('egi.reservation.fegi_reservation');
                                } else {
                                    $priceLabel = __('egi.reservation.highest_bid');
                                }
                            }
                            
                            $isForSale = $displayPrice && $displayPrice > 0 && !$egi->mint;
                            $canBeReserved = !$egi->mint && ($egi->is_published || (App\Helpers\FegiAuth::check() && App\Helpers\FegiAuth::id() === $collection->creator_id));
                        @endphp

                        <div class="p-6 border bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-xl border-gray-700/30">
                            @if($isForSale)
                                <div class="mb-6 text-center">
                                    <p class="mb-2 text-sm text-gray-400">{{ $priceLabel }}</p>
                                    <div class="flex items-baseline justify-center">
                                        <span class="text-4xl font-bold text-white">{{ number_format($displayPrice, 2) }}</span>
                                        <span class="ml-2 text-lg font-medium text-gray-400">ALGO</span>
                                    </div>
                                    
                                    {{-- Miglior offerente (STRONG vs WEAK) --}}
                                    @if($displayUser || $highestPriorityReservation)
                                    @php
                                    $isWeakReservation = $highestPriorityReservation && $highestPriorityReservation->type === 'weak';
                                    $bgColor = $isWeakReservation ? 'bg-amber-500/10' : 'bg-emerald-500/10';
                                    $borderColor = $isWeakReservation ? 'border-amber-500/20' : 'border-emerald-500/20';
                                    $iconBg = $isWeakReservation ? 'bg-amber-500' : 'bg-emerald-500';
                                    $textColor = $isWeakReservation ? 'text-amber-300' : 'text-emerald-300';
                                    @endphp
                                    
                                    <div class="flex items-center justify-center gap-2 mt-3 p-2 {{ $bgColor }} border {{ $borderColor }} rounded-lg">
                                        <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 {{ $iconBg }} rounded-full">
                                            @if ($isWeakReservation)
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" clip-rule="evenodd" />
                                            </svg>
                                            @else
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            @endif
                                        </div>
                                        <span class="text-sm {{ $textColor }}">
                                            @if ($isWeakReservation)
                                                {{ __('egi.reservation.weak_bidder') }}: <span class="font-semibold text-white">{{ $highestPriorityReservation->fegi_code ?? 'FG#******' }}</span>
                                            @else
                                                {{ __('egi.reservation.strong_bidder') }}: <span class="font-semibold text-white">{{ $displayUser->name }}</span>
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="mb-6 text-center">
                                    <p class="text-lg font-semibold text-gray-300">{{ __('egi.not_currently_listed') }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ __('egi.contact_owner_availability') }}</p>
                                </div>
                            @endif

                            {{-- Main Action Buttons --}}
                            <div class="space-y-3">
                                {{-- Like Button - Full Version --}}
                                <button class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-pink-600/80 to-purple-600/80 hover:from-pink-600 hover:to-purple-600 backdrop-blur-sm text-white font-medium rounded-lg transition-all duration-200 border border-pink-500/30 hover:border-pink-400/50 like-button {{ $egi->is_liked ?? false ? 'is-liked ring-2 ring-pink-400/50' : '' }}"
                                        data-resource-type="egi"
                                        data-resource-id="{{ $egi->id }}">
                                    <svg class="-ml-1 mr-3 h-5 w-5 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-300' : 'text-white' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="like-text">{{ $egi->is_liked ?? false ? __('egi.liked') : __('egi.add_to_favorites') }}</span>
                                    <span class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs like-count-display">{{ $egi->likes_count ?? 0 }}</span>
                                </button>

                                {{-- Reserve Button --}}
                                @if($canBeReserved)
                                    <button class="inline-flex items-center justify-center w-full px-6 py-4 font-medium text-white transition-all duration-200 border rounded-lg bg-gradient-to-r from-emerald-600/80 to-teal-600/80 hover:from-emerald-600 hover:to-teal-600 backdrop-blur-sm border-emerald-500/30 hover:border-emerald-400/50 reserve-button"
                                            data-egi-id="{{ $egi->id }}">
                                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('egi.reserve_this_piece') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Properties Section --}}
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-white">{{ __('egi.properties') }}</h3>
                            <div class="grid grid-cols-1 gap-3">
                                @if($collection->epp)
                                    <div class="p-4 border rounded-lg bg-emerald-500/10 border-emerald-400/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="mb-1 text-xs font-medium uppercase text-emerald-400">{{ __('egi.supports_epp') }}</p>
                                                <a href="{{ route('epps.show', $collection->epp->id) }}" class="text-sm font-semibold transition-colors text-emerald-300 hover:text-emerald-200">
                                                    {{ Str::limit($collection->epp->name, 25) }}
                                                </a>
                                            </div>
                                            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                        </div>
                                    </div>
                                @endif

                                @if($egi->type)
                                    <div class="p-4 border rounded-lg bg-blue-500/10 border-blue-400/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="mb-1 text-xs font-medium text-blue-400 uppercase">{{ __('egi.asset_type') }}</p>
                                                <p class="text-sm font-semibold text-blue-300">{{ ucfirst($egi->type) }}</p>
                                            </div>
                                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                        </div>
                                    </div>
                                @endif

                                @if($egi->extension)
                                    <div class="p-4 border rounded-lg bg-purple-500/10 border-purple-400/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="mb-1 text-xs font-medium text-purple-400 uppercase">{{ __('egi.format') }}</p>
                                                <p class="text-sm font-semibold text-purple-300">{{ strtoupper($egi->extension) }}</p>
                                            </div>
                                            <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Description Section --}}
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-white">{{ __('egi.about_this_piece') }}</h3>
                            <div class="leading-relaxed prose-sm prose text-gray-300 prose-invert max-w-none">
                                {!! nl2br(e($egi->description ?? __('egi.default_description'))) !!}
                            </div>
                        </div>

                        {{-- Reservation History --}}
                        @if($egi->reservationCertificates)
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-white">{{ __('egi.provenance') }}</h3>
                                <x-egi-reservation-history :egi="$egi" :certificates="$egi->reservationCertificates" />
                            </div>
                        @endif

                        {{-- Collection Link --}}
                        <div class="pt-6 border-t border-gray-700/50">
                            <a href="{{ route('home.collections.show', $collection->id) }}"
                               class="inline-flex items-center text-gray-300 transition-colors duration-200 hover:text-white group">
                                <svg class="w-4 h-4 mr-2 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                {{ __('egi.view_full_collection') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($canDeleteEgi)
    <div id="delete-modal" class="fixed inset-0 z-50 items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="max-w-md p-6 mx-4 bg-gray-800 border rounded-xl border-red-700/30">
            <div class="text-center">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-medium text-white">{{ __('egi.crud.delete_confirmation_title') }}</h3>
                <p class="mb-6 text-sm text-gray-300">{{ __('egi.crud.delete_confirmation_message') }}</p>

                <div class="flex gap-3">
                    <button id="delete-cancel" class="flex-1 px-4 py-2 text-white transition-colors bg-gray-600 rounded-lg hover:bg-gray-700">
                        {{ __('egi.crud.cancel') }}
                    </button>
                    <form id="delete-form" action="{{ route('egis.destroy', $egi->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                            {{ __('egi.crud.delete_confirm') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Custom Styles for Enhanced Interactivity --}}
    <style>
        .artwork-container:hover img {
            transform: scale(1.01);
            filter: brightness(1.05) contrast(1.02);
        }

        .like-button.is-liked {
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.3) 0%, rgba(147, 51, 234, 0.3) 100%);
            border-color: rgba(236, 72, 153, 0.5);
        }

        @media (max-width: 1024px) {
            .artwork-container {
                margin-bottom: 2rem;
            }
        }

        /* Scrollbar styling for sidebar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: rgba(55, 65, 81, 0.3);
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.8);
        }
    </style>

    {{-- JavaScript per CRUD Interactions --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editStartBtn = document.getElementById('egi-edit-start');
            const editToggleBtn = document.getElementById('egi-edit-toggle');
            const editForm = document.getElementById('egi-edit-form');
            const viewMode = document.getElementById('egi-view-mode');
            const deleteBtn = document.getElementById('egi-delete-btn');
            const deleteModal = document.getElementById('delete-modal');
            const deleteCancel = document.getElementById('delete-cancel');

            // Toggle edit mode
            function toggleEditMode() {
                const isEditing = editForm.style.display !== 'none';

                if (isEditing) {
                    editForm.style.display = 'none';
                    viewMode.style.display = 'block';
                } else {
                    editForm.style.display = 'block';
                    viewMode.style.display = 'none';
                }
            }

            if (editStartBtn) {
                editStartBtn.addEventListener('click', toggleEditMode);
            }

            if (editToggleBtn) {
                editToggleBtn.addEventListener('click', toggleEditMode);
            }

            // Delete modal
            if (deleteBtn && deleteModal) {
                deleteBtn.addEventListener('click', function() {
                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');
                });
            }

            if (deleteCancel) {
                deleteCancel.addEventListener('click', function() {
                    deleteModal.classList.add('hidden');
                    deleteModal.classList.remove('flex');
                });
            }

            // Close modal on background click
            if (deleteModal) {
                deleteModal.addEventListener('click', function(e) {
                    if (e.target === deleteModal) {
                        deleteModal.classList.add('hidden');
                        deleteModal.classList.remove('flex');
                    }
                });
            }

            // Character counter for title
            const titleInput = document.getElementById('title');
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    const remaining = 60 - this.value.length;
                    const hint = this.nextElementSibling;
                    if (hint) {
                        hint.textContent = `{{ __('egi.crud.title_hint') }} (${remaining} {{ __('egi.crud.characters_remaining') }})`;
                        hint.style.color = remaining < 10 ? '#fbbf24' : '#9ca3af';
                    }
                });
            }
        });
    </script>
    {{-- Lightbox Zoom Overlay --}}
    <div
        id="zoom-overlay"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/80 backdrop-blur-sm"
    >
        <div id="zoom-content" class="relative max-w-[90%] max-h-[90%]">
            <img
                id="zoom-overlay-image"
                src=""
                alt=""
                class="max-w-full max-h-full touch-none user-select-none"
                style="object-fit: contain;"
            />
            <button
                id="zoom-close"
                aria-label="Chiudi ingrandimento"
                class="absolute flex items-center justify-center w-10 h-10 text-3xl text-white transition-colors rounded-full top-4 right-4 bg-black/50 hover:bg-black/70"
            >
                ×
            </button>
        </div>
</div>
</x-slot>



<style>
/* Prevenire selezione del testo durante il pan */
.touch-none {
    touch-action: none;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* Migliorare l'overlay */
#zoom-overlay {
    backdrop-filter: blur(4px);
}

/* Cursor style per indicare zoom disponibile */
#zoom-image-trigger:hover {
    cursor: zoom-in;
}

/* Smooth transitions */
#zoom-overlay {
    transition: opacity 0.2s ease-in-out;
}

#zoom-overlay.hidden {
    opacity: 0;
    pointer-events: none;
}

#zoom-overlay:not(.hidden) {
    opacity: 1;
    pointer-events: all;
}
</style>

</x-guest-layout>

{{-- SOSTITUISCI questa riga alla fine del file show.blade.php --}}
{{-- DA: @vite(['resources/ts/zoom.ts']) --}}
{{-- A: Script inline JavaScript --}}

{{-- JavaScript Zoom Implementation - OS2.0 Compliant --}}
<script>
/**
 * @Oracode ImageZoom: Timing-Fixed Implementation
 * 🎯 Purpose: Robust image zoom with element waiting mechanism
 * 🛡️ Security: Error handling and element availability checking
 * 🧱 Core Logic: Waits for all elements before initialization
 *
 * @package FlorenceEGI\Frontend\Zoom
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI MVP Zoom - Timing Fixed)
 * @date 2025-06-30
 */

class ImageZoom {
    constructor(triggerId) {
        this.triggerId = triggerId;
        this.maxRetries = 50; // 5 seconds max wait
        this.retryCount = 0;

        // Zoom state
        this.scale = 1;
        this.panX = 0;
        this.panY = 0;
        this.startX = 0;
        this.startY = 0;
        this.isPanning = false;
        this.startDistance = 0;
        this.isZoomOpen = false;

        // Bind methods to preserve context
        this.handleWheel = this.handleWheel.bind(this);
        this.handlePointerDown = this.handlePointerDown.bind(this);
        this.handlePointerMove = this.handlePointerMove.bind(this);
        this.handlePointerUp = this.handlePointerUp.bind(this);
        this.handleTouchStart = this.handleTouchStart.bind(this);
        this.handleTouchMove = this.handleTouchMove.bind(this);
        this.handleTouchEnd = this.handleTouchEnd.bind(this);

        // Start waiting for elements
        this.waitForElements();
    }

    waitForElements() {
        console.log(`🔍 ZOOM: Waiting for elements... (attempt ${this.retryCount + 1}/${this.maxRetries})`);

        // Try to find all required elements
        this.trigger = document.getElementById(this.triggerId);
        this.overlay = document.getElementById('zoom-overlay');
        this.overlayImage = document.getElementById('zoom-overlay-image');
        this.closeButton = document.getElementById('zoom-close');

        const elementsFound = {
            trigger: !!this.trigger,
            overlay: !!this.overlay,
            overlayImage: !!this.overlayImage,
            closeButton: !!this.closeButton
        };

        console.log('🔍 ZOOM: Elements status:', elementsFound);

        // Check if all elements are available
        const allElementsReady = this.trigger && this.overlay && this.overlayImage && this.closeButton;

        if (allElementsReady) {
            console.log('✅ ZOOM: All elements found! Initializing...');
            this.bindEvents();
        } else {
            this.retryCount++;

            if (this.retryCount >= this.maxRetries) {
                console.error('❌ ZOOM: Failed to find all elements after maximum retries:', elementsFound);
                return;
            }

            // Wait 100ms and try again
            setTimeout(() => this.waitForElements(), 100);
        }
    }

    bindEvents() {
        try {
            console.log('🔗 ZOOM: Binding events...');

            // Trigger click to open zoom
            this.trigger.addEventListener('click', (e) => {
                console.log('🎯 ZOOM: Image clicked!');
                e.preventDefault();
                e.stopPropagation();
                this.open();
            });

            // Close zoom
            this.closeButton.addEventListener('click', (e) => {
                console.log('🎯 ZOOM: Close button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.close();
            });

            this.overlay.addEventListener('click', (e) => {
                if (e.target === this.overlay) {
                    console.log('🎯 ZOOM: Overlay background clicked');
                    this.close();
                }
            });

            // Escape key to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isZoomOpen) {
                    console.log('🎯 ZOOM: Escape key pressed');
                    this.close();
                }
            });

            // Desktop wheel zoom
            this.overlayImage.addEventListener('wheel', this.handleWheel, { passive: false });

            // Pointer events for pan
            this.overlayImage.addEventListener('pointerdown', this.handlePointerDown);
            this.overlayImage.addEventListener('pointermove', this.handlePointerMove);
            this.overlayImage.addEventListener('pointerup', this.handlePointerUp);
            this.overlayImage.addEventListener('pointercancel', this.handlePointerUp);

            // Touch events for pinch-to-zoom
            this.overlayImage.addEventListener('touchstart', this.handleTouchStart, { passive: false });
            this.overlayImage.addEventListener('touchmove', this.handleTouchMove, { passive: false });
            this.overlayImage.addEventListener('touchend', this.handleTouchEnd);
            this.overlayImage.addEventListener('touchcancel', this.handleTouchEnd);

            console.log('✅ ZOOM: All events bound successfully!');

        } catch (error) {
            console.error('❌ ZOOM: Error binding events', error);
        }
    }

    open() {
        try {
            console.log('🚀 ZOOM: Opening zoom...');

            // Get image source with multiple fallbacks
            const src = this.trigger.dataset.zoomSrc ||
                       this.trigger.dataset.src ||
                       this.trigger.src ||
                       this.trigger.getAttribute('src');

            console.log('🔍 ZOOM: Image source:', src);

            if (!src) {
                console.error('❌ ZOOM: No valid image source found');
                return;
            }

            // Set overlay image source
            this.overlayImage.src = src;

            // Show overlay
            this.overlay.classList.remove('hidden');
            this.overlay.style.display = 'flex';

            this.isZoomOpen = true;

            // Prevent body scroll
            document.body.style.overflow = 'hidden';

            this.reset();

            console.log('✅ ZOOM: Zoom opened successfully!');
        } catch (error) {
            console.error('❌ ZOOM: Error opening zoom', error);
        }
    }

    close() {
        try {
            console.log('🔒 ZOOM: Closing zoom...');

            this.overlay.classList.add('hidden');
            this.overlay.style.display = 'none';
            this.isZoomOpen = false;

            // Restore body scroll
            document.body.style.overflow = '';

            this.reset();

            console.log('✅ ZOOM: Zoom closed successfully');
        } catch (error) {
            console.error('❌ ZOOM: Error closing zoom', error);
        }
    }

    reset() {
        this.scale = 1;
        this.panX = 0;
        this.panY = 0;
        this.isPanning = false;
        this.updateTransform();
    }

    handleWheel(e) {
        if (!this.isZoomOpen) return;

        try {
            e.preventDefault();

            const delta = -e.deltaY * 0.002;
            const newScale = Math.min(Math.max(1, this.scale + delta), 5);

            const rect = this.overlayImage.getBoundingClientRect();
            const centerX = (e.clientX - rect.left) / rect.width;
            const centerY = (e.clientY - rect.top) / rect.height;

            if (newScale !== this.scale) {
                const scaleDiff = newScale - this.scale;
                this.panX -= (centerX - 0.5) * rect.width * scaleDiff * 0.5;
                this.panY -= (centerY - 0.5) * rect.height * scaleDiff * 0.5;
            }

            this.scale = newScale;
            this.updateTransform();
        } catch (error) {
            console.error('❌ ZOOM: Error handling wheel', error);
        }
    }

    handlePointerDown(e) {
        if (!this.isZoomOpen) return;

        try {
            e.preventDefault();
            this.isPanning = true;
            this.startX = e.clientX - this.panX;
            this.startY = e.clientY - this.panY;

            if (this.overlayImage.setPointerCapture) {
                this.overlayImage.setPointerCapture(e.pointerId);
            }
        } catch (error) {
            console.error('❌ ZOOM: Error handling pointer down', error);
        }
    }

    handlePointerMove(e) {
        if (!this.isPanning || !this.isZoomOpen) return;

        try {
            this.panX = e.clientX - this.startX;
            this.panY = e.clientY - this.startY;
            this.updateTransform();
        } catch (error) {
            console.error('❌ ZOOM: Error handling pointer move', error);
        }
    }

    handlePointerUp() {
        this.isPanning = false;
    }

    handleTouchStart(e) {
        if (!this.isZoomOpen) return;

        try {
            if (e.touches.length === 2) {
                e.preventDefault();
                const [t1, t2] = Array.from(e.touches);
                this.startDistance = this.getDistance(t1, t2);
            }
        } catch (error) {
            console.error('❌ ZOOM: Error handling touch start', error);
        }
    }

    handleTouchMove(e) {
        if (!this.isZoomOpen) return;

        try {
            if (e.touches.length === 2) {
                e.preventDefault();
                const [t1, t2] = Array.from(e.touches);
                const newDistance = this.getDistance(t1, t2);

                if (this.startDistance > 0) {
                    const factor = newDistance / this.startDistance;
                    this.scale = Math.min(Math.max(1, this.scale * factor), 5);
                    this.startDistance = newDistance;
                    this.updateTransform();
                }
            }
        } catch (error) {
            console.error('❌ ZOOM: Error handling touch move', error);
        }
    }

    handleTouchEnd() {
        this.startDistance = 0;
    }

    getDistance(t1, t2) {
        return Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);
    }

    updateTransform() {
        try {
            this.overlayImage.style.transform =
                `translate(${this.panX}px, ${this.panY}px) scale(${this.scale})`;
        } catch (error) {
            console.error('❌ ZOOM: Error updating transform', error);
        }
    }
}

// Multiple initialization strategies for maximum compatibility
function initializeZoom() {
    console.log('🚀 ZOOM: Attempting to initialize ImageZoom...');
    try {
        new ImageZoom('zoom-image-trigger');
    } catch (error) {
        console.error('❌ ZOOM: Failed to initialize', error);
    }
}

// Strategy 1: DOMContentLoaded
document.addEventListener('DOMContentLoaded', initializeZoom);

// Strategy 2: Immediate if DOM is ready
if (document.readyState !== 'loading') {
    initializeZoom();
}

// Strategy 3: Window load as final fallback
window.addEventListener('load', () => {
    console.log('🔄 ZOOM: Window load event - final initialization attempt');
    setTimeout(initializeZoom, 100);
});

console.log('📝 ZOOM: Script loaded successfully');
</script>
