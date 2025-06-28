{{-- resources/views/collections/show.blade.php --}}
{{-- 📜 Oracode View: Collection Detail Page --}}
{{-- Displays the detailed view of a single EGI Collection, including its metadata, --}}
{{-- associated EGIs, EPP information, and creator details. --}}
{{-- Expects $collection (loaded with creator, epp, egis) and optionally $relatedCollections. --}}
{{-- Uses Tailwind CSS for styling and layout. --}}

<x-collection-layout :title="$collection->collection_name . ' | FlorenceEGI'"
    :metaDescription="Str::limit($collection->description, 155) ?? 'Details for the collection ' . $collection->collection_name">

{{-- Inserisci qui lo Schema.org specifico per questa pagina --}}
<x-slot name="schemaMarkup">
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemPage", // O CollectionPage
        "name": "{{ $collection->collection_name }}",
        "description": "{{ $collection->description }}",
        "image": "{{ $collection->image_banner ? Storage::url($collection->image_banner) : asset('images/default_banner.jpg') }}",
        "author": {
        "@type": "Person",
        "name": "{{ $collection->creator->name ?? 'Unknown Creator' }}"
        },
        "mainEntity": {
            "@type": "CreativeWork",
            "name": "{{ $collection->collection_name }}"
        }
    }
</script>
</x-slot>

{{-- 🏛️ Contenitore Principale --}}
<div class="collection-detail-container">

{{-- 🖼️ Header della Collezione con Banner --}}
{{-- @style: Relative positioning, background image, overlay gradient, text color. --}}
<header class="relative bg-gray-700 text-white pt-24 pb-12 md:pt-32 md:pb-16 lg:pt-40 lg:pb-20">
{{-- Immagine Banner --}}
{{-- @style: Absolute positioning, full cover, background properties. --}}

