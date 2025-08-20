<?php

return [
    // Tipos de Usuario
    'user_types' => [
        'weak' => 'Usuarios Autenticación Débil',
        'creator' => 'Creadores de Contenido',
        'collector' => 'Coleccionistas Privados',
        'commissioner' => 'Coleccionistas Públicos',
        'company' => 'Entidades Empresariales',
        'epp' => 'Proyectos Protección Ambiental',
        'trader_pro' => 'Traders Profesionales',
        'vip' => 'Usuarios VIP',
    ],

    // Descripciones Tipos de Usuario
    'user_types_desc' => [
        'weak' => 'Usuarios con autenticación solo por wallet',
        'creator' => 'Artistas y creadores de contenido',
        'collector' => 'Coleccionistas privados y entusiastas',
        'commissioner' => 'Coleccionistas públicos con visibilidad',
        'company' => 'Entidades empresariales y organizaciones',
        'epp' => 'Proyectos de protección ambiental',
        'trader_pro' => 'Operadores profesionales del mercado',
        'vip' => 'Usuarios con estatus privilegiado',
    ],

    // Estado de Distribución
    'status' => [
        'pending' => 'Pendiente de Procesamiento',
        'processed' => 'Procesado Exitosamente',
        'confirmed' => 'Confirmado en Blockchain',
        'failed' => 'Procesamiento Fallido',
    ],

    // Descripciones Estado de Distribución
    'status_desc' => [
        'pending' => 'Distribución creada pero aún no procesada',
        'processed' => 'Distribución procesada exitosamente off-chain',
        'confirmed' => 'Distribución confirmada en blockchain',
        'failed' => 'Procesamiento de distribución fallido',
    ],
];
