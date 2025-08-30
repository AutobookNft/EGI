{{-- resources/views/egis/partials/sidebar/description-section.blade.php --}}
{{-- 
    Sezione descrizione dell'EGI
    ORIGINE: righe 149-156 di show.blade.php
    VARIABILI: $egi
--}}

{{-- Description Section --}}
<div class="space-y-4">
    <h3 class="text-lg font-semibold text-white">{{ __('egi.about_this_piece') }}</h3>
    <div class="leading-relaxed prose-sm prose text-gray-300 prose-invert max-w-none">
        {!! nl2br(e($egi->description ?? __('egi.default_description'))) !!}
    </div>
</div>
