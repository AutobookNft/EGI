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
    {{-- Wrapper senza margin negativo --}}
    <div class="relative z-20 pt-20 pb-12">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            {{-- Card principale con backdrop blur --}}
            <div class="overflow-hidden shadow-2xl bg-white/95 backdrop-blur-sm rounded-2xl">

                {{-- Header con immagine EGI --}}
                <div class="relative bg-gray-900 h-96">
                    @if($imageUrl = ($egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension ? asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s', $egi->collection_id, $egi->user_id, $egi->key_file, $egi->extension)) : null))
                        <img src="{{ $imageUrl }}"
                             alt="{{ $egi->title ?? 'EGI Image' }}"
                             class="object-contain w-full h-full"
                             loading="eager" />
                    @else
                        <div class="flex items-center justify-center w-full h-full bg-gray-800">
                            <svg class="w-24 h-24 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                        </div>
                    @endif

                    {{-- Overlay gradiente --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                    {{-- Titolo sovrapposto --}}
                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <h1 class="mb-2 text-4xl font-bold text-white">{{ $egi->title }}</h1>
                        <div class="flex items-center text-sm text-white/80">
                            <a href="{{ route('home.collections.show', $collection->id) }}"
                               class="transition hover:text-white">
                                {{ $collection->collection_name }}
                            </a>
                            <span class="mx-2">•</span>
                            <span>by {{ $egi->user->name ?? $collection->creator->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Body con info e azioni --}}
                <div class="grid grid-cols-1 gap-8 p-8 lg:grid-cols-3">

                    {{-- Colonna sinistra: Descrizione e proprietà --}}
                    <div class="space-y-6 lg:col-span-2">
                        {{-- Descrizione --}}
                        <div>
                            <h3 class="mb-3 text-lg font-semibold">Description</h3>
                            <div class="prose-sm prose text-gray-700 max-w-none">
                                {!! nl2br(e($egi->description ?? 'No description provided.')) !!}
                            </div>
                        </div>

                        {{-- Properties --}}
                        <div>
                            <h3 class="mb-3 text-lg font-semibold">Properties</h3>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @if($collection->epp)
                                    <div class="p-3 text-center border border-green-200 rounded-md bg-green-50">
                                        <p class="text-xs uppercase font-medium text-green-600 mb-0.5">Supports EPP</p>
                                        <p class="text-sm font-semibold text-green-900 truncate">
                                            <a href="{{ route('epps.show', $collection->epp->id) }}" class="hover:underline">
                                                {{ Str::limit($collection->epp->name, 20) }}
                                            </a>
                                        </p>
                                    </div>
                                @endif

                                @if($egi->type)
                                    <div class="p-3 text-center border border-blue-200 rounded-md bg-blue-50">
                                        <p class="text-xs uppercase font-medium text-blue-600 mb-0.5">Asset Type</p>
                                        <p class="text-sm font-semibold text-blue-900">{{ ucfirst($egi->type) }}</p>
                                    </div>
                                @endif

                                @if($egi->extension)
                                    <div class="p-3 text-center border border-gray-200 rounded-md bg-gray-50">
                                        <p class="text-xs uppercase font-medium text-gray-500 mb-0.5">Format</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ strtoupper($egi->extension) }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Colonna destra: Prezzo e azioni --}}
                    <div class="lg:col-span-1">
                        <div class="sticky space-y-4 top-24">
                            {{-- Box prezzo --}}
                            <div class="p-6 rounded-lg bg-gray-50">
                                @php
                                    $isForSale = $egi->price && $egi->price > 0 && !$egi->mint;
                                    $canBeReserved = !$egi->mint && ($egi->is_published || (auth()->check() && auth()->id() === $collection->creator_id));
                                @endphp

                                @if($isForSale)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500">Current price</p>
                                        <p class="text-3xl font-bold text-gray-900">
                                            {{ number_format($egi->price, 2) }} <span class="text-lg font-medium text-gray-500">ALGO</span>
                                        </p>
                                    </div>
                                @else
                                    <div class="mb-4">
                                        <p class="text-lg font-semibold text-gray-700">Not currently for sale</p>
                                    </div>
                                @endif

                                {{-- Bottone Like (VERSIONE CORRETTA) --}}
                                <button class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 like-button {{ $egi->is_liked ?? false ? 'is-liked ring-2 ring-pink-500 text-pink-600' : '' }} mb-3"
                                        data-resource-type="egi"
                                        data-resource-id="{{ $egi->id }}">
                                    <svg class="-ml-1 mr-2 h-5 w-5 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-500' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="like-text">{{ $egi->is_liked ?? false ? __('Liked') : __('Like') }}</span>
                                    <span class="ml-1.5 like-count-display">({{ $egi->likes_count ?? 0 }})</span>
                                </button>

                                {{-- Altri bottoni azioni --}}
                                @if($canBeReserved)
                                    <button class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 reserve-button"
                                            data-egi-id="{{ $egi->id }}">
                                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                                        </svg>
                                        Reserve this EGI
                                    </button>
                                @endif
                            </div>

                            {{-- Share buttons --}}
                            <div class="p-4 bg-white border border-gray-200 rounded-lg">
                                <h4 class="mb-3 text-sm font-semibold text-gray-700">Share</h4>
                                <div class="flex space-x-2">
                                    <button class="p-2 bg-gray-100 rounded-full hover:bg-gray-200">
                                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                                        </svg>
                                    </button>
                                    <button class="p-2 bg-gray-100 rounded-full hover:bg-gray-200">
                                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-slot>

</x-guest-layout>
