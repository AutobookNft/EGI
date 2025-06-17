<button class="flex-shrink-0 p-3 transition-colors duration-150 rounded-md notification-thumbnail relativet"
    data-notification-id="{{ $notif->id }}" data-created-at="{{ $notif->created_at }}"
    data-status="{{ $notif->model->status ?? null }}"
    style="background-color: {{ $notif->id === $activeNotificationId ? '#4a5568' : '#2d3748' }};">

    <div id="text-tooltip" class="z-0 flex items-center space-x-3">
        @if ($notif->type === 'App\Notifications\Wallets\WalletCreation')
            <x-repo-icon name="wallet" class="w-10 h-10 text-gray-500 opacity-50" />
        @else
            <svg class="w-5 h-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
            </svg>
        @endif

        <div class="text-left">
            <p class="max-w-[150px] truncate text-sm font-medium text-white">
                @if (!isset($notif->data['message']))
                    {{ __($notif->model->message) }}
                @else
                    {{ (Str::limit($notif->data['message'], 20)) }}
                @endif
                {{-- @dump($notif->message ?? 'fava') --}}
            </p>
            <p id="expiration-warning" class="relative z-10 text-xs text-gray-400 group"></p>
            <p class="text-xs text-gray-400">
                {{ $notif->created_at->diffForHumans() }}
            </p>
        </div>
    </div>
</button>
