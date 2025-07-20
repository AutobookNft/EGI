@vite(['resources/css/creator-home.css', 'resources/js/creator-home.js'])

<x-creator-layout :title="$creator->name . ' - ' . __('creator.home.title_suffix')" :metaDescription="__('creator.home.meta_description', ['name' => $creator->name])">


    {{-- Schema.org Markup --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ProfilePage",
            "mainEntity": {
                "@type": "Person",
                "@id": "{{ url('/creator/' . $creator->id) }}",
                "name": "{{ $creator->name }}",
                "url": "{{ url('/creator/' . $creator->id) }}",
                "description": "{{ $creator->bio ?? __('creator.home.default_bio') }}",
                "image": "{{ $creator->profile_photo_url }}",
                @if($creator->social_links)
                "sameAs": {!! json_encode(json_decode($creator->social_links, true)) !!},
                @endif
                "makesOffer": {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "CreativeWork",
                        "name": "{{ __('creator.home.creative_work_type') }}"
                    }
                }
            },
            "breadcrumb": {
                "@type": "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "{{ __('breadcrumb.home') }}",
                        "item": "{{ url('/') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "{{ __('breadcrumb.creators') }}",
                        "item": "{{ url('/creators') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "{{ $creator->name }}"
                    }
                ]
            }
        }
        </script>
    </x-slot>

    {{-- Hero Section --}}
    <section class="relative min-h-[60vh] overflow-hidden bg-gradient-to-br from-gray-900 via-blu-algoritmo to-gray-900"
        role="banner" aria-label="{{ __('creator.home.hero_aria_label', ['name' => $creator->name]) }}">

        {{-- Renaissance Pattern --}}
        <div class="absolute inset-0 opacity-50" aria-hidden="true">
            {{-- Padmin: Stile corretto per un'immagine di sfondo a copertura totale --}}
            <div class="absolute inset-0"
                style="background-image: url('/images/default/random_background/7.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            </div>
        </div>

        {{-- Hero Content --}}
        <div class="relative z-10 px-4 py-16 mx-auto max-w-7xl sm:px-6 md:py-24 lg:px-8">
            {{-- Padmin: Aumentato il gap verticale su mobile per dare più respiro tra il blocco profilo e quello delle azioni/statistiche --}}
            <div class="grid items-end grid-cols-1 gap-12 md:grid-cols-12 md:gap-8">

                {{-- Profile Section --}}
                {{-- Padmin: Aumentato il gap e aggiunto `items-center` su mobile per un migliore allineamento verticale --}}
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end sm:gap-8 md:col-span-8">
                    {{-- Avatar --}}
                    {{-- Padmin: Aggiunto flex-shrink-0 per evitare che l'avatar si restringa su schermi stretti --}}
                    <div class="relative flex-shrink-0 group">
                        <div
                            class="w-32 h-32 overflow-hidden rounded-full shadow-2xl ring-oro-fiorentino/30 ring-4 md:h-40 md:w-40">
                            <img src="{{ $creator->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($creator->name) . '&size=160&background=D4A574&color=2D5016' }}"
                                alt="{{ __('creator.home.avatar_alt', ['name' => $creator->name]) }}"
                                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
                                loading="lazy">
                        </div>
                        @if ($creator->is_verified)
                            <div class="absolute p-2 rounded-full shadow-lg -bottom-2 -right-2 bg-verde-rinascita"
                                title="{{ __('creator.home.verified_badge_title') }}">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">{{ __('creator.home.verified_sr') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Creator Info --}}
                    {{-- Padmin: Aggiunto un wrapper per dare più struttura e controllo all'allineamento. --}}
                    <div class="flex flex-col text-center sm:text-left">
                        <h1 class="mb-1 text-3xl font-bold text-white font-playfair md:text-5xl">
                            {{ $creator->name }}
                        </h1>
                        @if ($creator->tagline)
                            <p class="text-lg italic text-oro-fiorentino font-source-sans md:text-xl">
                                "{{ $creator->tagline }}"
                            </p>
                        @endif
                        {{-- Padmin: Separato in un div per un miglior controllo del layout e aggiunto un margine superiore. --}}
                        <div class="mt-3">
                            <p class="text-gray-400">
                                {{ $creator->name }} {{ $creator->last_name ? $creator->last_name : '' }} &middot;
                                {{ __('creator.home.member_since', ['year' => $creator->created_at->format('Y')]) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions & Stats --}}
                {{-- Padmin: Aggiunto un margine superiore su mobile (mt-8) che scompare su schermi medi (md:mt-0) quando il layout cambia. --}}
                <div class="flex flex-col items-center gap-6 md:col-span-4 md:items-end">
                    {{-- CTA Buttons --}}
                    <div class="flex gap-3">
                        @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() !== $creator->id)
                            <button type="button"
                                class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 rounded-full px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:shadow-xl"
                                aria-label="{{ __('creator.home.follow_aria', ['name' => $creator->name]) }}">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{ __('creator.home.follow_button') }}
                                </span>
                            </button>
                            <button type="button"
                                class="rounded-full bg-verde-rinascita px-6 py-2.5 font-semibold text-white shadow-lg transition-all duration-300 hover:bg-verde-rinascita/90 hover:shadow-xl"
                                aria-label="{{ __('creator.home.become_patron_aria', ['name' => $creator->name]) }}">
                                {{ __('creator.home.become_patron_button') }}
                            </button>
                        @elseif(\App\Helpers\FegiAuth::guest())
                            <button type="button" onclick="window.location.href='{{ route('login') }}'"
                                class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 rounded-full px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:shadow-xl">
                                {{ __('creator.home.login_to_follow') }}
                            </button>
                        @endif
                    </div>

                    {{-- Quick Stats --}}
                    {{-- Padmin: Aumentato leggermente il gap per una migliore leggibilità --}}
                    <div class="flex gap-8 text-center">
                        <div>
                            <p class="text-2xl font-bold text-white"
                                @if ($stats['animate'] ?? false) data-stat-value="{{ $stats['total_egis'] }}" @endif>
                                {{ number_format($stats['total_egis']) }}
                            </p>
                            <p class="text-sm tracking-wider text-gray-400 uppercase">
                                {{ __('creator.home.stats.works') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white"
                                @if ($stats['animate'] ?? false) data-stat-value="{{ $stats['total_collections'] }}" @endif>
                                {{ number_format($stats['total_collections']) }}
                            </p>
                            <p class="text-sm tracking-wider text-gray-400 uppercase">
                                {{ __('creator.home.stats.collections') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white"
                                @if ($stats['animate'] ?? false) data-stat-value="{{ $stats['total_supporters'] }}" data-stat-type="supporters" @endif>
                                {{ number_format($stats['total_supporters']) }}
                            </p>
                            <p class="text-sm tracking-wider text-gray-400 uppercase">
                                {{ __('creator.home.stats.patrons') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Navigation Tabs --}}
    <nav class="sticky top-0 z-40 border-b border-gray-800 bg-gray-900/95 backdrop-blur-md" role="navigation"
        aria-label="{{ __('creator.home.nav_aria_label') }}">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex overflow-x-auto scrollbar-hide">
                <a href="{{ route('creator.home', $creator->id) }}"
                    class="px-6 py-4 text-sm font-medium border-b-2 text-oro-fiorentino border-oro-fiorentino whitespace-nowrap"
                    aria-current="page">
                    {{ __('creator.home.nav.overview') }}
                </a>
                <a href="{{ route('creator.portfolio', $creator->id) }}"
                    class="px-6 py-4 text-sm font-medium text-gray-300 border-b-2 border-transparent whitespace-nowrap hover:border-gray-600 hover:text-white">
                    {{ __('creator.home.nav.portfolio') }}
                </a>
                <a href="{{ route('creator.collections', $creator->id) }}"
                    class="px-6 py-4 text-sm font-medium text-gray-300 border-b-2 border-transparent whitespace-nowrap hover:border-gray-600 hover:text-white">
                    {{ __('creator.home.nav.collections') }}
                </a>
                <a href="{{ route('biography.show', $creator) }}"
                    class="px-6 py-4 text-sm font-medium text-gray-300 border-b-2 border-transparent whitespace-nowrap hover:border-gray-600 hover:text-white">
                    {{ __('creator.home.nav.biography') }}
                </a>
                <a href="{{ route('creator.impact', $creator->id) }}"
                    class="px-6 py-4 text-sm font-medium text-gray-300 border-b-2 border-transparent whitespace-nowrap hover:border-gray-600 hover:text-white">
                    {{ __('creator.home.nav.impact') }}
                </a>
                <a href="{{ route('creator.community', $creator->id) }}"
                    class="px-6 py-4 text-sm font-medium text-gray-300 border-b-2 border-transparent whitespace-nowrap hover:border-gray-600 hover:text-white">
                    {{ __('creator.home.nav.community') }}
                </a>
            </div>
        </div>
    </nav>

    {{-- Main Content Area --}}
    <main class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            {{-- Featured Works Section --}}
            <section class="lg:col-span-2" aria-labelledby="featured-works-heading">
                <h2 id="featured-works-heading" class="mb-6 text-2xl font-bold text-white font-playfair">
                    {{ __('creator.home.featured_works_heading') }}
                </h2>

                @if ($featuredEgis->isNotEmpty())
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @foreach ($featuredEgis as $egi)
                            <x-egi-card :egi="$egi" />
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center bg-gray-800 rounded-xl">
                        <p class="text-gray-400">{{ __('creator.home.no_works_yet') }}</p>
                    </div>
                @endif
            </section>

            {{-- Sidebar --}}
            <aside class="space-y-8" aria-label="{{ __('creator.home.sidebar_aria_label') }}">
                {{-- Impact Score Widget --}}
                <div
                    class="p-6 border rounded-xl border-verde-rinascita/30 bg-gradient-to-br from-verde-rinascita/20 to-verde-rinascita/10">
                    <h3 class="mb-4 text-lg font-bold text-white">{{ __('creator.home.environmental_impact') }}</h3>
                    <div class="text-3xl font-bold text-verde-rinascita">
                        {{ number_format($stats['impact_score']) }} {{ __('creator.home.impact_points') }}
                    </div>
                    <p class="mt-2 text-sm text-gray-300">
                        {{ __('creator.home.epp_contribution') }}
                    </p>
                </div>

                {{-- Recent Collections Widget --}}
                <div>
                    <h3 class="mb-4 text-lg font-bold text-white">{{ __('creator.home.recent_collections') }}</h3>
                    @if ($creator->collections->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($creator->collections->take(3) as $collection)
                                <a href="{{ route('creator.collection.show', [$creator->id, $collection->id]) }}"
                                    class="block p-4 transition-colors bg-gray-800 rounded-lg hover:bg-gray-700">
                                    <h4 class="font-medium text-white">{{ $collection->collection_name }}</h4>
                                    <p class="text-sm text-gray-400">
                                        {{ __('creator.home.collection_works_count', ['count' => $collection->egis_count ?? 0]) }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('creator.collections', $creator->id) }}"
                            class="inline-flex items-center mt-4 text-sm text-oro-fiorentino hover:text-oro-fiorentino/80">
                            {{ __('creator.home.view_all_collections') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <p class="text-gray-400">{{ __('creator.home.no_collections_yet') }}</p>
                    @endif
                </div>
            </aside>
        </div>
    </main>

</x-creator-layout>
