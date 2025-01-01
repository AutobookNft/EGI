<div class="w-full flex-shrink-0 px-2 sm:max-w-[300px]">
    <h3 class="mb-2 text-lg font-semibold text-white">
        {{ __('collection.EGI_image') }}
    </h3>

    @php
        // Determina lo stato dell'immagine
        $borderColor = 'border-red-500'; // Default: nessuna immagine caricata

        if ($image_EGI instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $borderColor = 'border-yellow-500'; // Immagine in anteprima
        } elseif ($existingImageUrl) {
            $borderColor = 'border-green-500'; // Immagine salvata
        }
    @endphp

    <!-- Applica il bordo al div genitore -->
    <div class="{{ $borderColor }} flex h-48 w-full cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-4 bg-gray-900 shadow-md transition-shadow duration-300 hover:shadow-lg"
        @if (!$imageUrl) onclick="document.getElementById('egi-image-{{ $collectionId }}').click();" @endif>
        <!-- Immagine o icona di default -->
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="EGI Image" class="h-full w-full object-contain" loading="lazy">
        @else
            <x-repo-icon name="camera" class="h-16 w-16 text-gray-500 opacity-50" />
        @endif
    </div>

    <!-- Input nascosto per caricare l'immagine -->
    <input type="file" wire:model="image_EGI" id="egi-image-{{ $collectionId }}" class="hidden" accept="image/*">

    <!-- Pulsanti per Salvare e Rimuovere l’Immagine -->
    <div class="mt-2 flex justify-center gap-2">
        @if ($image_EGI instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            <button type="button" wire:click="saveImage" class="btn btn-success">
                {{ __('collection.save_EGI') }}
            </button>
        @endif

        @if ($existingImageUrl || $image_EGI)
            <button type="button" onclick="confirmDelete('EGI')" class="btn btn-error">
                {{ __('collection.delete_EGI') }}
            </button>
        @endif
    </div>
</div>