@if($collection->image_banner)
    <div class="absolute inset-0 z-0">
        <img src="{{ $collection->image_banner }}" alt="Banner for {{ $collection->collection_name }}" class="w-full h-full object-cover">
        {{-- Overlay Gradiente per leggibilità testo --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
    </div>
@else
    {{-- Fallback se non c'è banner (es. colore solido scuro) --}}
    <div class="absolute inset-0 z-0 bg-gray-800"></div>
@endif

{{-- Contenuto Header (sopra overlay) --}}
<div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Link Indietro --}}
    {{-- @style: Inline flex, vertical alignment, hover effect. --}}
    <div class="mb-6">
        <a href="{{ route('home.collections.index') }}" class="inline-flex items-center text-sm text-gray-300 hover:text-white transition duration-150 ease-in-out">
            {{-- @accessibility-trait: Icon is decorative --}}
            <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            {{ __('Back to Collections') }}
        </a>
    </div>

    {{-- Info Principali: Avatar, Nome, Creator --}}
    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 mb-8">
        {{-- Avatar Collezione --}}
        {{-- @style: Size, rounded, border, shadow. --}}
        <div class="flex-shrink-0 w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg bg-gray-300 flex items-center justify-center text-gray-500 overflow-hidden">

            @if($collection->image_avatar)
                <img src="{{ $collection->image_avatar }}" alt="{{ $collection->collection_name }}" class="w-full h-full object-cover">
            @else
                {{-- Placeholder Icona --}}
                <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm16.5-1.5H3.75" />
                </svg>
            @endif
        </div>

        {{-- Nome Collection e Creator --}}
        <div class="text-center sm:text-left">
            {{-- 🎯 Nome Collezione --}}
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight break-words">
                {{ $collection->collection_name }}
            </h1>
            {{-- 👤 Info Creator --}}
            <div class="mt-2 flex items-center justify-center sm:justify-start text-sm text-gray-200">
                <span>{{ __('By') }}</span>
                <div class="ml-1.5 flex items-center">
                    {{-- Avatar Creator --}}
                    @if ($collection->creator && $collection->creator->profile_photo_url)
                        <img src="{{ $collection->creator->profile_photo_url }}" alt="{{ $collection->creator->name }}" class="w-5 h-5 rounded-full mr-1.5 object-cover">
                    @else
                        {{-- Placeholder --}}
                        <span class="inline-block h-5 w-5 rounded-full overflow-hidden bg-gray-400 mr-1.5">
                            <svg class="h-full w-full text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </span>
                    @endif
                    {{-- Nome Creator (Link opzionale) --}}
                    <span class="font-medium hover:text-white transition duration-150 ease-in-out">
                        {{-- <a href="{{ route('users.show', $collection->creator_id) }}"> --}}
                            {{ $collection->creator->name ?? __('Unknown Creator') }}
                        {{-- </a> --}}
                    </span>
                    {{-- 💎 Badge Verificato --}}
                    @if ($collection->creator && $collection->creator->usertype === 'verified') {{-- Logica verifica --}}
                        <svg class="ml-1 w-4 h-4 text-blue-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" title="{{ __('Verified Creator') }}">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.06 0l4-5.5Z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 Statistiche Principali --}}
    {{-- @style: Flex layout, gap, padding, background semi-trasparente, bordi arrotondati. --}}
    <div class="bg-black/30 backdrop-blur-sm rounded-lg p-4 flex flex-wrap justify-center sm:justify-start gap-x-6 gap-y-3 text-sm">
        <div class="text-center sm:text-left">
            <div class="font-semibold text-lg">{{ $collection->EGI_number ?? $collection->egis_count ?? 0 }}</div>
            <div class="text-xs uppercase text-gray-300 tracking-wider">{{ __('Items') }}</div>
        </div>
        <div class="text-center sm:text-left">
            <div class="font-semibold text-lg">{{ $collection->likes_count ?? 0 }}</div>
            <div class="text-xs uppercase text-gray-300 tracking-wider">{{ __('Likes') }}</div>
        </div>
        <div class="text-center sm:text-left">
            <div class="font-semibold text-lg">{{ $collection->reservations_count ?? 0 }}</div>
            <div class="text-xs uppercase text-gray-300 tracking-wider">{{ __('Reservations') }}</div>
        </div>
        @if($collection->floor_price && $collection->floor_price > 0)
            <div class="text-center sm:text-left">
                <div class="font-semibold text-lg">{{ number_format($collection->floor_price, 2) }} <span class="text-xs text-gray-300">ALGO</span></div>
                <div class="text-xs uppercase text-gray-300 tracking-wider">{{ __('Floor Price') }}</div>
            </div>
        @endif
    </div>
</div> {{-- Fine Contenuto Header --}}
</header>

{{-- 🧩 Corpo Principale (Sidebar + Contenuto EGI) --}}
{{-- @style: Layout a griglia, gap. --}}
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

