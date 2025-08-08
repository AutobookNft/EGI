<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - Traduzioni Italiane
    |--------------------------------------------------------------------------
    |
    | Traduzioni per il sistema CRUD degli EGI in FlorenceEGI
    | Versione: 1.0.0 - Oracode System 2.0 Compliant
    |
    */

    // Meta e SEO
    'meta_description_default' => 'Dettagli per EGI: :title',
    'image_alt_default' => 'Immagine EGI',
    'view_full' => 'Visualizza Completa',
    'artwork_loading' => 'Opera in Caricamento...',

    // Informazioni Base
    'by_author' => 'di :name',
    'unknown_creator' => 'Creatore Sconosciuto',

    // Azioni Principali
    'like_button_title' => 'Aggiungi ai Preferiti',
    'share_button_title' => 'Condividi questo EGI',
    'current_price' => 'Prezzo Attuale',
    'not_currently_listed' => 'Non Attualmente in Vendita',
    'contact_owner_availability' => 'Contatta il proprietario per disponibilità',
    'liked' => 'Piaciuto',
    'add_to_favorites' => 'Aggiungi ai Preferiti',
    'reserve_this_piece' => 'Prenota Quest\'Opera',

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - Sistema Carte NFT
    |--------------------------------------------------------------------------
    */

    // Badge e Stati
    'badge' => [
        'owned' => 'POSSEDUTO',
        'media_content' => 'Contenuto Media',
        'winning_bid' => 'OFFERTA VINCENTE',
        'outbid' => 'SUPERATO',
    ],

    // Titoli
    'title' => [
        'untitled' => '✨ EGI Senza Titolo',
    ],

    // Piattaforma
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Creatore
    'creator' => [
        'created_by' => '👨‍🎨 Creato da:',
    ],

    // Prezzi
    'price' => [
        'purchased_for' => '💳 Acquistato per',
        'price' => '💰 Prezzo',
        'floor' => '📊 Floor',
    ],

    // Stati
    'status' => [
        'not_for_sale' => '🚫 Non in vendita',
        'draft' => '⏳ Bozza',
    ],

    // Azioni
    'actions' => [
        'view' => 'Visualizza',
        'view_details' => 'Visualizza dettagli EGI',
        'reserve' => 'Prenota',
        'reserved' => 'Prenotato',
        'view_history' => 'Cronologia',
        'reserve_egi' => 'Prenota :title',
    ],

    // Sistema cronologia prenotazioni
    'history' => [
        'title' => 'Cronologia Prenotazioni',
        'no_reservations' => 'Nessuna prenotazione trovata',
        'total_reservations' => '{1} :count prenotazione|[2,*] :count prenotazioni',
        'current_highest' => 'Priorità massima attuale',
        'superseded' => 'Priorità inferiore',
        'created_at' => 'Creato il',
        'amount' => 'Importo',
        'type_strong' => 'Prenotazione forte',
        'type_weak' => 'Prenotazione debole',
        'loading' => 'Caricamento cronologia...',
        'error' => 'Errore nel caricamento della cronologia',
    ],

    // Sezioni Informative
    'properties' => 'Proprietà',
    'supports_epp' => 'Supporta EPP',
    'asset_type' => 'Tipo Asset',
    'format' => 'Formato',
    'about_this_piece' => 'Su Quest\'Opera',
    'default_description' => 'Questa opera digitale unica rappresenta un momento di espressione creativa, catturando l\'essenza dell\'arte digitale nell\'era blockchain.',
    'provenance' => 'Provenienza',
    'view_full_collection' => 'Visualizza Collezione Completa',

    /*
    |--------------------------------------------------------------------------
    | CRUD System - Sistema di Modifica
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Header e Navigation
        'edit_egi' => 'Modifica EGI',
        'toggle_edit_mode' => 'Attiva/Disattiva Modalità Modifica',
        'start_editing' => 'Inizia Modifica',
        'save_changes' => 'Salva Modifiche',
        'cancel' => 'Annulla',

        // Campo Title
        'title' => 'Titolo',
        'title_placeholder' => 'Inserisci il titolo dell\'opera...',
        'title_hint' => 'Massimo 60 caratteri',
        'characters_remaining' => 'caratteri rimanenti',

        // Campo Description
        'description' => 'Descrizione',
        'description_placeholder' => 'Descrivi la tua opera, la sua storia e il suo significato...',
        'description_hint' => 'Racconta la storia dietro la tua creazione',

        // Campo Price
        'price' => 'Prezzo',
        'price_placeholder' => '0.00',
        'price_hint' => 'Prezzo in ALGO (lascia vuoto se non in vendita)',

        // Campo Creation Date
        'creation_date' => 'Data Creazione',
        'creation_date_hint' => 'Quando hai creato quest\'opera?',

        // Campo Published
        'is_published' => 'Pubblicato',
        'is_published_hint' => 'Rendi l\'opera visibile pubblicamente',

        // View Mode - Stato Attuale
        'current_title' => 'Titolo Attuale',
        'no_title' => 'Nessun titolo impostato',
        'current_price' => 'Prezzo Attuale',
        'price_not_set' => 'Prezzo non impostato',
        'current_status' => 'Stato Pubblicazione',
        'status_published' => 'Pubblicato',
        'status_draft' => 'Bozza',

        // Delete System
        'delete_egi' => 'Elimina EGI',
        'delete_confirmation_title' => 'Conferma Eliminazione',
        'delete_confirmation_message' => 'Sei sicuro di voler eliminare quest\'EGI? Questa azione non può essere annullata.',
        'delete_confirm' => 'Elimina Definitivamente',

        // Validation Messages
        'title_required' => 'Il titolo è obbligatorio',
        'title_max_length' => 'Il titolo non può superare i 60 caratteri',
        'price_numeric' => 'Il prezzo deve essere un numero valido',
        'price_min' => 'Il prezzo non può essere negativo',
        'creation_date_format' => 'Formato data non valido',

        // Success Messages
        'update_success' => 'EGI aggiornato con successo!',
        'delete_success' => 'EGI eliminato con successo.',

        // Error Messages
        'update_error' => 'Errore durante l\'aggiornamento dell\'EGI.',
        'delete_error' => 'Errore durante l\'eliminazione dell\'EGI.',
        'permission_denied' => 'Non hai i permessi necessari per questa azione.',
        'not_found' => 'EGI non trovato.',

        // General Messages
        'no_changes_detected' => 'Nessuna modifica rilevata.',
        'unsaved_changes_warning' => 'Hai modifiche non salvate. Sei sicuro di voler uscire?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Labels - Mobile/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Modifica',
        'save_short' => 'Salva',
        'delete_short' => 'Elimina',
        'cancel_short' => 'Annulla',
        'published_short' => 'Pubbl.',
        'draft_short' => 'Bozza',
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Labels - Screen Readers
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'Modulo di modifica EGI',
        'delete_button' => 'Pulsante elimina EGI',
        'toggle_edit' => 'Attiva modalità modifica',
        'save_form' => 'Salva modifiche EGI',
        'close_modal' => 'Chiudi finestra di conferma',
        'required_field' => 'Campo obbligatorio',
        'optional_field' => 'Campo opzionale',
    ],

];
