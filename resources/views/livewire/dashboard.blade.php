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
                    @php
                        $icon = (new IconRepository())->getIcon('open_collection', 'elegant', '');
                    @endphp

                    @if ($icon)
                        {!! $icon !!}
                    @endif
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
                    @php
                        $icon = (new IconRepository())->getIcon('members', 'elegant', '');
                    @endphp

                    @if ($icon)
                        {!! $icon !!}
                    @endif
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

        @forelse ($notifications as $notification)
            <div class="bg-gray-600 p-4 mb-4 rounded-lg flex items-center justify-between">
                <div>
                    <p class="text-lg">{{ $notification->data['message'] }}</p>
                    <p class="text-sm text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex space-x-2">
                    @if (isset($notification->data['invitation_id']))
                        <button wire:click="acceptInvitation({{ $notification->data['invitation_id'] }})" class="btn btn-primary">{{ __('Accept') }}</button>
                        <button wire:click="declineInvitation({{ $notification->data['invitation_id'] }})" class="btn btn-secondary">{{ __('Decline') }}</button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-400">{{ __('No notifications available.') }}</p>
        @endforelse
    </div>
</div>
