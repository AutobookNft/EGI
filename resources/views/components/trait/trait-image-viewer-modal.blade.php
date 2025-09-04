{{-- 
    Modal semplice per visualizzare SOLO l'immagine del trait
    Per utenti non proprietari o EGI pubblicati
--}}
@props(['trait'])

<div id="trait-view-modal-{{ $trait->id }}" 
     class="fixed inset-0 z-50 hidden bg-black bg-opacity-75" 
     data-trait-id="{{ $trait->id }}"
     style="align-items: center; justify-content: center;">
    
    <div class="relative max-w-4xl max-h-[90vh] mx-4">
        {{-- Pulsante chiudi --}}
        <button type="button" 
                class="absolute z-10 text-3xl text-white top-4 right-4 hover:text-gray-300 trait-view-modal-close"
                style="background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            &times;
        </button>

        {{-- Immagine del trait --}}
        <div class="text-center">
            @if($trait->getFirstMedia('trait_images'))
                @php
                    $media = $trait->getFirstMedia('trait_images');
                    $fullImageUrl = $media->getUrl();
                @endphp
                <img src="{{ $fullImageUrl }}" 
                     alt="{{ $trait->name }}"
                     class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
            @else
                <div class="p-8 text-center bg-white rounded-lg">
                    <div class="text-gray-500">
                        <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" 
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mb-2 text-xl font-semibold text-gray-700">{{ $trait->name }}</h3>
                        <p class="text-gray-500">{{ __('traits.no_image_available') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Info del trait (opzionale) --}}
        @if($trait->getFirstMedia('trait_images'))
        <div class="mt-4 text-center">
            <div class="inline-block px-4 py-2 bg-black bg-opacity-50 rounded-lg">
                <h3 class="text-lg font-semibold text-white">{{ $trait->name }}</h3>
                @if($trait->category)
                    <p class="text-sm text-gray-300">{{ $trait->category->name }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
