{{-- resources/views/components/egi-card-enhanced.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 2.0.0 (FlorenceEGI - EGI List Enhanced)
* @date 2025-08-12
* @purpose EGI List component con badge di stato attivazione e informazioni attivatore
--}}

@props([
'egi',
'showPurchasePrice' => false,
'context' => 'default'
])

@php
// Usa la funzione helper esistente per ottenere lo status
$activationStatus = getEgiActivationStatus($egi);
$isHyper = $egi->hyper ?? false;
@endphp

<<article
    class="relative p-4 transition-all duration-300 border egi-card-list group bg-gray-800/50 rounded-xl border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70"
    data-egi-id="{{ $egi->id }}">

    <div class="flex items-start gap-4">
        <!-- EGI Image Section -->
        <div
            class="relative flex-shrink-0 overflow-hidden transition-all duration-300 rounded-lg w-28 h-28 bg-gradient-to-br from-gray-700 to-gray-800 group-hover:ring-2 group-hover:ring-purple-400">

            @if($egi->main_image_url)
            <a href="{{ route('egis.show', $egi->id) }}" class="block w-full h-full">
                <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title ?? 'EGI #' . $egi->id }}"
                    class="object-cover w-full h-full transition-transform duration-300 cursor-pointer group-hover:scale-110">
            </a>
            @else
            <a href="{{ route('egis.show', $egi->id) }}"
                class="flex items-center justify-center w-full h-full cursor-pointer">
                <svg class="w-12 h-12 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                        clip-rule="evenodd" />
                </svg>
            </a>
            @endif

            <!-- Hover overlay for visual feedback -->
            <div
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 opacity-0 bg-purple-400/20 group-hover:opacity-100">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                </svg>
            </div>

            <!-- Status Badge -->
            <div class="absolute -right-1 -top-1">
                @if($activationStatus['status'] === 'activated')
                {{-- Badge Attivato --}}
                <div class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 ring-2 ring-gray-800"
                    title="Attivato da {{ $activationStatus['activator']['name'] ?? 'Attivatore' }}">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                @elseif($activationStatus['status'] === 'in_competition')
                {{-- Badge In Competizione --}}
                <div class="flex items-center justify-center w-6 h-6 bg-orange-500 rounded-full ring-2 ring-gray-800 animate-pulse"
                    title="In competizione - {{ $activationStatus['reservations_count'] }} offerte">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100 4h2a1 1 0 100 2 2 2 0 01-2 2H4a2 2 0 01-2-2V7a2 2 0 012-2z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                @else
                {{-- Badge Disponibile --}}
                <div class="flex items-center justify-center w-6 h-6 bg-green-500 rounded-full ring-2 ring-gray-800"
                    title="Disponibile per attivazione">
                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                </div>
                @endif
            </div>
        </div>

        <!-- Content Section -->
        <div class="flex-1 min-w-0">
            <!-- Title and Collection -->
            <h3 class="mb-1 text-lg font-bold text-white truncate transition-colors group-hover:text-purple-300">
                {{ $egi->title ?? '#' . str_pad($egi->id, 6, '0', STR_PAD_LEFT) }}
            </h3>

            @if($egi->collection)
            <p class="mb-2 text-sm text-gray-400 truncate">
                {{ $egi->collection->title }}
            </p>
            @endif

            <!-- Status and Activator Info -->
            <div class="mb-2">
                @if($activationStatus['status'] === 'activated' && $activationStatus['activator'])
                <div class="flex items-center gap-2 text-sm">
                    {{-- Avatar sempre presente dal backend (gestisce automaticamente la privacy) --}}
                    @if($activationStatus['activator']['avatar'])
                    <img src="{{ $activationStatus['activator']['avatar'] }}"
                        alt="{{ $activationStatus['activator']['name'] }}"
                        class="object-cover w-5 h-5 border rounded-full border-emerald-400/30">
                    @else
                    {{-- Fallback solo se non c'è avatar dal backend (caso molto raro) --}}
                    <div class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    @endif
                    <span class="font-medium text-emerald-300">
                        Attivato da {{ $activationStatus['activator']['name'] }}
                    </span>
                </div>
                @elseif($activationStatus['status'] === 'in_competition')
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100 4h2a1 1 0 100 2 2 2 0 01-2 2H4a2 2 0 01-2-2V7a2 2 0 012-2z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-orange-300">
                        In competizione - {{ $activationStatus['reservations_count'] }} offerte
                    </span>
                </div>
                @else
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="font-medium text-green-300">
                        Disponibile per attivazione
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Price Section -->
        <div class="flex flex-col items-end justify-start text-right">
            <!-- Price -->
            @if($activationStatus['highest_bid'])
            <span class="text-xs text-gray-500 line-through">
                {{-- Desktop: formato standard, Mobile: formato abbreviato --}}
                <span class="hidden md:inline">{{ formatPrice($egi->price) }}</span>
                <span class="md:hidden">{{ formatPriceAbbreviated($egi->price) }}</span>
            </span>
            <span class="text-lg font-bold {{ $isHyper ? 'text-yellow-400' : 'text-orange-400' }}">
                {{-- Desktop: formato standard, Mobile: formato abbreviato --}}
                <span class="hidden md:inline">{{ formatPrice($activationStatus['highest_bid']) }}</span>
                <span class="md:hidden">{{ formatPriceAbbreviated($activationStatus['highest_bid']) }}</span>
            </span>
            @elseif($egi->price)
            <span class="text-lg font-bold {{ $isHyper ? 'text-yellow-400' : 'text-orange-400' }}">
                {{-- Desktop: formato standard, Mobile: formato abbreviato --}}
                <span class="hidden md:inline">{{ formatPrice($egi->price) }}</span>
                <span class="md:hidden">{{ formatPriceAbbreviated($egi->price) }}</span>
            </span>
            @endif

            <!-- Action indicator (only for reservable items) -->
            @if($activationStatus['can_reserve'])
            <div class="flex items-center gap-1 mt-2 text-xs text-purple-300">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h10a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <span>Prenota</span>
            </div>
            @endif
        </div>
    </div>
    </article>
