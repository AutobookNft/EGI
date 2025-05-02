<script>
    console.log('resources/views/livewire/collections/head-images-manager.blade.php');
</script>
<div class="mx-auto max-w-4xl rounded-2xl border border-gray-700 bg-gray-800 p-6 shadow-lg">

    <h2 class="mb-6 text-2xl font-bold text-white">{{ __('collection.manage_head_images') }}</h2>

    <livewire:collections.images.banner-image-upload :collectionId="$collectionId">

    <div class="grid grid-cols-1 gap-6 sm:col-span-2 sm:grid-cols-3">
        <livewire:collections.images.card-image-upload :collectionId="$collectionId" />

        <!-- EGI Image -->
        @if (config('app.egi_asset'))
            <livewire:collections.images.egi-image-upload :collectionId="$collectionId" />
        @endif

        <!-- Avatar -->
        <livewire:collections.images.avatar-image-upload :collectionId="$collectionId" />
    </div>

    <!-- Bottone che permette di aprire la collection -->
    @include('livewire.collection-manager-includes.back_to_collection_button')

</div>

<!-- Script per SweetAlert2 -->
<script>

    function confirmDelete(type) {

        console.log('confirmDelete',type);

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
                @this.removeImage(type);
                Swal.fire(
                    '{{ __("collection.deleted_title") }}',
                    '{{ __("collection.deleted_text", ["type" => ":type"]) }}'.replace(':type', typeMap[type]),
                    'success'
                );
            }
        });
    }

</script>
