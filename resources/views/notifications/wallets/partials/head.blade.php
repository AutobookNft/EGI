@php
    // Associa ogni status a una classe CSS per il colore
    switch ($notification->model->status) {
        case App\Enums\NotificationStatus::PENDING_CREATE->value:
            $dataAction = App\Enums\NotificationStatus::ACCEPTED->value;
            break;
        case App\Enums\NotificationStatus::PENDING_UPDATE->value:
            $dataAction = App\Enums\NotificationStatus::UPDATE->value;
            break;
        case App\Enums\NotificationStatus::PENDING->value:
            $statusClass = 'text-yellow-500';
            $status = ucfirst(App\Enums\NotificationStatus::PENDING->value);
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
            $status = ucfirst(App\Enums\NotificationStatus::DONE->value);
            break;
        default:
            $statusClass = 'text-gray-300';
            $status = 'unknown';
    }
@endphp

<!-- Contenitore dei Dettagli -->
<div class="w-full rounded-lg bg-gray-800 p-4 shadow-md" itemprop="result" itemscope itemtype="https://schema.org/Message"
    aria-label="Dettagli della notifica di creazione del wallet">
    <!-- Header -->
    <div class="mb-4 flex flex-col items-start border-b border-gray-600 pb-2" aria-label="Informazioni principali">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-blue-500" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
            </svg>
            <p class="-mt-1 text-lg font-bold text-white" itemprop="headline">

                {{ $notification->data['message'] }}
            </p>
        </div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-blue-500" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
            </svg>
            <p class="-mt-1 text-lg font-bold text-white">
                {{ __('label.from') . ': ' }}<span itemprop="sender">{{ $notification->data['sender'] }}</span>
            </p>
        </div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-blue-500" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
            </svg>
            <p class="-mt-1 text-lg font-bold text-white">
                {{ __('collection.collection') . ': ' }}<span
                    itemprop="about">{{ $notification->data['collection_name'] }}</span>
            </p>
        </div>
    </div>

    <!-- Dettagli Principali -->
    <div class="space-y-3" id="notification-details-{{ $notification->id }}">
        @if ($notification->model)

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="rounded bg-gray-700 p-3" aria-label="Indirizzo wallet">
                    <p class="mb-1 text-sm text-gray-400">{{ __('collection.wallet.address') }}</p>
                    <p class="truncate text-sm text-white" title="{{ $notification->model->wallet }}"
                        itemprop="identifier">
                        {{ $notification->model->wallet }}
                    </p>
                </div>

                <div class="rounded bg-gray-700 p-3" aria-label="Stato wallet">
                    <p class="mb-1 text-sm text-gray-400">{{ __('collection.wallet.status') }}</p>
                    <p id="status-field-{{ $notification->id }}" class="field-status {{ $statusClass }} text-sm"
                        itemprop="additionalType">{{ $status }}</p>
                </div>
                <!-- Royalty Mint -->
                <div class="rounded bg-gray-700 p-3" aria-label="Royalty Mint">
                    <p class="mb-1 text-sm text-gray-400">{{ __('collection.wallet.royalty_mint') }}</p>
                    @if ($notification->model->type === App\Enums\NotificationStatus::UPDATE->value)
                        <p class="text-sm font-bold text-red-500">
                            {{ $notification->data['old_royalty_mint'] }}%
                        </p>
                        @if ($notification->data['old_royalty_mint'] !== null && $notification->data['old_royalty_mint'] != 0)
                            <p class="text-sm font-bold text-green-500">
                                → {{ $notification->model->royalty_mint }}%
                            </p>
                        @endif
                    @else
                        <p class="text-sm font-bold text-white">
                            {{ $notification->model->royalty_mint }}%
                        </p>
                    @endif
                </div>

                <!-- Royalty Rebind -->
                <div class="rounded bg-gray-700 p-3" aria-label="Royalty Rebind">
                    <p class="mb-1 text-sm text-gray-400">{{ __('collection.wallet.royalty_rebind') }}</p>
                    @if ($notification->model->type === App\Enums\NotificationStatus::UPDATE->value)
                        <p class="text-sm font-bold text-red-500">
                            {{ $notification->data['old_royalty_rebind'] }}%
                        </p>
                        @if ($notification->data['old_royalty_rebind'] !== null && $notification->data['old_royalty_rebind'] != 0)
                            <p class="text-sm font-bold text-green-500">
                                → {{ $notification->model->royalty_rebind }}%
                            </p>
                        @endif
                    @else
                        <p class="text-sm font-bold text-white">
                            {{ $notification->model->royalty_rebind }}%
                        </p>
                    @endif
                </div>
            </div>
        @else
            <p class="text-gray-400">{{ __('Details unavailable.') }}</p>
        @endif

        <!-- Meta info -->
        <div class="flex items-center justify-between border-t border-gray-600 pt-3 text-xs text-gray-400">
            <p>
                {{ __('label.created_at') . ': ' }}
                <time datetime="{{ $notification->created_at->toIso8601String() }}" itemprop="datePublished">
                    {{ $notification->created_at->diffForHumans() }}
                </time>
            </p>
        </div>
    </div>
</div>
