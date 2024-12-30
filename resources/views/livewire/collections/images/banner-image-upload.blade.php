<div class="mb-8">
    <h3 class="text-lg font-semibold text-white mb-2">{{ __('collection.banner_image') }}</h3>

    @php
        // Determina lo stato dell'immagine
        $borderColor = 'border-red-500'; // Default: nessuna immagine caricata

        if ($image_banner instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $borderColor = 'border-yellow-500'; // Immagine in anteprima
        } elseif ($existingImageUrl) {
            $borderColor = 'border-green-500'; // Immagine salvata
        }
    @endphp

    <!-- Cornice con bordo dinamico -->
    <div class="max-w-[1200px] max-h-[300px] w-full h-auto mx-auto md:h-64 bg-gray-900 rounded-2xl shadow-md flex items-center justify-center overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300 border-4 {{ $borderColor }}"
         @if(!$imageUrl) onclick="document.getElementById('banner-image-{{ $collectionId }}').click();" @endif>
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="Banner image" class="w-full h-full object-contain" loading="lazy">
        @else
            <x-repo-icon name="camera" class="w-16 h-16 text-gray-500 opacity-50" />
        @endif
    </div>

    <!-- Input nascosto per caricare l'immagine -->
    <input type="file" wire:model="image_banner" id="banner-image-{{ $collectionId }}" class="hidden" accept="image/*">

    <!-- Pulsanti per Salvare e Rimuovere l’Immagine -->
    <div class="mt-2 flex gap-2 justify-center">
        @if($image_banner instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            <button type="button" wire:click="saveImage" class="btn btn-success">
                {{ __('collection.save_banner') }}
            </button>
        @endif

        @if($existingImageUrl || $image_banner)
            <button type="button" onclick="confirmDelete('banner')" class="btn btn-error">
                {{ __('collection.delete_banner') }}
            </button>
        @endif
    </div>
</div>
