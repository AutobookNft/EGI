{{-- resources/views/egis/show.blade.php --}}
{{-- 📜 Oracode View: Single EGI Detail --}}
{{-- Displays the detailed view for one EGI, including image, metadata, actions, etc. --}}
{{-- Expects $egi (loaded with relationships) and $collection from the controller. --}}

<x-collection-layout :title="$egi->title . ' | ' . $collection->collection_name"
    :metaDescription="Str::limit($egi->description, 155) ?? 'Details for EGI: ' . $egi->title">

{{-- Schema.org per l'EGI specifico --}}
<x-slot name="schemaMarkup">
@php
$egiImageUrl = $egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension
? asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s', $egi->collection_id, $egi->user_id, $egi->key_file, $egi->extension))
: asset('images/default_egi_placeholder.jpg'); // Fallback
@endphp
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VisualArtwork", // O tipo più specifico: Photograph, Painting, CreativeWork ecc.
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
        },
        "dateCreated": "{{ $egi->creation_date ? $egi->creation_date->toDateString() : $egi->created_at->toDateString() }}",
        "material": "{{ $egi->type }} / {{ $egi->extension }}", // Esempio di proprietà
        "size": "{{ $egi->dimension }}", // Esempio
        @if($egi->price > 0)
            "offers": {
                "@type": "Offer",
                "price": "{{ $egi->price }}",
                "priceCurrency": "ALGO" // O altra valuta/token se applicabile
            }
        @endif
    }
</script>
</x-slot>

{{-- 🏛️ Contenitore Principale Pagina --}}
{{-- @style: Layout a griglia responsive (1 colonna su mobile, 2 su lg), gap, container --}}
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

