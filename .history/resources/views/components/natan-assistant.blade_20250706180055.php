{{-- resources/views/components/natan-assistant.blade.php --}}

<div id="natan-assistant-container">
    {{-- Pulsante principale Natan --}}
    <button
        id="natan-assistant-toggle"
        type="button"
        aria-expanded="false"
        aria-controls="natan-assistant-menu"
        class="flex items-center justify-center w-12 h-12 overflow-hidden transition-all duration-300 bg-gray-900 rounded-full shadow-lg ring-2 ring-emerald-500/40 hover:ring-emerald-400 group"
        data-natan-state="idle"
    >
        <img
            src="/images/default/natan-face.png"
            alt="Natan Assistant"
            class="w-10 h-10 transition-transform duration-300 group-hover:scale-110 natan-pulse-mini"
        />
        <span class="sr-only">Apri assistente Natan</span>
    </button>

    {{-- Menu di aiuto (inizialmente nascosto) --}}
    <div id="natan-assistant-menu" class="flex flex-col items-end hidden mt-3 space-y-2">
        {{-- Le opzioni saranno generate dinamicamente da JS usando assistantOptions --}}
    </div>

    <style>
    /* NUOVI STILI - SOSTITUISCE TUTTI I PRECEDENTI */
    /* Stili di base per animazioni */
    .natan-pulse-mini {
        animation: natan-pulse-mini 3s ease-in-out infinite;
    }

    @keyframes natan-pulse-mini {
        0%, 100% { filter: brightness(1) drop-shadow(0 0 1px rgba(16, 185, 129, 0.2)); }
        50% { filter: brightness(1.2) drop-shadow(0 0 3px rgba(16, 185, 129, 0.4)); }
    }

    /* Stili specifici per il funzionamento del menu */
    #natan-assistant-menu {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    #natan-assistant-menu.hidden {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    #natan-assistant-menu:not(.hidden) {
        display: flex !important;
        flex-direction: column !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    /* Animazione menu items */
    .natan-item {
        transition: transform 0.3s, opacity 0.3s;
    }

    #natan-assistant-menu.hidden .natan-item {
        transform: translateX(20px);
        opacity: 0;
    }

    #natan-assistant-menu:not(.hidden) .natan-item {
        transform: translateX(0);
        opacity: 1;
        transition-delay: 0.1s;
    }

    /* Riduzione animazioni per utenti che le preferiscono ridotte */
    @media (prefers-reduced-motion: reduce) {
        .natan-pulse-mini {
            animation: none;
        }
    }

    /* Stili specifici per mobile */
    @media (max-width: 768px) {
        #natan-assistant-container {
            bottom: 4rem;
            right: 1rem;
        }

        #natan-assistant-toggle {
            width: 3rem;
            height: 3rem;
        }

        #natan-assistant-toggle img {
            width: 2.5rem;
            height: 2.5rem;
        }
    }
    </style>

</div>
