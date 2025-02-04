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
                                    <h3 class="text-md text-gray-300">
                                        {{ __('notification.reply') }}:
                                        <span class="font-bold {{ $notification->outcome === 'rejected' ? 'text-red-500' : 'text-green-500' }}">
                                            {{ ucfirst($notification->outcome) }}
                                        </span>
                                    </h3>

                                    <div class="m-2"></div> <!-- Spazio vuoto -->

                                    <!-- Controlliamo che ci siano dettagli della proposta -->
                                    @if(isset($notification->data) && is_array($notification->data))
                                        <div class="bg-gray-800 text-white p-4 rounded-lg shadow-md">
                                            <h3 class="text-md font-bold mb-2 text-yellow-400">Dati della Notifica</h3>
                                            <ul class="space-y-1">
                                                @foreach ($notification->data as $key => $value)
                                                    @if($key !=="message")
                                                        <li class="border-b border-gray-700 py-1 text-sm">
                                                            <strong class="text-green-400">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            @if(is_array($value) || is_object($value))
                                                                <pre class="text-gray-300 bg-gray-900 p-2 rounded">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @else
                                                                <span class="text-gray-200">{{ $value ?? 'N/A' }}</span>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <p class="text-red-500">Nessun dato disponibile.</p>
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
