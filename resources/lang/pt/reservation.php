<?php

return [
    // Messaggi di successo
    'success' => 'Sua reserva foi realizada com sucesso! O certificado foi gerado.',
    'cancel_success' => 'Sua reserva foi cancelada com sucesso.',

    // Errori
    'unauthorized' => 'Você precisa conectar sua carteira ou fazer login para realizar uma reserva.',
    'validation_failed' => 'Por favor, verifique os dados inseridos e tente novamente.',
    'auth_required' => 'Autenticação necessária para visualizar suas reservas.',
    'list_failed' => 'Não foi possível recuperar suas reservas. Tente novamente mais tarde.',
    'status_failed' => 'Não foi possível recuperar o status da reserva. Tente novamente mais tarde.',
    'unauthorized_cancel' => 'Você não tem permissão para cancelar esta reserva.',
    'cancel_failed' => 'Não foi possível cancelar a reserva. Tente novamente mais tarde.',

    // Formulario
    'form' => [
        'title' => 'Reservar este EGI',
        'offer_amount_label' => 'Sua Oferta (EUR)',
        'offer_amount_placeholder' => 'Insira o valor em EUR',
        'algo_equivalent' => 'Aproximadamente :amount ALGO',
        'terms_accepted' => 'Aceito os termos e condições para reservas de EGI',
        'contact_info' => 'Informações de Contato Adicionais (opcional)',
        'submit_button' => 'Fazer Reserva',
        'cancel_button' => 'Cancelar'
    ],

    // Errori specifici
    'errors' => [
        'RESERVATION_EGI_NOT_AVAILABLE' => 'Este EGI não está disponível para reserva no momento.',
        'RESERVATION_AMOUNT_TOO_LOW' => 'O valor da sua oferta é muito baixo. Insira um valor mais alto.',
        'RESERVATION_UNAUTHORIZED' => 'Você precisa conectar sua carteira ou fazer login para realizar uma reserva.',
        'RESERVATION_CERTIFICATE_GENERATION_FAILED' => 'Não conseguimos gerar seu certificado de reserva. Nossa equipe foi notificada.',
        'RESERVATION_CERTIFICATE_NOT_FOUND' => 'O certificado solicitado não foi encontrado.',
        'RESERVATION_ALREADY_EXISTS' => 'Você já possui uma reserva ativa para este EGI.',
        'RESERVATION_CANCEL_FAILED' => 'Não conseguimos cancelar sua reserva. Tente novamente mais tarde.',
        'RESERVATION_UNAUTHORIZED_CANCEL' => 'Você não tem permissão para cancelar esta reserva.',
        'RESERVATION_STATUS_FAILED' => 'Não foi possível recuperar o status da reserva. Tente novamente mais tarde.',
        'RESERVATION_UNKNOWN_ERROR' => 'Algo deu errado com sua reserva. Nossa equipe foi notificada.'
    ],

    // Badge e status descriptor
    'type' => [
        'strong' => 'Reserva Forte',
        'weak' => 'Reserva Básica'
    ],
    'status' => [
        'active' => 'Ativa',
        'cancelled' => 'Cancelada',
        'expired' => 'Expirada',
        'superseded' => 'Substituída'
    ],
    'priority' => [
        'highest' => 'Prioridade Máxima',
        'superseded' => 'Prioridade Inferior'
    ]
];
