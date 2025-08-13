<?php

return [
    // Titoli e descrizioni pagine
    'page_title' => 'Certificado de Reserva #:uuid',
    'meta_description' => 'Certificado de Reserva :type para EGI - FlorenceEGI',
    'verify_page_title' => 'Verificar Certificado #:uuid',
    'verify_meta_description' => 'Verifica la autenticidad del certificado de reserva EGI #:uuid en FlorenceEGI',
    'list_by_egi_title' => 'Certificados para EGI #:egi_id',
    'list_by_egi_meta_description' => 'Ver todos los certificados de reserva para EGI #:egi_id en FlorenceEGI',
    'user_certificates_title' => 'Tus Certificados de Reserva',
    'user_certificates_meta_description' => 'Ver todos tus certificados de reserva EGI en FlorenceEGI',

    // Messaggi errore
    'not_found' => 'El certificado solicitado no se encontró.',
    'download_failed' => 'No se pudo descargar el PDF del certificado. Inténtalo de nuevo más tarde.',
    'verification_failed' => 'No se pudo verificar el certificado. Puede que no sea válido o ya no exista.',
    'list_failed' => 'No se pudo recuperar la lista de certificados.',
    'auth_required' => 'Inicia sesión para ver tus certificados.',

    // Dettagli certificato
    'details' => [
        'title' => 'Detalles del Certificado',
        'egi_title' => 'Título del EGI',
        'collection' => 'Colección',
        'reservation_type' => 'Tipo de Reserva',
        'wallet_address' => 'Dirección de la Cartera',
        'offer_amount_fiat' => 'Monto de la Oferta (EUR)',
        'offer_amount_algo' => 'Monto de la Oferta (ALGO)',
        'certificate_uuid' => 'UUID del Certificado',
        'signature_hash' => 'Hash de la Firma',
        'created_at' => 'Creado el',
        'status' => 'Estado',
        'priority' => 'Prioridad'
    ],

    // Azioni
    'actions' => [
        'download_pdf' => 'Descargar PDF',
        'verify' => 'Verificar Certificado',
        'view_egi' => 'Ver EGI',
        'back_to_list' => 'Volver a Certificados',
        'share' => 'Compartir Certificado'
    ],

    // Verifica
    'verification' => [
        'title' => 'Resultado de la Verificación del Certificado',
        'valid' => 'Este certificado es válido y auténtico.',
        'invalid' => 'Este certificado parece no ser válido o ha sido alterado.',
        'highest_priority' => 'Este certificado representa la reserva de mayor prioridad para este EGI.',
        'not_highest_priority' => 'Este certificado ha sido superado por una reserva de mayor prioridad.',
        'egi_available' => 'El EGI para esta reserva aún está disponible.',
        'egi_not_available' => 'El EGI para esta reserva ha sido acuñado o ya no está disponible.',
        'what_this_means' => 'Qué Significa Esto',
        'explanation_valid' => 'Este certificado fue emitido por FlorenceEGI y no ha sido modificado.',
        'explanation_invalid' => 'Los datos del certificado no coinciden con la firma. Puede haber sido modificado.',
        'explanation_priority' => 'Se ha realizado una reserva de mayor prioridad (tipo fuerte o monto más alto) después de esta.',
        'explanation_not_available' => 'El EGI ha sido acuñado o ya no está disponible para reserva.'
    ],

    // Altro
    'unknown_egi' => 'EGI Desconocido',
    'no_certificates' => 'No se encontraron certificados.',
    'success_message' => '¡Reserva exitosa! Aquí está tu certificado.',
    'created_just_now' => 'Creado ahora',
    'qr_code_alt' => 'Código QR para la verificación del certificado'
];
