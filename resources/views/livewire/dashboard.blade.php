<div class="p-6 bg-gray-800 text-white rounded-2xl shadow-lg">
    @php
        use App\Repositories\IconRepository;
    @endphp
    <script>
        console.log('Dashboard loaded...');
    </script>

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
    <div id="head-notifications-container">

        <!-- Thumbnails delle notifiche -->
        @include('livewire.partials.head-thumbnails-list')

    </div>

    <!-- Contenuto della notifica selezionata (che verrà aggiornato via JS) -->
    <div id="notification-details" class="bg-gray-800 border border-gray-600 rounded-lg shadow-lg p-6 transition duration-300 ease-in-out hover:shadow-2xl">
        @php
            if (count($pendingNotifications) > 0) {
                $text = __('notification.select_notification');
            } else {
                $text = __('notification.no_notifications');
            }
        @endphp
        <p class="text-gray-300 text-lg italic">{{$text}}</p>
    </div>

    <!-- Altri elementi come storico notifiche e modali -->
    <div class="text-right mt-4">
        <button wire:click="toggleHistoricalNotifications" class="btn btn-sm btn-secondary">
            {{ $showHistoricalNotifications ? __('notification.hide_processed_notifications') : __('notification.show_processed_notifications') }}
        </button>
    </div>

    @include('livewire.partials.notification-history')
    <livewire:notifications.wallets.decline-proposal-modal />
</div>
