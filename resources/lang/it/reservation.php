<?php

return [
    // Messaggi di successo
    'success' => 'La tua prenotazione è stata completata con successo! Il certificato è stato generato.',
    'cancel_success' => 'La tua prenotazione è stata annullata con successo.',

    // Errori
    'unauthorized' => 'Devi connettere il tuo wallet o accedere per effettuare una prenotazione.',
    'validation_failed' => 'Controlla i dati inseriti e riprova.',
    'auth_required' => 'Autenticazione richiesta per visualizzare le tue prenotazioni.',
    'list_failed' => 'Impossibile recuperare le tue prenotazioni. Riprova più tardi.',
    'status_failed' => 'Impossibile recuperare lo stato della prenotazione. Riprova più tardi.',
    'unauthorized_cancel' => 'Non hai il permesso per annullare questa prenotazione.',
    'cancel_failed' => 'Impossibile annullare la prenotazione. Riprova più tardi.',

    // Formulario
    'form' => [
        'title' => 'Prenota questo EGI',
        'offer_amount_label' => 'La tua offerta (EUR)',
        'offer_amount_placeholder' => 'Inserisci l\'importo in EUR',
        'algo_equivalent' => 'Circa :amount ALGO',
        'terms_accepted' => 'Accetto i termini e le condizioni per le prenotazioni EGI',
        'contact_info' => 'Informazioni di contatto aggiuntive (opzionale)',
        'submit_button' => 'Effettua Prenotazione',
        'cancel_button' => 'Annulla'
    ],

    // Errori specifici
    'errors' => [
        'RESERVATION_EGI_NOT_AVAILABLE' => 'Questo EGI non è attualmente disponibile per la prenotazione.',
        'RESERVATION_AMOUNT_TOO_LOW' => 'L\'importo offerto è troppo basso. Inserisci un importo più alto.',
        'RESERVATION_UNAUTHORIZED' => 'Devi connettere il tuo wallet o accedere per effettuare una prenotazione.',
        'RESERVATION_CERTIFICATE_GENERATION_FAILED' => 'Non siamo riusciti a generare il tuo certificato di prenotazione. Il nostro team è stato informato.',
        'RESERVATION_CERTIFICATE_NOT_FOUND' => 'Il certificato richiesto non è stato trovato.',
        'RESERVATION_ALREADY_EXISTS' => 'Hai già una prenotazione attiva per questo EGI.',
        'RESERVATION_CANCEL_FAILED' => 'Non siamo riusciti ad annullare la tua prenotazione. Riprova più tardi.',
        'RESERVATION_UNAUTHORIZED_CANCEL' => 'Non hai il permesso per annullare questa prenotazione.',
        'RESERVATION_STATUS_FAILED' => 'Impossibile recuperare lo stato della prenotazione. Riprova più tardi.',
        'RESERVATION_UNKNOWN_ERROR' => 'Qualcosa è andato storto con la tua prenotazione. Il nostro team è stato informato.'
    ],

    // Badge e status descriptor
    'type' => [
        'strong' => 'Prenotazione Forte',
        'weak' => 'Prenotazione Base'
    ],
    'status' => [
        'active' => 'Attiva',
        'cancelled' => 'Annullata',
        'expired' => 'Scaduta',
        'superseded' => 'Superata'
    ],
    'priority' => [
        'highest' => 'Priorità Massima',
        'superseded' => 'Priorità Inferiore'
    ]
];
