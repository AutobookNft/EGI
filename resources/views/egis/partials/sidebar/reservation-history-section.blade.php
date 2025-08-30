{{-- resources/views/egis/partials/sidebar/reservation-history-section.blade.php --}}
{{-- 
    Sezione cronologia prenotazioni dell'EGI
    ORIGINE: righe 152-159 di show.blade.php
    VARIABILI: $egi
--}}

{{-- Reservation History --}}
@if($egi->reservationCertificates && $egi->price && $egi->price > 0)
<div class="space-y-4">
    <h3 class="text-lg font-semibold text-white">{{ __('egi.provenance') }}</h3>
    <x-egi-reservation-history :egi="$egi" :certificates="$egi->reservationCertificates" />
</div>
@endif