{{-- 📌 Sidebar (Informazioni Aggiuntive) --}}
{{-- @style: Colonna laterale su schermi grandi. --}}
<aside class="lg:col-span-4 xl:col-span-3 space-y-6">

    {{-- 📄 Card Descrizione --}}
    <div class="bg-white p-5 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">{{ __('About this Collection') }}</h3>
        {{-- @style: Prosa per formattazione testo, gestione link. --}}
        <div class="prose prose-sm text-gray-700 max-w-none">
            {!! nl2br(e($collection->description ?? __('No description available.'))) !!}
        </div>
        {{-- Metadati (Data Creazione, Tipo) --}}
        <div class="mt-5 border-t border-gray-200 pt-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">{{ __('Created') }}</span>
                <span class="text-gray-800">{{ $collection->created_at->format('M d, Y') }}</span>
            </div>
            @if($collection->type)
                <div class="flex justify-between">
                    <span class="text-gray-500 font-medium">{{ __('Type') }}</span>
                    <span class="inline-block bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded text-xs font-semibold">
                        {{ __(ucfirst($collection->type)) }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- 🌳 Card EPP --}}
    @if($collection->epp)
        <div class="bg-white p-5 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">{{ __('Environmental Project Supported') }}</h3>
            <div class="flex items-start gap-4">
                {{-- Icona EPP --}}
                <div class="flex-shrink-0 text-green-600 mt-1">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.983 2.504a1 1 0 0 0-1.966 0l-3.044 6.164a1 1 0 0 0 .763 1.52h6.5l.002-.001a1 1 0 0 0 .763-1.52L11.983 2.504ZM12.5 11.562v5.627a.75.75 0 0 1-1.5 0v-5.627a.75.75 0 0 1 1.5 0ZM8.45 18.16a.75.75 0 1 1-1.41-.496l.37-.923a.75.75 0 1 1 1.41.496l-.37.923ZM16.96 17.664a.75.75 0 1 0-1.41.496l.37.923a.75.75 0 1 0 1.41-.496l-.37-.923Z" clip-rule="evenodd" /> <path d="M11.158 19.75a.75.75 0 1 0 1.41-.496l-.185-.462a.75.75 0 1 0-1.41.496l.185.462Zm1.598-1.841a.75.75 0 1 0-1.41-.496l-.878.349a.75.75 0 1 0 .59 1.393l.878-.349ZM9.49 17.407a.75.75 0 1 0 .59 1.393l.878-.349a.75.75 0 1 0-.59-1.393l-.878.349Z" />
                    </svg>
                </div>
                {{-- Dettagli EPP --}}
                <div class="flex-grow">
                    <h4 class="font-semibold text-gray-800">{{ $collection->epp->name }}</h4>
                    <p class="text-sm text-gray-600 mt-1 mb-3 line-clamp-3">{{ $collection->epp->description }}</p>
                    {{-- Impatto (Percentuale - Rendi dinamica se necessario) --}}
                    <div class="text-xs text-gray-500 mb-3">
                        <span class="font-medium text-green-700">20%</span> {{ __('of each primary sale funds this project.') }}
                    </div>
                    {{-- Link Scopri di più --}}
                    <a href="{{ route('epps.show', $collection->epp_id) }}" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800 group">
                        {{ __('Learn more') }}
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-150" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- 🛠️ Card Azioni --}}
    <div class="bg-white p-4 rounded-lg shadow-md">
         <h3 class="text-base font-semibold text-gray-900 mb-3">{{ __('Collection Actions') }}</h3>
         <div class="flex flex-col space-y-2">
             {{-- Pulsante Like (se implementato JS) --}}
             <button class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 like-button {{ $collection->is_liked ?? false ? 'is-liked ring-2 ring-pink-500 text-pink-600' : '' }}"
                     data-collection-id="{{ $collection->id }}"
                     data-like-url="{{ route('api.toggle.collection.like', $collection->id) }}">
                <svg class="-ml-1 mr-2 h-5 w-5 icon-heart {{ $collection->is_liked ?? false ? 'text-pink-500' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z" clip-rule="evenodd" />
                </svg>
                <span class="like-text">{{ $collection->is_liked ?? false ? __('Liked') : __('Like') }}</span>
                <span class="ml-1.5 like-count-display">({{ $collection->likes_count ?? 0 }})</span>
             </button>
             {{-- Pulsante Share (richiede JS per implementazione) --}}
             <button class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                     onclick="alert('Share functionality to be implemented. URL: {{ route('home.collections.show', $collection->id) }}')"> {{-- Placeholder JS --}}
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path d="M13 4.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-7.56-.154a3.5 3.5 0 0 0 6.118 3.027l.755 1.51a.5.5 0 0 0 .87-.43l-.5-1a3.502 3.502 0 0 0-3.231-1.993 3.5 3.5 0 0 0-3.232 1.993l-.5 1a.5.5 0 0 0 .87.43l.755-1.51A3.502 3.502 0 0 0 5.44 4.346ZM3.75 11.5a.75.75 0 0 0 0 1.5h12.5a.75.75 0 0 0 0-1.5H3.75Z" />
                </svg>
                {{ __('Share') }}
             </button>
             {{-- Pulsante Report (richiede logica/modal) --}}
             <button class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                     onclick="alert('Report functionality to be implemented for collection ID: {{ $collection->id }}')"> {{-- Placeholder JS --}}
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7.414a1 1 0 0 0-.293-.707l-4.414-4.414A1 1 0 0 0 12.586 2H4a1 1 0 0 0-1 1Zm10.293 1.293a1 1 0 0 1 1.414 0l2 2a1 1 0 0 1 0 1.414l-9.5 9.5a1 1 0 0 1-.474.264l-3 1a1 1 0 0 1-1.212-1.212l1-3a1 1 0 0 1 .264-.474l9.5-9.5Z" clip-rule="evenodd" />
                </svg>
                {{ __('Report') }}
             </button>
         </div>
    </div>
