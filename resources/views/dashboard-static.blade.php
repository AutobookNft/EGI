<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-gray-800 text-white rounded-2xl shadow-lg">
                    <!-- Titolo della Dashboard -->
                    <h2 class="text-3xl font-bold mb-6">{{ __('Dashboard') }}</h2>

                    <!-- Statistiche -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-700 p-4 rounded-lg shadow-md flex items-center">
                            <div class="icon mr-4">
                                <div class="icon-placeholder bg-gray-600 w-12 h-12 rounded-full flex items-center justify-center">
                                    <x-repo-icon name="open_collection" class="text-gray-500 opacity-90" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold">{{ __('Collections') }}</h3>
                                <p class="text-2xl font-bold">{{ $collectionsCount }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-700 p-4 rounded-lg shadow-md flex items-center">
                            <div class="icon mr-4">
                                <div class="icon-placeholder bg-gray-600 w-12 h-12 rounded-full flex items-center justify-center">
                                    <x-repo-icon name="collection-name" class="" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold">{{ __('Collection Members') }}</h3>
                                <p class="text-2xl font-bold">{{ $collectionMembersCount }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Header con thumbnails delle notifiche statiche -->
                    <div id="head-notifications-container" class="flex overflow-x-auto p-2 space-x-2 no-scrollbar">
                        @foreach($pendingNotifications as $notif)
                            <button class="notification-thumbnail relative flex-shrink-0 rounded-md p-3 transition-colors duration-150"
                                data-notification-id="{{ $notif->id }}" data-created-at="{{ $notif->created_at }}"
                                data-status="{{ $notif->model->status ?? null }}"
                                style="background-color: {{ $notif->id === $activeNotificationId ? '#4a5568' : '#2d3748' }};">
                                <div id="text-tooltip" class="z-0 flex items-center space-x-3">
                                    @if ($notif->type === 'App\Notifications\Wallets\WalletCreation')
                                        <x-repo-icon name="wallet" class="h-10 w-10 text-gray-500 opacity-50" />
                                    @else
                                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
                                        </svg>
                                    @endif

                                    <div class="text-left">
                                        <p class="max-w-[150px] truncate text-sm font-medium text-white">
                                            {{ Str::limit($notif->data['message'] ?? '', 20) }}
                                        </p>
                                        <p id="expiration-warning" class="group relative z-10 text-xs text-gray-400"></p>
                                        <p class="text-xs text-gray-400">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                            <button class="invitation-response-btn flex-1 items-center justify-center rounded-lg bg-green-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-green-600"
                                data-notification-id="{{ $notif->id }}" data-action="{{ App\Enums\NotificationStatus::ACCEPTED->value }}">
                                Accetta
                            </button>
                        @endforeach
                    </div>

                    <!-- Contenuto della notifica selezionata (aggiornato via JS) -->
                    <div id="notification-details" class="bg-gray-800 border border-gray-600 rounded-lg shadow-lg p-6 transition duration-300 ease-in-out hover:shadow-2xl">
                        @php
                            if (count($pendingNotifications) > 0) {
                                $text = __('notification.select_notification');
                            } else {
                                $text = __('notification.no_notifications');
                            }
                        @endphp
                        <p class="text-gray-300 text-lg italic">{{ $text }}</p>
                    </div>

                    <!-- Bottoni per storico notifiche statici -->
                    {{-- <div class="text-right mt-4">
                        <a href="{{ route('notifications.historical') }}" class="btn btn-sm btn-secondary">
                            {{ count($pendingNotifications) > 0 ? __('notification.show_processed_notifications') : __('notification.hide_processed_notifications') }}
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
