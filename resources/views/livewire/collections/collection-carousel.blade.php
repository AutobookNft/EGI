<div x-data="{ activeSlide: @entangle('activeSlide') }" class="relative w-full overflow-hidden">
    <h3 class="mt-6 text-xl">{{ __('collection.collections') }}</h3>

    <!-- Contenitore del carousel con larghezza fissa per le card -->
    <div class="relative">
        <div class="flex transition-transform duration-500 ease-in-out"
             :style="{ transform: `translateX(-${activeSlide * 25}%)` }">

            @foreach($collections as $index => $collection)
            <div class="w-full sm:max-w-[350px] px-2 flex-shrink-0">
                <x-collection-card :id="$collection->id" :editable="false" imageType="card" />

                </div>
            @endforeach

        </div>

        <!-- Pulsante per Slide Precedente -->
        <button wire:click="prevSlide"
                class="btn btn-circle absolute left-4 top-1/2 transform -translate-y-1/2 z-50">
            ❮
        </button>

        <!-- Pulsante per Slide Successiva -->
        <button wire:click="nextSlide"
                class="btn btn-circle absolute right-4 top-1/2 transform -translate-y-1/2 z-50">
            ❯
        </button>
    </div>

    <!-- Indicatori del Carousel -->
    <div class="flex justify-center mt-4">
        @foreach($collections as $index => $collection)
            <button wire:click="$set('activeSlide', {{ $index }})"
                    class="btn btn-xs mx-1"
                    :class="{ 'btn-active': activeSlide === {{ $index }} }">
            </button>
        @endforeach
    </div>
</div>

