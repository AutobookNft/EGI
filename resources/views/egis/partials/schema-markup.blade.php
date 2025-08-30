{{-- resources/views/egis/partials/schema-markup.blade.php --}}
{{-- 
    Schema.org JSON-LD markup per SEO
    ORIGINE: righe 6-25 di show.blade.php
    VARIABILI: $egi, $collection, $isCreator (definita nel file principale)
--}}

@php
// Usa l'immagine ottimizzata per Schema.org e fallback
$egiImageUrl = $egi->main_image_url ?? asset('images/default_egi_placeholder.jpg');
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
