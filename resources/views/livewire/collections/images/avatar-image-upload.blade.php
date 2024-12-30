<div class="mb-8 text-center">
    <h3 class="text-lg font-semibold text-white mb-2">{{ __('collection.avatar_image') }}</h3>

    @php
        // Determina lo stato dell'immagine
        $borderColor = 'border-red-500'; // Default: nessuna immagine caricata

        if ($image_avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $borderColor = 'border-yellow-500'; // Immagine in anteprima
        } elseif ($existingImageUrl) {
            $borderColor = 'border-green-500'; // Immagine salvata
        }
    @endphp

    <!-- Cornice con bordo dinamico -->
    <div class="w-32 h-32 mx-auto bg-gray-900 rounded-full shadow-md flex items-center justify-center overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300 border-4 {{ $borderColor }}"
         @if(!$imageUrl) onclick="document.getElementById('avatar-image-{{ $collectionId }}').click();" @endif>
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="Avatar" class="w-full h-full object-cover" loading="lazy">
        @else
            <x-repo-icon name="camera" class="w-10 h-10 text-gray-500 opacity-50" />
        @endif
    </div>

    <!-- Input nascosto per caricare l'immagine -->
    <input type="file" wire:model="image_avatar" id="avatar-image-{{ $collectionId }}" class="hidden" accept="image/*">

    <!-- Pulsanti per Salvare e Rimuovere l’Immagine -->
    <div class="mt-2 flex gap-2 justify-center">
        @if($image_avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            <button type="button" wire:click="saveImage" class="btn btn-success">
                {{ __('collection.save_avatar') }}
            </button>
        @endif

        @if($existingImageUrl || $image_avatar)
            <button type="button" onclick="confirmDelete('avatar')" class="btn btn-error">
                {{ __('collection.delete_avatar') }}
            </button>
        @endif
    </div>
</div>

