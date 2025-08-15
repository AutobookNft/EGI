<div class="p-6 mb-4 transition-colors duration-200 rounded-lg shadow-lg bg-emerald-600 hover:bg-emerald-700"
    itemscope itemtype="https://schema.org/Message"
    data-notification-id="{{ $notification->id }}"
    data-payload="reservation"
    aria-label="Notifica: Offerta più alta">

    <!-- Header della notifica con icona -->
    <div class="flex items-center mb-3">
        <div class="p-2 mr-3 rounded-full bg-emerald-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-white">🏆 {{ __('Sei in prima posizione!') }}</h3>
    </div>

    <!-- Contenuto della notifica -->
    <div class="mb-4 space-y-2">
        <p class="font-medium text-emerald-100">
            {{ __('Congratulazioni! La tua offerta è ora la più alta per') }}
            <strong>{{ $notification->data['egi_title'] ?? 'questo EGI' }}</strong>
        </p>

        <div class="p-3 space-y-2 rounded-lg bg-emerald-700">
            <div class="flex items-center justify-between text-emerald-100">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('La tua offerta:') }}</span>
                </span>
                <span class="text-xl font-bold">€{{ number_format($notification->data['amount_eur'] ?? 0, 2) }}</span>
            </div>

            @if(isset($notification->data['total_competitors']) && $notification->data['total_competitors'] > 0)
            <div class="flex items-center text-sm text-emerald-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>{{ __('Hai superato') }} {{ $notification->data['total_competitors'] }} {{ __('altri offerenti') }}</span>
            </div>
            @endif
        </div>

        @if(isset($notification->data['previous_rank']) && $notification->data['previous_rank'] > 1)
        <div class="flex items-center text-sm text-emerald-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span>{{ __('Sei salito dalla posizione') }} #{{ $notification->data['previous_rank'] }}</span>
        </div>
        @endif
    </div>

    <!-- Call to Action -->
    <div class="p-3 mb-4 rounded-lg bg-emerald-800">
        <p class="text-sm text-emerald-100">
            💡 {{ __('Mantieni la tua posizione! Altri utenti potrebbero fare offerte più alte.') }}
        </p>
    </div>

    <!-- Pulsanti di azione -->
    <div class="flex space-x-3">
        <a href="{{ route('egi.show', $notification->data['egi_id'] ?? '#') }}"
           class="flex items-center justify-center flex-1 px-4 py-2 font-medium transition-colors duration-200 bg-white rounded-lg text-emerald-600 hover:bg-emerald-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ __('Vedi EGI') }}
        </a>

        <button class="flex items-center justify-center flex-1 px-4 py-2 text-white transition-colors duration-200 rounded-lg reservation-archive-btn bg-emerald-500 hover:bg-emerald-600"
                data-notification-id="{{ $notification->id }}"
                data-action="archive"
                aria-label="Archivia questa notifica">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            {{ __('OK, Capito!') }}
        </button>
    </div>

    <!-- Footer con timestamp -->
    <div class="flex items-center mt-4 text-sm text-emerald-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <time datetime="{{ $notification->created_at->toIso8601String() }}">
            {{ $notification->created_at->diffForHumans() }}
        </time>
    </div>
</div>
