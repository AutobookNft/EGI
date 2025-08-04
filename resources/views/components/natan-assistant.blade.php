{{-- resources/views/components/natan-assistant.blade.php --}}

<div id="natan-assistant-container" class="relative">
    {{-- Pulsante principale Natan --}}
    <button id="natan-assistant-toggle" type="button" aria-expanded="false" aria-controls="natan-assistant-menu"
        class="flex items-center justify-center w-12 h-12 overflow-hidden transition-all duration-300 bg-gray-900 rounded-full shadow-lg group ring-2 ring-emerald-500/40 hover:ring-emerald-400"
        data-natan-state="idle">
        <img src="/images/default/natan-face.png" alt="Natan Assistant"
            class="w-10 h-10 transition-transform duration-300 natan-pulse-mini group-hover:scale-110" />
        <span class="sr-only">Apri assistente Natan</span>
    </button>

    {{-- Menu di aiuto (inizialmente nascosto) --}}
    <div id="natan-assistant-menu" class="absolute right-0 top-full mt-2 flex-col items-end space-y-2 hidden z-50">
        {{-- Le opzioni saranno generate dinamicamente da JS usando assistantOptions --}}
    </div>

    <style>
        /* NUOVI STILI - SOSTITUISCE TUTTI I PRECEDENTI */
        /* Stili di base per animazioni */
        .natan-pulse-mini {
            animation: natan-pulse-mini 3s ease-in-out infinite;
        }

        @keyframes natan-pulse-mini {

            0%,
            100% {
                filter: brightness(1) drop-shadow(0 0 1px rgba(16, 185, 129, 0.2));
            }

            50% {
                filter: brightness(1.2) drop-shadow(0 0 3px rgba(16, 185, 129, 0.4));
            }
        }

        /* Stili specifici per il funzionamento del menu */
        #natan-assistant-menu {
            transition: opacity 0.3s ease, transform 0.3s ease;
            min-width: 200px;
        }

        #natan-assistant-menu.hidden {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            transform: translateY(-10px);
        }

        #natan-assistant-menu:not(.hidden) {
            display: flex !important;
            flex-direction: column !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            transform: translateY(0);
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

        /* Stili specifici per mobile nella navbar */
        @media (max-width: 640px) {
            #natan-assistant-toggle {
                width: 2.75rem;
                height: 2.75rem;
            }

            #natan-assistant-toggle img {
                width: 2.25rem;
                height: 2.25rem;
            }
            
            #natan-assistant-menu {
                min-width: 180px;
                right: -1rem;
            }
        }
    </style>

</div>

@push('scripts')
    <script>
        window.natanAssistantAutoOpen = {{ session('natan_assistant_auto_open', true) ? 'true' : 'false' }};
    </script>
@endpush

{{-- In fondo alla modale Butler --}}
{{-- <div style="margin-top: 1rem; text-align: center;">
    <label style="color: #D4A574; font-size: 0.85rem;">
        <input type="checkbox" id="natan-auto-open-checkbox" />
        {{ __('assistant.auto_open_label') }}
    </label>
    <div style="font-size: 0.75rem; color: #aaa; margin-top: 0.25rem;">
        {{ __('assistant.auto_open_hint') }}
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var cb = document.getElementById('natan-auto-open-checkbox');
            if (cb) {
                cb.checked = window.natanAssistantAutoOpen;
                cb.addEventListener('change', function() {
                    fetch('/api/assistant/auto-open', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.Laravel?.csrfToken || document.querySelector(
                                'meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            auto_open: cb.checked
                        })
                    });
                });
            }
        });
    </script>
@endpush --}}
