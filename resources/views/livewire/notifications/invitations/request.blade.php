<div class="bg-blue-600 hover:bg-blue-700 transition-colors duration-200 p-6 mb-4 rounded-lg shadow-lg">
    <!-- Header della notifica con icona -->
    <div class="flex items-center mb-3">
        <div class="p-2 bg-blue-500 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-white">{{ __('Invito alla Collection') }}</h3>
    </div>

    <!-- Contenuto della notifica -->
    <div class="space-y-2 mb-4">
        <p class="text-blue-100">{{ $notification->data['message'] }}</p>

        <div class="flex items-center text-blue-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">{{ $notification->data['user_name'] }}</span>
        </div>

        <div class="flex items-center text-blue-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span class="font-medium">{{ $notification->data['collection_name'] }}</span>
        </div>
    </div>

    <!-- Pulsanti di azione -->
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

            <button
                id="reject-invitation"
                {{-- wire:dispatch="response('rejected')" --}}
                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ __('Decline') }}
            </button>
        </div>


    <div class="border-t border-gray-200 dark:border-gray-600 mb-4"></div>

    <!-- Footer con timestamp -->
    <div class="flex items-center text-blue-200 text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ $notification->created_at->diffForHumans() }}
    </div>
</div>

@script
<script>

    const acceptButton = document.getElementById('accept-invitation');
    const rejectButton = document.getElementById('reject-invitation');

    // Gestione messaggio di conferma per accettare
    acceptButton.addEventListener('click', function (event) {
        event.preventDefault();
        Swal.fire({
            title: 'Sei sicuro?',
            text: 'Accettando entrerai nel team della collection.',
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
                console.log('Invito accettato!');
                $wire.dispatch('response', { option: 'accepted' });
            }
        });
    });

    // Gestione messaggio di conferma per rifiutare
    rejectButton.addEventListener('click', function (event) {
        event.preventDefault();
        Swal.fire({
            title: 'Sei sicuro?',
            text: 'Rifiutando non entrerai nel team della collection.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sì, rifiuto!',
            cancelButtonText: 'No, annulla',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.dispatch('response', { option: 'rejected' });
            }
        });
    });

    // Ascolta la risposta dal backend
    $wire.on('notification-response', (event) => {
        if (event.detail.success) {
            Swal.fire(
                event.detail.option === 'accept' ? 'Invito accettato!' : 'Invito rifiutato!',
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
