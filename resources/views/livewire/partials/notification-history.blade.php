@if ($showHistoricalNotifications)
        <div class="mt-8">
            <h3 class="mb-6 text-xl font-bold text-white">{{ __('Processed Notifications') }}</h3>

            <div class="space-y-4">
                @forelse ($historicalNotifications as $notification)
                    <!-- Notifica semplificata senza annidamenti -->
                    <div class="p-6 bg-gray-800/50 rounded-xl">
                        <!-- Header con titolo e bottone elimina -->
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-200">{{ $notification->data['message'] }}</h4>
                            <button onclick="confirmDelete('{{ $notification->id }}')" class="px-3 py-1 text-sm text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                                {{ __('label.delete') }}
                            </button>
                        </div>

                        <!-- Status della risposta -->
                        <div class="mb-4">
                            <span class="text-gray-300">{{ __('notification.reply') }}:</span>
                            <span class="font-bold ml-2 {{ $notification->outcome === App\Enums\NotificationStatus::REJECTED->value ? 'text-red-500' : 'text-green-500' }}">
                                {{ ucfirst($notification->outcome) }}
                            </span>
                        </div>

                        <!-- Dati della notifica -->
                        @if(isset($notification->data) && is_array($notification->data))
                            <div class="p-4 rounded-lg bg-gray-900/30">
                                <h5 class="mb-3 text-sm font-bold text-yellow-400">Dati della Notifica</h5>
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    @foreach ($notification->data as $key => $value)
                                        @if($key !=="message")
                                            <div class="text-sm">
                                                <span class="font-medium text-green-400">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                @if(is_array($value) || is_object($value))
                                                    <pre class="p-2 mt-1 overflow-x-auto text-xs text-gray-300 rounded bg-gray-900/50">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                @else
                                                    <span class="ml-2 text-gray-200">{{ $value ?? 'N/A' }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-red-400">Nessun dato disponibile.</p>
                        @endif

                        <!-- Timestamp -->
                        <div class="mt-4 text-xs text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-gray-400">{{ __('notification.no_historical_notifications') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
@endif

    @script
        <script>
        window.confirmDelete = function(notificationId) {
            Swal.fire({
                title: "{{ __('label.delete') }}",
                text: "{{ __('notification.confirm_delete') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "{{ __('label.confirm') }}",
                cancelButtonText: "{{ __('label.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.dispatch('deleteNotification', { notificationId: notificationId });
                }
            });
        }
        </script>
    @endscript

