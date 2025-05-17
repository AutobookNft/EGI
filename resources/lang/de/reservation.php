<?php

return [
    // Messaggi di successo
    'success' => 'Ihre Reservierung war erfolgreich! Das Zertifikat wurde generiert.',
    'cancel_success' => 'Ihre Reservierung wurde erfolgreich storniert.',

    // Errori
    'unauthorized' => 'Sie müssen Ihre Wallet verbinden oder sich anmelden, um eine Reservierung vorzunehmen.',
    'validation_failed' => 'Bitte überprüfen Sie Ihre Eingaben und versuchen Sie es erneut.',
    'auth_required' => 'Authentifizierung erforderlich, um Ihre Reservierungen anzuzeigen.',
    'list_failed' => 'Ihre Reservierungen konnten nicht abgerufen werden. Bitte versuchen Sie es später erneut.',
    'status_failed' => 'Der Status der Reservierung konnte nicht abgerufen werden. Bitte versuchen Sie es später erneut.',
    'unauthorized_cancel' => 'Sie haben keine Berechtigung, diese Reservierung zu stornieren.',
    'cancel_failed' => 'Die Reservierung konnte nicht storniert werden. Bitte versuchen Sie es später erneut.',

    // Formulario
    'form' => [
        'title' => 'Diesen EGI reservieren',
        'offer_amount_label' => 'Ihr Angebot (EUR)',
        'offer_amount_placeholder' => 'Betrag in EUR eingeben',
        'algo_equivalent' => 'Ungefähr :amount ALGO',
        'terms_accepted' => 'Ich akzeptiere die Bedingungen für EGI-Reservierungen',
        'contact_info' => 'Zusätzliche Kontaktinformationen (optional)',
        'submit_button' => 'Reservierung vornehmen',
        'cancel_button' => 'Abbrechen'
    ],

    // Errori specifici
    'errors' => [
        'RESERVATION_EGI_NOT_AVAILABLE' => 'Dieser EGI ist derzeit nicht für eine Reservierung verfügbar.',
        'RESERVATION_AMOUNT_TOO_LOW' => 'Ihr Angebot ist zu niedrig. Bitte geben Sie einen höheren Betrag ein.',
        'RESERVATION_UNAUTHORIZED' => 'Sie müssen Ihre Wallet verbinden oder sich anmelden, um eine Reservierung vorzunehmen.',
        'RESERVATION_CERTIFICATE_GENERATION_FAILED' => 'Wir konnten Ihr Reservierungszertifikat nicht generieren. Unser Team wurde informiert.',
        'RESERVATION_CERTIFICATE_NOT_FOUND' => 'Das angeforderte Zertifikat wurde nicht gefunden.',
        'RESERVATION_ALREADY_EXISTS' => 'Sie haben bereits eine aktive Reservierung für diesen EGI.',
        'RESERVATION_CANCEL_FAILED' => 'Wir konnten Ihre Reservierung nicht stornieren. Bitte versuchen Sie es später erneut.',
        'RESERVATION_UNAUTHORIZED_CANCEL' => 'Sie haben keine Berechtigung, diese Reservierung zu stornieren.',
        'RESERVATION_STATUS_FAILED' => 'Der Status der Reservierung konnte nicht abgerufen werden. Bitte versuchen Sie es später erneut.',
        'RESERVATION_UNKNOWN_ERROR' => 'Etwas ist mit Ihrer Reservierung schiefgelaufen. Unser Team wurde informiert.'
    ],

    // Badge e status descriptor
    'type' => [
        'strong' => 'Starke Reservierung',
        'weak' => 'Grundlegende Reservierung'
    ],
    'status' => [
        'active' => 'Aktiv',
        'cancelled' => 'Storniert',
        'expired' => 'Abgelaufen',
        'superseded' => 'Überschritten'
    ],
    'priority' => [
        'highest' => 'Höchste Priorität',
        'superseded' => 'Niedrigere Priorität'
    ]
];
