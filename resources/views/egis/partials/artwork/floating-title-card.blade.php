{{-- resources/views/egis/partials/artwork/floating-title-card.blade.php --}}
{{-- 
    Card floating con titolo e azioni rapide
    ORIGINE: righe 124-175 di show.blade.php (Floating Title Card)
    VARIABILI: $egi, $collection, $isCreator
--}}

{{-- Floating Title Card - Elegantly Positioned --}}
<div class="absolute bottom-3 left-3 right-3 lg:bottom-6 lg:left-6 lg:right-6">
    <div class="p-3 border rounded-lg shadow-2xl lg:p-4 bg-black/10 border-white/5">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1
                    class="mb-1 text-lg font-bold tracking-tight text-white lg:text-2xl xl:text-3xl drop-shadow-2xl">
                    {{ $egi->title }}</h1>
                <div
                    class="flex items-center space-x-2 text-xs text-gray-100 lg:text-sm drop-shadow-lg">
                    <a href="{{ route('home.collections.show', $collection->id) }}"
                        class="font-medium transition-colors duration-200 hover:text-white">
                        {{ $collection->collection_name }}
                    </a>
                    <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                    <span>{{ __('egi.by_author', ['name' => $egi->user->name ??
                        $collection->creator->name ?? __('egi.unknown_creator')]) }}</span>
                </div>
            </div>

            {{-- Quick Actions in Title Area --}}
            <div class="flex items-center ml-2 space-x-1 lg:ml-4 lg:space-x-2">
                {{-- Like Button - Compact Version --}}
                @if(!$isCreator)
                <button
                    class="p-2 lg:p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full transition-all duration-200 border border-white/20 like-button {{ $egi->is_liked ?? false ? 'is-liked bg-pink-500/20 border-pink-400/50' : '' }}"
                    data-resource-type="egi" data-resource-id="{{ $egi->id }}"
                    title="{{ __('egi.like_button_title') }}">
                    <svg class="w-4 h-4 lg:w-5 lg:h-5 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-400' : 'text-white' }}"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                @endif

                {{-- Share Button --}}
                <button
                    class="p-2 transition-all duration-200 border rounded-full lg:p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm border-white/20"
                    title="{{ __('egi.share_button_title') }}">
                    <svg class="w-4 h-4 text-white lg:w-5 lg:h-5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
