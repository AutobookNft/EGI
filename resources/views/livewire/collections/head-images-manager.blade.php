<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">{{ __('collection.manage_head_images') }}</h2>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Upload Banner -->
        <div class="form-control">
            <label class="label">
                <span class="font-semibold label-text">{{ __('collection.banner_image') }}</span>
            </label>
            <div class="tooltip" data-tip="Click to upload banner image">
                <div class="w-full h-36 border rounded-lg cursor-pointer border-base-300 hover:border-primary"
                     @if(!$bannerImage) onclick="document.getElementById('banner').click();" @endif>
                    @if($bannerImage)
                        <img src="{{ $bannerImage->temporaryUrl() }}" class="object-cover w-full h-full rounded-lg">
                    @else
                        <div class="flex items-center justify-center h-full text-base-content">
                            <x-icon name="camera" class="w-12 h-12 opacity-50" />
                        </div>
                    @endif
                </div>
            </div>
            <input type="file" wire:model="bannerImage" id="banner" class="hidden" accept="image/*">
        </div>

        <!-- Upload Card -->
        <div class="form-control">
            <label class="label">
                <span class="font-semibold label-text">{{ __('collection.card_image') }}</span>
            </label>
            <div class="tooltip" data-tip="Click to upload card image">
                <div class="w-full h-24 border rounded-lg cursor-pointer border-base-300 hover:border-primary"
                     @if(!$cardImage) onclick="document.getElementById('card').click();" @endif>
                    @if($cardImage)
                        <img src="{{ $cardImage->temporaryUrl() }}" class="object-cover w-full h-full rounded-lg">
                    @else
                        <div class="flex items-center justify-center h-full text-base-content">
                            <x-icon name="camera" class="w-10 h-10 opacity-50" />
                        </div>
                    @endif
                </div>
            </div>
            <input type="file" wire:model="cardImage" id="card" class="hidden" accept="image/*">
        </div>

        <!-- Upload Avatar -->
        <div class="form-control">
            <label class="label">
                <span class="font-semibold label-text">{{ __('collection.avatar_image') }}</span>
            </label>
            <div class="tooltip" data-tip="Click to upload avatar image">
                <div class="w-24 h-24 border rounded-full cursor-pointer border-base-300 hover:border-primary"
                     @if(!$avatarImage) onclick="document.getElementById('avatar').click();" @endif>
                    @if($avatarImage)
                        <img src="{{ $avatarImage->temporaryUrl() }}" class="object-cover w-full h-full rounded-full">
                    @else
                        <div class="flex items-center justify-center h-full text-base-content">
                            <x-icon name="camera" class="w-8 h-8 opacity-50" />
                        </div>
                    @endif
                </div>
            </div>
            <input type="file" wire:model="avatarImage" id="avatar" class="hidden" accept="image/*">
        </div>
    </div>

    <!-- Remove Buttons -->
    <div class="mt-4 flex space-x-4">
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
