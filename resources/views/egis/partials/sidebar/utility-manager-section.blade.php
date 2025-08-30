{{-- resources/views/egis/partials/sidebar/utility-manager-section.blade.php --}}
{{-- 
    Sezione utility manager dell'EGI (solo per creator)
    ORIGINE: righe 158-163 di show.blade.php
    VARIABILI: $egi
--}}

{{-- Component Utility Manager (solo per creator) --}}
@if(auth()->id() === $egi->user_id)
    <div class="pt-6 border-t border-gray-700/50">
        <x-utility.utility-manager :egi="$egi" />
    </div>
@endif
