<script>console.log('resources/views/livewire/notification/wallets/creation.blade.php');</script>

<div class="bg-gray-600 p-4 mb-4 rounded-lg">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <!-- Contenitore dei Dettagli -->
        <div class="bg-gray-800 p-4 rounded-lg shadow-md w-full">
            <!-- Header -->
            <div class="flex flex-col items-left mb-4 pb-2 border-b border-gray-600">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
                    <p class="-mt-1 text-lg font-bold text-white">{{ $notification->data['message'] }}</p>
                </div>
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
                    <p class="-mt-1 text-lg font-bold text-white">{{ __('label.from'). ": ".  $notification->data['user_name'] }}</p>
                </div>
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
                    <p class="-mt-1 text-lg font-bold text-white">{{ __('collection.collection'). ": ".  $notification->data['collection_name'] }}</p>
                </div>
            </div>

            <!-- Dettagli Principali -->
            <div class="space-y-3">
                @if($notification->approval_details)
                    @php
                        // Associa ogni status a una classe CSS per il colore
                        switch ($notification->approval_details->status) {
                            case 'pending_create':
                                $statusClass = 'text-yellow-500';
                                $status = 'pending';
                                break;
                            case 'pending_update':
                                $statusClass = 'text-yellow-500';
                                $status = 'pending';
                                break;
                            case 'pending':
                                $statusClass = 'text-yellow-500';
                                $status = 'pending';
                                break;
                            case 'accepted':
                                $statusClass = 'text-green-500';
                                $status = 'accepted';
                                break;
                            case 'rejected':
                                $statusClass = 'text-red-500';
                                $status = 'rejected';
                                break;
                            default:
                                $statusClass = 'text-gray-300';
                                $status = 'unknown';
                        }
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.address') }}</p>
                            <p class="text-sm text-white truncate" title="{{ $notification->approval_details->wallet }}">
                                {{ $notification->approval_details->wallet }}
                            </p>
                        </div>

                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.status') }}</p>
                            <p class="text-sm {{ $statusClass }}">{{ $status }}</p>
                        </div>
                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.royalty_mint') }}</p>
                            <p class="text-sm text-white">{{ $notification->approval_details->royalty_mint }}%</p>
                        </div>
                        <div class="bg-gray-700 p-3 rounded">
                            <p class="text-sm text-gray-400 mb-1">{{ __('collection.wallet.royalty_rebind') }}</p>
                            <p class="text-sm text-white">{{ $notification->approval_details->royalty_rebind }}%</p>
                        </div>
                    </div>

                @else
                    <p class="text-gray-400">{{ __('Details unavailable.') }}</p>
                @endif

                <!-- Meta info -->
                <div class="flex justify-between items-center pt-3 border-t border-gray-600 text-xs text-gray-400">
                    <p>{{ __('label.created_at') . ": " }} {{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Contenitore dei Bottoni -->
        @if (isset($notification->approval_details) && $notification->approval_details->type === 'creation')
            <div class="flex space-x-3 mb-3">
                <div class="flex space-x-3 mb-3">
                    <button
                        id="accept-invitation"
                        {{-- wire:dispatch="response('accepted')" --}}
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Accept') }}
                    </button>

                    <button wire:click="openDeclineModal({{ json_encode([
                        'id' => $notification->id,
                        'data' => $notification['data'],
                        'message' => $notification['data']['message'],
                        'model_id' => $notification->model_id ?? null,
                        'model_type' => $notification->model_type ?? null,
                    ]) }})"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('Decline') }}
                    </button>
                </div>
            </div>
        @else
            <p class="text-gray-400">{{ __('notification.show_processed_notifications') }}</p>
        @endif
    </div>
</div>



@script
<script>

    const acceptButton = document.getElementById('accept-invitation');
    // const rejectButton = document.getElementById('reject-invitation');

    // Gestione messaggio di conferma per accettare
    acceptButton.addEventListener('click', function (event) {
        event.preventDefault();
        Swal.fire({
            title: 'Sei sicuro?',
            text: 'Accettando accetterai le condizioni del wallet.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sì, accetto!',
            cancelButtonText: 'No, annulla',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Nuovo Wallet accettato!');
                $wire.dispatch('response', { option: 'accepted' });
            }
        });
    });

    // Gestione messaggio di conferma per rifiutare
    // rejectButton.addEventListener('click', function (event) {
    //     event.preventDefault();
    //     Swal.fire({
    //         title: 'Sei sicuro?',
    //         text: 'Rifiutando non beneficerai di questo wallet.',
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonText: 'Sì, rifiuto!',
    //         cancelButtonText: 'No, annulla',
    //         customClass: {
    //             confirmButton: 'btn btn-danger',
    //             cancelButton: 'btn btn-secondary'
    //         },
    //         buttonsStyling: false
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $wire.dispatch('response', { option: 'rejected' });
    //         }
    //     });
    // });

    // Ascolta la risposta dal backend
    $wire.on('notification-response', (event) => {
        if (event.detail.success) {
            Swal.fire(
                event.detail.option === 'accept' ? 'Nuovo Wallet accettato!' : 'Nuovo Wallet rifiutato!',
                'Operazione completata con successo.',
                'success'
            );
        } else {
            Swal.fire(
                'Errore!',
                event.detail.error || 'Si è verificato un errore.',
                'error'
            );
        }
    });
</script>
@endscript


