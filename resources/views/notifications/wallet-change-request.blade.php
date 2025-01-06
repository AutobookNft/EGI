<div class="bg-gray-600 p-4 mb-4 rounded-lg">
    <!-- Contenitore principale -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <!-- Contenitore dei Dettagli -->
        <div class="bg-gray-800 p-4 rounded-lg shadow-md mb-4 md:mb-0 w-full md:w-auto">
            <div class="mb-2">
                <p class="text-lg font-semibold text-white">{{ $notification->data['message'] }}</p>
            </div>

            @if($notification->approval_details)
                <div class="mb-2">
                    <p class="text-md text-gray-300 truncate w-full" title="{{ $notification->approval_details->wallet_address }}">
                        <strong>{{ __('Wallet Address:') }}</strong>
                        {{ $notification->approval_details->wallet_address }}
                    </p>
                </div>
                <div class="mb-2">
                    <p class="text-md text-gray-300">
                        <strong>{{ __('Mint Royalty:') }}</strong> {{ $notification->approval_details->royalty_mint }}%
                    </p>
                </div>
                <div class="mb-2">
                    <p class="text-md text-gray-300">
                        <strong>{{ __('Rebind Royalty:') }}</strong> {{ $notification->approval_details->royalty_rebind }}%
                    </p>
                </div>
                <div class="mb-2">
                    <p class="text-md text-gray-300">
                        <strong>{{ __('Status:') }}</strong> {{ ucfirst($notification->approval_details->status) }}
                    </p>
                </div>
                <div class="mb-2">
                    <p class="text-md text-gray-300">
                        <strong>{{ __('Change Type:') }}</strong> {{ ucfirst($notification->approval_details->change_type) }}
                    </p>
                </div>
            @else
                <div class="mb-2">
                    <p class="text-md text-gray-300">
                        {{ __('Details unavailable.') }}
                    </p>
                </div>
            @endif

            <div>
                <p class="text-sm text-gray-400">
                    {{ __('Created:') }} {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
       <!-- Contenitore dei Bottoni -->
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
            @if (isset($notification->approval_details) && $notification->approval_details->change_type === 'create')
                <button wire:click="handleNotificationAction('{{ $notification->id }}', 'accept')" class="btn btn-primary w-full md:w-auto">
                    {{ __('Accept') }}
                </button>

                <button
                    wire:click="openDeclineModal({{ json_encode([
                        'id' => $notification->id,
                        'approval_id' => $notification->approval_details->id ?? null,
                        'message' => $notification->data['message'],
                        'change_type' => $notification->approval_details->change_type ?? null,
                    ]) }})"
                    class="btn btn-secondary">
                    {{ __('Decline') }}
                </button>
            @else
                <p class="text-gray-400">{{ __('No actions available for this notification.') }}</p>
            @endif
        </div>

    </div>
</div>

