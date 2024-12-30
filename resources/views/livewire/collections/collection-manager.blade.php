<div class="max-w-4xl p-4 mx-auto">

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <h2 class="mb-4 text-2xl font-bold">{{ __('collection.manage_collection') }}</h2>

    <form wire:submit.prevent="save({{ $collectionId }})"
        class="p-6 space-y-6 bg-white rounded-lg shadow-sm">

        <!-- Sezione dei dati della collection -->
        @include('livewire.collection-manager-includes.data_section')

        <div class="mt-6 bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 flex items-center justify-center">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Bottone per aprire la vista per la gestione delle immagini di testata -->
                <a href="{{ route('collections.head_images', ['id' => $collectionId]) }}" class="btn btn-primary btn-lg">
                    {{ __('collection.collection_image') }}
                </a>

                <!-- Bottone per aprire la vista dei membri della collection -->
                <a href="{{ route('collections.collection_user', ['id' => $collectionId]) }}" class="btn btn-primary btn-lg">
                    {{ __('collection.collection_members') }}
                </a>
                <!-- Bottone per il salvataggio -->
                <div class="flex justify-end">
                    <x-form-button type="submit" style="primary" class="px-6">
                        {{ __('label.save') }}
                    </x-form-button>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
    console.log('resources/views/livewire/collection-manager.blade.php');
</script>


<script>
    function closeModal() {
    document.querySelector('.fixed').remove();
}
</script>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Indirizzo copiato negli appunti!');
        }).catch(err => {
            console.error('Errore durante la copia: ', err);
        });
    }
</script>

<script>
    document.addEventListener('livewire:init', () => {
        // Gestisce errori di permessi o appartenenza
        Livewire.on('swal:error', (text) => {
            Swal.fire({
                icon: 'error',
                title: text[0]['title'],
                text: text[0]['text'],
                confirmButtonColor: '#d33',
                confirmButtonText: 'Chiudi'
            });
        });
    });
</script>
