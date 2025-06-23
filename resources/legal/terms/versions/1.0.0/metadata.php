<?php

/**
 * @Oracode Legal Metadata: Version 1.0.0
 * 🎯 Purpose: Centralized metadata for legal document version management
 * 🛡️ Security: Immutable version tracking with cryptographic integrity
 *
 * @package FlorenceEGI\Legal
 * @version 1.0.0 - OS2.0 Initial Implementation
 */

return [
    'version' => '1.0.0',
    'release_date' => '2025-06-22',
    'effective_date' => '2025-06-30',
    'created_by' => 'legal@florenceegi.com',
    'approved_by' => 'fabio.cherici@florenceegi.com',

    'summary_of_changes' => 'Initial release of versioned legal document system',

    'change_details' => [
        'migration' => 'Conversion from hardcoded blade template to versioned PHP structure',
        'new_features' => [
            'User type differentiated terms',
            'Jurisdiction-specific clauses support',
            'Professional Creator terms with KYC/KYB requirements',
            'Egili tokenomics integration',
            'EPP contribution framework'
        ],
        'compliance_updates' => [
            'GDPR Article 7 consent documentation',
            'Digital Services Act (EU) 2022/2065 compliance',
            'Italian fiscal reporting (DAC7) integration'
        ]
    ],

    'available_user_types' => [
        'collector' => [
            'status' => 'in_development',
            'priority' => 'high',
            'notes' => 'Migration from existing blade template required'
        ],
        'creator' => [
            'status' => 'ready',
            'priority' => 'high',
            'notes' => 'Converted from markdown v1.4.0 - comprehensive professional terms'
        ],
        'patron' => [
            'status' => 'pending',
            'priority' => 'medium',
            'notes' => 'Terms to be developed based on creator template'
        ],
        'epp' => [
            'status' => 'pending',
            'priority' => 'medium',
            'notes' => 'Specialized terms for environmental project entities'
        ],
        'company' => [
            'status' => 'pending',
            'priority' => 'low',
            'notes' => 'Corporate terms for enterprise users'
        ],
        'trader_pro' => [
            'status' => 'pending',
            'priority' => 'low',
            'notes' => 'Professional trading terms'
        ]
    ],

    'available_locales' => [
        'it' => [
            'status' => 'primary',
            'completion' => '50%', // Only creator.php ready
            'notes' => 'Source language - legal team edits here'
        ],
        'en' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + dev review required'
        ],
        'es' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + dev review required'
        ],
        'pt' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + dev review required'
        ],
        'fr' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + dev review required'
        ],
        'de' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + dev review required'
        ]
    ],

    'content_hashes' => [
        'it' => [
            'creator' => hash('sha256', serialize(include __DIR__ . '/it/creator.php')),
            // Other user types will be added as they're created
        ]
        // Other locales will be added as translations are completed
    ],

    'legal_review' => [
        'reviewed_by' => 'legal@florenceegi.com',
        'review_date' => '2025-06-22',
        'review_status' => 'approved',
        'review_notes' => 'Creator terms approved for v1.0.0 release. Comprehensive coverage of professional requirements, KYC/KYB, revenue sharing, and compliance framework.',
        'next_review_due' => '2025-12-31'
    ],

    'technical_notes' => [
        'file_structure' => '/resources/legal/terms/versions/1.0.0/',
        'integration_status' => 'in_development',
        'consent_type_slug' => 'terms-of-service',
        'required_permissions' => ['legal.terms.edit', 'legal.terms.create_version'],
        'deployment_requirements' => [
            'ConsentService integration',
            'GdprController extension',
            'Menu system integration',
            'File system permissions setup'
        ]
    ],

    'compliance_checklist' => [
        'gdpr_article_7' => true,  // Consent documentation
        'gdpr_article_13' => true, // Information to be provided
        'dsa_2022_2065' => true,   // Digital Services Act
        'italian_privacy_code' => true, // D.Lgs. 196/2003
        'consumer_code' => true,   // D.Lgs. 206/2005
        'dac7_reporting' => true   // Fiscal transparency
    ]
];
