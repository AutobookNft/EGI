{{-- resources/views/egis/partials/sidebar/traits-section.blade.php --}}
{{-- 
    Sezione traits viewer e editor
    ORIGINE: righe 146-155 di show.blade.php (Traits Section)
    VARIABILI: $egi
--}}

{{-- Traits Section - Forza il posizionamento naturale --}}
<div class="traits-container" style="order: 0; position: relative; z-index: 1;">
    {{-- Traits Section - Solo se ci sono traits esistenti --}}
    @if($egi->egiTraits && $egi->egiTraits->count() > 0)
    <div class="space-y-4">
        <x-egi.traits-viewer :egi="$egi" />
    </div>
    @endif
    {{-- Traits Manager --}}
    <div class="pt-6 mt-6 border-t border-emerald-700/30">
        <x-egi.traits-editor :egi="$egi" />
    </div>
</div>
