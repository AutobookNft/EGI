{{-- resources/views/components/egi-card.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Card --}}
{{-- Displays a single EGI card, typically within a collection grid. --}}
{{-- Expects an $egi object (App\Models\Egi) and optionally $collection (for creator comparison). --}}
{{-- Uses Tailwind CSS for a modern, responsive design. --}}

{{-- Props: Definisci l'oggetto egi come richiesto --}}
@props([
'egi',
'collection' => null,
'showPurchasePrice' => false,
'hideReserveButton' => false,
'portfolioContext' => false,
]) {{-- 🚀 Nuovo prop per context portfolio --}}

@php
// 🔥 HYPER MODE: Leggiamo direttamente dal database il campo hyper dell'EGI
$isHyper = $egi->hyper ?? false;
// 📦 Portfolio: calcolo stato outbid per applicare opacità e badge corretti
$portfolioOutbid = false;
if ($portfolioContext && auth()->check()) {
    try {
        // Ultima prenotazione dell'utente su questo EGI
        $userLastReservation = $egi->reservations()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->first();

        $isWinning = $userLastReservation && $userLastReservation->is_current && $userLastReservation->status === 'active' && !$userLastReservation->superseded_by_id;
        $portfolioOutbid = $userLastReservation && !$isWinning;
    } catch (\Throwable $th) {
        $portfolioOutbid = false;
    }
}
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

        {{-- Badges (Posizione, Media Type, Owned) --}}
        @if ($egi->position)
        <span
            class="position-badge absolute left-2 top-2 inline-block rounded-full bg-black/50 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
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
        // Determina lo status della prenotazione per questo EGI nel contesto portfolio
        $userReservation = $egi->reservations()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->first();
        $isWinning = $userReservation && $userReservation->is_current && $userReservation->status === 'active' && !$userReservation->superseded_by_id;
        @endphp

        @if ($isWinning)
            @if ($isHyper)
            {{-- Badge composto HYPER + OFFERTA VINCENTE (portfolio, winning) --}}
            <div class="badge-composite" data-portfolio-badge="1" title="{{ __('egi.badge.winning_bid') }}" data-lbl-winning="{{ __('egi.badge.winning_bid') }}" data-lbl-not-owned="{{ __('egi.badge.not_owned') }}">
                <div class="hyper-overlay">⭐ HYPER ⭐</div>
                <div class="owned-base">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('egi.badge.winning_bid') }}
                </div>
            </div>
            @else
            <span data-portfolio-badge="1" data-lbl-winning="{{ __('egi.badge.winning_bid') }}" data-lbl-not-owned="{{ __('egi.badge.not_owned') }}"
                class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 bg-green-500/90 backdrop-blur-sm"
                title="You have the winning bid for this EGI">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('egi.badge.winning_bid') }}
            </span>
            @endif
        @else
        @if ($isHyper)
        {{-- Badge composto HYPER + NON POSSEDUTO (portfolio, outbid) --}}
    <div class="badge-composite" data-portfolio-badge="1" title="{{ __('egi.badge.not_owned') }}" data-lbl-winning="{{ __('egi.badge.winning_bid') }}" data-lbl-not-owned="{{ __('egi.badge.not_owned') }}">
            <div class="hyper-overlay">⭐ HYPER ⭐</div>
            <div class="not-owned-base">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 11-6 0 3 3 0 016 0zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                {{ __('egi.badge.not_owned') }}
            </div>
        </div>
        @else
    <span data-portfolio-badge="1" data-lbl-winning="{{ __('egi.badge.winning_bid') }}" data-lbl-not-owned="{{ __('egi.badge.not_owned') }}"
            class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 bg-red-600/90 backdrop-blur-sm"
            title="{{ __('egi.badge.not_owned') }}">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            {{ __('egi.badge.not_owned') }}
        </span>
        @endif
        @endif
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
                {{-- Logo piattaforma --}}
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/logo/logo_1.webp') }}" alt=""
                        class="w-4 h-4 transition-opacity duration-200 opacity-60 group-hover:opacity-80" loading="lazy"
                        decoding="async" aria-hidden="true" role="img"
                        title="{{ __('egi.platform.powered_by', ['platform' => 'Frangette']) }}">
                </div>
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
            @elseif ($showPurchasePrice && $egi->pivot && $egi->pivot->offer_amount_eur)
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
                    <span class="text-sm font-bold text-white">€{{ number_format($egi->pivot->offer_amount_eur, 2)
                        }}</span>
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
                $displayPrice = $highestPriorityReservation->offer_amount_algo ?? $egi->price;
                $displayUser = $highestPriorityReservation->user;
            }
            @endphp
            
            <div
                class="p-3 border rounded-xl border-green-500/30 bg-gradient-to-r from-green-500/20 to-emerald-500/20">
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
                                {{ $highestPriorityReservation->type === 'weak' ? __('egi.reservation.fegi_reservation') : __('egi.reservation.highest_bid') }}
                            @else
                                {{ __('egi.price.price') }}
                            @endif
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-white">{{ number_format($displayPrice, 2) }}</span>
                        <span class="ml-1 text-xs text-green-300">ALGO</span>
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
                            <path fill-rule="evenodd" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" clip-rule="evenodd" />
                        </svg>
                        @else
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        @endif
                    </div>
                    <span class="text-xs {{ $textColor }} truncate">
                        @if ($isWeakReservation)
                            {{ __('egi.reservation.weak_bidder') }}: <span class="font-semibold">{{ $highestPriorityReservation->fegi_code ?? 'FG#******' }}</span>
                        @else
                            {{ __('egi.reservation.strong_bidder') }}: <span class="font-semibold">{{ $displayUser->name }}</span>
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

    {{-- 🎬 Footer Card con design migliorato --}}
    <div class="px-4 py-3 border-t border-gray-700/50 bg-gray-900/80 backdrop-blur-sm">
        <div class="flex min-h-[36px] items-center gap-2">
            {{-- Link Visualizza Dettaglio con stile migliorato --}}
            <a href="{{ route('egis.show', $egi->id) }}"
                class="inline-flex items-center justify-center flex-shrink-0 px-3 py-2 text-xs font-semibold text-gray-300 transition-all duration-200 bg-gray-800 border border-gray-600 rounded-lg shadow-sm hover:border-gray-500 hover:bg-gray-700 hover:text-white hover:shadow-md"
                aria-label="{{ __('egi.actions.view_details') }}">
                <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                    <path fill-rule="evenodd"
                        d="M.664 10.59a1.651 1.651 0 0 1 0-1.18C3.6 8.229 6.614 6.61 10 6.61s6.4 1.619 9.336 3.8a1.651 1.651 0 0 1 0 1.18C16.4 13.771 13.386 15.39 10 15.39s-6.4-1.619-9.336-3.8ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('egi.actions.view') }}
            </a>

            {{-- Spacer per spingere i bottoni a destra ma non fino al bordo --}}
            <div class="flex-1"></div>

            {{-- Container fisso per i bottoni di azione --}}
            <div class="flex items-center gap-1">

                {{-- Pulsante Riserva stilizzato (solo se non è nascosto) --}}
                @if (!$hideReserveButton)
                @php
                // Determina se $collection è disponibile (potrebbe non essere passato in alcuni contesti)
                $creatorId = isset($collection)
                ? $collection->creator_id
                : $egi->collection->creator_id ?? null;
                // Usa solo il campo booleano is_published per determinare se l'EGI è pubblicato
                $isPublished = (bool) $egi->is_published;
                // Controlla se ha un prezzo (quindi è effettivamente in vendita)
                $hasPrice =
                ($egi->price && $egi->price > 0) || ($egi->floorDropPrice && $egi->floorDropPrice > 0);
                // L'utente è il creatore?
                $isCreator = auth()->check() && auth()->id() === $creatorId;

                // Il pulsante deve apparire SOLO se:
                // 1. È pubblicato
                // 2. Ha un prezzo (quindi è in vendita)
                // 3. L'utente attuale NON è il creatore
                // 4. Non è mintato
                $canReserve = !$egi->mint && $isPublished && $hasPrice && !$isCreator;

                // 🔥 CONTROLLA SE L'EGI È GIÀ PRENOTATO
                $reservationService = app('App\Services\ReservationService');
                $highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);
                $isReserved = $highestPriorityReservation !== null;
                @endphp
                @if ($canReserve)
                {{-- Pulsante Prenota con colore diverso se già prenotato --}}
                <button
                    class="reserve-button {{ $isReserved ? 'bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600' : 'bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600' }} inline-flex flex-shrink-0 items-center justify-center rounded-lg px-3 py-2 text-xs font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
                    data-egi-id="{{ $egi->id }}" data-has-reservations="{{ $isReserved ? 'true' : 'false' }}">
                    @if ($isReserved)
                    {{-- Icona per EGI già prenotato (warning/alert) --}}
                    <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd" />
                    </svg>
                    @else
                    {{-- Icona per EGI non ancora prenotato (bookmark) --}}
                    <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z"
                            clip-rule="evenodd" />
                    </svg>
                    @endif
                    <span class="button-text">{{ __('egi.actions.reserve') }}</span>
                </button>

                {{-- Se ci sono prenotazioni, aggiungi pulsante Cronologia con margine --}}
                @if ($isReserved)
                <button
                    class="inline-flex items-center justify-center flex-shrink-0 px-2 py-2 ml-1 mr-2 text-xs font-semibold text-white transition-all duration-200 rounded-lg shadow-lg history-button bg-gradient-to-r from-amber-500 to-orange-500 hover:scale-105 hover:from-amber-600 hover:to-orange-600 hover:shadow-xl"
                    data-egi-id="{{ $egi->id }}" title="Visualizza cronologia prenotazioni">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                @endif
                @endif
            </div>
            @endif
        </div>
    </div>

</article>
