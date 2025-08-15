{{--
Currency Price Display Component - EUR Only System

Mostra un prezzo in EUR con supporto opzionale per conversione ALGO.

@param float $price - Il prezzo da mostrare in EUR
@param string $class - Classi CSS aggiuntive
@param bool $showAlgoConversion - Mostra la conversione in ALGO (default: false)
@param string $reservation - Dati di prenotazione per note informative (opzionale)
@param string $size - Dimensione del componente (small|normal|large)

Esempio uso:
<x-currency-price :price="$egi->price" class="text-2xl font-bold" :show-algo-conversion="true" />
--}}

@props([
'price',
'class' => '',
'showAlgoConversion' => false,
'reservation' => null,
'size' => 'normal'
])

@php
    // 🔧 VALIDATION: Assicuro che price sia sempre un numero valido
    $safePrice = is_numeric($price) ? (float)$price : 0;

    // 🏷️ RESERVATION NOTE LOGIC: Nota di prenotazione originale (solo se diversa da EUR)
    $shouldShowReservationNote = false;
    $originalCurrency = '';
    $originalPrice = 0;
    $formattedOriginalPrice = '';

    if ($reservation) {
    $originalCurrency = $reservation->fiat_currency ?? 'EUR';
    $originalPrice = $reservation->offer_amount_fiat ?? $safePrice;

    // Mostra la nota solo se la prenotazione era in una valuta diversa da EUR
    $shouldShowReservationNote = ($originalCurrency !== 'EUR');

    if ($shouldShowReservationNote) {
    // Formato il prezzo originale con il simbolo corretto
    switch($originalCurrency) {
    case 'USD':
    $formattedOriginalPrice = '$' . number_format($originalPrice, 2);
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
    'text' => 'text-xs font-normal'
    ],
    'normal' => [
    'container' => 'mt-2 px-2 py-1 text-xs',
    'text' => 'text-xs font-medium'
    ],
    'large' => [
    'container' => 'mt-3 px-3 py-1.5 text-sm',
    'text' => 'text-sm font-medium'
    ]
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="currency-price-container">
    <span {{ $attributes->merge(['class' => "currency-display {$class}"]) }}>
        {{-- Sempre EUR - Sistema semplificato --}}
        €{{ number_format($safePrice, 2) }}
    </span>

    {{-- 🪙 ALGO CONVERSION: Sempre visibile --}}
    <div class="inline-flex items-center mt-1 space-x-1 text-xs font-medium text-emerald-600">
        <span>≈</span>
        <span data-eur-amount="{{ $safePrice }}" class="algo-conversion-display">-- ALGO</span>
    </div>

    {{-- 💰 RESERVATION CURRENCY NOTE: Nota se prenotazione era in altra valuta --}}
    @if($shouldShowReservationNote && $formattedOriginalPrice)
    <div
        class="mt-1 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 text-xs text-amber-700 animate-pulse font-normal inline-block whitespace-nowrap">
        @lang('egi.originally_reserved_in_short', ['currency' => $originalCurrency, 'amount' =>
        $formattedOriginalPrice])
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funzione per aggiornare la conversione ALGO
    function updateAlgoConversion() {
        const algoElements = document.querySelectorAll('.algo-conversion-display');
        
        // Prende il tasso dal badge nella navbar
        const rateElement = document.getElementById('currency-rate-value');
        if (!rateElement) return;
        
        const eurToAlgoRate = parseFloat(rateElement.textContent);
        if (!eurToAlgoRate || eurToAlgoRate === 0) return;
        
        algoElements.forEach(element => {
            const eurAmount = parseFloat(element.getAttribute('data-eur-amount'));
            if (eurAmount && eurToAlgoRate) {
                const algoAmount = eurAmount / eurToAlgoRate;
                const formattedAlgo = formatAlgoAmount(algoAmount);
                element.textContent = formattedAlgo + ' ALGO';
            }
        });
    }
    
    function formatAlgoAmount(algoAmount) {
        // Sempre formato numerico intero, senza K
        return Math.round(algoAmount).toLocaleString();
    }
    
    // Aggiorna immediatamente
    setTimeout(updateAlgoConversion, 1000);
    
    // Aggiorna ogni volta che il tasso cambia
    setInterval(updateAlgoConversion, 5000);
});
</script>
