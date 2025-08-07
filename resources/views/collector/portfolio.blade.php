@vite(['resources/css/creator-home.css', 'resources/js/creator-home.js'])

<x-creator-layout>
    <x-slot name="title">{{ $collector->name }} - {{ __('collector.portfolio.title') }}</x-slot>
    <x-slot name="description">{{ __('collector.portfolio.meta_description', ['name' => $collector->name]) }}</x-slot>

    {{-- Schema.org Markup --}}
    <x-slot name="schema">
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "mainEntity": {
                "@type": "Person",
                "@id": "{{ url('/collector/' . $collector->id) }}",
                "name": "{{ $collector->name }}",
                "owns": {
                    "@type": "Collection",
                    "name": "{{ $collector->name }}'s EGI Portfolio",
                    "numberOfItems": {{ $purchasedEgis->total() }}
                }
            }
        }
        </script>
    </x-slot>

    {{-- Header --}}
    <section class="py-12 bg-gradient-to-br from-gray-900 via-blu-algoritmo to-gray-900">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('collector.home', $collector->id) }}"
                    class="text-oro-fiorentino hover:text-oro-fiorentino/80">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white font-playfair">{{ $collector->name }}</h1>
                    <p class="text-oro-fiorentino">{{ __('collector.portfolio.subtitle') }}</p>
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="grid grid-cols-3 gap-6 mt-6 text-center">
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['total_egis'] }}</span>
                    <span class="text-sm text-gray-300">{{ __('collector.portfolio.total_egis') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['total_collections'] }}</span>
                    <span class="text-sm text-gray-300">{{ __('collector.portfolio.collections') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">€{{ $stats['total_spent_eur'] }}</span>
                    <span class="text-sm text-gray-300">{{ __('collector.portfolio.total_value') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Navigation Tabs --}}
    <section class="sticky top-0 z-20 bg-white shadow-md">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <nav class="flex space-x-8" aria-label="{{ __('collector.home.navigation_aria') }}">
                <a href="{{ route('collector.home', $collector->id) }}"
                    class="flex items-center px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    </svg>
                    {{ __('collector.home.overview_tab') }}
                </a>
                <a href="{{ route('collector.portfolio', $collector->id) }}"
                    class="flex items-center px-1 py-4 text-sm font-medium border-b-2 border-blu-algoritmo text-blu-algoritmo"
                    aria-current="page">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ __('collector.home.portfolio_tab') }}
                </a>
                <a href="{{ route('collector.collections', $collector->id) }}"
                    class="flex items-center px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ __('collector.home.collections_tab') }}
                </a>
            </nav>
        </div>
    </section>

    {{-- Portfolio Content --}}
    <div class="min-h-screen bg-gray-800">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Filters & Search --}}
        <div class="mb-8">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 lg:grid-cols-6">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <form method="GET" action="{{ route('collector.portfolio', $collector->id) }}">
                        <div class="relative">
                            <input type="text" name="query" value="{{ $query }}"
                                placeholder="{{ __('collector.portfolio.search_placeholder') }}"
                                class="w-full px-4 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:border-purple-400 focus:ring-purple-400">
                            <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-4">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                        {{-- Hidden fields to preserve other filters --}}
                        <input type="hidden" name="collection" value="{{ $collection_filter }}">
                        <input type="hidden" name="creator" value="{{ $creator_filter }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="view" value="{{ $view }}">
                    </form>
                </div>

                {{-- Collection Filter --}}
                <div>
                    <form method="GET" action="{{ route('collector.portfolio', $collector->id) }}"
                        onchange="this.submit()">
                        <select name="collection"
                            class="w-full px-3 py-2 text-white bg-gray-700 border border-gray-600 rounded-lg focus:border-purple-400 focus:ring-purple-400">
                            <option value="">{{ __('collector.portfolio.all_collections') }}</option>
                            @foreach ($availableCollections as $collection)
                                <option value="{{ $collection->id }}"
                                    {{ $collection_filter == $collection->id ? 'selected' : '' }}>
                                    {{ $collection->collection_name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="query" value="{{ $query }}">
                        <input type="hidden" name="creator" value="{{ $creator_filter }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="view" value="{{ $view }}">
                    </form>
                </div>

                {{-- Creator Filter --}}
                <div>
                    <form method="GET" action="{{ route('collector.portfolio', $collector->id) }}"
                        onchange="this.submit()">
                        <select name="creator"
                            class="w-full px-3 py-2 text-white bg-gray-700 border border-gray-600 rounded-lg focus:border-purple-400 focus:ring-purple-400">
                            <option value="">{{ __('collector.portfolio.all_creators') }}</option>
                            @foreach ($availableCreators as $creator)
                                <option value="{{ $creator->id }}"
                                    {{ $creator_filter == $creator->id ? 'selected' : '' }}>
                                    {{ $creator->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="query" value="{{ $query }}">
                        <input type="hidden" name="collection" value="{{ $collection_filter }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="view" value="{{ $view }}">
                    </form>
                </div>

                {{-- Sort --}}
                <div>
                    <form method="GET" action="{{ route('collector.portfolio', $collector->id) }}"
                        onchange="this.submit()">
                        <select name="sort"
                            class="w-full px-3 py-2 text-white bg-gray-700 border border-gray-600 rounded-lg focus:border-purple-400 focus:ring-purple-400">
                            <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>
                                {{ __('collector.portfolio.sort_latest') }}</option>
                            <option value="title" {{ $sort == 'title' ? 'selected' : '' }}>
                                {{ __('collector.portfolio.sort_title') }}</option>
                            <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>
                                {{ __('collector.portfolio.sort_price_high') }}</option>
                            <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>
                                {{ __('collector.portfolio.sort_price_low') }}</option>
                        </select>
                        <input type="hidden" name="query" value="{{ $query }}">
                        <input type="hidden" name="collection" value="{{ $collection_filter }}">
                        <input type="hidden" name="creator" value="{{ $creator_filter }}">
                        <input type="hidden" name="view" value="{{ $view }}">
                    </form>
                </div>

                {{-- View Toggle --}}
                <div class="flex space-x-1">
                    <a href="{{ route('collector.portfolio', $collector->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'grid'])) }}"
                        class="{{ $view == 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-l-lg border border-gray-600 p-2 hover:bg-purple-500 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </a>
                    <a href="{{ route('collector.portfolio', $collector->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'list'])) }}"
                        class="{{ $view == 'list' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-r-lg border border-gray-600 p-2 hover:bg-purple-500 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- EGI Grid/List --}}
        @if ($purchasedEgis->count() > 0)
            @if ($view == 'grid')
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($purchasedEgis as $egi)
                        <x-egi-card 
                            :egi="$egi" 
                            :collection="$egi->collection"
                            :showPurchasePrice="true"
                        />
                    @endforeach
                </div>
            @else
                {{-- List View --}}
                <div class="space-y-4">
                    @foreach ($purchasedEgis as $egi)
                        <article class="flex overflow-hidden transition-all duration-300 bg-white border border-gray-100 shadow-lg group rounded-2xl hover:shadow-xl hover:border-gray-200">
                            <!-- Image -->
                            <div class="relative flex-shrink-0 w-32 h-32 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                                @if ($egi->main_image_url)
                                    <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                                        class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">
                                @else
                                    <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-purple-100 to-blue-100">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Owned Badge -->
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-500/90 text-white backdrop-blur-sm">
                                        <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Owned
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 p-6">
                                <div class="flex items-start justify-between h-full">
                                    <div class="flex-1 min-w-0">
                                        <!-- Title -->
                                        <h3 class="mb-2 text-xl font-bold text-gray-900 transition-colors duration-200 group-hover:text-purple-600">
                                            <a href="{{ route('egis.show', $egi->id) }}" class="hover:underline">
                                                {{ $egi->title }}
                                            </a>
                                        </h3>

                                        <!-- Collection and Creator -->
                                        <div class="flex flex-wrap items-center gap-4 mb-3">
                                            @if ($egi->collection)
                                                <div class="flex items-center gap-2">
                                                    <div class="flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-r from-purple-500 to-blue-500">
                                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                                        </svg>
                                                    </div>
                                                    <a href="{{ route('home.collections.show', $egi->collection->id) }}"
                                                        class="text-sm font-medium text-gray-600 transition-colors hover:text-purple-600">
                                                        {{ $egi->collection->collection_name }}
                                                    </a>
                                                </div>
                                            @endif

                                            @if ($egi->collection && $egi->collection->creator)
                                                <div class="flex items-center gap-2">
                                                    <div class="flex items-center justify-center w-5 h-5 bg-gray-200 rounded-full">
                                                        <svg class="w-2.5 h-2.5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm text-gray-500">{{ $egi->collection->creator->name }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Purchase Price -->
                                        @if ($egi->pivot && $egi->pivot->offer_amount_eur)
                                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50">
                                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-sm font-medium text-green-600">Purchased for</span>
                                                <span class="text-lg font-bold text-green-700">€{{ number_format($egi->pivot->offer_amount_eur, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- View Button -->
                                    <div class="flex items-center ml-4">
                                        <a href="{{ route('egis.show', $egi->id) }}"
                                            class="inline-flex items-center px-4 py-2 font-medium text-gray-700 transition-all duration-200 bg-gray-100 rounded-full hover:bg-purple-100 hover:text-purple-700 group-hover:bg-purple-100 group-hover:text-purple-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View NFT
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $purchasedEgis->withQueryString()->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="py-12 text-center">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mb-4 text-2xl font-bold text-white">
                    {{ __('collector.portfolio.empty_title') }}
                </h3>
                <p class="mb-6 text-gray-400">
                    {{ __('collector.portfolio.empty_description') }}
                </p>
                <a href="{{ route('home.collections.index') }}"
                    class="px-6 py-3 font-medium text-white transition-colors duration-200 bg-purple-600 rounded-lg hover:bg-purple-700">
                    {{ __('collector.portfolio.discover_button') }}
                </a>
            </div>
        @endif
    </div>
    </div>

</x-creator-layout>
