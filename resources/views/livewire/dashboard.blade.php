<div class="p-6 bg-gray-800 text-white rounded-2xl shadow-lg">
    @php
        use App\Repositories\IconRepository;
    @endphp

    <!-- Titolo della Dashboard -->
    <h2 class="text-3xl font-bold mb-6">{{ __('Dashboard') }}</h2>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-700 p-4 rounded-lg shadow-md flex items-center">
            <div class="icon mr-4">
                <!-- Icona per le Collections -->
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
                <!-- Icona per i Collection Members -->
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

    <!-- Header con thumbnails delle notifiche -->
    <div x-data="{
        activeId: @entangle('activeNotificationId'),
        showNotification(id) {
            this.activeId = id;
            $wire.setActiveNotification(id);
            // Forza il refresh del contenuto
            $wire.$refresh();
        }
    }">
        <!-- Thumbnails -->
        <div class="flex overflow-x-auto p-2 space-x-2 no-scrollbar">
            @foreach($pendingNotifications as $notif)
                <button
                    x-on:click="showNotification('{{ $notif->id }}')"
                    :class="{
                        'bg-gray-700': activeId === '{{ $notif->id }}',
                        'bg-gray-800 hover:bg-gray-700': activeId !== '{{ $notif->id }}'
                    }"
                    class="flex-shrink-0 p-3 rounded-md transition-colors duration-150"
                >
                    <div class="flex items-center space-x-3">
                        <!-- Icona basata sul tipo di notifica -->
                        @if($notif->type === 'App\Notifications\WalletChangeRequestCreation')
                            <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
                            </svg>
                        @endif

                        <!-- Preview del contenuto -->
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
            @endforeach
        </div>


       <!-- Contenuto notifica -->
       <div
       x-show="activeId"
       x-transition
       class="bg-gray-700 rounded-b-lg"
   >
       @if($activeNotificationId && ($notification = $this->getActiveNotification()))
           <div wire:key="notification-{{ $activeNotificationId }}">
               @include($this->getNotificationView($notification), ['notification' => $notification])
           </div>
       @endif
   </div>
</div>

    <!-- Bottone per mostrare/nascondere lo storico delle notifiche -->
    <div class="text-right mt-4">
        <button wire:click="toggleHistoricalNotifications" class="btn btn-sm btn-secondary">
            {{ $showHistoricalNotifications ? __('Hide Processed Notifications') : __('Show Processed Notifications') }}
        </button>
    </div>

    <!-- Notifiche Storiche -->
    @include('livewire.partials.notification-history')

    <livewire:proposals.decline-proposal-modal />

</div>
