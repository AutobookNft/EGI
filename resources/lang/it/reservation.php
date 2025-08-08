<?php

/**
 * Messaggi Prenotazione
 * @package FlorenceEGI
 * @subpackage Traduzioni
 * @language it
 * @version 1.0.0
 */

return [
    // Messaggi di successo
    'success' => 'La tua prenotazione è stata effettuata con successo! Il certificato è stato generato.',
    'cancel_success' => 'La tua prenotazione è stata annullata con successo.',
    'success_title' => 'Prenotazione effettuata!',
    'view_certificate' => 'Visualizza Certificato',
    'close' => 'Chiudi',

    // Messaggi di errore
    'unauthorized' => 'Devi collegare il tuo wallet o effettuare l’accesso per prenotare.',
    'validation_failed' => 'Controlla i dati inseriti e riprova.',
    'auth_required' => 'È richiesta l’autenticazione per visualizzare le tue prenotazioni.',
    'list_failed' => 'Impossibile recuperare le tue prenotazioni. Riprova più tardi.',
    'status_failed' => 'Impossibile recuperare lo stato della prenotazione. Riprova più tardi.',
    'unauthorized_cancel' => 'Non hai il permesso per annullare questa prenotazione.',
    'cancel_failed' => 'Impossibile annullare la prenotazione. Riprova più tardi.',

    // Pulsanti UI
    'button' => [
        'reserve' => 'Prenota',
        'reserved' => 'Prenotato',
        'make_offer' => 'Fai un’offerta'
    ],

    // Badge
    'badge' => [
        'highest' => 'Massima Priorità',
        'superseded' => 'Priorità Inferiore',
        'has_offers' => 'Prenotato'
    ],

    // Dettagli prenotazione
    'already_reserved' => [
        'title' => 'Già Prenotato',
        'text' => 'Hai già una prenotazione per questo EGI.',
        'details' => 'Dettagli della tua prenotazione:',
        'type' => 'Tipo',
        'amount' => 'Importo',
        'status' => 'Stato',
        'view_certificate' => 'Visualizza Certificato',
        'ok' => 'OK',
        'new_reservation' => 'Nuova Prenotazione',
        'confirm_new' => 'Vuoi effettuare una nuova prenotazione?'
    ],

    // Storico prenotazioni
    'history' => [
        'title' => 'Storico Prenotazioni',
        'entries' => 'Voci di Prenotazione',
        'view_certificate' => 'Visualizza Certificato',
        'no_entries' => 'Nessuna prenotazione trovata.',
        'be_first' => 'Sii il primo a prenotare questo EGI!'
    ],

    // Messaggi di errore
    'errors' => [
        'button_click_error' => 'Si è verificato un errore nell’elaborazione della tua richiesta.',
        'form_validation' => 'Controlla i dati inseriti e riprova.',
        'api_error' => 'Si è verificato un errore nella comunicazione con il server.',
        'unauthorized' => 'Devi collegare il tuo wallet o effettuare l’accesso per prenotare.'
    ],

    // Form
    'form' => [
        'title' => 'Prenota questo EGI',
        'offer_amount_label' => 'La tua Offerta (EUR)',
        'offer_amount_placeholder' => 'Inserisci l’importo in EUR',
        'algo_equivalent' => 'Circa :amount ALGO',
        'terms_accepted' => 'Accetto i termini e le condizioni per la prenotazione degli EGI',
        'contact_info' => 'Informazioni di Contatto Aggiuntive (Opzionale)',
        'submit_button' => 'Effettua Prenotazione',
        'cancel_button' => 'Annulla'
    ],

    // Tipologia prenotazione
    'type' => [
        'strong' => 'Prenotazione Forte',
        'weak' => 'Prenotazione Debole'
    ],

    // Livelli di priorità
    'priority' => [
        'highest' => 'Prenotazione Attiva',
        'superseded' => 'Superata',
    ],

    // Stato della prenotazione
    'status' => [
        'active' => 'Attiva',
        'pending' => 'In attesa',
        'cancelled' => 'Annullata',
        'expired' => 'Scaduta'
    ]
];
