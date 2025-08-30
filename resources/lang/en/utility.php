<?php

return [
    // Titles and headers
    'title' => 'Utility Management',
    'subtitle' => 'Add real value to your EGI',
    'status_configured' => 'Utility Configured',
    'status_none' => 'No Utility',
    'available_images' => ':count images available for ":title"',
    'view_details' => 'View Details',

    // Alerts and messages
    'info_edit_before_publish' => 'The utility can only be added or modified before the collection is published. Once published, it cannot be modified.',
    'success_created' => 'Utility successfully added!',
    'success_updated' => 'Utility successfully updated!',
    'confirm_reset' => 'Are you sure you want to cancel? Unsaved changes will be lost.',
    'confirm_remove_image' => 'Remove this image?',
    'note' => 'Note',

    // Utility types
    'types' => [
        'label' => 'Utility Type',
        'physical' => [
            'label' => 'Physical Good',
            'description' => 'Physical object to ship (painting, sculpture, etc.)'
        ],
        'service' => [
            'label' => 'Service',
            'description' => 'Service or experience (workshop, consultation, etc.)'
        ],
        'hybrid' => [
            'label' => 'Hybrid',
            'description' => 'Physical + service combination'
        ],
        'digital' => [
            'label' => 'Digital',
            'description' => 'Digital content or access'
        ],
        'remove' => 'Remove Utility'
    ],

    // Base form fields
    'fields' => [
        'title' => 'Utility Title',
        'title_placeholder' => 'E.g.: Original Painting 50x70cm',
        'description' => 'Detailed Description',
        'description_placeholder' => 'Describe in detail what the buyer will receive...',
    ],

    // Shipping section
    'shipping' => [
        'title' => 'Shipping Details',
        'weight' => 'Weight (kg)',
        'dimensions' => 'Dimensions (cm)',
        'length' => 'Length',
        'width' => 'Width',
        'height' => 'Height',
        'days' => 'Preparation/shipping days',
        'fragile' => 'Fragile Item',
        'insurance' => 'Insurance Recommended',
        'notes' => 'Shipping Notes',
        'notes_placeholder' => 'Special instructions for packaging or shipping...'
    ],

    // Service section
    'service' => [
        'title' => 'Service Details',
        'valid_from' => 'Valid From',
        'valid_until' => 'Valid Until',
        'max_uses' => 'Maximum Number of Uses',
        'max_uses_placeholder' => 'Leave empty for unlimited',
        'instructions' => 'Activation Instructions',
        'instructions_placeholder' => 'How the buyer can use the service...'
    ],

    // Escrow
    'escrow_tiers' => [
        'immediate' => 'Immediate Payment',
        'standard' => 'Standard Escrow',
        'premium' => 'Premium Escrow'
    ],

    'escrow' => [
        'immediate' => [
            'label' => 'Immediate Payment',
            'description' => 'No escrow, direct payment to creator'
        ],
        'standard' => [
            'label' => 'Standard Escrow',
            'description' => 'Funds released after 14 days from delivery',
            'requirement_tracking' => 'Tracking required'
        ],
        'premium' => [
            'label' => 'Premium Escrow',
            'description' => 'Funds released after 21 days from delivery',
            'requirement_tracking' => 'Tracking required',
            'requirement_signature' => 'Signature on delivery',
            'requirement_insurance' => 'Insurance recommended'
        ]
    ],

    // Media/Gallery
    'media' => [
        'title' => 'Detail Images Gallery',
        'description' => 'Add photos of the object from various angles, important details, authenticity certificates, etc. (Max 10 images)',
        'upload_prompt' => 'Click to upload or drag images here',
        'current_images' => 'Current Images:',
        'remove_image' => 'Remove',
        'images' => 'images',
        'no_images' => 'No images available'
    ],

    // Validation errors
    'validation' => [
        'title_required' => 'Title is required',
        'type_required' => 'Please select a utility type',
        'weight_required' => 'Weight is required for physical goods',
        'valid_until_after' => 'End date must be after start date'
    ]
];