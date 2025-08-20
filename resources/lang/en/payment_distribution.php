<?php

return [
    // User Types
    'user_types' => [
        'weak' => 'Weak Authentication Users',
        'creator' => 'Content Creators',
        'collector' => 'Private Collectors',
        'commissioner' => 'Public Collectors',
        'company' => 'Business Entities',
        'epp' => 'Environmental Protection Projects',
        'trader_pro' => 'Professional Traders',
        'vip' => 'VIP Users',
    ],

    // User Types Descriptions
    'user_types_desc' => [
        'weak' => 'Users with wallet-only authentication',
        'creator' => 'Artists and content creators',
        'collector' => 'Private collectors and enthusiasts',
        'commissioner' => 'Public collectors with visibility',
        'company' => 'Business entities and organizations',
        'epp' => 'Environmental protection projects',
        'trader_pro' => 'Professional market operators',
        'vip' => 'Users with privileged status',
    ],

    // Distribution Status
    'status' => [
        'pending' => 'Pending Processing',
        'processed' => 'Processed Successfully',
        'confirmed' => 'Blockchain Confirmed',
        'failed' => 'Processing Failed',
    ],

    // Distribution Status Descriptions
    'status_desc' => [
        'pending' => 'Distribution created but not yet processed',
        'processed' => 'Distribution processed successfully off-chain',
        'confirmed' => 'Distribution confirmed on blockchain',
        'failed' => 'Distribution processing failed',
    ],
];
