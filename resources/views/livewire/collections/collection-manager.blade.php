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

    <form wire:submit.prevent="collectionUpdate"
        class="p-6 space-y-6 bg-white rounded-lg shadow-sm">

        <!-- Sezione dei dati della collection -->
        @include('livewire.collection-manager-includes.data_section')

        <!-- Sezione delle immagini -->
        {{-- @include('livewire.collection-manager-includes.image_section') --}}

        <!-- Sezione dei wallets -->
        @include('livewire.collection-manager-includes.wallets_section')

        <div class="flex justify-end">
            <x-form-button type="submit" style="primary" class="px-6">
                {{ __('label.save') }}
            </x-form-button>
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

