<script>
    console.log('resources/views/livewire/collections/images/card-image-upload.blade.php');
</script>
<div class="w-full sm:max-w-[300px] px-2 flex-shrink-0">
    <h3 class="text-lg font-semibold text-white mb-2"> {{ __('collection.card_image') }}</h3>

    @php
        // Determina lo stato dell'immagine
        $borderColor = 'border-red-500'; // Default: nessuna immagine caricata

        if ($image_card instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $borderColor = 'border-yellow-500'; // Immagine in anteprima
        } elseif ($existingImageUrl) {
            $borderColor = 'border-green-500'; // Immagine salvata
        }
    @endphp

    <!-- Applica il bordo al div genitore -->
    <div class="w-full h-48 bg-gray-900 rounded-2xl shadow-md flex items-center justify-center overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300 border-4 {{ $borderColor }}"
        @if(!$imageUrl) onclick="document.getElementById('card-image-{{ $collectionId }}').click();" @endif>
        <!-- Immagine o icona di default -->
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="Card Image" class="w-full h-full object-contain">
        @else
            <x-repo-icon name="camera" class="w-16 h-16 text-gray-500 opacity-50" />
        @endif
    </div>

    <!-- Input nascosto per caricare l'immagine -->
    <input type="file" wire:model="image_card" id="card-image-{{ $collectionId }}" class="hidden" accept="image/*">

    <!-- Pulsanti per Salvare e Rimuovere l’Immagine -->
    <div class="mt-2 flex gap-2 justify-center">
        @if($image_card instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            <button type="button" wire:click="saveImage" class="btn btn-success">
                {{ __('collection.save_card') }}
            </button>
        @endif

        @if($existingImageUrl || $image_card)
            <button type="button" onclick="confirmDelete('card')" class="btn btn-error">
                {{ __('collection.delete_card') }}
            </button>
        @endif
    </div>
</div>

