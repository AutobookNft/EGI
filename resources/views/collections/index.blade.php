{{-- resources/views/collections/index.blade.php --}} {{-- O il percorso corretto, es. collections-grid.blade.php --}}
{{-- 📜 Oracode View: Collections Grid --}}
{{-- Displays a paginated and filterable grid of EGI Collections. --}}
{{-- Uses Tailwind CSS for styling and layout. --}}
{{-- Expects $collections (Paginator instance) and $epps (Collection of Epp) from the controller. --}}

<x-collection-layout :title="__('Explore EGI Collections | FlorenceEGI')"
    :metaDescription="__('Discover unique Ecological Goods Invent collections on FlorenceEGI. Filter by project, status, and sort by popularity or date.')">

    {{-- Aggiungiamo Schema.org per la pagina elenco --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "CollectionPage", // Indica che questa è una pagina elenco
          "name": "{{ __('Explore EGI Collections') }}",
          "description": "{{ __('Discover unique Ecological Goods Invent collections on FlorenceEGI.') }}",
          "url": "{{ route('home.collections.index') }}",
          "mainEntity": {
            "@type": "ItemList",
            "itemListElement": [
              @foreach ($collections as $index => $collection)
                {
                  "@type": "ListItem",
                  "position": {{ $collections->firstItem() + $index }},
                  "item": {
                    "@type": "CreativeWork", // O tipo più specifico se conosciuto
                    "name": "{{ $collection->collection_name }}",
                    "url": "{{ route('home.collections.show', $collection->id) }}",
                    "image": "{{ $collection->image_card ? Storage::url($collection->image_card) : asset('images/default_card.jpg') }}",
                    "author": {
                      "@type": "Person",
                      "name": "{{ $collection->creator->name ?? 'Unknown Creator' }}"
                    }
                  }
                }{{ !$loop->last ? ',' : '' }}
              @endforeach
            ]
          }
        }
        </script>
    </x-slot>

    {{-- 🏛️ Container Principale --}}
    {{-- @style: Container centrato, padding responsive, spaziatura verticale. --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- 🚦 Header della Sezione: Titolo e Filtri --}}
        <header class="mb-8 md:mb-12">
            {{-- 🎯 Titolo della Sezione --}}
            {{-- @style: Dimensione font responsive, grassetto, colore, margine inferiore, allineamento. --}}
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-6 text-center">
                {{ __('Explore Collections') }}
            </h2>

            {{-- 🔍 Sezione Filtri --}}
            {{-- @style: Sfondo bianco, padding, angoli arrotondati, ombra. --}}
            {{-- @accessibility-trait: Form elements have labels. --}}
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
                {{-- @style: Layout Flex, responsive (colonna su mobile, riga su md), allineamento alla base in riga, gap. --}}
                <form action="{{ route('home.collections.index') }}" method="GET" id="filterForm" class="flex flex-col md:flex-row md:items-end gap-4">

                    {{-- 🔢 Gruppo Filtro: Ordinamento --}}
                    {{-- @style: Occupa spazio disponibile nel layout flex. --}}
                    <div class="filter-group flex-grow">
                        {{-- @style: Stile etichetta standard. --}}
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sort by:') }}</label>
                        {{-- @style: Stile select standard Tailwind/Form. --}}
                        <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Name (A-Z)') }}</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>{{ __('Popularity') }}</option>
                        </select>
                    </div>

                    {{-- 🌳 Gruppo Filtro: EPP --}}
                    <div class="filter-group flex-grow">
                        <label for="epp" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Environmental Project (EPP):') }}</label>
                        <select name="epp" id="epp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">{{ __('All EPPs') }}</option>
                            @foreach ($epps as $epp)
                                <option value="{{ $epp->id }}" {{ request('epp') == $epp->id ? 'selected' : '' }}>{{ $epp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 🏷️ Gruppo Filtro: Stato --}}
                    {{-- @permission-check: Mostra filtro bozze solo a utenti autorizzati. --}}
                    @can('view_draft_collections') {{-- Assicurati che il permesso 'view_draft_collections' esista e sia assegnato correttamente --}}
                        <div class="filter-group flex-grow">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status:') }}</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="published" {{ request('status', 'published') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option> {{-- Default a published se permesso non presente? --}}
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            </select>
                        </div>
                    @else
                         {{-- Per utenti non autorizzati, potresti non mostrare il filtro o forzare 'published' --}}
                         {{-- <input type="hidden" name="status" value="published"> --}}
                         {{-- O semplicemente omettere questo blocco div --}}
                    @endcan
                </form>
            </div>
        </header>

        {{-- 🖼️ Griglia delle Collezioni --}}
        {{-- @style: Layout a griglia responsive, gap tra elementi. --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
            {{-- @forelse: Itera sulle collezioni o mostra lo stato vuoto. --}}
            @forelse ($collections as $collection)
                {{-- 💳 Inclusione del Componente Card --}}
                {{-- Passa l'oggetto collection al componente. Assicurati che x-collection-card esista e sia stilizzato. --}}
                {{-- <x-collection-card :id="$collection->id" :collection="$collection" :key="$collection->id" /> Aggiunta :key per ottimizzazione loop Livewire/Vue/React --}}
                <x-home-collection-card :id="$collection->id" :editable="false" imageType="card" :collection="$collection"/>
            @empty
                {{-- 💨 Stato Vuoto: Nessuna collezione trovata --}}
                {{-- @style: Occupa tutta la larghezza della griglia, centrato, padding, stili testo/icona. --}}
                <div class="col-span-full text-center py-16 px-4 bg-gray-50 rounded-lg shadow-inner">
                    <div class="inline-block bg-gray-200 p-5 rounded-full mb-5">
                         {{-- @accessibility-trait: Icona decorativa. --}}
                        <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No Collections Found') }}</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">{{ __('It seems there are no collections matching your criteria right now. Be the first to showcase yours!') }}</p>
                    {{-- @permission-check: Mostra bottone creazione solo se l'utente ha il permesso. --}}
                    @can('create_collection')
                        <a href="{{ route('collections.create') }}" {{-- Assicurati che questa rotta esista e sia corretta --}}
                           class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                           {{-- Icona Opzionale --}}
                           <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                             <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                           </svg>
                           {{ __('Create Collection') }}
                        </a>
                    @endcan
                </div>
            @endforelse
        </div>

        {{-- 🔢 Paginazione --}}
        {{-- @style: Margine superiore, centrato. --}}
        <div class="mt-10 md:mt-16 flex justify-center">
            {{-- Assicura che la vista di paginazione usi Tailwind. `appends` mantiene i parametri di filtro/sort. --}}
            {{ $collections->appends(request()->query())->links() }}
        </div>

    </div> {{-- Fine Container Principale --}}

</x-guest-layout>
