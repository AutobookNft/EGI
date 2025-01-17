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
