@php
    /**
     * @var \App\Models\CustomDatabaseNotification $notification
     *
     * --- OS1.5 DOCUMENTATION ---
     * @oracode-intent: To render an interactive GDPR notification, asking the user for explicit confirmation of a past action and providing a clear path to either confirm the action or flag it as unrecognized (potential security issue).
     * @oracode-security: The "disavow" button does not trigger the security protocol directly but initiates a clarifying dialogue via JavaScript, preventing accidental alarms. ARIA roles enhance accessibility for security-sensitive actions.
     * @os1-compliance: Full.
     */

    // Estrae il tipo specifico di evento GDPR (es. 'consent_updated') dalla chiave della vista (es. 'gdpr.consent_updated')
    $gdprEventType = explode('.', $notification->view)[1] ?? 'unknown';

    // Costruisce la chiave di traduzione per il contenuto della notifica

    $contentContent = "notification.gdpr.{$gdprEventType}.content";
    $contentTitle = "notification.gdpr.{$gdprEventType}.title";

    // Recupera il contesto dal payload per la sostituzione dei placeholder (es. :days, :report_id)
    $context = $notification->model->data ?? [];
@endphp

{{-- ✅ CONTAINER CON DATA ATTRIBUTES CORRETTI --}}
<div class="p-4 bg-gray-700 rounded-lg shadow-inner notification-item"
     data-notification-id="{{ $notification->id }}"
     data-payload="gdpr"
     data-payload-id="{{ $notification->id }}"
     role="alertdialog"
     aria-labelledby="notification-title-{{ $notification->id }}"
     aria-describedby="notification-content-{{ $notification->id }}"
     itemscope
     itemtype="http://schema.org/Message">

     
    <h3 id="notification-title-{{ $notification->id }}" itemprop="name" class="mb-2 text-xl font-bold text-yellow-400">
        {{ $notification->data['message'] ?? __($contentTitle) }}
    </h3>

    <p id="notification-content-{{ $notification->id }}" itemprop="text" class="mb-4 text-gray-200">
        {{ __($contentContent, $context) }}
        <span class="block mt-2 text-sm italic text-gray-400">{{ __('notification.gdpr.confirm_action_prompt') }}</span>
    </p>

    @if(!empty($context))
        <div class="p-3 mb-4 bg-gray-800 border border-gray-600 rounded-md">
            <h4 class="mb-2 text-sm font-semibold text-gray-400">{{ __('notification.label.additional_details') }}</h4>
            <ul class="space-y-1 text-xs text-gray-300">
                @foreach($context as $key => $value)
                    <li>
                        <strong class="font-medium text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                        <span class="ml-2 font-mono">{{ is_array($value) ? json_encode($value) : $value }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-end space-x-3" role="group" aria-label="{{ __('notification.aria.actions_label') }}">

        {{-- ✅ PRIMO PULSANTE: CONFIRM (SICURO) --}}
        <button
            class="px-4 py-2 font-bold text-white transition duration-300 ease-in-out bg-green-600 rounded-lg response-btn hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
            data-action="confirm"
            aria-label="{{ __('notification.gdpr.confirm_button_aria_label') }}">
            <span class="flex items-center space-x-2" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction">
                {{-- Icona di check --}}
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>

                <meta itemprop="name" content="{{ __('notification.gdpr.confirm_button_label') }}" />
                <span>{{ __('notification.gdpr.confirm_button_label') }}</span>
            </span>
        </button>

        {{-- 🚨 SECONDO PULSANTE: DISAVOW (ESTREMAMENTE PERICOLOSO) --}}
        <div class="relative">
            {{-- Effetto glow rosso attorno --}}
            <div class="absolute rounded-lg opacity-75 -inset-1 bg-gradient-to-r from-red-600 to-red-400 blur animate-pulse"></div>

            <button
                class="relative px-4 py-2 font-bold text-white transition-all duration-300 transform bg-red-700 border-2 border-red-400 rounded-lg shadow-2xl response-btn hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-500 hover:scale-105 shadow-red-500/60"
                data-action="init_disavow"
                aria-label="{{ __('notification.gdpr.disavow_button_aria_label') }}"
                style="animation: danger-pulse 1.5s infinite;"
                title="⚠️ PERICOLO: Questa azione attiverà protocolli di sicurezza di emergenza!">

                {{-- Badge di pericolo lampeggiante --}}
                <div class="absolute flex w-5 h-5 -top-2 -right-2">
                    <span class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                    <span class="relative inline-flex items-center justify-center w-5 h-5 bg-red-500 rounded-full">
                        <span class="text-xs font-bold text-white">!</span>
                    </span>
                </div>

                {{-- Bordi lampeggianti multipli --}}
                <div class="absolute inset-0 border-2 border-yellow-400 rounded-lg opacity-50 animate-ping"></div>
                <div class="absolute inset-0 border border-red-300 rounded-lg" style="animation: danger-border 0.8s infinite alternate;"></div>

                <span class="relative z-10 flex items-center space-x-2" itemprop="potentialAction" itemscope itemtype="http://schema.org/Action">
                    {{-- Icona skull o warning molto evidente --}}
                    <svg class="w-5 h-5 text-yellow-300 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>

                    <meta itemprop="name" content="{{ __('notification.gdpr.disavow_button_label') }}" />
                    <span class="font-black">{{ __('notification.gdpr.disavow_button_label') }}</span>

                    {{-- Seconda icona warning --}}
                    <svg class="w-4 h-4 text-yellow-300 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
        </div>

    </div>

    {{-- CSS Personalizzato per effetti di pericolo --}}
    <style>
        @keyframes danger-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 20px rgba(239, 68, 68, 0.6); }
            50% { transform: scale(1.02); box-shadow: 0 0 30px rgba(239, 68, 68, 0.9), 0 0 40px rgba(239, 68, 68, 0.4); }
        }

        @keyframes danger-border {
            0% { border-color: #fbbf24; box-shadow: inset 0 0 10px rgba(251, 191, 36, 0.5); }
            100% { border-color: #f59e0b; box-shadow: inset 0 0 20px rgba(245, 158, 11, 0.8); }
        }

        .response-btn[data-action="init_disavow"]:hover {
            animation: danger-pulse 0.5s infinite !important;
            transform: scale(1.08) !important;
        }
    </style>

</div>
