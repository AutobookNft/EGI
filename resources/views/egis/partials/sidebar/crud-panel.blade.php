{{-- resources/views/egis/partials/sidebar/crud-panel.blade.php --}}
{{-- 
    Pannello CRUD per editing (solo per creator)
    ORIGINE: righe 151-520 di show.blade.php
    VARIABILI: $egi, $canUpdateEgi, $canDeleteEgi, $isPriceLocked, $canModifyPrice
--}}

{{-- 
    SPOSTA QUI IL CODICE:
    - Div: lg:col-span-3 xl:col-span-2 bg-gradient-to-b from-emerald-900/20
    - Header con toggle edit
    - Edit Form: title, description, price, creation_date, is_published
    - Action Buttons: save e delete
    - View Mode (default)
    - Gestione price lock se c'è reservation
--}}
