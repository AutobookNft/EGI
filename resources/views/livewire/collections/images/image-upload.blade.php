<div class="w-full sm:max-w-[300px] px-2 flex-shrink-0">
    <h3 class="text-lg font-semibold text-white mb-2">
        {{ $imageType === 'card' ? __('collection.card_image') : __('collection.EGI_image') }}
    </h3>

    @php
        // Determina lo stato dell'immagine
        $borderColor = 'border-red-500'; // Default: nessuna immagine caricata

        if ($image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $borderColor = 'border-yellow-500'; // Immagine in anteprima
        } elseif ($existingImageUrl) {
            $borderColor = 'border-green-500'; // Immagine salvata
        }
    @endphp

    <!-- Applica il bordo al div genitore -->
    <div class="w-full h-48 bg-gray-900 rounded-2xl shadow-md flex items-center justify-center overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300 border-4 {{ $borderColor }}"
         @if(!$imageUrl) onclick="document.getElementById('image-{{ $imageType }}-{{ $collectionId }}').click();" @endif>
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ ucfirst($imageType) }} Image" class="w-full h-full object-contain" loading="lazy">
        @else
            <x-repo-icon name="camera" class="w-16 h-16 text-gray-500 opacity-50" />
        @endif
    </div>

    <!-- Input nascosto per caricare l'immagine -->
    <input type="file" wire:model="image" id="image-{{ $imageType }}-{{ $collectionId }}" class="hidden" accept="image/*">

    <!-- Pulsanti per Salvare e Rimuovere l’Immagine -->
    <div class="mt-2 flex gap-2 justify-center">
        @if($image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            <button type="button" wire:click="saveImage" class="btn btn-success">
                {{ __('collection.save_' . $imageType) }}
            </button>
        @endif

        @if($existingImageUrl || $image)
            <button type="button" onclick="confirmDelete({{ json_encode($imageType) }})" class="btn btn-error">
                {{ __('collection.delete_' . $imageType) }}
            </button>
        @endif
    </div>
</div>

<!-- Script per SweetAlert2 -->
<script>

    function confirmDelete(type) {
        // Mappa dei tipi di immagine per le traduzioni
        const typeMap = {
            'banner': '{{ __("collection.banner_image") }}',
            'avatar': '{{ __("collection.avatar_image") }}',
            'card': '{{ __("collection.card_image") }}',
            'EGI': '{{ __("collection.EGI_image") }}'
        };

        Swal.fire({
            title: '{{ __("collection.confirm_delete_title") }}',
            text: '{{ __("collection.confirm_delete_text", ["type" => ":type"]) }}'.replace(':type', typeMap[type]),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("collection.confirm_delete_button") }}',
            cancelButtonText: '{{ __("collection.cancel_delete_button") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.removeImage();
                Swal.fire(
                    '{{ __("collection.deleted_title") }}',
                    '{{ __("collection.deleted_text", ["type" => ":type"]) }}'.replace(':type', typeMap[type]),
                    'success'
                );
            }
        });
    }

</script>
