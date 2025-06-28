{{-- resources/views/egis/show.blade.php --}}
<x-guest-layout
    :title="$egi->title . ' | ' . $collection->collection_name"
    :metaDescription="Str::limit($egi->description, 155) ?? 'Details for EGI: ' . $egi->title">

{{-- Schema.org nel head --}}
<x-slot name="schemaMarkup">
    @php
    $egiImageUrl = $egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension
        ? asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s',
            $egi->collection_id, $egi->user_id, $egi->key_file, $egi->extension))
        : asset('images/default_egi_placeholder.jpg');
    @endphp
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VisualArtwork",
        "name": "{{ $egi->title }}",
        "description": "{{ $egi->description }}",
        "image": "{{ $egiImageUrl }}",
        "isPartOf": {
            "@type": "CollectionPage",
            "name": "{{ $collection->collection_name }}",
            "url": "{{ route('home.collections.show', $collection->id) }}"
        },
        "author": {
            "@type": "Person",
            "name": "{{ $egi->user->name ?? $collection->creator->name ?? 'Unknown Creator' }}"
        }
    }
    </script>
</x-slot>

{{-- Slot personalizzato per disabilitare la hero section --}}
<x-slot name="noHero">true</x-slot>

{{-- Contenuto principale --}}
<x-slot name="slot">
    {{-- Gallery Layout - Cinema Style --}}
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">

        {{-- Cinematic Artwork Display --}}
        <div class="relative w-full">

            {{-- Main Gallery Grid --}}
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-12">

                {{-- Left: Dominant Artwork Area --}}
                <div class="relative flex items-center justify-center p-4 lg:col-span-8 xl:col-span-9 lg:p-8">

                    {{-- Artwork Container --}}
                    <div class="relative w-full max-w-6xl">

                        {{-- Main Image Display --}}
                        <div class="relative cursor-pointer group artwork-container">
                            @if($imageUrl = ($egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension ? asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s', $egi->collection_id, $egi->user_id, $egi->key_file, $egi->extension)) : null))
                                <div class="relative overflow-hidden bg-black shadow-2xl rounded-2xl">
                                    <img src="{{ $imageUrl }}"
                                         alt="{{ $egi->title ?? 'EGI Artwork' }}"
                                         class="w-full h-auto max-h-[85vh] object-contain mx-auto transition-all duration-700 group-hover:scale-[1.02] group-hover:brightness-110"
                                         loading="eager" />

                                    {{-- Subtle Overlay for Interaction Hints --}}
                                    <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-t from-black/20 via-transparent to-black/10 group-hover:opacity-100"></div>

                                    {{-- Zoom Hint --}}
                                    <div class="absolute px-3 py-1 text-sm text-white transition-all duration-300 rounded-full opacity-0 top-4 right-4 bg-black/70 group-hover:opacity-100 backdrop-blur-sm">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                        View Full
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-full h-96 lg:h-[70vh] bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-2xl">
                                    <div class="text-center">
                                        <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                        </svg>
                                        <p class="text-lg text-gray-400">Artwork Loading...</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Floating Title Card - Elegantly Positioned --}}
                        <div class="absolute bottom-6 left-6 right-6 lg:bottom-8 lg:left-8 lg:right-8">
                            <div class="p-6 border shadow-2xl bg-black/80 backdrop-blur-xl rounded-xl border-white/10">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h1 class="mb-2 text-2xl font-bold tracking-tight text-white lg:text-4xl">{{ $egi->title }}</h1>
                                        <div class="flex items-center space-x-3 text-sm text-gray-300">
                                            <a href="{{ route('home.collections.show', $collection->id) }}"
                                               class="font-medium transition-colors duration-200 hover:text-white">
                                                {{ $collection->collection_name }}
                                            </a>
                                            <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                                            <span>by {{ $egi->user->name ?? $collection->creator->name ?? 'Unknown' }}</span>
                                        </div>
                                    </div>

                                    {{-- Quick Actions in Title Area --}}
                                    <div class="flex items-center ml-4 space-x-2">
                                        {{-- Like Button - Compact Version --}}
                                        <button class="p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full transition-all duration-200 border border-white/20 like-button {{ $egi->is_liked ?? false ? 'is-liked bg-pink-500/20 border-pink-400/50' : '' }}"
                                                data-resource-type="egi"
                                                data-resource-id="{{ $egi->id }}">
                                            <svg class="w-5 h-5 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-400' : 'text-white' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        {{-- Share Button --}}
                                        <button class="p-3 transition-all duration-200 border rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border-white/20">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Elegant Sidebar --}}
                <div class="overflow-y-auto border-l lg:col-span-4 xl:col-span-3 bg-gray-900/95 backdrop-blur-xl border-gray-700/50">

                    {{-- Sidebar Content --}}
                    <div class="sticky top-0 p-6 space-y-8 lg:p-8">

                        {{-- Price & Purchase Section --}}
                        @php
                            $isForSale = $egi->price && $egi->price > 0 && !$egi->mint;
                            $canBeReserved = !$egi->mint && ($egi->is_published || (auth()->check() && auth()->id() === $collection->creator_id));
                        @endphp

                        <div class="p-6 border bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-xl border-gray-700/30">
                            @if($isForSale)
                                <div class="mb-6 text-center">
                                    <p class="mb-2 text-sm text-gray-400">Current Price</p>
                                    <div class="flex items-baseline justify-center">
                                        <span class="text-4xl font-bold text-white">{{ number_format($egi->price, 2) }}</span>
                                        <span class="ml-2 text-lg font-medium text-gray-400">ALGO</span>
                                    </div>
                                </div>
                            @else
                                <div class="mb-6 text-center">
                                    <p class="text-lg font-semibold text-gray-300">Not Currently Listed</p>
                                    <p class="mt-1 text-sm text-gray-500">Contact owner for availability</p>
                                </div>
                            @endif

                            {{-- Main Action Buttons --}}
                            <div class="space-y-3">
                                {{-- Like Button - Full Version --}}
                                <button class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-pink-600/80 to-purple-600/80 hover:from-pink-600 hover:to-purple-600 backdrop-blur-sm text-white font-medium rounded-lg transition-all duration-200 border border-pink-500/30 hover:border-pink-400/50 like-button {{ $egi->is_liked ?? false ? 'is-liked ring-2 ring-pink-400/50' : '' }}"
                                        data-resource-type="egi"
                                        data-resource-id="{{ $egi->id }}">
                                    <svg class="-ml-1 mr-3 h-5 w-5 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-300' : 'text-white' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="like-text">{{ $egi->is_liked ?? false ? __('Liked') : __('Add to Favorites') }}</span>
                                    <span class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs like-count-display">{{ $egi->likes_count ?? 0 }}</span>
                                </button>

                                {{-- Reserve Button --}}
                                @if($canBeReserved)
                                    <button class="inline-flex items-center justify-center w-full px-6 py-4 font-medium text-white transition-all duration-200 border rounded-lg bg-gradient-to-r from-emerald-600/80 to-teal-600/80 hover:from-emerald-600 hover:to-teal-600 backdrop-blur-sm border-emerald-500/30 hover:border-emerald-400/50 reserve-button"
                                            data-egi-id="{{ $egi->id }}">
                                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                                        </svg>
                                        Reserve This Piece
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Properties Section --}}
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-white">Properties</h3>
                            <div class="grid grid-cols-1 gap-3">
                                @if($collection->epp)
                                    <div class="p-4 border rounded-lg bg-emerald-500/10 border-emerald-400/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="mb-1 text-xs font-medium uppercase text-emerald-400">Supports EPP</p>
                                                <a href="{{ route('epps.show', $collection->epp->id) }}" class="text-sm font-semibold transition-colors text-emerald-300 hover:text-emerald-200">
                                                    {{ Str::limit($collection->epp->name, 25) }}
                                                </a>
                                            </div>
                                            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                        </div>
                                    </div>
                                @endif

                                @if($egi->type)
                                    <div class="p-4 border rounded-lg bg-blue-500/10 border-blue-400/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="mb-1 text-xs font-medium text-blue-400 uppercase">Asset Type</p>
                                                <p class="text-sm font-semibold text-blue-300">{{ ucfirst($egi->type) }}</p>
                                            </div>
                                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                        </div>
                                    </div>
                                @endif

                                @if($egi->extension)
                                    <div class="p-4 border rounded-lg bg-purple-500/10 border-purple-400/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="mb-1 text-xs font-medium text-purple-400 uppercase">Format</p>
                                                <p class="text-sm font-semibold text-purple-300">{{ strtoupper($egi->extension) }}</p>
                                            </div>
                                            <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Description Section --}}
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-white">About This Piece</h3>
                            <div class="leading-relaxed prose-sm prose text-gray-300 prose-invert max-w-none">
                                {!! nl2br(e($egi->description ?? 'This unique digital artwork represents a moment of creative expression, capturing the essence of digital artistry in the blockchain era.')) !!}
                            </div>
                        </div>

                        {{-- Reservation History --}}
                        @if($egi->reservationCertificates)
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-white">Provenance</h3>
                                <x-egi-reservation-history :egi="$egi" :certificates="$egi->reservationCertificates" />
                            </div>
                        @endif

                        {{-- Collection Link --}}
                        <div class="pt-6 border-t border-gray-700/50">
                            <a href="{{ route('home.collections.show', $collection->id) }}"
                               class="inline-flex items-center text-gray-300 transition-colors duration-200 hover:text-white group">
                                <svg class="w-4 h-4 mr-2 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                View Full Collection
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom Styles for Enhanced Interactivity --}}
    <style>
        .artwork-container:hover img {
            transform: scale(1.01);
            filter: brightness(1.05) contrast(1.02);
        }

        .like-button.is-liked {
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.3) 0%, rgba(147, 51, 234, 0.3) 100%);
            border-color: rgba(236, 72, 153, 0.5);
        }

        @media (max-width: 1024px) {
            .artwork-container {
                margin-bottom: 2rem;
            }
        }

        /* Scrollbar styling for sidebar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: rgba(55, 65, 81, 0.3);
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.8);
        }
    </style>
</x-slot>

</x-guest-layout>
