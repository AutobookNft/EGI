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
                    <x-repo-icon name="members" class="" />
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

        @forelse ($notifications->filter(fn($notification) => $notification->outcome === 'pending') as $notification)
            @include($this->getNotificationView($notification))
        @empty
            <p class="text-gray-400">{{ __('No notifications available.') }}</p>
        @endforelse

    </div>

    <div class="bg-gray-700 p-6 rounded-lg shadow-md mt-2">
        <h3 class="text-xl font-bold text-white mb-4">{{ __('Processed Notifications') }}</h3>

        <div class="space-y-4">
            @forelse ($notifications->whereIn('outcome', ['accepted', 'declined']) as $notification)
                <div class="flex items-start space-x-4">
                    <!-- Indicatore Temporale -->
                    <div class="w-2 h-2 rounded-full bg-gray-400 mt-2"></div>

                    <!-- Contenuto della Notifica -->
                    <div class="flex-1 bg-gray-800 p-4 rounded-lg shadow-md">
                        <p class="text-md font-semibold text-white">{{ $notification->data['message'] }}</p>
                        <p class="text-sm text-gray-300">
                            {{ __('Outcome:') }} <span class="font-bold">{{ ucfirst($notification->outcome) }}</span>
                        </p>
                        <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400">{{ __('No processed notifications.') }}</p>
            @endforelse
        </div>
    </div>

</div>
