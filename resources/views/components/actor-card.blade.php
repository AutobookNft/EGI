{{-- resources/views/components/actor-card.blade.php --}}
@props([
    'icon', // Nome dell'icona Material Symbols Outlined
    'title', // Titolo per l'attore (es. "Per il Creator")
    'message', // Il messaggio specifico per l'attore
    'ctaText', // Testo del pulsante Call to Action
    'ctaLink', // Link per il pulsante Call to Action
    'ctaIcon' => null, // Icona opzionale per la CTA
    'accentColorClass' => 'border-florence-gold', // Classe Tailwind per il colore dell'accento (es. border-t-2 border-florence-gold)
    'ctaBgColorClass' => 'bg-florence-gold',
    'ctaTextColorClass' => 'text-gray-900', // Testo scuro per sfondo oro
    'ctaHoverBgColorClass' => 'hover:bg-florence-gold-dark',
    'iconColorClass' => 'text-florence-gold' // Colore per l'icona principale
])

<article class="flex flex-col h-full p-6 overflow-hidden transition-shadow duration-300 ease-in-out bg-gray-800 border border-gray-700 rounded-lg shadow-lg md:p-8 hover:shadow-xl {{ $accentColorClass }}">
    {{-- Icona --}}
    <div class="flex-shrink-0 mb-6 text-center">
        <span class="material-symbols-outlined text-5xl md:text-6xl {{ $iconColorClass }}" aria-hidden="true">
            {{ $icon }}
        </span>
    </div>

    {{-- Titolo --}}
    <h3 class="mb-3 text-2xl font-bold text-center text-white font-display md:text-3xl">
        {{ $title }}
    </h3>

    {{-- Messaggio --}}
    <p class="flex-grow mb-8 text-base text-center text-gray-300 font-body">
        {{ $message }}
    </p>

    {{-- Call to Action --}}
    <div class="mt-auto text-center">
        <a href="{{ $ctaLink }}"
           class="inline-flex items-center justify-center px-8 py-3 text-base font-semibold border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-current {{ $ctaBgColorClass }} {{ $ctaTextColorClass }} {{ $ctaHoverBgColorClass }} transition-colors duration-300">
            @if($ctaIcon)
                <span class="mr-2 -ml-1 material-symbols-outlined" aria-hidden="true">{{ $ctaIcon }}</span>
            @endif
            <span>{{ $ctaText }}</span>
        </a>
    </div>
</article>
