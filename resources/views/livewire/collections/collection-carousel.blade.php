<div x-data="{ activeSlide: @entangle('activeSlide') }" class="relative w-full">
    <h3 class="mt-6 text-xl">{{ __('collection.collections') }}</h3>
    <div class="overflow-hidden">
        <div class="flex transition-transform duration-300 ease-in-out"
             :style="{ transform: `translateX(-${activeSlide * 100}%)` }">
            @foreach($collections as $index => $collection)
                <div class="w-full flex-shrink-0">
                    <div class="shadow-xl card bg-base-100 w-96">
                        <figure class="px-10 pt-10">
                            @if($collection->verified_image_card_path && $collection->verified_image_card_path !== '')
                                <div class="object-cover px-10 pt-10 rounded-full">
                                    <img src="{{ Storage::url($collection->verified_image_card_path) }}" class="object-cover w-full h-full rounded-lg" alt="{{ $collection->path_image_card }}">
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
                                <a href="{{ route('collections.edit', ['id' => $collection->id]) }}" class="btn btn-primary">
                                    {{ __('collection.open_collection') }}
                                </a>
                                <button wire:click="delete({{ $collection->id }})" class="btn btn-error">
                                    {{ __('collection.collection_delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button wire:click="prevSlide" class="btn btn-circle absolute left-0 top-1/2 transform -translate-y-1/2">❮</button>
    <button wire:click="nextSlide" class="btn btn-circle absolute right-0 top-1/2 transform -translate-y-1/2">❯</button>

    <div class="flex justify-center mt-4">
        @foreach($collections as $index => $collection)
            <button wire:click="$set('activeSlide', {{ $index }})"
                    class="btn btn-xs mx-1"
                    :class="{ 'btn-active': activeSlide === {{ $index }} }"></button>
        @endforeach
    </div>
</div>
