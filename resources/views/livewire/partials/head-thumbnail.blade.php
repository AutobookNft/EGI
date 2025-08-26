@php
    // Determine notification priority and styling
    $priority = 'medium'; // default
    $priorityColors = [
        'high' => 'from-red-500 to-pink-600',
        'medium' => 'from-yellow-500 to-orange-500',
        'low' => 'from-green-500 to-emerald-500'
    ];

    $borderColors = [
        'high' => 'border-red-500/30 hover:border-red-400/50',
        'medium' => 'border-yellow-500/30 hover:border-yellow-400/50',
        'low' => 'border-green-500/30 hover:border-green-400/50'
    ];

    // Priority logic based on notification type and age
    if ($notif->type === 'App\Notifications\Wallets\WalletCreation') {
        $priority = 'high';
        $priorityLabel = __('Urgent');
        $priorityIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>';
    } elseif ($notif->created_at->diffInHours() < 2) {
        $priority = 'high';
        $priorityLabel = __('Recent');
        $priorityIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    } elseif ($notif->created_at->diffInDays() < 1) {
        $priority = 'medium';
        $priorityLabel = __('Today');
        $priorityIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>';
    } else {
        $priority = 'low';
        $priorityLabel = __('Older');
        $priorityIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    }

    $gradientClass = $priorityColors[$priority];
    $borderClass = $borderColors[$priority];
@endphp

<!-- Modern Notification Card with Priority System -->
<div class="notification-thumbnail group bg-white/10 hover:bg-white/20 border {{ $borderClass }} rounded-2xl p-4 transition-all duration-300 cursor-pointer transform hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20 relative overflow-hidden"
     data-notification-id="{{ $notif->id }}"
     data-created-at="{{ $notif->created_at }}"
     data-status="{{ $notif->model->status ?? null }}"
     data-priority="{{ $priority }}">

    <!-- Priority Indicator Strip -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {{ $gradientClass }}"></div>

    <!-- Priority Badge (Top Right) -->
    <div class="absolute -top-2 -right-2 bg-gradient-to-r {{ $gradientClass }} text-white text-xs px-2 py-1 rounded-full font-medium shadow-lg {{ $priority === 'high' ? 'animate-pulse' : '' }}">
        {{ $priorityLabel }}
    </div>

    <!-- Card Header with Icon and Type -->
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center space-x-3">
            <!-- Notification Type Icon with Priority Colors -->
            @if ($notif->type === 'App\Notifications\Wallets\WalletCreation')
                <div class="p-2 bg-gradient-to-r {{ $gradientClass }} rounded-xl {{ $priority === 'high' ? 'animate-pulse' : '' }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $priorityIcon !!}
                    </svg>
                </div>
            @else
                <div class="p-2 bg-gradient-to-r {{ $gradientClass }} rounded-xl">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
                    </svg>
                </div>
            @endif

            <!-- Type Badge with Priority Context -->
            <span class="bg-gradient-to-r from-purple-600 to-blue-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                @if($notif->type === 'App\Notifications\Wallets\WalletCreation')
                    {{ __('Wallet') }}
                @else
                    {{ __('Alert') }}
                @endif
            </span>
        </div>        <!-- Time Badge with Priority Context -->
        <div class="flex flex-col items-end space-y-1">
            <span class="text-xs text-gray-400 bg-white/5 px-2 py-1 rounded-lg">
                {{ $notif->created_at->diffForHumans() }}
            </span>
            @if($priority === 'high')
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-red-500 rounded-full animate-ping"></div>
                    <span class="text-xs text-red-400 font-medium">{{ __('Urgent') }}</span>
                </div>
            @endif
        </div>
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

        <!-- Status Indicator with Priority -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r {{ $gradientClass }} rounded-full {{ $priority === 'high' ? 'animate-pulse' : '' }}"></div>
                <span class="text-xs text-gray-400">
                    @if($priority === 'high')
                        {{ __('Requires immediate attention') }}
                    @elseif($priority === 'medium')
                        {{ __('Awaiting action') }}
                    @else
                        {{ __('Review when convenient') }}
                    @endif
                </span>
            </div>

            <!-- Action Hint with Priority Arrow -->
            <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-400 transition-colors {{ $priority === 'high' ? 'animate-bounce' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </div>
</div>
