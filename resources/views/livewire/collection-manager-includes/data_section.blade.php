<div id="image_of_collection" class="p-6 border border-gray-700 rounded-2xl bg-gray-800 shadow-lg">

    <!-- Titolo della sezione -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ __('collection.data_section_title') }}</h2>
            <p class="text-sm text-gray-400">{{ __('collection.data_section_description') }}</p>
        </div>

        <div class="ml-4">
            @include('livewire.modale.collection_general_suggestion')
        </div>
    </div>

    <!-- Campi di Input -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        <!-- Collection Name -->
        <div class="bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
            <x-form-input
                id="collection_name"
                type="text"
                :label="__('collection.collection_name')"
                :placeholder="__('collection.collection_name')"
                :datatip="__('collection.collection_name')"
                model="collection.collection_name"
                width_label="w-full"
                width_input="w-full"
                icon="collection-name"
                required
                icon_class="w-5 h-5 text-gray-400" />
        </div>

        <!-- Collection is_published -->
        <div class="bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="form-control w-52">
                <label class="cursor-pointer label">
                    <span class="label-text text-white">{{ __('collection.publish_collection') }}</span>
                    <input type="checkbox" class="toggle toggle-primary" wire:model='collection.is_published' />
                </label>
            </div>
        </div>

        <!-- Select Content Type -->
        <div class="bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text text-white">{{ __('collection.select_content_type') }}</span>
                </div>
                <select class="select select-bordered select-primary bg-gray-700 text-white">
                    <option disabled selected>{{ __('collection.select_type') }}</option>
                    <option value="image">{{ __('collection.type_image') }}</option>
                    <option value="ebook">{{ __('collection.type_ebook') }}</option>
                    <option value="audio">{{ __('collection.type_audio') }}</option>
                    <option value="video">{{ __('collection.type_video') }}</option>
                </select>
            </label>
        </div>

    </div>

    <!-- Altri Campi -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mt-6">

        <!-- EGI Number -->
        <div class="bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
            <x-form-input
                :label="__('collection.EGI_number')"
                :placeholder="__('collection.EGI_number')"
                :datatip="__('collection.EGI_number')"
                type="number"
                model="collection.EGI_number"
                id="EGI_number"
                width_label="w-full"
                width_input="w-full"
                required
                icon="collection-number"
                icon_class="w-5 h-5 text-gray-400" />
        </div>

        <!-- Floor Price -->
        <div class="bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
            <x-form-input
                :label="__('collection.EGI_floor_price')"
                :placeholder="__('collection.set_base_EcoNFT_price')"
                :datatip="__('collection.set_base_EcoNFT_price')"
                type="number"
                model="collection.floor_price"
                id="floor_price"
                width_label="w-full"
                width_input="w-full"
                required
                icon="egi-base-price"
                icon_class="w-5 h-5 text-gray-400" />
        </div>

        <!-- Collection Position -->
        <div class="bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
            <x-form-input
                :label="__('collection.position')"
                type="number"
                :datatip="__('collection.position_for_mor_than_one_collection')"
                model="collection.position"
                :placeholder="__('collection.position')"
                id="position"
                width_label="w-full"
                width_input="w-full"
                required
                icon="collection-position"
                icon_class="w-5 h-5 text-gray-400" />
        </div>

    </div>

    <!-- Textarea per la Descrizione -->
    <div class="mt-6">
        <h2 for="description" class="block mb-2 text-sm font-medium text-white">
            {{ __('collection.collection_description') }}
        </h2>
        <div class="w-full tooltip tooltip-info z-10" data-tip="{{ __('collection.collection_description_suggest') }}">
            <textarea wire:model="collection.description"
                      id="description"
                      class="textarea textarea-bordered textarea-primary bg-gray-900 text-white w-full min-h-[120px] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300"
                      required
                      placeholder="{{ __('collection.collection_description_placeholder') }}">
            </textarea>
        </div>
        @error('collection.description')
            <span class="text-xs text-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- URL della Collection -->
    <div class="mt-6 bg-gray-900 p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
        <x-form-input
            :label="__('collection.collection_site_URL')"
            type="url"
            :datatip="__('collection.collection_site_URL_suggest')"
            model="collection.url_collection_site"
            id="url_collection_site"
            :placeholder="__('collection.collection_site_URL')"
            width_label="w-full"
            width_input="w-11/12"
            required
            icon="url"
            icon_class="w-5 h-5 text-gray-400" />
    </div>

</div>
