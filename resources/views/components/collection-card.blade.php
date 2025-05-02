@props(['id', 'editable' => false, 'imageType' => 'card', 'show_save_button' => true])

<div x-data="{ preview: null }" class="bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-shadow duration-300 overflow-hidden flex flex-col h-full">
    <!-- Immagine o Placeholder -->
    <figure class="h-48 w-full overflow-hidden bg-gray-900 flex items-center justify-center">
        <!-- Anteprima dell'immagine caricata con Alpine.js -->
        <template x-if="preview">
            <img
                :src="preview"
                alt="Preview"
                class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105"
            >
        </template>

        @php
            $imagePath = $imageType === 'card' ? $collection->image_card : $collection->image_EGI;
        @endphp

        <!-- Immagine salvata se esiste, nascosta se c'è l'anteprima -->
        @if($imagePath)
            <img
                x-show="!preview"
                src="{{ $imagePath }}"
                alt="{{ $collection->collection_name }}"
                class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105"
            >
        @endif

        <!-- Icona della fotocamera se non c'è immagine -->
        @if(!$imagePath)
            <div
                class="h-full w-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600"
                @if($editable) onclick="document.getElementById('image-{{ $imageType }}-{{ $id }}').click();" @endif
            >
                <x-repo-icon name="camera" class="w-20 h-20 text-white opacity-50" />
            </div>
        @endif
    </figure>

    <!-- Input File per il Caricamento dell’Immagine -->
    {{-- <input
        type="file"
        wire:model="uploadedImage"
        id="image-{{ $imageType }}-{{ $id }}"
        class="hidden"
        accept="image/*"
        @change="preview = URL.createObjectURL($event.target.files[0])"
    > --}}

    <!-- Contenuto della Card -->
    <div class="p-4 flex flex-col justify-between flex-grow">
        <h2 class="text-xl font-bold text-white mb-2">
            {{ $collection->collection_name }}
        </h2>
        <p class="text-gray-400 text-sm line-clamp-2 mb-4">
            {{ Str::limit($collection->description, 100) }}
        </p>

        <!-- Badge e ID -->
        <div class="flex justify-between items-center mt-auto">
            <span class="badge bg-gradient-to-r from-purple-500 to-blue-500 text-white px-4 py-3 rounded-full">
                EGI
            </span>
            <span class="text-xs text-gray-500">ID: {{ $collection->id }}</span>
        </div>

        <!-- Pulsanti di Azione -->
        <div class="flex justify-end space-x-2 mt-4">
            @if($show_save_button)
                <a href="{{ route('collections.edit', ['id' => $collection->id]) }}" class="btn btn-primary btn-sm">
                    {{ __('collection.open_collection') }}
                </a>
            @endif
        </div>
    </div>
</div>
