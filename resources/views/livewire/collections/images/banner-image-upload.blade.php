<div class="mb-8">
    <h3 class="text-lg font-semibold text-white mb-2">{{ __('collection.banner_image') }}</h3>

    @php
        // Determina lo stato dell'immagine
        $borderColor = 'border-red-500'; // Default: nessuna immagine caricata

        if ($bannerImage instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $borderColor = 'border-yellow-500'; // Immagine in anteprima
        } elseif ($existingImageUrl) {
            $borderColor = 'border-green-500'; // Immagine salvata
        }
    @endphp

    <!-- Cornice con bordo dinamico -->
    <div class="max-w-[1200px] max-h-[300px] w-full h-auto mx-auto md:h-64 bg-gray-900 rounded-2xl shadow-md flex items-center justify-center overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300 border-4 {{ $borderColor }}"
         @if(!$imageUrl) onclick="document.getElementById('banner-image-{{ $collectionId }}').click();" @endif>
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="Banner" class="w-full h-full object-contain" loading="lazy">
        @else
            <x-repo-icon name="camera" class="w-16 h-16 text-gray-500 opacity-50" />
        @endif
    </div>

    <!-- Input nascosto per caricare l'immagine -->
    <input type="file" wire:model="bannerImage" id="banner-image-{{ $collectionId }}" class="hidden" accept="image/*">

    <!-- Pulsanti per Salvare e Rimuovere l’Immagine -->
    <div class="mt-2 flex gap-2 justify-center">
        @if($bannerImage instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            <button type="button" wire:click="saveImage" class="btn btn-success">
                {{ __('collection.save_banner') }}
            </button>
        @endif

        @if($existingImageUrl || $bannerImage)
            <button type="button" onclick="confirmDelete('banner')" class="btn btn-error">

                {{ __('collection.delete_banner') }}
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
