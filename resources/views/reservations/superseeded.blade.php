<div class="p-6 mb-4 transition-colors duration-200 bg-yellow-600 rounded-lg shadow-lg hover:bg-yellow-700"
    itemscope itemtype="https://schema.org/Message"
    data-notification-id="{{ $notification->id }}"
    data-payload="reservation"
    aria-label="Notifica: Offerta superata">

    <!-- Header della notifica con icona -->
    <div class="flex items-center mb-3">
        <div class="p-2 mr-3 bg-yellow-500 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-white">⚠️ {{ __('La tua offerta è stata superata') }}</h3>
    </div>

    <!-- Contenuto della notifica -->
    <div class="mb-4 space-y-2">
        <p class="text-yellow-100">
            {{ __('Un altro utente ha fatto un\'offerta più alta per') }}
            <strong>{{ $notification->data['egi_title'] ?? 'questo EGI' }}</strong>
        </p>

        <div class="p-3 space-y-2 bg-yellow-700 rounded-lg">
            <div class="flex items-center justify-between text-yellow-100">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('La tua offerta:') }}</span>
                </span>
                <span class="font-bold">€{{ number_format($notification->data['previous_amount'] ?? 0, 2) }}</span>
            </div>

            <div class="flex items-center justify-between text-yellow-100">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>{{ __('Offerta più alta:') }}</span>
                </span>
                <span class="text-xl font-bold">€{{ number_format($notification->data['new_highest_amount'] ?? 0, 2) }}</span>
            </div>

            @if(isset($notification->data['new_rank']))
            <div class="flex items-center text-sm text-yellow-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <span>{{ __('Sei ora in posizione') }} #{{ $notification->data['new_rank'] }}</span>
            </div>
            @endif
        </div>

        @if(isset($notification->data['superseded_by_user']))
        <div class="flex items-center text-sm text-yellow-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>{{ __('Superato da:') }} {{ $notification->data['superseded_by_user'] }}</span>
        </div>
        @endif
    </div>

    <!-- Call to Action -->
    <div class="p-3 mb-4 bg-yellow-800 rounded-lg">
        <p class="text-sm font-medium text-yellow-100">
            💰 {{ __('Vuoi tornare in prima posizione? Fai un\'offerta più alta!') }}
        </p>
    </div>

    <!-- Pulsanti di azione -->
    <div class="flex space-x-3">
        <a href="{{ route('egi.show', $notification->data['egi_id'] ?? '#') }}"
           class="flex items-center justify-center flex-1 px-4 py-2 font-medium text-yellow-600 transition-colors duration-200 bg-white rounded-lg hover:bg-yellow-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            {{ __('Rilancia Ora') }}
        </a>

        <button class="flex items-center justify-center flex-1 px-4 py-2 text-white transition-colors duration-200 bg-yellow-500 rounded-lg reservation-archive-btn hover:bg-yellow-600"
                data-notification-id="{{ $notification->id }}"
                data-action="archive"
                aria-label="Archivia questa notifica">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            {{ __('OK') }}
        </button>
    </div>

    <!-- Footer con timestamp -->
    <div class="flex items-center mt-4 text-sm text-yellow-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <time datetime="{{ $notification->created_at->toIso8601String() }}">
            {{ $notification->created_at->diffForHumans() }}
        </time>
    </div>
</div>
