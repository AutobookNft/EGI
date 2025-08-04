<div class="w-full max-w-4xl mx-auto md:p-4">

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

    <h2 class="px-4 mb-4 text-2xl font-bold md:px-0">{{ __('collection.manage_collection') }}</h2>

    <form wire:submit.prevent="save({{ $collectionId }})"
        class="space-y-6 md:bg-white md:rounded-lg md:shadow-sm md:p-6">

        <!-- Sezione dei dati della collection -->
        @include('livewire.collection-manager-includes.data_section')

        <div class="flex items-center justify-center p-4 mt-6 transition-shadow duration-300 bg-gray-900 shadow-md md:rounded-xl hover:shadow-lg">
            <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                <!-- Bottone per aprire la vista per la gestione delle immagini di testata -->
                <a href="{{ route('collections.head_images', ['id' => $collectionId]) }}" class="w-full btn btn-primary btn-lg">
                    {{ __('collection.collection_image') }}
                </a>

                <!-- Bottone per aprire la vista dei membri della collection -->
                @if(App\Helpers\FegiAuth::can('update_team'))
                    <a href="{{ route('collections.collection_user', ['id' => $collectionId]) }}" class="w-full btn btn-primary btn-lg">
                        {{ __('collection.collection_members') }}
                    </a>
                @endif
                <!-- Bottone per il salvataggio -->
                <div class="flex justify-center md:justify-end">
                    <x-form-button type="submit" style="primary" class="w-full px-6 md:w-auto">
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
