{{--
Currency Price Display Component

Mostr// 🎯 LOGICA CORRETTA: // 💰 FORMAT: Formatta il prezzo originale con il simbolo corretto
$formattedOriginalPrice = '';
if ($shouldShowReservationNote) {
    switch($originalCurrency) {
        case 'USD':
            $formattedOriginalPrice = '$' . number_format($originalPrice, 2);
            break;
        case 'EUR':
            $formattedOriginalPrice = '€' . number_format($originalPrice, 2);
            break;
        case 'GBP':
            $formattedOriginalPrice = '£' . number_format($originalPrice, 2);
            break;
        default:
            $formattedOriginalPrice = $originalCurrency . ' ' . number_format($originalPrice, 2);
            break;
    }
}

// 🎨 SIZE-BASED CLASSES: Classi CSS basate sulla dimensione
$sizeClasses = [
    'small' => [
        'container' => 'mt-1 px-1 py-0.5 text-xs',
        'icon' => 'w-2 h-2',
        'text' => 'text-xs font-normal'
    ],
    'normal' => [
        'container' => 'mt-2 px-2 py-1 text-xs',
        'icon' => 'w-3 h-3',
        'text' => 'text-xs font-medium'
    ],
    'large' => [
        'container' => 'mt-3 px-3 py-1.5 text-sm',
        'icon' => 'w-4 h-4',
        'text' => 'text-sm font-medium'
    ]
];

$currentSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp di confronto per la nota
$comparisonCurrency = $targetCurrency ?? (App\Helpers\FegiAuth::check() ? App\Helpers\FegiAuth::user()->preferred_currency ?? 'EUR' : 'EUR');

// 🐛 DEBUG: Log per capire cosa succede
\Log::info('Currency Component Debug', [
    'originalCurrency' => $originalCurrency,
    'comparisonCurrency' => $comparisonCurrency,
    'targetCurrency' => $targetCurrency,
    'currency' => $currency,
    'price' => $price,
    'originalPrice' => $originalPrice
]);un prezzo con supporto per conversione multi-currency real-time.
Include una nota esplicativa quando la valuta di visualizzazione è diversa da quella originale.

@param float $price - Il prezzo da mostrare
@param string $currency - La valuta originale (default: EUR)
@param string $class - Classi CSS aggiuntive
@param bool $showOriginal - Mostra la valuta originale come nota (default: false)
@param bool $showConversionNote - Mostra un indicatore di conversione (default: false)
@param string $originalCurrency - Valuta originale della transazione/prenotazione (opzionale)
@param float $originalPrice - Prezzo originale nella valuta originale (opzionale)
@param bool $showReservationNote - Mostra nota se prezzo da prenotazione (default: true)

Esempio uso base:
<x-currency-price :price="$egi->price" currency="EUR" class="text-2xl font-bold" />

Esempio con nota prenotazione:
<x-currency-price 
    :price="$displayPrice" 
    :currency="$displayCurrency"
    :original-currency="$highestPriorityReservation->fiat_currency"
    :original-price="$highestPriorityReservation->offer_amount_fiat"
    class="text-4xl font-bold text-white" 
/>
--}}

@props([
'price',
'currency' => 'EUR',
'class' => '',
'showOriginal' => false,
'showConversionNote' => false,
'originalCurrency' => null,
'originalPrice' => null,
'showReservationNote' => true,
'targetCurrency' => null,  // Valuta finale per il confronto (es. EUR dal badge)
'size' => 'normal'  // 'small', 'normal', 'large'
])

@php
// 🔧 VALIDATION: Assicuro che price sia sempre un numero valido
$safePrice = is_numeric($price) ? (float)$price : 0;

// � LOGICA CORRETTA: Determina la valuta di confronto per la nota
$comparisonCurrency = $targetCurrency ?? (App\Helpers\FegiAuth::check() ? App\Helpers\FegiAuth::user()->preferred_currency ?? 'EUR' : 'EUR');