{{-- 🖼️ Colonna Sinistra: Immagine/Visual EGI --}}
{{-- @style: Occupa più spazio su schermi grandi, posizione sticky opzionale. --}}
<aside class="lg:col-span-5 xl:col-span-5"> {{-- Aumentato span per dare più risalto --}}
<div class="sticky top-24"> {{-- Navbar è alta 16 (h-16), top-24 = 6rem (96px) spazio --}}
   <div class="aspect-square w-full overflow-hidden rounded-xl border border-gray-200 bg-gray-100 shadow-md">
       @if($imageUrl = ($egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension ? asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s', $egi->collection_id, $egi->user_id, $egi->key_file, $egi->extension)) : null))
           {{-- @accessibility-trait: Alt text descrittivo. --}}
           <img src="{{ $imageUrl }}"
                alt="{{ $egi->title ?? 'EGI Image' }}"
                class="h-full w-full object-contain" {{-- object-contain per vedere tutto, o object-cover --}}
                loading="eager" /> {{-- Eager loading per l'immagine principale --}}
       @else
           {{-- Placeholder --}}
           <div class="flex h-full w-full items-center justify-center bg-gray-200">
               <svg class="h-24 w-24 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
           </div>
       @endif
   </div>
    {{-- (Opzionale) Piccoli controlli immagine: Zoom, Fullscreen etc. --}}
</div>
</aside>

{{-- ℹ️ Colonna Destra: Dettagli e Azioni --}}
{{-- @style: Occupa lo spazio rimanente. --}}
<main class="lg:col-span-7 xl:col-span-7">

{{-- 🔗 Link alla Collezione --}}
<div class="mb-2">
   <a href="{{ route('home.collections.show', $collection->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
       {{ $collection->collection_name }}
   </a>
</div>

{{-- 👑 Titolo EGI --}}
<h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3 break-words">
   {{ $egi->title }}
</h1>

{{-- 👤 Ownership & Creator Info --}}
<div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600 mb-6">
   {{-- Proprietario Attuale --}}
   <div class="flex items-center">
       <span class="mr-1.5">{{ __('Owned by') }}</span>
       @if($egi->owner)
           {{-- <a href="#" class="font-medium text-gray-800 hover:text-indigo-600"> --}}
               {{ $egi->owner->name }}
           {{-- </a> --}}
       @else
           <span class="font-medium text-gray-800">{{ Str::limit($egi->owner_wallet ?? 'Unknown', 12, '...') }}</span>
       @endif
   </div>
    {{-- Creatore Originale (se diverso dal proprietario) --}}
   @if($egi->user && (!$egi->owner || $egi->user->id !== $egi->owner->id))
        <div class="flex items-center">
            <span class="mr-1.5">{{ __('Created by') }}</span>
            {{-- <a href="#" class="font-medium text-gray-800 hover:text-indigo-600"> --}}
                {{ $egi->user->name }}
            {{-- </a> --}}
        </div>
    @endif
    {{-- Icona Like (se la metti qui) --}}
    <div class="flex items-center">
        <svg class="w-4 h-4 mr-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
           <path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
         </svg>
        <span>{{ $egi->likes_count ?? 0 }} {{ __('likes') }}</span> {{-- Assumendo Eager Loading o relazione definita --}}
    </div>
</div>

{{-- 🛒 Box Azioni (Prezzo, Riserva/Compra) --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
   {{-- @logic: Determina lo stato (in vendita, non in vendita, prenotabile) --}}
   @php
       $isForSale = $egi->price && $egi->price > 0 && !$egi->mint; // Esempio di logica "in vendita"
       $canBeReserved = !$egi->mint && ($egi->is_published || (auth()->check() && auth()->id() === $collection->creator_id));
   @endphp

   @if($isForSale)
       <div class="mb-4">
           <p class="text-sm text-gray-500">{{ __('Current price') }}</p>
           <p class="text-3xl font-bold text-gray-900">
               {{ number_format($egi->price, 2) }} <span class="text-lg font-medium text-gray-500">ALGO</span>
           </p>
           {{-- Aggiungere eventuale prezzo in USD/EUR qui --}}
       </div>
       {{-- <button class="w-full btn btn-primary btn-lg"> {{-- Stile bottone primario grande --}}
           {{-- Buy Now --}}
       {{-- </button> --}}
   @elseif ($egi->floorDropPrice && $egi->floorDropPrice > 0 && !$egi->mint)
        <div class="mb-4">
           <p class="text-sm text-gray-500">{{ __('Floor price') }}</p>
           <p class="text-2xl font-bold text-gray-900">
               {{ number_format($egi->floorDropPrice, 2) }} <span class="text-lg font-medium text-gray-500">ALGO</span>
           </p>
       </div>
   @else
        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-700">{{ __('Not currently for sale') }}</p>
        </div>
   @endif

   {{-- Pulsante Riserva (se applicabile) --}}
   @if($canBeReserved)
       <button class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 reserve-button mt-4"
               data-egi-id="{{ $egi->id }}"
               data-reserve-url="{{ route('api.egis.reserve', $egi->id) }}"> {{-- Assicurati che esista rotta API --}}
           <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
             <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
           </svg>
           {{ __('Reserve this EGI') }}
       </button>
   @elseif($egi->mint)
       <div class="mt-4 p-3 bg-gray-100 rounded-md text-center">
            <p class="text-sm font-medium text-gray-600">{{ __('This EGI has been minted.') }}</p>
            {{-- Aggiungi link a explorer blockchain se disponibile --}}
        </div>
   @endif
   {{-- Pulsante Make Offer (Post-MVP) --}}
   {{-- <button class="w-full btn btn-secondary btn-lg mt-3">Make Offer</button> --}}
</div>

{{-- 📄 Card Descrizione --}}
{{-- @component('components.details.description-card', ['description' => $egi->description]) --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
   <div class="border-b border-gray-200 px-4 py-3">
       <h3 class="text-base font-semibold leading-6 text-gray-900">{{ __('Description') }}</h3>
   </div>
   <div class="p-4 text-sm text-gray-700 prose prose-sm max-w-none">
       {!! nl2br(e($egi->description ?? __('No description provided.'))) !!}
   </div>
</div>

{{-- ✨ Card Proprietà/Traits (Esempio) --}}
{{-- @component('components.details.properties-card', ['egi' => $egi, 'collection' => $collection]) --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
    <div class="border-b border-gray-200 px-4 py-3">
       <h3 class="text-base font-semibold leading-6 text-gray-900">{{ __('Properties') }}</h3>
   </div>
   {{-- @style: Griglia per le proprietà. --}}
   <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4">
       {{-- Esempio Proprietà EPP --}}
       @if($collection->epp)
           <div class="text-center p-3 bg-green-50 border border-green-200 rounded-md">
               <p class="text-xs uppercase font-medium text-green-600 mb-0.5">{{ __('Supports EPP') }}</p>
               <p class="text-sm font-semibold text-green-900 truncate" title="{{ $collection->epp->name }}">
                   <a href="{{ route('epps.show', $collection->epp->id) }}" class="hover:underline">
                       {{ Str::limit($collection->epp->name, 20) }}
                   </a>
               </p>
           </div>
       @endif
       {{-- Esempio Proprietà Tipo File --}}
       @if($egi->type)
           <div class="text-center p-3 bg-blue-50 border border-blue-200 rounded-md">
               <p class="text-xs uppercase font-medium text-blue-600 mb-0.5">{{ __('Asset Type') }}</p>
               <p class="text-sm font-semibold text-blue-900">{{ __(ucfirst($egi->type)) }}</p>
           </div>
       @endif
        {{-- Esempio Proprietà Estensione --}}
       @if($egi->extension)
           <div class="text-center p-3 bg-gray-50 border border-gray-200 rounded-md">
               <p class="text-xs uppercase font-medium text-gray-500 mb-0.5">{{ __('Format') }}</p>
               <p class="text-sm font-semibold text-gray-800">{{ strtoupper($egi->extension) }}</p>
           </div>
       @endif
        {{-- Esempio Proprietà Dimensioni --}}
       @if($egi->dimension)
           <div class="text-center p-3 bg-gray-50 border border-gray-200 rounded-md">
               <p class="text-xs uppercase font-medium text-gray-500 mb-0.5">{{ __('Dimensions') }}</p>
               <p class="text-sm font-semibold text-gray-800">{{ $egi->dimension }}</p>
           </div>
       @endif
       {{-- Aggiungi altre proprietà/traits qui in modo simile --}}
   </div>
</div>

{{-- ⏳ Card Attività/Storia (Post-MVP o con Audit) --}}
{{-- @component('components.details.activity-card', ['activities' => $egi->audits ?? []]) --}}
{{-- <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="border-b border-gray-200 px-4 py-3">
        <h3 class="text-base font-semibold leading-6 text-gray-900">{{ __('Item Activity') }}</h3>
    </div>
    <div class="p-4">
        <p class="text-sm text-gray-500">Activity history will be shown here (e.g., mint, transfers, reservations).</p>
        @if($egi->audits && $egi->audits->count() > 0)
            <ul class="mt-4 space-y-2">
                @foreach($egi->audits->sortByDesc('created_at') as $audit)
                    <li class="text-xs border-b pb-1">
                        <strong>{{ ucfirst($audit->action) }}</strong> by {{ $audit->user->name ?? 'System' }} on {{ $audit->created_at->format('M d, Y H:i') }}
                        <!--Puoi mostrare $audit->old_values / $audit->new_values se necessario -->
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div> --}}

</main>

</div>
</div>

{{-- Script specifici (es. per pulsante riserva, share, etc.) --}}
@push('scripts')
{{-- <script> console.log('EGI ID: {{ $egi->id }}'); </script> --}}
@endpush

</x-collection-layout>
