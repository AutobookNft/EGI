<div class="bg-gray-600 p-4 mb-4 rounded-lg" itemscope itemtype="https://schema.org/InformAction" aria-label="Notifica: Creazione wallet">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <!-- Contenitore dei Dettagli -->
        <div class="bg-gray-800 p-4 rounded-lg shadow-md w-full" itemprop="result" itemscope itemtype="https://schema.org/Message" aria-label="Dettagli della notifica di creazione del wallet">
            <!-- Header -->
            <div class="flex flex-col items-start mb-4 pb-2 border-b border-gray-600" aria-label="Informazioni principali">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
                    <p class="-mt-1 text-lg font-bold text-white" itemprop="headline">

                        {{ $notification->data['message'] }}
                    </p>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
                    <p class="-mt-1 text-lg font-bold text-white">
                        {{ __('label.from'). ": " }}<span itemprop="sender">{{ $notification->data['sender'] }}</span>
                    </p>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
                    <p class="-mt-1 text-lg font-bold text-white">
                        {{ __('collection.collection'). ": " }}<span itemprop="about">{{ $notification->data['collection_name'] }}</span>
                    </p>
                </div>
            </div>

            <!-- Dettagli Principali -->
            <div class="space-y-3" id="notification-details-{{ $notification->id }}">
                @if($notification->model)
                    @php
                        // Associa ogni status a una classe CSS per il colore
                        switch ($notification->model->status) {
                            case App\Enums\NotificationStatus::PENDING_CREATE->value:
                            case App\Enums\NotificationStatus::PENDING_UPDATE->value:
                            case App\Enums\NotificationStatus::PENDING->value:
                                $statusClass = 'text-yellow-500';
                                $status =  ucfirst(App\Enums\NotificationStatus::PENDING->value);
                                break;
                            case App\Enums\NotificationStatus::ACCEPTED->value:
                                $statusClass = 'text-green-500';
                                $status = App\Enums\NotificationStatus::ACCEPTED->value;
                                break;
                            case App\Enums\NotificationStatus::REJECTED->value:
                                $statusClass = 'text-red-500';
                                $status = App\Enums\NotificationStatus::REJECTED->value;
                                break;
                            case App\Enums\NotificationStatus::DONE->value:
                                $statusClass = 'text-green-500';
                                $status =  ucfirst(App\Enums\NotificationStatus::DONE->value);;
                                break;
                            default:
                                $statusClass = 'text-gray-300';
                                $status = 'unknown';
                        }
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="bg-gray-700 p-3 rounded" aria-label="Indirizzo wallet">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.address') }}</p>
                            <p class="text-sm text-white truncate" title="{{ $notification->model->wallet }}" itemprop="identifier">
                                {{ $notification->model->wallet }}
                            </p>
                        </div>

                        <div class="bg-gray-700 p-3 rounded" aria-label="Stato wallet">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.status') }}</p>
                            <p id="status-field-{{ $notification->id }}" class="field-status text-sm {{ $statusClass }}" itemprop="additionalType">{{ $status }}</p>
                        </div>
                        <!-- Royalty Mint -->
                        <div class="bg-gray-700 p-3 rounded" aria-label="Royalty Mint">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.royalty_mint') }}</p>
                            <p class="text-sm text-red-500 font-bold">
                                {{ $notification->data['old_royalty_mint'] }}%
                            </p>
                            @if($notification->data['old_royalty_mint'] !== null && $notification->data['old_royalty_mint'] != 0)
                                <p class="text-sm text-green-500 font-bold">
                                    → {{ $notification->model->royalty_mint }}%
                                </p>
                            @endif
                        </div>

                        <!-- Royalty Rebind -->
                        <div class="bg-gray-700 p-3 rounded" aria-label="Royalty Rebind">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.royalty_rebind') }}</p>
                            <p class="text-sm text-red-500 font-bold">
                                {{ $notification->data['old_royalty_rebind'] }}%
                            </p>
                            @if($notification->data['old_royalty_rebind'] !== null && $notification->data['old_royalty_rebind'] != 0)
                                <p class="text-sm text-green-500 font-bold">
                                    → {{ $notification->model->royalty_rebind }}%
                                </p>
                            @endif
                        </div>
                    </div>

                @else
                    <p class="text-gray-400">{{ __('Details unavailable.') }}</p>
                @endif

                <!-- Meta info -->
                <div class="flex justify-between items-center pt-3 border-t border-gray-600 text-xs text-gray-400">
                    <p>
                        {{ __('label.created_at'). ": " }}
                        <time datetime="{{ $notification->created_at->toIso8601String() }}" itemprop="datePublished">
                            {{ $notification->created_at->diffForHumans() }}
                        </time>
                    </p>
                </div>
            </div>
        </div>

        <!-- Contenitore dei Bottoni -->
        @if (isset($notification->model) && $notification->model->type === App\Enums\NotificationStatus::UPDATE->value)
            <div class="notification-item flex space-x-3 mb-3" data-notification-id="{{ $notification->id }}" aria-label="Azioni per la notifica di creazione del wallet">
                <div class="notification-actions flex space-x-3">
                    @if($notification->outcome === App\Enums\NotificationStatus::PENDING_UPDATE->value)
                        <button
                            class="response-btn flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center"
                            data-notification-id="{{ $notification->id }}"
                            data-action={{ App\Enums\NotificationStatus::UPDATE->value }}
                            aria-label="Accetta la notifica di creazione del wallet">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('label.accept') }}
                        </button>

                        <button id="reject-btn-{{ $notification->id }}"
                            class="reject-btn flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center"
                            data-notification-id="{{ $notification->id }}"
                            data-action={{ App\Enums\NotificationStatus::REJECTED->value }}
                            aria-label="Rifiuta la notifica di creazione del wallet">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ __('label.decline') }}
                        </button>
                    @endif

                </div>

                <!-- Pulsante Archive (sempre presente nel DOM ma nascosto se non necessario) -->

                <button class="archive-btn mt-3 px-4 py-2 text-white font-medium rounded-lg bg-emerald-500 hover:bg-emerald-700"
                    id="archive-btn-{{ $notification->id }}"
                    data-notification-id="{{ $notification->id }}"
                    data-action={{ App\Enums\NotificationStatus::ARCHIVED->value }}
                    aria-label="Archivia questa notifica"
                    style="{{ $notification->outcome === 'Accepted' ? 'display: block;' : 'display: none;' }}">
                    🗄️ {{ __('label.archived') }}
                </button>
            </div>
        @endif

    </div>

</div>




