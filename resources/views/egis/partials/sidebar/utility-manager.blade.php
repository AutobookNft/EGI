{{-- resources/views/egis/partials/sidebar/utility-manager.blade.php --}}
{{-- 
    Component Utility Manager (solo per creator)
    ORIGINE: righe 850-870 di show.blade.php
    VARIABILI: $egi (con check auth()->id())
--}}

{{-- 
    SPOSTA QUI IL CODICE:
    - Component Utility Manager (solo per creator)
    - Border-t con padding
    - <x-utility.utility-manager :egi="$egi" />
    - Controllo: @if(auth()->id() === $egi->user_id)
--}}
