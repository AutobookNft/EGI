{{--
Currency Price Display Component

Mostra un prezzo con supporto per conversione multi-currency real-time.

@param float $price - Il prezzo da mostrare
@param string $currency - La valuta originale (default: EUR)
@param string $class - Classi CSS aggiuntive
@param bool $showOriginal - Mostra la valuta originale come nota (default: false)
@param bool $showConversionNote - Mostra un indicatore di conversione (default: false)

Esempio uso:
<x-currency-price :price="$egi->price" currency="EUR" class="text-2xl font-bold" />
--}}

@props([
'price',
'currency' => 'EUR',
'class' => '',
'showOriginal' => false,
'showConversionNote' => false,
'reservation' => null,
'targetCurrency' => null,
'size' => 'normal'
])

@php
// 🔧 VALIDATION: Assicuro che price sia sempre un numero valido
$safePrice = is_numeric($price) ? (float)$price : 0;

// 🏷️ RESERVATION NOTE LOGIC: Logica per mostrare la nota di prenotazione originale
$shouldShowReservationNote = false;
$originalCurrency = '';
$originalPrice = 0;
$formattedOriginalPrice = '';

if ($reservation && $targetCurrency) {
    $originalCurrency = $reservation->fiat_currency ?? 'EUR';
    $originalPrice = $reservation->offer_amount_fiat ?? $safePrice;
    
    // Mostra la nota solo se la valuta target è diversa da quella originale
    $shouldShowReservationNote = ($targetCurrency !== $originalCurrency);
    
    if ($shouldShowReservationNote) {
        // Formato il prezzo originale con il simbolo corretto
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
}

// 🎨 SIZE-BASED CLASSES: Classi CSS basate sulla dimensione
$sizeClasses = [
    'small' => [
        'container' => 'mt-1 px-2 py-1 text-xs',
        'icon' => 'w-3 h-3',
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

    {{-- 💰 RESERVATION CURRENCY NOTE: Nota di conversione quando necessario --}}
    @if($shouldShowReservationNote && $formattedOriginalPrice)
        <div class="mt-1 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 text-xs text-amber-700 animate-pulse font-normal inline-block whitespace-nowrap">
            @lang('egi.originally_reserved_in_short', ['currency' => $originalCurrency, 'amount' => $formattedOriginalPrice])
        </div>
    @endif
</div>
