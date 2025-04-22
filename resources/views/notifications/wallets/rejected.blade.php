<div
    class="notification-item hover:bg-gray-650 mb-4 rounded-lg bg-gray-700 p-4 shadow-lg transition-colors duration-200">
    <!-- Header con icona di stato -->
    <div class="notification-item mb-3 flex items-center justify-between border-b border-gray-600 pb-2"
        data-notification-id="{{ $notification->id }}"
        data-payload="{{ App\Enums\NotificationHandlerType::WALLET->value }}">
        <div class="flex items-center space-x-3">
            <div class="rounded-full bg-red-500/20 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-red-500">{{ __('collection.wallet.proposal_rejected') }}</h3>
        </div>

        <!-- Pulsante di archiviazione -->
        @include('notifications.wallets.partials.button-archived')

    </div>

    <!-- Contenuto principale -->
    <div class="space-y-3">
        <!-- Messaggio principale -->
        <div class="rounded-lg border border-gray-600/30 bg-gray-800/50 p-3">
            <p class="text-gray-300">{{ $notification->data['message'] }}</p>
        </div>

        <!-- Motivo del rifiuto -->
        <div class="rounded-lg border border-red-500/20 bg-red-500/10 p-3">
            <div class="flex items-start space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 text-red-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-100">{{ $notification->data['reason'] }}</p>
            </div>
        </div>

        <!-- Footer info -->
        <div class="flex items-center justify-between pt-2 text-sm text-gray-400">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ $notification->data['sender'] }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $notification->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>
