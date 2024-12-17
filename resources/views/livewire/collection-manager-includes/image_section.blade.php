<div id="image_of_collection" class="p-4 border border-gray-300 rounded-lg bg-white shadow-md">

    <div class="mb-4 flex items-start justify-between">
        <!-- Primo div con il titolo e la descrizione -->
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ __('collection.image_section_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('collection.image_section_description') }}</p>
        </div>

        <!-- Div per il pulsante dei suggerimenti -->
        <div class="ml-4">
            @include('livewire.modale.collection_image_suggestion')
        </div>
    </div>

    <!-- Griglia delle immagini -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Immagine banner -->
        <x-image-uploader id="banner"
            id="banner"
            :label="__('collection.banner_image')"
            model="path_image_banner"
            :image="$collection['path_image_banner']"
            icon="camera"
            icon_class="w-6 h-6 opacity-50 text-base-content"
            remove-method="removeImage"
            />
        <!-- Immagine Card -->
        <x-image-uploader
            id="card"
            :label="__('collection.card_image')"
            model="path_image_card"
            :image="$collection['path_image_card']"
            icon="camera"
            icon_class="w-6 h-6 opacity-50 text-base-content"
            remove-method="removeImage"
            />
        {{-- @include('livewire.collection-manager-includes.path_image_card') --}}

        <!-- Immagine Avatar -->
        <x-image-uploader id="avatar"
            id="avatar"
            :label="__('collection.avatar_image')"
            model="path_image_avatar"
            :image="$collection['path_image_avatar']"
            icon="camera"
            icon_class="w-6 h-6 opacity-50 text-base-content"
            remove-method="removeImage"
            />
    </div>

    <div class="flex justify-end space-x-2 mt-4">
        <a href="{{ route('collections.head_images', ['id' => $collectionId]) }}" class="btn btn-primary btn-sm">
            {{ __('collection.open_collection') }}
        </a>
    </div>
</div>