</aside>

{{-- 📦 Contenuto Principale (Griglia EGI) --}}
{{-- @style: Colonna principale su schermi grandi. --}}
<main class="lg:col-span-8 xl:col-span-9">

    {{-- Barra Filtri EGI --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 pb-4 border-b border-gray-200 gap-4">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 flex-shrink-0">{{ __('EGIs in this Collection') }}</h2>
        {{-- Filtri (esempio - da implementare con JS/Livewire) --}}
        <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto">
            <select name="sort" id="egis-sort" class="flex-grow sm:flex-grow-0 block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" aria-label="{{ __('Sort EGIs') }}">
                <option value="position">{{ __('Position') }}</option>
                <option value="newest">{{ __('Newest') }}</option>
                <option value="oldest">{{ __('Oldest') }}</option>
                <option value="price_low">{{ __('Price: Low to High') }}</option>
                <option value="price_high">{{ __('Price: High to Low') }}</option>
            </select>
            {{-- Toggle Vista (Grid/List - richiede JS) --}}
            {{-- <div class="flex rounded-md shadow-sm bg-white border border-gray-300">
                <button class="view-btn p-2 rounded-l-md text-gray-500 hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 is-active" data-view="grid" aria-pressed="true" aria-label="{{ __('Grid view') }}">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 0 0 2 4.25v2.5A2.25 2.25 0 0 0 4.25 9h2.5A2.25 2.25 0 0 0 9 6.75v-2.5A2.25 2.25 0 0 0 6.75 2h-2.5ZM4.25 11A2.25 2.25 0 0 0 2 13.25v2.5A2.25 2.25 0 0 0 4.25 18h2.5A2.25 2.25 0 0 0 9 15.75v-2.5A2.25 2.25 0 0 0 6.75 11h-2.5ZM11 4.25A2.25 2.25 0 0 1 13.25 2h2.5A2.25 2.25 0 0 1 18 4.25v2.5A2.25 2.25 0 0 1 15.75 9h-2.5A2.25 2.25 0 0 1 11 6.75v-2.5ZM13.25 11A2.25 2.25 0 0 0 11 13.25v2.5A2.25 2.25 0 0 0 13.25 18h2.5A2.25 2.25 0 0 0 18 15.75v-2.5A2.25 2.25 0 0 0 15.75 11h-2.5Z" clip-rule="evenodd" /></svg>
                </button>
                <button class="view-btn p-2 -ml-px rounded-r-md text-gray-400 hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500" data-view="list" aria-pressed="false" aria-label="{{ __('List view') }}">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
                </button>
            </div> --}}
        </div>
    </div>

    {{-- Griglia EGI --}}
    {{-- @style: Layout a griglia responsive. --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6" id="egis-container">
        @forelse($collection->egis as $egi)
            {{-- Includi la card EGI (assumendo esista un componente x-egi-card) --}}
            <x-egi-card :egi="$egi" :collection='$collection' />
        @empty
            {{-- 💨 Stato Vuoto EGIs --}}
            <div class="sm:col-span-2 xl:col-span-3 text-center py-12 px-4 bg-gray-50 rounded-lg">
                <div class="inline-block bg-gray-200 p-4 rounded-full mb-4">
                    <svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ __('No EGIs in this Collection Yet') }}</h3>
                <p class="text-sm text-gray-600 mb-5">{{ __("The creator hasn't added any EGIs to this collection.") }}</p>
                {{-- @permission-check: Mostra bottone solo al creator della collezione --}}
                @if(auth()->id() === $collection->creator_id)
                    {{-- <a href="{{ route('egis.create', ['collection_id' => $collection->id]) }}" --}} {{-- Assumi esista questa rotta --}}
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                       {{ __('Add First EGI') }}
                    </a>
                @endif
            </div>
        @endforelse
    </div>
     {{-- Qui potrebbe andare la paginazione degli EGI se sono molti --}}
     {{-- {{ $egis->links() }} --}}

    {{-- 📚 Collezioni Correlate (se presenti) --}}
    @if(isset($relatedCollections) && $relatedCollections->count() > 0)
        <div class="mt-12 md:mt-16 pt-8 border-t border-gray-200">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-6">{{ __('More from this Creator') }}</h2>
            {{-- @style: Usa una griglia simile a quella principale, magari con meno colonne. --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
                 @foreach($relatedCollections as $relatedCollection)
                    <x-collection-card :collection="$relatedCollection" imageType="card" />
                @endforeach
            </div>
        </div>
    @endif

</main>

</div> {{-- Fine Corpo Principale --}}

</div> {{-- Fine Container Principale --}}

{{-- Eventuale JS specifico per questa pagina (es. per filtri EGI, share button) --}}
@push('scripts')
<script>
// Esempio per il pulsante like (richiede integrazione con API)
document.querySelectorAll('.like-button').forEach(button => {
button.addEventListener('click', async function() {
    const collectionId = this.dataset.collectionId;
    const likeUrl = this.dataset.likeUrl; // Assicurati di avere questa rotta API
    const icon = this.querySelector('.icon-heart');
    const text = this.querySelector('.like-text');
    const countDisplay = document.querySelector(`.like-count-display[data-collection-id="${collectionId}"]`) ?? this.querySelector('.like-count-display');


    // Aggiungi stato 'loading' visivo (opzionale)
    this.disabled = true;
    // icon.classList.add('animate-pulse');

    try {
        const response = await fetch(likeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json' // Se invii dati nel body
            },
            // body: JSON.stringify({ collection_id: collectionId }) // Se necessario
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            this.classList.toggle('is-liked', data.is_liked);
            icon.classList.toggle('text-pink-500', data.is_liked);
            icon.classList.toggle('text-gray-400', !data.is_liked);
            if (text) text.textContent = data.is_liked ? '{{__("Liked")}}' : '{{__("Like")}}';
            if (countDisplay) countDisplay.textContent = `(${data.likes_count ?? 0})`;

             // Aggiorna anche il conteggio nella barra delle statistiche se presente
             const statsBarLikeCount = document.getElementById(`stats-like-count-${collectionId}`); // Dovresti aggiungere un ID univoco
             if (statsBarLikeCount) {
                statsBarLikeCount.textContent = data.likes_count ?? 0;
             }

        } else {
            // Gestisci errore (es. mostra messaggio)
            console.error('Like toggle failed:', data.message);
            alert('Could not update like status. Please try again.');
        }

    } catch (error) {
        console.error('Error toggling like:', error);
         alert('An error occurred. Please try again.');
    } finally {
        // Rimuovi stato 'loading'
        this.disabled = false;
        // icon.classList.remove('animate-pulse');
    }
});
});

// Aggiungere qui eventuale JS per i filtri EGI o il toggle Grid/List view
</script>
@endpush

</x-collection-layout>
