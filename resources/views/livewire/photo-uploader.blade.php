<div class="p-6 bg-white shadow-md rounded-md">
    <h2 class="text-xl font-bold mb-4">Carica una Foto</h2>

    <!-- Form per il caricamento della foto -->
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- Input per il caricamento del file -->
        <input type="file" wire:model="photo" class="block w-full text-gray-700 border border-gray-300 rounded-md shadow-sm">

        <!-- Mostra l'anteprima se è stato selezionato un file -->
        @if ($photo)
            @dump($this->getTemporaryUrl())
            @if ($this->getTemporaryUrl())
                <img src="{{ $this->getTemporaryUrl() }}" alt="Anteprima della foto" class="w-48 h-48 object-cover rounded-md">
            @else
                <p class="text-red-500">Errore nel generare l'anteprima.</p>
            @endif
        @endif

        <!-- Bottone di caricamento -->
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md shadow hover:bg-blue-600">
            Carica
        </button>
    </form>

    <!-- Messaggio di successo -->
    @if (session()->has('message'))
        <div class="mt-4 p-2 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif
</div
