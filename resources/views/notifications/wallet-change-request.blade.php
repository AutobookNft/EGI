<div class="bg-gray-600 p-4 mb-4 rounded-lg flex items-center justify-between">
    <div class="bg-gray-800 p-4 rounded-lg shadow-md">
        <div class="mb-2">
            <p class="text-lg font-semibold text-white">{{ $notification->data['message'] }}</p>
        </div>
        <div class="mb-2">
            <p class="text-md text-gray-300">
                <strong>{{ __('Wallet Address:') }}</strong> {{ $notification->data['wallet_address'] }}
            </p>
        </div>
        <div class="mb-2">
            <p class="text-md text-gray-300">
                <strong>{{ __('Mint Royalty:') }}</strong> {{ $notification->data['royalty_mint'] }}%
            </p>
        </div>
        <div class="mb-2">
            <p class="text-md text-gray-300">
                <strong>{{ __('Rebind Royalty:') }}</strong> {{ $notification->data['royalty_rebind'] }}%
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-400">
                {{ __('Created:') }} {{ $notification->created_at->diffForHumans() }}
            </p>
        </div>
    </div>

    <div class="flex space-x-2">
        @if (isset($notification->data['approval_id']) && $notification->data['type'] === 'create')
            <button wire:click="handleNotificationAction('{{ $notification->id }}', 'accept')" class="btn btn-primary">
                {{ __('Accept') }}
            </button>
            <button wire:click="handleNotificationAction('{{ $notification->id }}', 'decline')" class="btn btn-secondary">
                {{ __('Decline') }}
            </button>
        @endif
    </div>
</div>
