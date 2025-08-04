{{-- resources/views/components/natan-assistant.blade.php --}}

@php
    $suffix = $suffix ?? '';
    $containerId = 'natan-assistant-container' . $suffix;
    $toggleId = 'natan-assistant-toggle' . $suffix;
    $menuId = 'natan-assistant-menu' . $suffix;
@endphp

<div id="{{ $containerId }}" class="relative">
    {{-- Pulsante principale Natan --}}
    <button id="{{ $toggleId }}" type="button" aria-expanded="false" aria-controls="{{ $menuId }}"
        class="group flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-gray-900 shadow-lg ring-2 ring-emerald-500/40 transition-all duration-300 hover:ring-emerald-400"
        data-natan-state="idle"
        onclick="console.log('🎯 Natan clicked! Screen width:', window.innerWidth, 'ID:', '{{ $toggleId }}')">
        <img src="/images/default/natan-face.png" alt="Natan Assistant"
            class="natan-pulse-mini h-10 w-10 transition-transform duration-300 group-hover:scale-110" />
        <span class="sr-only">Apri assistente Natan</span>
    </button>

    {{-- Menu di aiuto (inizialmente nascosto) --}}
    <div id="{{ $menuId }}"
        class="absolute right-0 top-full z-[9999] mt-2 hidden flex-col items-end space-y-2 rounded-lg border border-gray-700 bg-gray-900 p-3 shadow-xl backdrop-blur-sm">
        {{-- Le opzioni saranno generate dinamicamente da JS usando assistantOptions --}}
        <div style="padding: 10px; color: #fff; font-size: 12px;">DEBUG: Menu container visible
            ({{ $suffix ?: 'desktop' }})</div>
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
        [id^="natan-assistant-menu"] {
            transition: opacity 0.3s ease, transform 0.3s ease;
            min-width: 200px;
        }

        [id^="natan-assistant-menu"].hidden {
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
        @media (max-width: 767px) {
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
                right: 0;
                left: auto;
                transform: none;
                margin-right: -1rem;
            }
        }

        @media (min-width: 768px) {
            #natan-assistant-menu {
                min-width: 220px;
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
