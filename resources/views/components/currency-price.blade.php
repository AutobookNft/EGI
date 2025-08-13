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
'showConversionNote' => false
])

<span {{ $attributes->merge(['class' => "currency-display {$class}"]) }}
    data-price="{{ $price }}"
    data-currency="{{ $currency }}"
    data-display-options="{{ json_encode([
    'showOriginalCurrency' => $showOriginal,
    'showConversionNote' => $showConversionNote
    ]) }}"
    >
    {{-- Fallback display mentre JS carica --}}
    @if($currency === 'EUR')
    €{{ number_format($price, 2) }}
    @elseif($currency === 'USD')
    ${{ number_format($price, 2) }}
    @elseif($currency === 'GBP')
    £{{ number_format($price, 2) }}
    @else
    {{ $currency }} {{ number_format($price, 2) }}
    @endif
</span>
