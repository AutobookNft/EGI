{{-- resources/views/egis/partials/sidebar/traits-section.blade.php --}}
{{-- 
    Sezione traits viewer e editor con controllo autorizzazioni
    ORIGINE: righe 146-155 di show.blade.php (Traits Section)
    VARIABILI: $egi, $canManage
--}}

{{-- Traits Section - Forza il posizionamento naturale --}}
<div class="traits-container" style="order: 0; position: relative; z-index: 1;">
    
    @if(isset($canManage) && $canManage)
        {{-- Solo il creator vede l'editor completo --}}
        <div class="space-y-4">
            <x-egi.traits-editor :egi="$egi" :can-edit="true" />
        </div>
    @else
        {{-- Tutti gli altri vedono SOLO i traits esistenti (se ci sono) --}}
        @if($egi->egiTraits && $egi->egiTraits->count() > 0)
            <div class="space-y-4">
                <x-egi.traits-viewer :egi="$egi" />
            </div>
        @endif
    @endif
    
</div>
