<div class="mx-auto max-w-4xl rounded-2xl border border-gray-700 bg-gray-800 p-6 shadow-lg">

    <h2 class="mb-6 text-2xl font-bold text-white">{{ __('collection.manage_head_images') }}</h2>

    <livewire:collections.images.banner-image-upload :collectionId="$collectionId">

    <div class="grid grid-cols-1 gap-6 sm:col-span-2 sm:grid-cols-3">
        <livewire:collections.images.image-upload :collectionId="$collectionId" imageType="card" wire:model="image" />

        <!-- EGI Image -->
        @if (config('app.egi_asset'))
            <livewire:collections.images.image-upload :collectionId="$collectionId" imageType="EGI" wire:model="EGIAssetImmage" />
        @endif

        <!-- Avatar -->
        <livewire:collections.images.avatar-image-upload :collectionId="$collectionId" wire:model="avatarImage" />
    </div>

</div>
