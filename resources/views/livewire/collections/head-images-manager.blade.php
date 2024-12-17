<div class="p-6 border border-gray-700 rounded-2xl bg-gray-800 shadow-lg">

    <h2 class="text-2xl font-bold text-white mb-6">{{ __('collection.manage_head_images') }}</h2>

    <!-- Banner -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-white mb-2">{{ __('collection.banner_image') }}</h3>
        <div class="w-full h-48 md:h-64 bg-gray-900 rounded-2xl shadow-md flex items-center justify-center overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300"
             @if(!$bannerImage) onclick="document.getElementById('banner').click();" @endif>
            @if($bannerImage)
                <img src="{{ $bannerImage->temporaryUrl() }}" alt="Banner" class="w-full h-full object-contain">
            @else
                <x-repo-icon name="camera" class="w-16 h-16 text-gray-500 opacity-50" />
            @endif
        </div>
        <input type="file" wire:model="bannerImage" id="banner" class="hidden" accept="image/*">
    </div>


    <div class="flex flex-wrap gap-4">
        <!-- Card Image -->
        <div class="w-full sm:max-w-[300px] px-2 flex-shrink-0">
            <x-collection-card :id="$collectionId" :editable="true" imageType="card" />

            <!-- Input nascosto per caricare l'immagine -->
            <input type="file" wire:model="cardImage" id="card-image-{{ $collectionId }}" class="hidden" accept="image/*">

            <!-- Pulsante per Rimuovere l'Immagine -->
            @if($cardImage)
                <div class="mt-2">
                    <button type="button" wire:click="removeImage('card')" class="btn btn-error">
                        {{ __('label.delete_card') }}
                    </button>
                </div>
            @endif
        </div>

        <!-- EGI Image -->
        @if (config('app.egi_asset'))
            <div class="w-full sm:max-w-[300px] px-2 flex-shrink-0">
                <x-collection-card :id="$collectionId" :editable="true" imageType="EGI" />

                <!-- Input nascosto per caricare l'immagine -->
                <input type="file" wire:model="EGIImage" id="EGI-image-{{ $collectionId }}" class="hidden" accept="image/*">

                <!-- Pulsante per Rimuovere l'Immagine EGI -->
                @if($EGIImage)
                    <div class="mt-2">
                        <button type="button" wire:click="removeImage('EGI')" class="btn btn-error">
                            {{ __('label.delete_egi') }}
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Avatar -->
    <div class="mb-8 text-center">
        <h3 class="text-lg font-semibold text-white mb-2">{{ __('collection.avatar_image') }}</h3>
        <div class="w-32 h-32 mx-auto bg-gray-900 rounded-full shadow-md flex items-center justify-center overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300"
             @if(!$avatarImage) onclick="document.getElementById('avatar').click();" @endif>
            @if($avatarImage)
                <img src="{{ $avatarImage->temporaryUrl() }}" alt="Avatar" class="w-full h-full object-cover">
            @else
                <x-repo-icon name="camera" class="w-10 h-10 text-gray-500 opacity-50" />
            @endif
        </div>
        <input type="file" wire:model="avatarImage" id="avatar" class="hidden" accept="image/*">
    </div>




    <!-- Pulsanti di Rimozione -->
    <div class="mt-8 flex flex-wrap gap-4">
        @if($bannerImage)
            <button type="button" wire:click="removeImage('banner')" class="btn btn-error">
                {{ __('label.delete_banner') }}
            </button>
        @endif
        @if($cardImage)
            <button type="button" wire:click="removeImage('card')" class="btn btn-error">
                {{ __('label.delete_card') }}
            </button>
        @endif
        @if($avatarImage)
            <button type="button" wire:click="removeImage('avatar')" class="btn btn-error">
                {{ __('label.delete_avatar') }}
            </button>
        @endif
    </div>

</div>
