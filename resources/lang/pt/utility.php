<?php

return [
    // Títulos e cabeçalhos
    'title' => 'Gestão de Utilidade',
    'subtitle' => 'Adicione valor real ao seu EGI',
    'status_configured' => 'Utilidade Configurada',
    'status_none' => 'Sem Utilidade',
    
    // Alertas e mensagens
    'info_edit_before_publish' => 'A utilidade só pode ser adicionada ou modificada antes da publicação da coleção. Uma vez publicada, não pode mais ser modificada.',
    'success_created' => 'Utilidade adicionada com sucesso!',
    'success_updated' => 'Utilidade atualizada com sucesso!',
    'confirm_reset' => 'Tem certeza de que quer cancelar? As mudanças não salvas serão perdidas.',
    'confirm_remove_image' => 'Remover esta imagem?',
    'note' => 'Nota',
    
    // Tipos de utilidade
    'types' => [
        'label' => 'Tipo de Utilidade',
        'physical' => [
            'label' => 'Bem Físico',
            'description' => 'Objeto físico para enviar (quadro, escultura, etc.)'
        ],
        'service' => [
            'label' => 'Serviço',
            'description' => 'Serviço ou experiência (workshop, consultoria, etc.)'
        ],
        'hybrid' => [
            'label' => 'Híbrido',
            'description' => 'Combinação físico + serviço'
        ],
        'digital' => [
            'label' => 'Digital',
            'description' => 'Conteúdo ou acesso digital'
        ],
        'remove' => 'Remover Utilidade'
    ],
    
    // Campos do formulário base
    'fields' => [
        'title' => 'Título da Utilidade',
        'title_placeholder' => 'Ex: Quadro Original 50x70cm',
        'description' => 'Descrição Detalhada',
        'description_placeholder' => 'Descreva em detalhes o que o comprador receberá...',
    ],
    
    // Seção de envio
    'shipping' => [
        'title' => 'Detalhes de Envio',
        'weight' => 'Peso (kg)',
        'dimensions' => 'Dimensões (cm)',
        'length' => 'Comprimento',
        'width' => 'Largura',
        'height' => 'Altura',
        'days' => 'Dias de preparação/envio',
        'fragile' => 'Objeto Frágil',
        'insurance' => 'Seguro Recomendado',
        'notes' => 'Notas de Envio',
        'notes_placeholder' => 'Instruções especiais para embalagem ou envio...'
    ],
    
    // Seção de serviço
    'service' => [
        'title' => 'Detalhes do Serviço',
        'valid_from' => 'Válido A Partir De',
        'valid_until' => 'Válido Até',
        'max_uses' => 'Número Máximo de Usos',
        'max_uses_placeholder' => 'Deixe vazio para ilimitado',
        'instructions' => 'Instruções de Ativação',
        'instructions_placeholder' => 'Como o comprador pode usar o serviço...'
    ],
    
    // Escrow
    'escrow' => [
        'immediate' => [
            'label' => 'Pagamento Imediato',
            'description' => 'Sem escrow, pagamento direto ao criador'
        ],
        'standard' => [
            'label' => 'Escrow Padrão',
            'description' => 'Fundos liberados após 14 dias da entrega',
            'requirement_tracking' => 'Rastreamento obrigatório'
        ],
        'premium' => [
            'label' => 'Escrow Premium',
            'description' => 'Fundos liberados após 21 dias da entrega',
            'requirement_tracking' => 'Rastreamento obrigatório',
            'requirement_signature' => 'Assinatura na entrega',
            'requirement_insurance' => 'Seguro recomendado'
        ]
    ],
    
    // Media/Galeria
    'media' => [
        'title' => 'Galeria de Imagens Detalhes',
        'description' => 'Adicione fotos do objeto de vários ângulos, detalhes importantes, certificados de autenticidade, etc. (Máx 10 imagens)',
        'upload_prompt' => 'Clique para carregar ou arraste as imagens aqui',
        'current_images' => 'Imagens Atuais:',
        'remove_image' => 'Remover'
    ],
    
    // Erros de validação
    'validation' => [
        'title_required' => 'O título é obrigatório',
        'type_required' => 'Selecione um tipo de utilidade',
        'weight_required' => 'O peso é obrigatório para bens físicos',
        'valid_until_after' => 'A data de fim deve ser posterior à data de início'
    ]
];
