{{-- resources/views/egis/partials/sidebar/traits-section.blade.php --}}
{{--
    Sezione traits viewer e editor con controllo autorizzazioni
    ORIGINE: righe 146-155 di show.blade.php (Traits Section)
    VARIABILI: $egi, $canManage
--}}

{{-- Traits Section - Forza il posizionamento naturale --}}
<div class="traits-container" style="order: 0; position: relative; z-index: 1;">

    {{-- Titolo della sezione --}}

    {{-- VIEWER: SEMPRE visibile - con edit mode integrato se canManage --}}
    <div class="space-y-4">
        <x-egi.traits-viewer :egi="$egi" :can-manage="$canManage ?? false" />
    </div>

</div>
