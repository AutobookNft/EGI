{{-- resources/views/egis/partials/sidebar/traits-section.blade.php --}}
{{-- 
    Sezione traits viewer e editor con controllo autorizzazioni
    ORIGINE: righe 146-155 di show.blade.php (Traits Section)
    VARIABILI: $egi, $canManage
--}}

{{-- Traits Section - Forza il posizionamento naturale --}}
<div class="traits-container" style="order: 0; position: relative; z-index: 1;">

    {{-- Titolo della sezione --}}
      
    {{-- VIEWER: SEMPRE visibile se ci sono traits --}}
    @if($egi->traits && $egi->traits->count() > 0)
        <div class="space-y-4">
            <x-egi.traits-viewer :egi="$egi" />
        </div>
    @endif
    
    {{-- EDITOR: Solo se creator (SENZA mostrare i traits) --}}
    @if(isset($canManage) && $canManage)
        <div class="space-y-4">
            <x-egi.traits-editor :egi="$egi" :can-edit="true" />
        </div>
    @endif
    
</div>
