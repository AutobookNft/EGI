<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Crea una Nuova Collection</h1>

    <form wire:submit.prevent="create">
        <div class="mb-4">
            <label for="collection_name" class="block font-semibold">Nome della Collection</label>
            <input type="text" id="collection_name" wire:model="collection.collection_name" class="w-full border rounded p-2">
            @error('collection_name') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">{{ __('collection.new_collection') }}</button>
    </form>

    @if (session()->has('error'))
        <div class="text-red-500 mt-4">{{ session('error') }}</div>
    @endif
</div>

<script>
    console.log('resources/views/livewire/create-collection.blade.php');
</script>


