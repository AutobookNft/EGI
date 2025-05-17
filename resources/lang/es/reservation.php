<?php

return [
    // Messaggi di successo
    'success' => '¡Tu reserva fue exitosa! El certificado ha sido generado.',
    'cancel_success' => 'Tu reserva ha sido cancelada con éxito.',

    // Errori
    'unauthorized' => 'Debes conectar tu cartera o iniciar sesión para hacer una reserva.',
    'validation_failed' => 'Por favor, revisa los datos ingresados y vuelve a intentarlo.',
    'auth_required' => 'Se requiere autenticación para ver tus reservas.',
    'list_failed' => 'No se pudieron recuperar tus reservas. Inténtalo de nuevo más tarde.',
    'status_failed' => 'No se pudo recuperar el estado de la reserva. Inténtalo de nuevo más tarde.',
    'unauthorized_cancel' => 'No tienes permiso para cancelar esta reserva.',
    'cancel_failed' => 'No se pudo cancelar la reserva. Inténtalo de nuevo más tarde.',

    // Formulario
    'form' => [
        'title' => 'Reservar este EGI',
        'offer_amount_label' => 'Tu oferta (EUR)',
        'offer_amount_placeholder' => 'Ingresa el monto en EUR',
        'algo_equivalent' => 'Aproximadamente :amount ALGO',
        'terms_accepted' => 'Acepto los términos y condiciones para las reservas de EGI',
        'contact_info' => 'Información de contacto adicional (opcional)',
        'submit_button' => 'Hacer Reserva',
        'cancel_button' => 'Cancelar'
    ],

    // Errori specifici
    'errors' => [
        'RESERVATION_EGI_NOT_AVAILABLE' => 'Este EGI no está disponible para reservar en este momento.',
        'RESERVATION_AMOUNT_TOO_LOW' => 'El monto de tu oferta es demasiado bajo. Ingresa un monto más alto.',
        'RESERVATION_UNAUTHORIZED' => 'Debes conectar tu cartera o iniciar sesión para hacer una reserva.',
        'RESERVATION_CERTIFICATE_GENERATION_FAILED' => 'No pudimos generar tu certificado de reserva. Nuestro equipo ha sido notificado.',
        'RESERVATION_CERTIFICATE_NOT_FOUND' => 'No se encontró el certificado solicitado.',
        'RESERVATION_ALREADY_EXISTS' => 'Ya tienes una reserva activa para este EGI.',
        'RESERVATION_CANCEL_FAILED' => 'No pudimos cancelar tu reserva. Inténtalo de nuevo más tarde.',
        'RESERVATION_UNAUTHORIZED_CANCEL' => 'No tienes permiso para cancelar esta reserva.',
        'RESERVATION_STATUS_FAILED' => 'No se pudo recuperar el estado de la reserva. Inténtalo de nuevo más tarde.',
        'RESERVATION_UNKNOWN_ERROR' => 'Algo salió mal con tu reserva. Nuestro equipo ha sido notificado.'
    ],

    // Badge e status descriptor
    'type' => [
        'strong' => 'Reserva Fuerte',
        'weak' => 'Reserva Básica'
    ],
    'status' => [
        'active' => 'Activa',
        'cancelled' => 'Cancelada',
        'expired' => 'Expirada',
        'superseded' => 'Superada'
    ],
    'priority' => [
        'highest' => 'Prioridad Máxima',
        'superseded' => 'Prioridad Inferior'
    ]
];
