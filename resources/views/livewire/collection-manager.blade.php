<div class="max-w-4xl p-4 mx-auto">

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <h2 class="mb-4 text-2xl font-bold">{{ __('collection.manage_collection') }}</h2>

    <form wire:submit.prevent="{{ $collectionId ? 'update' : 'create' }}"
        class="p-6 space-y-6 bg-white rounded-lg shadow-sm">

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
                      <option disabled selected>{{__('collection.type_image')}}</option>
                      <option>{{ __('collection.type_image') }}</option>
                      <option>{{ __('collection.type_ebook') }}</option>
                      <option>{{ __('collection.type_audio') }}</option>
                      <option>{{ __('collection.type_video') }}</option>
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

        <div id="image_of_collection" class="p-4 border border-gray-300 rounded-lg bg-white shadow-md">
            <!-- Titolo della sezione -->
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
        </div>

        <div id="wallet_section" class="p-4 border border-gray-300 rounded-lg bg-white shadow-md">
            <!-- Titolo della sezione -->
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-800">{{ __('collection.wallet_section_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('collection.wallet_section_description') }}</p>
            </div>

            <!-- Vista degli wallets Desktop -->
            @if($wallets)
                <div class="hidden md:block overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">{{ __('collection.wallet_address') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('collection.user_role') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('collection.royalty_mint') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('collection.royalty_rebind') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('collection.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($wallets as $wallet)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">
                                        @if ($wallet->wallet)
                                            <div class="tooltip tooltip-right text-sm" data-tip="{{ $wallet->wallet }}">
                                                <span class="ml-2 text-blue-500 hover:underline" onclick="copyToClipboard('{{ $wallet->wallet }}')">
                                                    {{ $wallet->short_wallet }}
                                                </span>
                                            </div>
                                            <button
                                                class="ml-2 text-blue-500 hover:underline"
                                                onclick="copyToClipboard('{{ $wallet->wallet }}')"
                                            >
                                                {{ __('Copia') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $wallet->user_role ?? __('collection.role_unknown') }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $wallet->royalty_mint }}%</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $wallet->royalty_rebind }}%</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ $wallet->status ? __('collection.active') : __('collection.inactive') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                        {{ __('collection.no_wallets') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Vista degli wallets mobile -->
            @if($wallets)
                <div class="block md:hidden">
                    @forelse ($wallets as $wallet)
                        <div class="p-4 mb-4 border border-gray-300 rounded-lg bg-gray-50">
                            <p class="text-gray-500">
                                <label>{{ __('collection.wallet.address') }}:</label>
                                <div class="tooltip tooltip-right" data-tip="{{ $wallet->wallet }}">
                                    <span class= "ml-2 text-blue-500 hover:underline text-xs" onclick="copyToClipboard('{{ $wallet->wallet }}')">{{ $wallet->short_wallet }}</span>
                                </div>
                                <button
                                    class="ml-2 text-blue-500 hover:underline"
                                    onclick="copyToClipboard('{{ $wallet->wallet }}')"
                                >
                                    {{ __('Copia') }}
                                </button>
                            </p>
                            <p class="text-sm text-gray-500">
                                <strong>{{ __('collection.wallet.user_role') }}:</strong> {{ $wallet->user_role ?? __('collection.wallet.role_unknown') }}
                            </p>
                            <p class="text-sm text-gray-500">
                                <strong>{{ __('collection.wallet.royalty_mint') }}:</strong> {{ $wallet->royalty_mint }}%
                            </p>
                            <p class="text-sm text-gray-500">
                                <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->royalty_rebind }}%
                            </p>
                            <p class="text-sm text-gray-500">
                                <strong>{{ __('collection.wallet.status') }}:</strong>
                                {{ $wallet->status ? __('label.active') : __('label.inactive') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-center text-gray-500">{{ __('collection.no_wallets') }}</p>
                    @endforelse
                </div>
            @endif
        </div>

        <div class="flex justify-end">
            <x-form-button type="submit" style="primary" class="px-6">
                {{ __('label.save') }}
            </x-form-button>
        </div>
    </form>

    <!-- Vista delel collections -->
    @if($collections)
        <h3 class="mt-6 text-xl">{{ __('collection.collections') }}</h3>
        <div class="grid grid-cols-3 gap-4">
            @php
                $repository = app(App\Repositories\IconRepository::class);
                $iconHtml = $repository->getIcon('camera', 'elegant', '');
            @endphp
            @foreach ($collections as $collection)
                <div class="shadow-xl card bg-base-100 w-96">
                    <figure class="px-10 pt-10">
                        @if($collection['verified_image_card_path'] && $collection['verified_image_card_path'] !== '')
                            <div class="object-cover px-10 pt-10 rounded-full">
                                <img src="{{ Storage::url($collection['verified_image_card_path']) }}" class="object-cover w-full h-full rounded-lg" alt="{{ $collection['path_image_card'] }}">
                            </div>
                        @else
                            <div class="object-cover px-10 pt-10 rounded-full"> {!! $iconHtml !!} </div>
                        @endif
                    </figure>
                    <div class="items-center text-center card-body">
                        <h2 class="card-title">{{ $collection->collection_name }}</h2>

                        <div class="text-center break-words overflow-hidden">
                            <p class="break-words whitespace-normal px-4 text-sm">
                                {{ $collection->description }}
                            </p>
                            <p class="text-gray-500 text-xs">
                                ID: {{ $collection->id }}
                            </p>
                        </div>

                        <div class="card-actions mt-4">
                            <button wire:click="edit({{ $collection->id }})" class="btn btn-primary">
                                {{ __('collection.open_collection') }}
                            </button>
                            <button wire:click="delete({{ $collection->id }})" class="btn btn-error">
                                {{ __('collection.collection_delete') }}
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    @php
        Log::info('Collection ID value:', ['id' => $collectionId]);
    @endphp

</div>

<script>
    console.log('resources/views/livewire/collection-manager.blade.php');
</script>


<script>
    function closeModal() {
    document.querySelector('.fixed').remove();
}
</script>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Indirizzo copiato negli appunti!');
        }).catch(err => {
            console.error('Errore durante la copia: ', err);
        });
    }
</script>

