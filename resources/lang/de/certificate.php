<?php

return [
    // Titoli e descrizioni pagine
    'page_title' => 'Reservierungszertifikat #:uuid',
    'meta_description' => ':type Reservierungszertifikat für EGI - FlorenceEGI',
    'verify_page_title' => 'Zertifikat #:uuid Verifizieren',
    'verify_meta_description' => 'Überprüfen Sie die Echtheit des EGI-Reservierungszertifikats #:uuid auf FlorenceEGI',
    'list_by_egi_title' => 'Zertifikate für EGI #:egi_id',
    'list_by_egi_meta_description' => 'Alle Reservierungszertifikate für EGI #:egi_id auf FlorenceEGI anzeigen',
    'user_certificates_title' => 'Ihre Reservierungszertifikate',
    'user_certificates_meta_description' => 'Alle Ihre EGI-Reservierungszertifikate auf FlorenceEGI anzeigen',

    // Messaggi errore
    'not_found' => 'Das angeforderte Zertifikat konnte nicht gefunden werden.',
    'download_failed' => 'Das Zertifikat-PDF konnte nicht heruntergeladen werden. Bitte versuchen Sie es später erneut.',
    'verification_failed' => 'Das Zertifikat konnte nicht verifiziert werden. Es könnte ungültig sein oder nicht mehr existieren.',
    'list_failed' => 'Die Liste der Zertifikate konnte nicht abgerufen werden.',
    'auth_required' => 'Bitte melden Sie sich an, um Ihre Zertifikate zu sehen.',

    // Dettagli certificato
    'details' => [
        'title' => 'Zertifikatdetails',
        'egi_title' => 'EGI-Titel',
        'collection' => 'Sammlung',
        'reservation_type' => 'Reservierungsart',
        'wallet_address' => 'Wallet-Adresse',
        'offer_amount_eur' => 'Angebotsbetrag (EUR)',
        'offer_amount_algo' => 'Angebotsbetrag (ALGO)',
        'certificate_uuid' => 'Zertifikat-UUID',
        'signature_hash' => 'Signatur-Hash',
        'created_at' => 'Erstellt am',
        'status' => 'Status',
        'priority' => 'Priorität'
    ],

    // Azioni
    'actions' => [
        'download_pdf' => 'PDF Herunterladen',
        'verify' => 'Zertifikat Verifizieren',
        'view_egi' => 'EGI Anzeigen',
        'back_to_list' => 'Zurück zu Zertifikaten',
        'share' => 'Zertifikat Teilen'
    ],

    // Verifica
    'verification' => [
        'title' => 'Ergebnis der Zertifikatverifizierung',
        'valid' => 'Dieses Zertifikat ist gültig und authentisch.',
        'invalid' => 'Dieses Zertifikat scheint ungültig zu sein oder wurde manipuliert.',
        'highest_priority' => 'Dieses Zertifikat repräsentiert die Reservierung mit der höchsten Priorität für diesen EGI.',
        'not_highest_priority' => 'Dieses Zertifikat wurde durch eine Reservierung mit höherer Priorität überholt.',
        'egi_available' => 'Der EGI für diese Reservierung ist noch verfügbar.',
        'egi_not_available' => 'Der EGI für diese Reservierung wurde geprägt oder ist nicht mehr verfügbar.',
        'what_this_means' => 'Was das bedeutet',
        'explanation_valid' => 'Dieses Zertifikat wurde von FlorenceEGI ausgestellt und nicht verändert.',
        'explanation_invalid' => 'Die Zertifikatdaten stimmen nicht mit der Signatur überein. Es könnte verändert worden sein.',
        'explanation_priority' => 'Nach dieser wurde eine Reservierung mit höherer Priorität (starker Typ oder höherer Betrag) vorgenommen.',
        'explanation_not_available' => 'Der EGI wurde geprägt oder ist nicht mehr für eine Reservierung verfügbar.'
    ],

    // Altro
    'unknown_egi' => 'Unbekannter EGI',
    'no_certificates' => 'Keine Zertifikate gefunden.',
    'success_message' => 'Reservierung erfolgreich! Hier ist Ihr Zertifikat.',
    'created_just_now' => 'Gerade erstellt',
    'qr_code_alt' => 'QR-Code zur Zertifikatverifizierung'
];
