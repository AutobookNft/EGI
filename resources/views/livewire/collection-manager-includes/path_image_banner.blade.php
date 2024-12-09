<div class="text-center">
    <label for="banner" class="block mb-2 text-sm font-semibold text-gray-700">Banner Image</label>
    <div class="relative inline-block cursor-pointer" onclick="document.getElementById('banner').click();">
        <div class="flex items-center justify-center transition-transform transform rounded-lg shadow-lg avatar w-28 h-28 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 hover:scale-105">
            @if ($path_image_banner)
                <img src="{{ $path_image_banner->temporaryUrl() }}" class="object-cover w-full h-full border-4 border-white rounded">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white rounded opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: block; margin: auto;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            @endif
        </div>
    </div>

    <input type="file" wire:model="path_image_banner" id="banner" class="hidden" accept="image/*">
    @error('path_image_banner')
        <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
    @enderror

    {{-- Action Button --}}
    @if ($path_image_banner)
        <div class="flex justify-center mt-3">
            <button type="button" wire:click="removeImage('banner')" class="px-4 py-1 text-white transition-all duration-200 transform bg-red-500 border-none rounded-lg shadow-lg btn hover:scale-110 hover:shadow-2xl">
                Remove
            </button>
        </div>
    @endif
</div>
