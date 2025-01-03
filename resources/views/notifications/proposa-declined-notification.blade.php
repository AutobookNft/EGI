<div class="bg-gray-600 p-4 mb-4 rounded-lg">
    <h3 class="text-2xl font-bold mb-4 text-red-500">{{ __('Proposta rifiutata') }}</h3>
    <!-- Contenitore principale -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <!-- Contenitore dei Dettagli -->
        <div class="bg-gray-800 p-4 rounded-lg shadow-md mb-4 md:mb-0 w-full md:w-auto">
            @if(isset($notification->data['reason']) && $notification->data['reason'])
                <p class="text-sm text-gray-300">
                    {{ __('notification.receiver') }}: <span class="font-bold">{{ $notification->data['approver'] }}</span>
                </p>
                <p class="text-sm text-gray-300">
                    {{ __('notification.proposal_declined_reason') }}: <span class="font-bold">{{ $notification->data['reason'] }}</span>
                </p>
                <p class="text-sm text-gray-300">
                    {{ __('collection.wallet.royalty_mint') }}: <span class="font-bold">{{ $notification->data['royalty_mint'] .'%'}}</span>
                </p>
                <p class="text-sm text-gray-300">
                    {{ __('collection.wallet.royalty_rebind') }}: <span class="font-bold">{{ $notification->data['royalty_rebind'] .'%' }}</span>
                </p>
            @endif
        </div>

        <!-- Contenitore dei Bottoni -->
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
            @if ( $notification->data['status'] === 'response')
                <button wire:click="notificationArchive('{{ $notification->id }}', 'declined')" class="btn btn-primary w-full md:w-auto">
                    {{ __('archive') }}
                </button>
            @endif
        </div>
    </div>
</div>

