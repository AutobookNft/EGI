<div id="image_of_collection" class="p-4 border border-gray-300 rounded-lg bg-white shadow-md">

    <!-- Titolo della sezione -->
    <div class="mb-4 flex items-start justify-between">
        <!-- Primo div con il titolo e la descrizione -->
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ __('collection.data_section_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('collection.data_section_description') }}</p>
        </div>

        <!-- Div per il pulsante dei suggerimenti -->
        <div class="ml-4">
            @include('livewire.modale.collection_general_suggestion')
        </div>
    </div>


    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        <!-- Collection name-->
        <x-form-input id="collection_name" type="text" :label="__('collection.collection_name')" :placeholder="__('collection.collection_name')" :datatip="__('collection.collection_name')" model="collection.collection_name"
            width_label="w-full" width_input="w-full" icon="collection-name" required
            icon_class="w-4 h-4 opacity-50 text-base-content"/>

        <!-- Collection show-->
            <div class="form-control w-52">
            <label class="cursor-pointer label">
                <span class="label-text">{{ __('collection.publish_collection') }}</span>
                <input type="checkbox" class="toggle toggle-primary" checked="checked" wire:model='collection.show' />
            </label>
        </div>
        {{-- <span>id: {{ $collectionId }}</span> --}}

        <label class="form-control w-full max-w-xs">
            <div class="label">
              <span class="label-text">{{__('collection.select_content_type')}}</span>
            </div>
            <select class="select select-bordered select-primary" wire:model="collection.type">
                <option disabled selected>{{ __('collection.select_type') }}</option>
                <option value="image">{{ __('collection.type_image') }}</option>
                <option value="ebook">{{ __('collection.type_ebook') }}</option>
                <option value="audio">{{ __('collection.type_audio') }}</option>
                <option value="video">{{ __('collection.type_video') }}</option>
            </select>
        </label>

    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

        <!-- Egi number-->
        <x-form-input :label="__('collection.EGI_number')" :placeholder="__('collection.EGI_number')" :datatip="__('collection.EGI_number')" type="number" model="collection.EGI_number"
            id="EGI_number" width_label="w-full" width_input="w-full" required icon="collection-number"
            icon_class="w-6 h-6 opacity-50 text-base-content material-symbols-outlined" />

        <!-- Egi floor price-->
        <x-form-input :label="__('collection.EGI_floor_price')" :placeholder="__('collection.set_base_EcoNFT_price')" :datatip=" __('collection.set_base_EcoNFT_price')" type="number" model="collection.floor_price"
            id="floor_price" width_label="w-full" width_input="w-full" required icon="egi-base-price"
            icon_class="w-6 h-6 opacity-50 text-base-content material-symbols-outlined"/>

        <!-- Posizione della collection -->
            <x-form-input :label="__('collection.position')" type="number" :datatip="__('collection.position_for_mor_than_one_collection')" model="collection.position" :placeholder="__('collection.position')" id="position"
            width_label="w-fit" width_input="w-fit" required icon="collection-position" icon_class=""/>

    </div>


    <div class="w-full">
        <h2 for="description" class="block mt-4 mb-2 text-sm font-medium">
            {{ __('collection.collection_description') }}
        </h2>
        <div class = "w-full tooltip tooltip-info z-10" data-tip = "{{ __('collection.collection_description_suggest') }}">
            <textarea wire:model="collection.description" id="description"
                class="textarea textarea-bordered textarea-primary min-h-[100px] w-full" required
                placeholder="{{ __('collection.collection_description_placeholder') }}"></textarea>
        </div>
            @error('collection.description')
            <span class="text-xs text-error">{{ $message }}</span>
        @enderror
    </div>

    <x-form-input :label="__('collection.collection_site_URL')" type="url" :datatip="__('collection.collection_site_URL_suggest')" model="collection.url_collection_site"
        id="url_collection_site" :placeholder="__('collection.collection_site_URL')" width_label="w-full" width_input="w-11/12" required
        icon="url" icon_class="w-6 h-6 opacity-50 text-base-content"/>

</div>
