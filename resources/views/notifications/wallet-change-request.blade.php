<div class="bg-gray-600 p-4 mb-4 rounded-lg">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <!-- Contenitore dei Dettagli -->
        <div class="bg-gray-800 p-4 rounded-lg shadow-md w-full">
            <!-- Header -->
            <div class="flex items-center mb-4 pb-2 border-b border-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                </svg>
                <h3 class="text-lg font-bold text-white">{{ __('Wallet Change Request') }}</h3>
            </div>


            <!-- Dettagli Principali -->
            <div class="space-y-3">
                @if($notification->approval_details)

                    @php
                        // Associa ogni status a una classe CSS per il colore
                        switch ($notification->approval_details->status) {
                            case 'pending':
                                $statusClass = 'text-yellow-500';
                                break;
                            case 'accepted':
                                $statusClass = 'text-green-500';
                                break;
                            case 'rejected':
                                $statusClass = 'text-red-500';
                                break;
                            default:
                                $statusClass = 'text-gray-300';
                        }
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('Wallet Address') }}</p>
                            <p class="text-sm text-white truncate" title="{{ $notification->approval_details->wallet }}">
                                {{ $notification->approval_details->wallet }}
                            </p>
                        </div>
                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('Change Type') }}</p>
                            <p class="text-sm text-white">{{ ucfirst($notification->approval_details->change_type) }}</p>
                        </div>
                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('Mint Royalty') }}</p>
                            <p class="text-sm text-white">{{ $notification->approval_details->royalty_mint }}%</p>
                        </div>
                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('Rebind Royalty') }}</p>
                            <p class="text-sm text-white">{{ $notification->approval_details->royalty_rebind }}%</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-700 p-3 rounded">
                        <p class="text-sm text-gray-400 mb-1">{{ __('Status') }}</p>
                        <p class="text-sm {{ $statusClass }}">{{ ucfirst($notification->approval_details->status) }}</p>
                    </div>
                @else
                    <p class="text-gray-400">{{ __('Details unavailable.') }}</p>
                @endif

                <!-- Meta info -->
                <div class="flex justify-between items-center pt-3 border-t border-gray-600 text-xs text-gray-400">
                    <p>{{ __('Created:') }} {{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Contenitore dei Bottoni -->
        @if (isset($notification->approval_details) && $notification->approval_details->type === 'creation')
            <div class="flex flex-col space-y-2 md:w-auto">
                <button wire:click="handleNotificationAction('{{ $notification->id }}', 'accept')"
                    class="flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-150"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('Accept') }}
                </button>

                <button wire:click="openDeclineModal({{ json_encode([
                        'id' => $notification->id,
                        'notification_payload_wallet_id' => $notification->approval_details->id ?? null,
                        'data' => $notification['data'],
                        'message' => $notification['data']['message'],
                        'model_id' => $notification->model_id ?? null,
                    ]) }})"
                    class="flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors duration-150"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('Decline') }}
                </button>
            </div>
        @else
            <p class="text-gray-400">{{ __('No actions available.') }}</p>
        @endif
    </div>
</div>
