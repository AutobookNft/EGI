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
            {{-- @dump($notification) --}}
        @empty
            <p class="text-gray-400">{{ __('No pending notifications available.') }}</p>
        @endforelse
    </div>

    <!-- Bottone per Mostrare/Nascondere Storico -->
    <div class="text-right mt-4">
        <button wire:click="toggleHistoricalNotifications" class="btn btn-sm btn-secondary">
            {{ $showHistoricalNotifications ? __('Hide Processed Notifications') : __('Show Processed Notifications') }}
        </button>
    </div>

    <!-- Notifiche Storiche -->
    @if ($showHistoricalNotifications)
        <div class="bg-gray-700 p-6 rounded-lg shadow-md mt-4">
            <h3 class="text-xl font-bold text-white mb-4">{{ __('Processed Notifications') }}</h3>

            <div class="space-y-4">
                @forelse ($historicalNotifications as $notification)
                    <div class="flex items-start space-x-4">
                        <div class="w-2 h-2 rounded-full bg-gray-400 mt-2"></div>
                        <div class="flex-1 bg-gray-800 p-4 rounded-lg shadow-md">
                            <!-- Inizio del Collapse -->
                            <div class="collapse collapse-arrow bg-gray-600 rounded-lg">
                                <input type="checkbox" id="collapse-{{ $notification->id }}" class="peer hidden" />

                                <!-- Label modificata -->
                                <label for="collapse-{{ $notification->id }}" class="collapse-title flex justify-between items-center text-lg font-medium cursor-pointer text-gray-200">
                                    <span>{{ $notification->data['message'] }}</span>
                                    <button
                                        wire:click="deleteNotificationAction('{{ $notification->id }}')"
                                        wire:confirm="{{ __('notification.confirm_delete') }}"
                                        class="btn btn-warning btn-sm">
                                        {{ __('label.delete') }}
                                    </button>
                                </label>

                                <!-- Contenuto del Collapse -->
                                <div class="collapse-content peer-checked:block hidden">
                                    <p class="text-sm text-gray-300">
                                        {{ __('notification.reply') }}:
                                        <span class="font-bold {{ $notification->outcome === 'declined' ? 'text-red-500' : 'text-green-500' }}">
                                            {{ ucfirst($notification->outcome) }}
                                        </span>
                                    </p>

                                    <!-- Controlliamo che ci siano dettagli della proposta -->
                                    @if($notification->approval_details)
                                        <p class="text-sm text-gray-300">
                                            {{ __('collection.wallet.wallet_address') }}:
                                            <span class="font-bold">{{ $notification->approval_details->wallet_address }}</span>
                                        </p>
                                        <p class="text-sm text-gray-300">
                                            {{ __('collection.wallet.royalty_mint') }}:
                                            <span class="font-bold">{{ $notification->approval_details->royalty_mint . '%' }}</span>
                                        </p>
                                        <p class="text-sm text-gray-300">
                                            {{ __('collection.wallet.royalty_rebind') }}:
                                            <span class="font-bold">{{ $notification->approval_details->royalty_rebind . '%' }}</span>
                                        </p>
                                        <p class="text-sm text-gray-300">
                                            {{ __('notification.status') }}:
                                            <span class="font-bold">{{ ucfirst($notification->approval_details->status) }}</span>
                                        </p>
                                        <p class="text-sm text-gray-300">
                                            {{ __('notification.type') }}:
                                            <span class="font-bold">{{ ucfirst($notification->approval_details->change_type) }}</span>
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-300">{{ __('notification.no_details_available') }}</p>
                                    @endif
                                </div>
                            </div>
                            <!-- Fine del Collapse -->
                            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">{{ __('No historical notifications.') }}</p>
                @endforelse
            </div>

        </div>
    @endif


    <livewire:proposals.decline-proposal-modal />

</div>
