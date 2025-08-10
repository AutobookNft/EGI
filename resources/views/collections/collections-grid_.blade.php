    {{-- resources/views/collections-grid.blade.php --}}
{{-- 📜 Oracode View: Collection Grid Display --}}
{{-- Displays a paginated and filterable grid of EGI Collections. --}}
{{-- Uses Tailwind CSS for styling and layout. --}}
{{-- Expects $collections (Paginator instance) and $epps (Collection of Epp) from the controller. --}}

<x-guest-layout :title="__('Explore EGI Collections | FlorenceEGI')"
                :metaDescription="__('Discover unique Ecological Goods Invent collections on FlorenceEGI. Filter by project, status, and sort by popularity or date.')">

    {{-- 🏛️ Container Principale --}}
    <div class="container px-4 py-12 mx-auto sm:px-6 lg:px-8">

        {{-- 🚦 Header della Sezione: Titolo e Filtri --}}
        <header class="mb-8 md:mb-12">
            {{-- 🎯 Titolo della Sezione --}}
            <h2 class="mb-6 text-2xl font-bold text-center text-gray-900 sm:text-3xl lg:text-4xl">
                {{ __('Explore Collections') }}
            </h2>

            {{-- 🔍 Sezione Filtri --}}
            {{-- @accessibility-trait: Form elements have labels. --}}
            <div class="p-4 bg-white rounded-lg shadow-md sm:p-6">
                <form action="{{ route('home.collections.index') }}" method="GET" id="filterForm" class="flex flex-col gap-4 md:flex-row md:items-end">

                    {{-- 🔢 Gruppo Filtro: Ordinamento --}}
                    <div class="flex-grow filter-group">
                        <label for="sort" class="block mb-1 text-sm font-medium text-gray-700">{{ __('Sort by:') }}</label>
                        <select name="sort" id="sort" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Name (A-Z)') }}</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>{{ __('Popularity') }}</option>
                        </select>
                    </div>

                    {{-- 🌳 Gruppo Filtro: EPP --}}
                    <div class="flex-grow filter-group">
                        <label for="epp" class="block mb-1 text-sm font-medium text-gray-700">{{ __('Environmental Project (EPP):') }}</label>
                        <select name="epp" id="epp" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">{{ __('All EPPs') }}</option>
                            @foreach ($epps as $epp)
                                <option value="{{ $epp->id }}" {{ request('epp') == $epp->id ? 'selected' : '' }}>{{ $epp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 🏷️ Gruppo Filtro: Stato --}}
                    {{-- @permission-check: Show draft filter only if user has permission --}}
                    @can('view_draft_collections') {{-- Assuming a permission controls this --}}
                        <div class="flex-grow filter-group">
                            <label for="status" class="block mb-1 text-sm font-medium text-gray-700">{{ __('Status:') }}</label>
                            <select name="status" id="status" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                {{-- Add other relevant statuses if needed --}}
                            </select>
                        </div>
                    @else
                        {{-- Optionally hide or disable status filter for guests, or default to published --}}
                        {{-- <input type="hidden" name="status" value="published"> --}}
                    @endcan

                    {{-- <button type="submit" class="mt-4 btn btn-secondary md:mt-0 md:self-end">Apply Filters</button> --}}
                    {{-- Submit button is removed as filters apply on change --}}
                </form>
            </div>
        </header>

        {{-- 🖼️ Griglia delle Collezioni --}}
        {{-- Layout responsivo: 1 colonna su mobile, 2 su sm, 3 su lg, 4 su xl --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 lg:gap-8">
            {{-- @forelse: Loop through collections, handle empty state --}}
            @forelse ($collections as $collection)
                {{-- Includi il componente card, passando la collection --}}
                {{-- Nota: Il componente card dovrebbe gestire i dati e lo stile interno --}}
                {{-- @component('components.collection-card', ['collection' => $collection]) --}}
                {{-- Assumendo che tu abbia un componente Blade x-collection-card --}}
                {{-- Passa i dati necessari come attributi --}}
                <x-home-collection-card :id="$collection->id" :editable="false" imageType="card" :collection="$collection"/>
            @empty
                {{-- 💨 Stato Vuoto: Nessuna collezione trovata --}}
                <div class="px-4 py-16 text-center col-span-full">
                    <div class="inline-block p-4 mb-4 bg-gray-100 rounded-full">
                        {{-- @accessibility-trait: Icona decorativa --}}
                        <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-800">{{ __('No Collections Found') }}</h3>
                    <p class="mb-6 text-gray-600">{{ __('No collections match your current filters. Why not create one?') }}</p>
                    {{-- @permission-check: Show create button only if user can create collections --}}
                    @can('create_collection')
                        <a href="{{ route('collections.create') }}" {{-- Assicurati che questa rotta esista --}}
                           class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                           {{ __('Create Your First Collection') }}
                        </a>
                    @endcan
                </div>
            @endforelse
        </div>

        {{-- 🔢 Paginazione --}}
        {{-- Assicura che la vista di paginazione sia configurata per Tailwind --}}
        <div class="flex justify-center mt-10 md:mt-16">
            {{ $collections->appends(request()->query())->links() }}
            {{-- Se usi una vista custom ->links('vendor.pagination.tailwind') --}}
        </div>

    </div> {{-- Fine Container Principale --}}
</x-guest-layout>
