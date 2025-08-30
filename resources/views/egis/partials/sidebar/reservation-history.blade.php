{{-- resources/views/egis/partials/sidebar/reservation-history.blade.php --}}
{{-- 
    Sezione storico prenotazioni
    ORIGINE: righe 781-800 di show.blade.php
    VARIABILI: $egi (con reservationCertificates)
--}}

{{-- 
    SPOSTA QUI IL CODICE:
    - Reservation History section
    - Controllo: @if($egi->reservationCertificates && $egi->price && $egi->price > 0)
    - H3: "Provenance"
    - Component: <x-egi-reservation-history>
--}}