// 🏷️ LOGIC: Mostra la nota quando:
// - Abbiamo i dati della prenotazione originale
// - La valuta della prenotazione è diversa dalla valuta target (badge)
$shouldShowReservationNote = $showReservationNote && 
                            $originalCurrency && 
                            $originalPrice && 
                            $originalCurrency !== $comparisonCurrency;

//  FORMAT: Formatta il prezzo originale con il simbolo corretto
$formattedOriginalPrice = '';
if ($shouldShowReservationNote) {
    switch($originalCurrency) {
        case 'USD':
            $formattedOriginalPrice = '$' . number_format($originalPrice, 2);
            break;
        case 'EUR':
            $formattedOriginalPrice = '€' . number_format($originalPrice, 2);
            break;
        case 'GBP':
            $formattedOriginalPrice = '£' . number_format($originalPrice, 2);
            break;
        default:
            $formattedOriginalPrice = $originalCurrency . ' ' . number_format($originalPrice, 2);
            break;
    }
}

// 🎨 SIZE-BASED CLASSES: Classi CSS basate sulla dimensione
$sizeClasses = [
    'small' => [
        'container' => 'mt-1 px-1 py-0.5 text-xs',
        'icon' => 'w-2 h-2',
        'text' => 'text-xs font-normal'
    ],
    'normal' => [
        'container' => 'mt-2 px-2 py-1 text-xs',
        'icon' => 'w-3 h-3',
        'text' => 'text-xs font-medium'
    ],
    'large' => [
        'container' => 'mt-3 px-3 py-1.5 text-sm',
        'icon' => 'w-4 h-4',
        'text' => 'text-sm font-medium'
    ]
];

$currentSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="currency-price-container">
    <span {{ $attributes->merge(['class' => "currency-display {$class}"]) }}
        data-price="{{ $safePrice }}"
        data-currency="{{ $currency }}"
        data-display-options="{{ json_encode([
        'showOriginalCurrency' => $showOriginal,
        'showConversionNote' => $showConversionNote
        ]) }}"
        >
        {{-- Fallback display mentre JS carica --}}
        @if($currency === 'EUR')
        €{{ number_format($safePrice, 2) }}
        @elseif($currency === 'USD')
        ${{ number_format($safePrice, 2) }}
        @elseif($currency === 'GBP')
        £{{ number_format($safePrice, 2) }}
        @else
        {{ $currency }} {{ number_format($safePrice, 2) }}
        @endif
    </span>

    {{-- 💡 RESERVATION NOTE: Mostra solo se valuta display ≠ valuta originale prenotazione --}}
    @if($shouldShowReservationNote)
    <div class="{{ $currentSize['container'] }} bg-amber-500/20 border border-amber-400/30 rounded-md text-amber-300 reservation-note reservation-pulse">
        <div class="flex items-center gap-1">
            <svg class="{{ $currentSize['icon'] }} text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <span class="{{ $currentSize['text'] }}">
                {{ __('egi.originally_reserved_in', [
                    'currency' => $originalCurrency,
                    'amount' => $formattedOriginalPrice
                ]) }}
            </span>
        </div>
    </div>

    {{-- 🎨 CUSTOM STYLES: Animazione personalizzata per la nota --}}
    <style>
        .reservation-pulse {
            animation: reservation-glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes reservation-glow {
            0% {
                background-color: rgba(245, 158, 11, 0.2);
                border-color: rgba(251, 191, 36, 0.3);
                box-shadow: 0 0 5px rgba(245, 158, 11, 0.3);
            }
            100% {
                background-color: rgba(245, 158, 11, 0.35);
                border-color: rgba(251, 191, 36, 0.5);
                box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
            }
        }
        
        .reservation-note:hover {
            background-color: rgba(245, 158, 11, 0.4) !important;
            border-color: rgba(251, 191, 36, 0.6) !important;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.6) !important;
            transition: all 0.3s ease-in-out;
        }
    </style>
    @endif
</div>
