{{-- resources/views/egis/partials/artwork/gallery-container.blade.php --}}
{{-- 
    Container principale della gallery con layout a 3 colonne
    ORIGINE: righe 35-200 di show.blade.php
    VARIABILI: $egi, $collection, $collectionEgis, $isCreator, $canUpdateEgi
--}}

{{-- 
    SPOSTA QUI IL CODICE:
    - Div principale: min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900
    - Grid layout: grid min-h-screen grid-cols-1 lg:grid-cols-12
    - Left: Artwork Area (lg:col-span-6 xl:col-span-7)
    - Center: CRUD Box (lg:col-span-3 xl:col-span-2) se $canUpdateEgi
    - Right: Sidebar (lg:col-span-3)
    
    INCLUDE:
    - @include('egis.partials.artwork.main-image-display')
    - @include('egis.partials.artwork.floating-title-card')
    - @include('egis.partials.sidebar.crud-panel') se $canUpdateEgi
    - @include('egis.partials.sidebar.main-content')
--}}
