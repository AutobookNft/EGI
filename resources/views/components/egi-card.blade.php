{{-- resources/views/components/egi-card.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Card --}}

{{-- Uses Tailwind CSS for a modern, responsive design. --}}

{{-- Props: Definisci l'oggetto egi come richiesto --}}
@props([
'egi',
'collection' => null,
'showPurchasePrice' => false,
'hideReserveButton' => false,
'portfolioContext' => false,
'portfolioOwner' => null,
'creatorPortfolioContext' => false, // 🆕 Nuovo prop per Creator Portfolio
]) {{-- 🚀 Nuovo prop per context portfolio --}}

@php
// 🔥 HYPER MODE: Leggiamo direttamente dal database il campo hyper dell'EGI
$isHyper = $egi->hyper ?? false;

// 📦 Portfolio: calcolo stato outbid per applicare opacità e badge corretti
$portfolioOutbid = false;
if ($portfolioContext && $portfolioOwner) {
try {
// Ultima prenotazione del proprietario del portfolio su questo EGI
$ownerLastReservation = $egi->reservations()
->where('user_id', $portfolioOwner->id)
->orderByDesc('created_at')
->first();

$isWinning = $ownerLastReservation && $ownerLastReservation->is_current && $ownerLastReservation->status ===
'active' &&
!$ownerLastReservation->superseded_by_id;
$portfolioOutbid = $ownerLastReservation && !$isWinning;
} catch (\Throwable $th) {
$portfolioOutbid = false;
}
}

// 🎨 Creator Portfolio Context: gestione badge "DA ATTIVARE" per opere non attivate
$showActivationBadge = false;
if ($creatorPortfolioContext && $portfolioOwner) {
// Nel Creator Portfolio, se l'EGI non ha prenotazioni attive, mostra "DA ATTIVARE"
$hasActiveReservations = $egi->reservations()->where('is_current', true)->exists();
$showActivationBadge = !$hasActiveReservations;
}

// 🔒 Creator check: Determina se l'utente corrente è il creatore dell'EGI
$creatorId = $egi->user_id ?? $collection->creator_id ?? null;
$isCreator = auth()->check() && auth()->id() === $creatorId;

// 🔄 Controlla se c'è una prenotazione corrente per il pulsante Rilancia
$hasCurrentReservation = $egi->reservations &&
$egi->reservations->where('is_current', true)->first();
$isCreator = auth()->check() && auth()->id() === $creatorId;
@endphp

{{-- Include CSS hyper se necessario --}}
@if($isHyper)
@once
<link rel="stylesheet" href="{{ asset('css/egi-hyper.css') }}">
@endonce
@endif

{{-- Include CSS tooltip per descrizioni --}}
@once
<link rel="stylesheet" href="{{ asset('css/egi-card-tooltip.css') }}">
@endonce

{{-- 🧱 Card Container --}}
<article
    class="egi-card {{ $isHyper ? 'egi-card--hiper' : '' }} group relative w-full overflow-hidden rounded-2xl border-2 border-purple-500/30 bg-gray-900 transition-all duration-300 hover:border-purple-400 hover:shadow-2xl hover:shadow-purple-500/20 {{ $portfolioOutbid ? 'opacity-35 hover:opacity-70' : '' }}"
    data-egi-id="{{ $egi->id }}" data-hyper="{{ $isHyper ? '1' : '0' }}" style="{{ $isHyper
        ? '--energy:0.95; --foilHue:265; --edge:#9b5cf6; --accent:#a78bfa;'
        : '' }}">

    @if($isHyper)
    <div class="egi-sparkles" aria-hidden="true"></div>
    {{-- Badge HYPER normale solo se NON c'è badge composto --}}
    @if(!$showPurchasePrice && !$portfolioContext)
    <div class="egi-hyper-badge">⭐ HYPER ⭐</div>
    @endif
    @endif
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
            class="object-contain object-center w-full h-full transition-transform duration-300 ease-in-out bg-gray-800 group-hover:scale-105"
            loading="lazy" />
        @else
        {{-- Placeholder --}}
        <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-gray-800 to-gray-900">
            <svg class="w-16 h-16 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        @endif

        {{-- Overlay leggero su hover --}}
        <div class="absolute inset-0 transition-opacity duration-300 opacity-0 bg-black/40 group-hover:opacity-100">
        </div>

        {{-- Logo piattaforma posizionato fuori dal badge --}}
        <img src="{{ asset('images/logo/logo_1.webp') }}" alt=""
            class="absolute w-6 h-6 transition-opacity duration-200 left-2 top-2 opacity-70 hover:opacity-100"
            loading="lazy" decoding="async" aria-hidden="true" role="img"
            title="{{ __('egi.platform.powered_by', ['platform' => 'Frangette']) }}">

        {{-- Badge del numero EGI --}}
        @if ($egi->position)
        <span
            class="position-badge absolute left-10 top-2 rounded-full bg-black/50 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
            #{{ $egi->position }}
        </span>
        @endif

        {{-- 🌟 BADGE COMPOSTO HYPER + POSSEDUTO (SOLUZIONE MICHELIN) --}}
        @if ($showPurchasePrice && $isHyper)
        <div class="badge-composite">
            <div class="hyper-overlay">⭐ HYPER ⭐</div>
            <div class="owned-base">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('egi.badge.owned') }}
            </div>
        </div>
        {{-- Badge Owned normale (no HYPER) --}}
        @elseif ($showPurchasePrice)
        <span
            class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 bg-green-500/90 backdrop-blur-sm">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            {{ __('egi.badge.owned') }}
        </span>
        {{-- 🚀 NEW: Context-aware badges per portfolio --}}
        @elseif ($portfolioContext)
        @php
        // CREATOR PORTFOLIO: Logica speciale per il portfolio del creator
        if ($creatorPortfolioContext) {
        // Nel Creator Portfolio, controlla se l'EGI ha prenotazioni attive (da chiunque)
        $hasAnyActiveReservations = $egi->reservations()->where('is_current', true)->exists();
        $isWinning = $hasAnyActiveReservations; // Se ha prenotazioni = "attivato"
        } else {
        // COLLECTOR PORTFOLIO: Logica normale per altri portfolio
        $ownerReservation = $egi->reservations()
        ->where('user_id', $portfolioOwner->id)
        ->orderByDesc('created_at')
        ->first();
        $isWinning = $ownerReservation && $ownerReservation->is_current && $ownerReservation->status === 'active' &&
        !$ownerReservation->superseded_by_id;
        }
        @endphp

        @if ($isWinning)
        @if ($isHyper)
        {{-- Badge composto HYPER + (ATTIVATO nel Creator Portfolio / OFFERTA VINCENTE negli altri) --}}
        <div class="badge-composite" data-portfolio-badge="1"
            title="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}"
            data-lbl-winning="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}">
            <div class="hyper-overlay">⭐ HYPER ⭐</div>
            <div class="owned-base">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    @if($creatorPortfolioContext)
                    {{-- Icona "Attivato" (check con cerchio) --}}
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                    @else
                    {{-- Icona "Offerta Vincente" originale --}}
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                    @endif
                </svg>
                {{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}
            </div>
        </div>
        @else
        <span data-portfolio-badge="1"
            data-lbl-winning="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}"
            class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 bg-green-500/90 backdrop-blur-sm"
            title="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                @if($creatorPortfolioContext)
                {{-- Icona "Attivato" (check con cerchio) --}}
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
                @else
                {{-- Icona "Offerta Vincente" originale --}}
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
                @endif
            </svg>
            {{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}
        </span>
        @endif
        @else
        @if ($isHyper)
        {{-- Badge composto HYPER + (DA ATTIVARE / NON POSSEDUTO) --}}
        <div class="badge-composite" data-portfolio-badge="1"
            title="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}"
            data-lbl-winning="{{ __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}">
            <div class="hyper-overlay">⭐ HYPER ⭐</div>
            <div class="not-owned-base">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    @if($creatorPortfolioContext && $showActivationBadge)
                    {{-- Icona "Attivazione" (play/start) --}}
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8.108v3.784a1 1 0 001.555.94l3.108-1.892a1 1 0 000-1.688L9.555 7.168z"
                        clip-rule="evenodd" />
                    @else
                    {{-- Icona "Non Posseduto" (persona) --}}
                    <path fill-rule="evenodd" d="M10 9a3 3 0 11-6 0 3 3 0 016 0zm-7 9a7 7 0 1114 0H3z"
                        clip-rule="evenodd" />
                    @endif
                </svg>
                {{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') :
                __('egi.badge.not_owned') }}
            </div>
        </div>
        @else
        <span data-portfolio-badge="1" data-lbl-winning="{{ __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}"
            class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 {{ $creatorPortfolioContext && $showActivationBadge ? 'bg-blue-600/90' : 'bg-red-600/90' }} backdrop-blur-sm"
            title="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                @if($creatorPortfolioContext && $showActivationBadge)
                {{-- Icona "Attivazione" (play/start) --}}
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8.108v3.784a1 1 0 001.555.94l3.108-1.892a1 1 0 000-1.688L9.555 7.168z"
                    clip-rule="evenodd" />
                @else
                {{-- Icona "Non Posseduto" (warning) --}}
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
                @endif
            </svg>
            {{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') :
            __('egi.badge.not_owned') }}
        </span>
        @endif
        @endif
        {{-- Badge per contenuto media --}}
        @elseif ($egi->media)
        <span
            class="absolute inline-flex items-center justify-center w-6 h-6 text-white rounded-full right-2 top-2 bg-black/50 backdrop-blur-sm"
            title="{{ __('egi.badge.media_content') }}">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path
                    d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.84Z" />
            </svg>
        </span>
        @endif
    </figure>

    {{-- ℹ️ Sezione Informazioni EGI --}}
    <div class="flex flex-col justify-between flex-1 p-4 bg-gradient-to-b from-gray-900/50 to-gray-900">
        <div>
            {{-- Titolo EGI con icona e logo piattaforma --}}
            <div class="flex items-center gap-2 mb-2">
                <div
                    class="flex items-center justify-center flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-purple-500 to-pink-500">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <h3
                    class="flex-1 text-base font-bold leading-tight text-white transition-colors duration-200 group-hover:text-purple-300 {{ $egi->description ? 'has-description' : '' }}">
                    {{ Str::limit($egi->title ?? __('egi.title.untitled'), 45) }}

                    {{-- Tooltip descrizione (solo desktop con hover) --}}
                    @if($egi->description)
                    <div
                        class="absolute z-50 px-3 py-2 mb-2 text-sm font-normal text-white bg-gray-900 border border-gray-700 rounded-lg shadow-xl bottom-full left-1/2 min-w-64 max-w-80">
                        {{ Str::limit($egi->description, 200) }}
                    </div>
                    @endif
                </h3>
                {{-- Like Button al posto del logo --}}
                @if(!$isCreator)
                <div class="flex-shrink-0">
                    <button
                        class="p-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full transition-all duration-200 border border-white/20 like-button {{ $egi->is_liked ?? false ? 'is-liked bg-pink-500/20 border-pink-400/50' : '' }}"
                        data-resource-type="egi" data-resource-id="{{ $egi->id }}"
                        title="{{ __('egi.like_button_title') }}">
                        <svg class="w-4 h-4 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-400' : 'text-white' }}"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                @endif
            </div>

            {{-- Creator EGI con badge stilizzato --}}
            @if (isset($collection) && $egi->user_id && $egi->user_id != $collection->creator_id && $egi->user)
            <div
                class="flex items-center gap-2 p-2 border rounded-lg border-gray-700/50 bg-gray-800/50 backdrop-blur-sm">
                <div
                    class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-gray-300">{{ __('egi.creator.created_by') }}</span>
                    </div>
                    <span class="text-xs font-semibold text-white truncate">{{ $egi->user->name }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Prezzo con simboli e design migliorato --}}
        <div class="mt-4">
            {{-- PRIORITÀ ASSOLUTA: Se non è pubblicato, mostra sempre "Bozza" --}}
            @if (!(bool) $egi->is_published)
            <div
                class="flex items-center justify-center p-3 border rounded-xl border-yellow-500/30 bg-gradient-to-r from-yellow-600/20 to-amber-500/20">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-6 h-6 bg-yellow-500 rounded-full">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-yellow-300">{{ __('egi.status.draft') }}</span>
                </div>
            </div>
            {{-- Solo per EGI pubblicati: mostra prezzi o status --}}
            @elseif ($showPurchasePrice && $egi->pivot && $egi->pivot->offer_amount_fiat)
            <div
                class="flex items-center justify-between p-3 border rounded-xl border-blue-500/30 bg-gradient-to-r from-blue-500/20 to-purple-500/20">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-6 h-6 bg-blue-500 rounded-full">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-blue-300">{{ __('egi.price.purchased_for') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-white">
                        <x-currency-price :price="$egi->pivot->offer_amount_fiat" />
                    </span>
                </div>
            </div>
            {{-- EGI con prenotazioni attive: mostra prezzo e utente prenotazione più alta --}}
            @elseif ($egi->price && $egi->price > 0)
            @php
            // Ottengo la prenotazione con priorità più alta per questo EGI
            $reservationService = app('App\Services\ReservationService');
            $highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);
            $displayPrice = $egi->price; // Prezzo base di default
            $displayUser = null;

            // Se c'è una prenotazione attiva, uso il suo prezzo e utente
            if ($highestPriorityReservation && $highestPriorityReservation->status === 'active') {
            $displayPrice = $highestPriorityReservation->offer_amount_fiat ?? $egi->price;
            $displayUser = $highestPriorityReservation->user;

            // 🎯 EUR-ONLY SYSTEM: Convertiamo in EUR se necessario
            if ($highestPriorityReservation->fiat_currency !== 'EUR') {
            $displayPrice = $highestPriorityReservation->amount_eur ?? $displayPrice;
            }
            }
            // Altrimenti usa il prezzo base dell'EGI (sempre in EUR)
            @endphp

            <div class="p-3 border rounded-xl border-green-500/30 bg-gradient-to-r from-green-500/20 to-emerald-500/20">
                {{-- Prezzo e icona --}}
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-6 h-6 bg-green-500 rounded-full">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-green-300">
                            @if ($highestPriorityReservation)
                            {{ $highestPriorityReservation->type === 'weak' ? __('egi.reservation.fegi_reservation')
                            :
                            __('egi.reservation.highest_bid') }}
                            @else
                            {{ __('egi.price.price') }}
                            @endif
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-white">
                            <x-currency-price :price="$displayPrice" :reservation="$highestPriorityReservation"
                                size="small" />
                        </span>
                    </div>
                </div>

                {{-- Utente/Codice prenotazione più alta (STRONG vs WEAK) --}}
                @if ($displayUser || $highestPriorityReservation)
                @php
                $isWeakReservation = $highestPriorityReservation && $highestPriorityReservation->type === 'weak';
                $badgeColor = $isWeakReservation ? 'bg-amber-600' : 'bg-green-600';
                $textColor = $isWeakReservation ? 'text-amber-200' : 'text-green-200';
                $borderColor = $isWeakReservation ? 'border-amber-500/20' : 'border-green-500/20';
                @endphp

                <div class="flex items-center gap-2 pt-2 border-t {{ $borderColor }}">
                    <div class="flex items-center justify-center flex-shrink-0 w-4 h-4 {{ $badgeColor }} rounded-full">
                        @if ($isWeakReservation)
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"
                                clip-rule="evenodd" />
                        </svg>
                        @else
                        @php
                        $activatorDisplay = formatActivatorDisplay($displayUser);
                        @endphp

                        @if ($activatorDisplay['is_commissioner'] && $activatorDisplay['avatar'])
                        {{-- Commissioner with avatar --}}
                        <img src="{{ $activatorDisplay['avatar'] }}" alt="{{ $activatorDisplay['name'] }}"
                            class="object-cover w-4 h-4 border rounded-full border-white/20">
                        @else
                        {{-- Regular collector or commissioner without avatar --}}
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        @endif
                        @endif
                    </div>
                    <span class="text-xs {{ $textColor }} truncate">
                        @if ($isWeakReservation)
                        {{ __('egi.reservation.weak_bidder') }}: <span class="font-semibold">{{
                            $highestPriorityReservation->fegi_code ?? 'FG#******' }}</span>
                        @else
                        @php
                        if (!isset($activatorDisplay)) {
                        $activatorDisplay = formatActivatorDisplay($displayUser);
                        }
                        @endphp
                        {{ __('egi.reservation.activator') }}:
                        @if ($displayUser && isset($displayUser->usertype))
                        @php
                        $homeRoute = match($displayUser->usertype) {
                        'collector' => route('collector.home', $displayUser->id),
                        'commissioner' => route('collector.home', $displayUser->id),
                        'patron' => route('collector.home', $displayUser->id),
                        default => '#'
                        };
                        @endphp
                        <a href="{{ $homeRoute }}"
                            class="font-semibold transition-colors duration-200 hover:underline hover:text-blue-400"
                            title="Visualizza profilo di {{ $activatorDisplay['name'] }}">
                            {{ $activatorDisplay['name'] }}
                        </a>
                        @else
                        <span class="font-semibold">{{ $activatorDisplay['name'] }}</span>
                        @endif
                        @endif
                    </span>
                </div>
                @endif
            </div>
            @elseif($egi->floorDropPrice && $egi->floorDropPrice > 0)
            <div
                class="flex items-center justify-between p-3 border rounded-xl border-blue-500/30 bg-gradient-to-r from-blue-500/20 to-indigo-500/20">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-6 h-6 bg-blue-500 rounded-full">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-blue-300">{{ __('egi.price.floor') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-white">{{ number_format($egi->floorDropPrice, 2) }}</span>
                    <span class="ml-1 text-xs text-blue-300">ALGO</span>
                </div>
            </div>
            {{-- Per EGI pubblicati senza prezzo: "Non in vendita" --}}
            @else
            <div
                class="flex items-center justify-center p-3 border rounded-xl border-gray-500/30 bg-gradient-to-r from-gray-600/20 to-gray-500/20">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-6 h-6 bg-gray-500 rounded-full">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM4 10a6 6 0 1112 0A6 6 0 014 10z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-300">{{ __('egi.status.not_for_sale') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- 🔥 Pulsante Prenota/Rilancia in fondo alla card (identico a egi-card-list) --}}
    @if(!$isCreator && !$hideReserveButton)
    <div class="mt-3">
        @if($egi->price && $egi->price > 0)
        <button type="button" class="reserve-button w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white
                {{ $hasCurrentReservation ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700' : 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700' }}
                rounded-t-none rounded-b-lg transition-all transform hover:scale-[1.01]" data-egi-id="{{ $egi->id }}">
            @if($hasCurrentReservation)
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
            {{ __('egi.actions.outbid') ?? 'Rilancia' }}
            @else
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('egi.actions.reserve') ?? 'Prenota' }}
            @endif
        </button>
        @else
        <div
            class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-t-none rounded-b-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            {{ __('egi.status.not_for_sale') ?? 'Non in vendita' }}
        </div>
        @endif
    </div>
    @endif
</article>
