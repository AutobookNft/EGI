<?php

return [
    // Titoli e descrizioni pagine
    'page_title' => 'Certificado de Reserva #:uuid',
    'meta_description' => 'Certificado de Reserva :type para EGI - FlorenceEGI',
    'verify_page_title' => 'Verificar Certificado #:uuid',
    'verify_meta_description' => 'Verifique a autenticidade do certificado de reserva EGI #:uuid no FlorenceEGI',
    'list_by_egi_title' => 'Certificados para EGI #:egi_id',
    'list_by_egi_meta_description' => 'Veja todos os certificados de reserva para EGI #:egi_id no FlorenceEGI',
    'user_certificates_title' => 'Seus Certificados de Reserva',
    'user_certificates_meta_description' => 'Veja todos os seus certificados de reserva EGI no FlorenceEGI',

    // Messaggi errore
    'not_found' => 'O certificado solicitado não foi encontrado.',
    'download_failed' => 'Não foi possível baixar o PDF do certificado. Tente novamente mais tarde.',
    'verification_failed' => 'Não foi possível verificar o certificado. Ele pode ser inválido ou não existir mais.',
    'list_failed' => 'Não foi possível recuperar a lista de certificados.',
    'auth_required' => 'Faça login para visualizar seus certificados.',

    // Dettagli certificato
    'details' => [
        'title' => 'Detalhes do Certificado',
        'egi_title' => 'Título do EGI',
        'collection' => 'Coleção',
        'reservation_type' => 'Tipo de Reserva',
        'wallet_address' => 'Endereço da Carteira',
        'offer_amount_fiat' => 'Valor da Oferta (EUR)',
        'offer_amount_algo' => 'Valor da Oferta (ALGO)',
        'certificate_uuid' => 'UUID do Certificado',
        'signature_hash' => 'Hash da Assinatura',
        'created_at' => 'Criado em',
        'status' => 'Status',
        'priority' => 'Prioridade'
    ],

    // Azioni
    'actions' => [
        'download_pdf' => 'Baixar PDF',
        'verify' => 'Verificar Certificado',
        'view_egi' => 'Ver EGI',
        'back_to_list' => 'Voltar para Certificados',
        'share' => 'Compartilhar Certificado'
    ],

    // Verifica
    'verification' => [
        'title' => 'Resultado da Verificação do Certificado',
        'valid' => 'Este certificado é válido e autêntico.',
        'invalid' => 'Este certificado parece ser inválido ou foi adulterado.',
        'highest_priority' => 'Este certificado representa a reserva de maior prioridade para este EGI.',
        'not_highest_priority' => 'Este certificado foi superado por uma reserva de maior prioridade.',
        'egi_available' => 'O EGI para esta reserva ainda está disponível.',
        'egi_not_available' => 'O EGI para esta reserva foi cunhado ou não está mais disponível.',
        'what_this_means' => 'O Que Isso Significa',
        'ex “‘explanation_valid' => 'Este certificado foi emitido pelo FlorenceEGI e não foi modificado.',
        'explanation_invalid' => 'Os dados do certificado não correspondem à assinatura. Ele pode ter sido modificado.',
        'explanation_priority' => 'Foi feita uma reserva de maior prioridade (tipo forte ou valor mais alto) após esta.',
        'explanation_not_available' => 'O EGI foi cunhado ou não está mais disponível para reserva.'
    ],

    // Altro
    'unknown_egi' => 'EGI Desconhecido',
    'no_certificates' => 'Nenhum certificado encontrado.',
    'success_message' => 'Reserva bem-sucedida! Aqui está o seu certificado.',
    'created_just_now' => 'Criado agora',
    'qr_code_alt' => 'Código QR para verificação do certificado'
];
