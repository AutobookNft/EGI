<button
    class="notification-thumbnail flex-shrink-0 p-3 rounded-md transition-colors duration-150"
    data-notification-id="{{ $notif->id }}"
    style="background-color: {{ $notif->id === $activeNotificationId ? '#4a5568' : '#2d3748' }};">
    <div class="flex items-center space-x-3">
        @if($notif->type === 'App\Notifications\Wallets\WalletCreation')
            <x-repo-icon name="wallet" class="w-10 h-10 text-gray-500 opacity-50" />
        @else
            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
            </svg>
        @endif

        <div class="text-left">
            <p class="text-sm font-medium text-white truncate max-w-[150px]">
                {{ Str::limit($notif->data['message'] ?? '', 20) }}
            </p>
            <p class="text-xs text-gray-400">
                {{ $notif->created_at->diffForHumans() }}
            </p>
        </div>
    </div>
</button>
