<div class="bg-blue-600 p-4 mb-4 rounded-lg shadow-md">
    <p class="text-lg font-semibold text-white">{{ $notification->data['message'] }}</p>

    <div class="flex space-x-2 mt-2">

        <button wire:click="handleNotificationAction('{{ $notification->id }}', 'accept')" class="btn btn-primary">
            {{ __('Accept') }}
        </button>
        <button wire:click="handleNotificationAction('{{ $notification->id }}', 'decline')" class="btn btn-secondary">
            {{ __('Decline') }}
        </button>
    </div>
    <p class="text-sm text-gray-400">{{ __('Created:') }} {{ $notification->created_at->diffForHumans() }}</p>
</div>
