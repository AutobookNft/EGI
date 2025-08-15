<div class="p-6 mb-4 transition-colors duration-200 bg-blue-600 rounded-lg shadow-lg hover:bg-blue-700"
    itemscope itemtype="https://schema.org/Message"
    data-notification-id="{{ $notification->id }}"
    data-payload="reservation"
    aria-label="Notifica: Cambio posizione">

    <!-- Header della notifica con icona -->
    <div class="flex items-center mb-3">
        <div class="p-2 mr-3 bg-blue-500 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-white">
            @if($notification->data['direction'] === 'up')
                📈 {{ __('Sei salito in classifica!') }}
            @else
                📉 {{ __('Cambio di posizione') }}
            @endif
        </h3>
    </div>

    <!-- Contenuto della notifica -->
    <div class="mb-4 space-y-2">
        <p class="text-blue-100">
            {{ __('La tua posizione per') }}
            <strong>{{ $notification->data['egi_title'] ?? 'questo EGI' }}</strong>
            {{ __('è cambiata') }}
        </p>

        <div class="p-3 space-y-2 bg-blue-700 rounded-lg">
            <!-- Cambio posizione -->
            <div class="flex items-center justify-center py-2 text-blue-100">
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold">#{{ $notification->data['old_rank'] ?? '?' }}</div>
                        <div class="text-xs tracking-wide uppercase">{{ __('Prima') }}</div>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($notification->data['direction'] === 'up')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        @endif
                    </svg>

                    <div class="text-center">
                        <div class="text-3xl font-bold">#{{ $notification->data['new_rank'] ?? '?' }}</div>
                        <div class="text-xs tracking-wide uppercase">{{ __('Ora') }}</div>
                    </div>
                </div>
            </div>

            @if(isset($notification->data['positions_changed']))
            <div class="text-sm text-center text-blue-100">
                @if($notification->data['direction'] === 'up')
                    <span class="font-medium text-green-300">
                        +{{ $notification->data['positions_changed'] }} {{ __('posizioni guadagnate') }}
                    </span>
                @else
                    <span class="font-medium text-red-300">
                        -{{ $notification->data['positions_changed'] }} {{ __('posizioni perse') }}
                    </span>
                @endif
            </div>
            @endif

            <div class="flex items-center justify-between pt-2 text-blue-100 border-t border-blue-600">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('La tua offerta:') }}</span>
                </span>
                <span class="font-bold">€{{ number_format($notification->data['amount_eur'] ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Pulsanti di azione -->
    <div class="flex space-x-3">
        <a href="{{ route('egi.show', $notification->data['egi_id'] ?? '#') }}"
           class="flex items-center justify-center flex-1 px-4 py-2 font-medium text-blue-600 transition-colors duration-200 bg-white rounded-lg hover:bg-blue-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            {{ __('Vedi Classifica') }}
        </a>

        <button class="flex items-center justify-center flex-1 px-4 py-2 text-white transition-colors duration-200 bg-blue-500 rounded-lg reservation-archive-btn hover:bg-blue-600"
                data-notification-id="{{ $notification->id }}"
                data-action="archive"
                aria-label="Archivia questa notifica">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ __('OK') }}
        </button>
    </div>

    <!-- Footer con timestamp -->
    <div class="flex items-center mt-4 text-sm text-blue-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <time datetime="{{ $notification->created_at->toIso8601String() }}">
            {{ $notification->created_at->diffForHumans() }}
        </time>
    </div>
</div>
