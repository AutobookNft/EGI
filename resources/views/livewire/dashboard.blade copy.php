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

    <!-- Notifiche -->
    <div class="bg-gray-700 p-6 rounded-lg shadow-md">
        <h3 class="text-2xl font-bold mb-4">{{ __('Notifications') }}</h3>

        <!-- Notifiche Pendenti -->
        @forelse ($pendingNotifications as $notification)

            @include($this->getNotificationView($notification))

        @empty
            <p class="text-gray-400">{{ __('No pending notifications available.') }}</p>
        @endforelse
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
