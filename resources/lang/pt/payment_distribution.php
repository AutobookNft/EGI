<?php

return [
    // Tipos de Usuário
    'user_types' => [
        'weak' => 'Usuários Autenticação Fraca',
        'creator' => 'Criadores de Conteúdo',
        'collector' => 'Colecionadores Privados',
        'commissioner' => 'Colecionadores Públicos',
        'company' => 'Entidades Empresariais',
        'epp' => 'Projetos Proteção Ambiental',
        'trader_pro' => 'Traders Profissionais',
        'vip' => 'Usuários VIP',
    ],

    // Descrições Tipos de Usuário
    'user_types_desc' => [
        'weak' => 'Usuários com autenticação apenas por carteira',
        'creator' => 'Artistas e criadores de conteúdo',
        'collector' => 'Colecionadores privados e entusiastas',
        'commissioner' => 'Colecionadores públicos com visibilidade',
        'company' => 'Entidades empresariais e organizações',
        'epp' => 'Projetos de proteção ambiental',
        'trader_pro' => 'Operadores profissionais do mercado',
        'vip' => 'Usuários com status privilegiado',
    ],

    // Status de Distribuição
    'status' => [
        'pending' => 'Pendente de Processamento',
        'processed' => 'Processado com Sucesso',
        'confirmed' => 'Confirmado na Blockchain',
        'failed' => 'Processamento Falhado',
    ],

    // Descrições Status de Distribuição
    'status_desc' => [
        'pending' => 'Distribuição criada mas ainda não processada',
        'processed' => 'Distribuição processada com sucesso off-chain',
        'confirmed' => 'Distribuição confirmada na blockchain',
        'failed' => 'Processamento de distribuição falhado',
    ],
];
