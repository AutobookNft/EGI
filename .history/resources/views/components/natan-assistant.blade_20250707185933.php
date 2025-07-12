{{-- resources/views/components/natan-assistant.blade.php --}}

<div id="natan-assistant-container">
    {{-- Pulsante principale Natan --}}
    <button id="natan-assistant-toggle" type="button" aria-expanded="false" aria-controls="natan-assistant-menu"
        class="group flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-gray-900 shadow-lg ring-2 ring-emerald-500/40 transition-all duration-300 hover:ring-emerald-400"
        data-natan-state="idle">
        <img src="/images/default/natan-face.png" alt="Natan Assistant"
            class="natan-pulse-mini h-10 w-10 transition-transform duration-300 group-hover:scale-110" />
        <span class="sr-only">Apri assistente Natan</span>
    </button>

    {{-- Menu di aiuto (inizialmente nascosto) --}}
    <div id="natan-assistant-menu" class="mt-3 flex hidden flex-col items-end space-y-2">
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

@push('scripts')
<script>
    window.natanAssistantAutoOpen = {{ session('natan_assistant_auto_open', true) ? 'true' : 'false' }};
</script>
@endpush

{{-- In fondo alla modale Butler --}}
<div style="margin-top: 1rem; text-align: center;">
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
                    'X-CSRF-TOKEN': window.Laravel?.csrfToken || document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ auto_open: cb.checked })
            });
        });
    }
});
</script>
@endpush
