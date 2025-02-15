<div class="bg-gray-600 p-4 mb-4 rounded-lg" itemscope itemtype="https://schema.org/InformAction" aria-label="Notifica: Invito alla Collection">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <!-- Contenitore dei Dettagli -->
        <div class="bg-gray-800 p-4 rounded-lg shadow-md w-full" itemprop="result" itemscope itemtype="https://schema.org/Message">
            <!-- Header della Notifica -->
            <div class="flex flex-col items-start mb-4 pb-2 border-b border-gray-600">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-700 rounded-full mr-3" aria-label="Icona notifica">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" itemprop="name">{{ __('Invito alla Collection') }}</h3>
                </div>
            </div>

            <!-- Contenuto della Notifica -->
            <div class="space-y-2 mb-4">
                <p class="text-gray-300" itemprop="description">{{ $notification->data['message'] }}</p>

                <div class="flex items-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium" itemprop="sender">{{ $notification->data['sender'] }}</span>
                </div>

                <div class="flex items-center {{ $notification->outcome === 'rejected' ? 'text-red-300' : 'text-emerald-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium" itemprop="email">{{ $notification->data['email'] }}</span>
                </div>

                <div class="flex items-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium" itemprop="about">{{ $notification->data['collection_name'] }}</span>
                </div>
            </div>

            <!-- Footer con Timestamp -->
            <div class="flex items-center text-gray-400 text-sm pt-3 border-t border-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
            </div>
        </div>

        <!-- Contenitore dei Bottoni di Azione -->
        <div class="flex flex-col space-y-3" aria-label="Azioni per la notifica">
            <button id="accept-invitation"
                class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center"
                aria-label="Accetta invito alla Collection">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('label.accept') }}
            </button>
            <button id="reject-invitation"
                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center"
                aria-label="Rifiuta invito alla Collection">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ __('label.decline') }}
            </button>
        </div>
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
