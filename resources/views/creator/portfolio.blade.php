@vite(['resources/css/creator-home.css', 'resources/js/creator-home.js'])

<x-creator-layout>
    <x-slot name="title">{{ $creator->name }} - {{ __('creator.portfolio.title') }}</x-slot>
    <x-slot name="description">{{ __('creator.portfolio.meta_description', ['name' => $creator->name]) }}</x-slot>
    {{-- Schema.org Markup opzionale --}}
    <x-slot name="schema">
        <script type="application/ld+json">
            {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "mainEntity": {
                "@type": "Person",
                "@id": "{{ url('/creator/' . $creator->id) }}",
                "name": "{{ $creator->name }}",
                "owns": {
                    "@type": "Collection",
                    "name": "{{ $creator->name }}'s EGI Portfolio",
                    "numberOfItems": {{ $egis->count() }}
                }
            }
        }
        </script>
    </x-slot>

    {{-- Header --}}
    <section class="py-12 bg-gradient-to-br from-gray-900 via-blu-algoritmo to-gray-900">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('creator.home', $creator->id) }}"
                    class="text-oro-fiorentino hover:text-oro-fiorentino/80">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white font-playfair">{{ $creator->name }}</h1>
                    <p class="text-oro-fiorentino">{{ __('creator.portfolio.subtitle') }}</p>
                </div>
            </div>

            {{-- Stats Bar - Enhanced per Creator Portfolio --}}
            <div class="grid grid-cols-2 gap-6 mt-6 text-center md:grid-cols-4">
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['total_egis'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.total_egis') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['total_collections'] ?? 0
                        }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.total_collections') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['reserved_egis'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.reserved_egis') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">€{{ number_format($stats['highest_offer']
                        ?? 0, 0, ',', '.') }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.highest_offer') }}</span>
                </div>
            </div>

            {{-- Secondary Stats Row --}}
            <div class="grid grid-cols-2 gap-6 mt-4 text-center">
                <div>
                    <span class="block text-lg font-semibold text-white">{{ $stats['available_egis'] ?? 0 }}</span>
                    <span class="text-xs text-gray-400">{{ __('creator.portfolio.available_egis') }}</span>
                </div>
                <div>
                    <span class="block text-lg font-semibold text-white">€{{ number_format($stats['total_value_eur'] ??
                        0, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-400">{{ __('creator.portfolio.total_value') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- EGI Grid/List --}}
    <div class="min-h-screen bg-gray-800">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if ($egis->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($egis as $egi)
                <x-egi-card :egi="$egi" :collection="$egi->collection" :portfolioContext="true"
                    :portfolioOwner="$creator" :creatorPortfolioContext="true" :hideReserveButton="false" />
                @endforeach
            </div>
            @else
            <div class="py-12 text-center">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mb-4 text-2xl font-bold text-white">
                    {{ __('creator.portfolio.empty_title') }}
                </h3>
                <p class="mb-6 text-gray-400">
                    {{ __('creator.portfolio.empty_description') }}
                </p>
                <a href="{{ route('home.collections.index') }}"
                    class="px-6 py-3 font-medium text-white transition-colors duration-200 bg-purple-600 rounded-lg hover:bg-purple-700">
                    {{ __('creator.portfolio.discover_button') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</x-creator-layout>
