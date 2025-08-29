{{-- Menu Collections Carousel Component --}}
@props([
    'collections' => []
])

<div class="p-4 border bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl border-purple-200/30 dark:border-purple-800/30 mega-card">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-2">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('collection.my_collections') }}</h4>
        </div>

        {{-- @if(count($collections) > 1)
            <div class="flex space-x-1">
                <button type="button" class="p-1 text-gray-400 transition-colors carousel-prev-menu hover:text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button" class="p-1 text-gray-400 transition-colors carousel-next-menu hover:text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        @endif --}}
    </div>

    <div class="relative menu-collections-carousel">
        <div class="flex gap-3 overflow-x-auto snap-x snap-mandatory scrollbar-hide">
            @forelse($collections as $collection)
                <div class="flex-shrink-0 w-full snap-start collection-slide">

                    <a href="{{ route('home.collections.show', $collection->id) }}"
                       class="block p-3 transition-all duration-200 rounded-lg bg-white/50 dark:bg-black/20 hover:bg-white/70 dark:hover:bg-black/30 hover:scale-105 group">

                        <div class="flex items-center space-x-3">
                            <!-- Collection Image/Icon -->
                            <div class="flex-shrink-0">
                                @if($collection->getFirstMediaUrl('head', 'thumb'))
                                    <img src="{{ $collection->getFirstMediaUrl('head', 'thumb') }}"
                                         alt="{{ $collection->collection_name }}"
                                         class="object-cover w-16 h-16 transition-transform duration-200 rounded-lg group-hover:scale-110">
                                @else
                                    <div class="flex items-center justify-center w-16 h-16 text-white rounded-lg bg-gradient-to-br from-purple-400 to-pink-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>


                            <!-- Collection Info -->
                            <div class="flex-1 min-w-0">
                                <h5 class="font-medium text-gray-900 truncate transition-colors dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                    {{ $collection->collection_name }}
                                </h5>
                                <div class="flex items-center justify-between text-xs font-medium text-gray-800 dark:text-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m3 0H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                                            </svg>
                                            {{ $collection->egis()->count() }} {{ __('label.egis') }}
                                        </span>

                                        @php
                                            $avgPrice = $collection->egis()->whereNotNull('price')->avg('price') ?? 0;
                                            $totalValue = $collection->egis()->whereNotNull('price')->sum('price') ?? 0;
                                            $soldCount = $collection->egis()->whereHas('reservations', function($q) {
                                                $q->where('sub_status', 'highest');
                                            })->count();
                                        @endphp

                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                            @if($avgPrice > 0)
                                                €{{ number_format($avgPrice, 0) }} med
                                            @else
                                                {{ __('collection.no_price') }}
                                            @endif
                                        </span>

                                        @if($soldCount > 0)
                                            <span class="flex items-center text-green-700 dark:text-green-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                      {{--  --}}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($collection->type)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full mb-2 {{
                                        $collection->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                        ($collection->status === 'local' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' :
                                        'bg-gray-200 text-gray-900 dark:bg-gray-600 dark:text-gray-100')
                                    }}">
                                        {{ __('collection.type_' . $collection->type) }}
                                    </span>
                                @endif
                            </div>                            <!-- Arrow indicator -->
                            <div class="flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover:text-purple-600 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="w-full py-4 text-center">
                    <div class="mb-2 text-gray-500 dark:text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        {{ __('collection.no_collections') }}
                    </div>
                    <button type="button"
                            data-action="open-create-collection-modal"
                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-600 transition-colors duration-200 bg-purple-100 rounded-lg hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50"
                            aria-label="{{ __('collection.create_collection') }}">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('collection.create_collection') }}
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

@if(count($collections) > 1)
    @once
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('.menu-collections-carousel');

            carousels.forEach(carousel => {
                const container = carousel.querySelector('.snap-x');
                const prevButton = carousel.parentElement.querySelector('.carousel-prev-menu');
                const nextButton = carousel.parentElement.querySelector('.carousel-next-menu');

                if (!container || !prevButton || !nextButton) return;

                const slides = container.querySelectorAll('.collection-slide');
                let currentSlide = 0;

                function updateSlide() {
                    const slideWidth = slides[0].offsetWidth;
                    container.scrollTo({
                        left: currentSlide * slideWidth,
                        behavior: 'smooth'
                    });
                }

                prevButton.addEventListener('click', () => {
                    currentSlide = currentSlide > 0 ? currentSlide - 1 : slides.length - 1;
                    updateSlide();
                });

                nextButton.addEventListener('click', () => {
                    currentSlide = currentSlide < slides.length - 1 ? currentSlide + 1 : 0;
                    updateSlide();
                });
            });
        });
        </script>
        @endpush
    @endonce
@endif
