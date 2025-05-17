<?php

return [
    // Titoli e descrizioni pagine
    'page_title' => 'Reservation Certificate #:uuid',
    'meta_description' => ':type Reservation Certificate for EGI - FlorenceEGI',
    'verify_page_title' => 'Verify Certificate #:uuid',
    'verify_meta_description' => 'Verify the authenticity of EGI reservation certificate #:uuid on FlorenceEGI',
    'list_by_egi_title' => 'Certificates for EGI #:egi_id',
    'list_by_egi_meta_description' => 'View all reservation certificates for EGI #:egi_id on FlorenceEGI',
    'user_certificates_title' => 'Your Reservation Certificates',
    'user_certificates_meta_description' => 'View all your EGI reservation certificates on FlorenceEGI',

    // Messaggi errore
    'not_found' => 'The certificate you requested could not be found.',
    'download_failed' => 'Could not download the certificate PDF. Please try again later.',
    'verification_failed' => 'Could not verify the certificate. It may be invalid or no longer exist.',
    'list_failed' => 'Could not retrieve the certificate list.',
    'auth_required' => 'Please log in to view your certificates.',

    // Dettagli certificato
    'details' => [
        'title' => 'Certificate Details',
        'egi_title' => 'EGI Title',
        'collection' => 'Collection',
        'reservation_type' => 'Reservation Type',
        'wallet_address' => 'Wallet Address',
        'offer_amount_eur' => 'Offer Amount (EUR)',
        'offer_amount_algo' => 'Offer Amount (ALGO)',
        'certificate_uuid' => 'Certificate UUID',
        'signature_hash' => 'Signature Hash',
        'created_at' => 'Created At',
        'status' => 'Status',
        'priority' => 'Priority'
    ],

    // Azioni
    'actions' => [
        'download_pdf' => 'Download PDF',
        'verify' => 'Verify Certificate',
        'view_egi' => 'View EGI',
        'back_to_list' => 'Back to Certificates',
        'share' => 'Share Certificate'
    ],

    // Verifica
    'verification' => [
        'title' => 'Certificate Verification Result',
        'valid' => 'This certificate is valid and authentic.',
        'invalid' => 'This certificate appears to be invalid or has been tampered with.',
        'highest_priority' => 'This certificate represents the highest priority reservation for this EGI.',
        'not_highest_priority' => 'This certificate has been superseded by a higher priority reservation.',
        'egi_available' => 'The EGI for this reservation is still available.',
        'egi_not_available' => 'The EGI for this reservation has been minted or is no longer available.',
        'what_this_means' => 'What This Means',
        'explanation_valid' => 'This certificate was issued by FlorenceEGI and has not been modified.',
        'explanation_invalid' => 'The certificate data does not match the signature. It may have been modified.',
        'explanation_priority' => 'A higher priority reservation (strong type or higher amount) has been made after this one.',
        'explanation_not_available' => 'The EGI has been minted or is no longer available for reservation.'
    ],

    // Altro
    'unknown_egi' => 'Unknown EGI',
    'no_certificates' => 'No certificates found.',
    'success_message' => 'Reservation successful! Here is your certificate.',
    'created_just_now' => 'Created just now',
    'qr_code_alt' => 'QR Code for certificate verification'
];
