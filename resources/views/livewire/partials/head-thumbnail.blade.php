<!-- Modern Notification Card -->
<div class="notification-thumbnail group bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30 rounded-2xl p-4 transition-all duration-300 cursor-pointer transform hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20"
     data-notification-id="{{ $notif->id }}"
     data-created-at="{{ $notif->created_at }}"
     data-status="{{ $notif->model->status ?? null }}">

    <!-- Card Header with Icon and Type -->
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center space-x-3">
            <!-- Notification Type Icon -->
            @if ($notif->type === 'App\Notifications\Wallets\WalletCreation')
                <div class="p-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl">
                    <x-repo-icon name="wallet" class="w-5 h-5 text-white" />
                </div>
            @else
                <div class="p-2 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
                    </svg>
                </div>
            @endif

            <!-- Priority Badge -->
            <span class="bg-gradient-to-r from-purple-600 to-blue-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                {{ $notif->type === 'App\Notifications\Wallets\WalletCreation' ? __('Wallet') : __('Alert') }}
            </span>
        </div>

        <!-- Time Badge -->
        <span class="text-xs text-gray-400 bg-white/5 px-2 py-1 rounded-lg">
            {{ $notif->created_at->diffForHumans() }}
        </span>
    </div>

    <!-- Card Content -->
    <div class="space-y-2">
        <!-- Message Preview -->
        <h4 class="text-white font-medium text-sm line-clamp-2 group-hover:text-purple-200 transition-colors">
            @if (!isset($notif->data['message']))
                {{ __($notif->model->message) }}
            @else
                {{ Str::limit($notif->data['message'], 80) }}
            @endif
        </h4>

        <!-- Status Indicator -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-400">{{ __('Awaiting action') }}</span>
            </div>

            <!-- Action Hint -->
            <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </div>
</div>
