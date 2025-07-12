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
        @php
            $explanations = [
                'what-is-egi' => [
                    'text' => "Un EGI è un NFT evoluto che combina tre elementi: <strong class=\"text-emerald-300\">E</strong>cological (impatto ambientale garantito), <strong class=\"text-indigo-300\">G</strong>oods (utility reale), <strong class=\"text-amber-300\">I</strong>nvent (creatività umana).",
                    'spotlight' => '.collection-card-nft:first-child',
                    'learnMore' => ['text' => 'Visualizza esempio', 'action' => 'spotlight', 'target' => '.collection-card-nft']
                ],
                'how-impact-works' => [
                    'text' => "Il 20% di ogni transazione viene destinato automaticamente a progetti ambientali verificabili. L'impatto è tracciato e garantito dalla blockchain.",
                    'spotlight' => '.nft-stats-section',
                    'learnMore' => ['text' => 'Vedi i progetti', 'action' => 'navigate', 'target' => route('epps.index')]
                ],
                'start-without-crypto' => [
                    'text' => "Puoi navigare e prenotare EGI senza wallet. Quando sei pronto, ti guideremo nel tuo primo acquisto crypto con pochi click.",
                    'spotlight' => '#register-link-desktop',
                    'learnMore' => ['text' => 'Registrati ora', 'action' => 'navigate', 'target' => route('register')]
                ],
                'granular-business' => [
                    'text' => "Tokenizza specifiche aree della tua attività come asset distinti (prodotti, servizi, linee di produzione) generando nuovi flussi di ricavi.",
                    'spotlight' => '.business-card, .collection-card-nft:nth-child(2)',
                    'learnMore' => ['text' => 'Scopri di più', 'action' => 'navigate', 'target' => '#']
                ]
            ];
        @endphp

        {{-- Menu Items (si espandono quando attivati) --}}
        @foreach([
            ['icon' => 'help_outline', 'text' => "Cos'è un EGI?", 'id' => 'what-is-egi'],
            ['icon' => 'eco', 'text' => "Come funziona l'impatto?", 'id' => 'how-impact-works'],
            ['icon' => 'account_balance_wallet', 'text' => "Iniziare senza crypto?", 'id' => 'start-without-crypto'],
            ['icon' => 'business', 'text' => "Business granulare", 'id' => 'granular-business']
        ] as $item)
        <div class="natan-menu-item">
            <button
                id="natan-item-{{ $item['id'] }}"
                type="button"
                class="flex items-center justify-end gap-2 py-2 pl-5 pr-3 text-sm font-medium transition-all duration-300 translate-x-20 bg-gray-900 border rounded-full shadow-md opacity-0 border-emerald-600/30 text-emerald-300 hover:bg-gray-800 hover:border-emerald-500/50 natan-item"
                aria-expanded="false"
            >
                <span>{{ $item['text'] }}</span>
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-800">
                    <span class="material-symbols-outlined text-[16px]">{{ $item['icon'] }}</span>
                </span>
            </button>

            {{-- Contenuto della spiegazione (inizialmente nascosto) --}}
            <div
                id="natan-content-{{ $item['id'] }}"
                class="absolute right-0 hidden w-64 p-4 mb-2 text-sm text-gray-200 border rounded-lg shadow-xl bottom-full bg-gray-900/95 backdrop-blur-md border-emerald-600/30"
                role="tooltip"
                data-spotlight="{{ $explanations[$item['id']]['spotlight'] ?? '' }}"
            >
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 p-1.5 bg-emerald-900/50 rounded-full">
                        <span class="material-symbols-outlined text-emerald-400">{{ $item['icon'] }}</span>
                    </div>
                    <div>
                        <h4 class="mb-1 font-semibold text-emerald-300">{{ $item['text'] }}</h4>
                        <p class="text-xs leading-relaxed text-gray-300">
                            {!! $explanations[$item['id']]['text'] ?? '' !!}
                        </p>

                        @if(isset($explanations[$item['id']]['learnMore']))
                        <div class="pt-2 mt-2 border-t border-gray-700">
                            <button
                                type="button"
                                class="flex items-center text-xs text-emerald-400 hover:text-emerald-300"
                                data-action="{{ $explanations[$item['id']]['learnMore']['action'] }}"
                                data-target="{{ $explanations[$item['id']]['learnMore']['target'] }}"
                            >
                                {{ $explanations[$item['id']]['learnMore']['text'] }}
                                <span class="ml-1 text-xs material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                {{-- Freccia/Punta del tooltip --}}
                <div class="absolute bottom-0 right-6 w-3 h-3 -mb-1.5 bg-gray-900 border-r border-b border-emerald-600/30 transform rotate-45"></div>
            </div>
        </div>
        @endforeach
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
