<?php

return [
    // Titel und Überschriften
    'title' => 'Nutzen-Verwaltung',
    'subtitle' => 'Fügen Sie echten Wert zu Ihrem EGI hinzu',
    'status_configured' => 'Nutzen Konfiguriert',
    'status_none' => 'Kein Nutzen',
    'available_images' => 'Es sind :count Bilder für ":title" verfügbar',
    'view_details' => 'Details Anzeigen',

    // Warnungen und Nachrichten
    'info_edit_before_publish' => 'Der Nutzen kann nur vor der Veröffentlichung der Sammlung hinzugefügt oder geändert werden. Nach der Veröffentlichung kann er nicht mehr geändert werden.',
    'success_created' => 'Nutzen erfolgreich hinzugefügt!',
    'success_updated' => 'Nutzen erfolgreich aktualisiert!',
    'confirm_reset' => 'Sind Sie sicher, dass Sie abbrechen möchten? Nicht gespeicherte Änderungen gehen verloren.',
    'confirm_remove_image' => 'Dieses Bild entfernen?',
    'note' => 'Hinweis',

    // Nutzen-Typen
    'types' => [
        'label' => 'Nutzen-Typ',
        'physical' => [
            'label' => 'Physisches Gut',
            'description' => 'Physisches Objekt zum Versenden (Gemälde, Skulptur, etc.)'
        ],
        'service' => [
            'label' => 'Dienstleistung',
            'description' => 'Service oder Erfahrung (Workshop, Beratung, etc.)'
        ],
        'hybrid' => [
            'label' => 'Hybrid',
            'description' => 'Physisch + Service Kombination'
        ],
        'digital' => [
            'label' => 'Digital',
            'description' => 'Digitaler Inhalt oder Zugang'
        ],
        'remove' => 'Nutzen Entfernen'
    ],

    // Basis-Formularfelder
    'fields' => [
        'title' => 'Nutzen-Titel',
        'title_placeholder' => 'Z.B.: Original Gemälde 50x70cm',
        'description' => 'Detaillierte Beschreibung',
        'description_placeholder' => 'Beschreiben Sie detailliert, was der Käufer erhalten wird...',
    ],

    // Versand-Bereich
    'shipping' => [
        'title' => 'Versanddetails',
        'weight' => 'Gewicht (kg)',
        'dimensions' => 'Abmessungen (cm)',
        'length' => 'Länge',
        'width' => 'Breite',
        'height' => 'Höhe',
        'days' => 'Vorbereitungs-/Versandtage',
        'fragile' => 'Zerbrechlicher Gegenstand',
        'insurance' => 'Versicherung Empfohlen',
        'notes' => 'Versandnotizen',
        'notes_placeholder' => 'Spezielle Anweisungen für Verpackung oder Versand...'
    ],

    // Service-Bereich
    'service' => [
        'title' => 'Service-Details',
        'valid_from' => 'Gültig Ab',
        'valid_until' => 'Gültig Bis',
        'max_uses' => 'Maximale Anzahl Nutzungen',
        'max_uses_placeholder' => 'Leer lassen für unbegrenzt',
        'instructions' => 'Aktivierungsanweisungen',
        'instructions_placeholder' => 'Wie der Käufer den Service nutzen kann...'
    ],

    // Escrow
    'escrow' => [
        'immediate' => [
            'label' => 'Sofortige Zahlung',
            'description' => 'Kein Escrow, direkte Zahlung an Ersteller'
        ],
        'standard' => [
            'label' => 'Standard Escrow',
            'description' => 'Gelder freigegeben nach 14 Tagen ab Lieferung',
            'requirement_tracking' => 'Verfolgung erforderlich'
        ],
        'premium' => [
            'label' => 'Premium Escrow',
            'description' => 'Gelder freigegeben nach 21 Tagen ab Lieferung',
            'requirement_tracking' => 'Verfolgung erforderlich',
            'requirement_signature' => 'Unterschrift bei Lieferung',
            'requirement_insurance' => 'Versicherung empfohlen'
        ]
    ],

    // Media/Galerie
    'media' => [
        'title' => 'Detail-Bilder Galerie',
        'description' => 'Fügen Sie Fotos des Objekts aus verschiedenen Winkeln, wichtige Details, Echtheitszertifikate, etc. hinzu (Max 10 Bilder)',
        'upload_prompt' => 'Klicken zum Hochladen oder Bilder hierher ziehen',
        'current_images' => 'Aktuelle Bilder:',
        'remove_image' => 'Entfernen'
    ],

    // Validierungsfehler
    'validation' => [
        'title_required' => 'Titel ist erforderlich',
        'type_required' => 'Bitte wählen Sie einen Nutzen-Typ',
        'weight_required' => 'Gewicht ist erforderlich für physische Güter',
        'valid_until_after' => 'Enddatum muss nach dem Startdatum liegen'
    ]
];