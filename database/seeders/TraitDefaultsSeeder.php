<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder for default trait categories and types
 *
 * @package FlorenceEGI\Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.1 (Extended FlorenceEGI Traits System)
 * @date 2025-08-31
 */
class TraitDefaultsSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Truncate tables to start fresh
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('trait_types')->truncate();
        DB::table('trait_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Default Categories with colors
        $categories = [
            ['name' => 'Materials', 'slug' => 'materials', 'icon' => '📦', 'color' => '#D4A574', 'is_system' => true, 'sort_order' => 1],
            ['name' => 'Visual', 'slug' => 'visual', 'icon' => '🎨', 'color' => '#8E44AD', 'is_system' => true, 'sort_order' => 2],
            ['name' => 'Dimensions', 'slug' => 'dimensions', 'icon' => '📐', 'color' => '#1B365D', 'is_system' => true, 'sort_order' => 3],
            ['name' => 'Special', 'slug' => 'special', 'icon' => '⚡', 'color' => '#E67E22', 'is_system' => true, 'sort_order' => 4],
            ['name' => 'Sustainability', 'slug' => 'sustainability', 'icon' => '🌿', 'color' => '#2D5016', 'is_system' => true, 'sort_order' => 5],
            ['name' => 'Cultural', 'slug' => 'cultural', 'icon' => '🏛️', 'color' => '#8B4513', 'is_system' => true, 'sort_order' => 6],
            ['name' => 'Categories', 'slug' => 'categories', 'icon' => '📋', 'color' => '#6366F1', 'is_system' => true, 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            // Insert category directly (since we truncated)
            $categoryId = DB::table('trait_categories')->insertGetId(
                array_merge($category, ['created_at' => now(), 'updated_at' => now()])
            );

            // Add trait types for each category
            $this->seedTraitTypes($categoryId, $category['slug']);
        }
    }

    /**
     * Seed trait types for each category
     */
    private function seedTraitTypes(int $categoryId, string $categorySlug): void {
        $traitTypes = [];

        switch ($categorySlug) {

            case 'categories':
                $traitTypes = [
                    [
                        'name' => 'Categories',
                        'slug' => 'categories',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Games',
                            'Business',
                            'Art',
                            'Music',
                            'Sports',
                            'Education',
                            'Science',
                            'Technology',
                            'Collectibles',
                            'Fantasy',
                            'History',
                            'Nature',
                            'Fashion',
                            'Food',
                            'Travel'
                        ])
                    ],
                ];
                break;
            case 'materials':
                $traitTypes = [
                    [
                        'name' => 'Primary Material',
                        'slug' => 'primary-material',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Wood',
                            'Metal',
                            'Canvas',
                            'Paper',
                            'Ceramic',
                            'Glass',
                            'Stone',
                            'Fabric',
                            'Plastic',
                            'Mixed Media',
                            'Leather',
                            'Clay',
                            'Marble',
                            'Granite',
                            'Concrete',
                            'Plaster',
                            'Resin',
                            'Rubber',
                            'Acrylic',
                            'Ink',
                            'Oil Paint',
                            'Watercolor',
                            'Charcoal',
                            'Pastel',
                            'Digital',
                            'Photographic',
                            'Organic',
                            'Recycled',
                            'Composite'
                        ])
                    ],
                    [
                        'name' => 'Finish',
                        'slug' => 'finish',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Matte',
                            'Glossy',
                            'Satin',
                            'Raw',
                            'Polished',
                            'Brushed',
                            'Patina',
                            'Textured'
                        ])
                    ],
                    [
                        'name' => 'Technique',
                        'slug' => 'technique',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Painting & Drawing
                            'Oil Paint',
                            'Acrylic',
                            'Watercolor',
                            'Gouache',
                            'Tempera',
                            'Encaustic',
                            'Ink Drawing',
                            'Charcoal Drawing',
                            'Pastel Drawing',
                            'Mixed Media Painting',
                            'Spray Paint',
                            'Airbrush',
                            'Fresco',
                            'Gilding',

                            // Printmaking
                            'Digital Print',
                            'Giclée Print',
                            'Screen Printing',
                            'Linocut',
                            'Woodcut',
                            'Etching',
                            'Lithography',
                            'Drypoint',
                            'Mezzotint',
                            'Aquatint',
                            'Risograph',

                            // Sculpture (general + material-specific)
                            'Hand Carved',
                            'Stone Carving',
                            'Wood Carving',
                            'Clay Modeling',
                            'Lost-Wax Casting',
                            'Cast',
                            'Welded',
                            'Forged',
                            'Repoussé & Chasing',
                            'Assembled Sculpture',
                            'Patinated',

                            // Ceramics
                            'Wheel-Thrown',
                            'Hand-Built',
                            'Slip Casting',
                            'Raku Firing',
                            'Sgraffito',
                            'Glazed',
                            'Underglaze Painting',
                            'Porcelain Casting',

                            // Glass
                            'Glass Blowing',
                            'Lampworking',
                            'Kiln Fusing',
                            'Slumping',
                            'Stained Glass',
                            'Cold Working',

                            // Woodworking
                            'Joinery',
                            'Wood Turning',
                            'Marquetry',
                            'Intarsia',
                            'Pyrography',
                            'Inlay',

                            // Metalwork & Jewelry
                            'Silversmithing',
                            'Goldsmithing',
                            'Stone Setting',
                            'Lost-Wax Jewelry Casting',
                            'Enameling',
                            'Electroforming',
                            'Wire Wrapping',

                            // Leather
                            'Leather Tooling',
                            'Leather Carving',
                            'Leather Embossing',
                            'Hand-Stitched Leather',

                            // Textile & Fashion
                            'Sewn',
                            'Weaving',
                            'Knitting',
                            'Crocheting',
                            'Embroidery',
                            'Appliqué',
                            'Quilting',
                            'Felting',
                            'Macramé',
                            'Tapestry',
                            'Tufting',
                            'Natural Dye',
                            'Batik',
                            'Shibori',
                            'Screen-Printed Textile',

                            // Mosaic & Surface
                            'Mosaic',
                            'Tessellation',
                            'Inlay Mosaic',
                            'Kintsugi',

                            // Photography (process)
                            'Darkroom Print',
                            'Cyanotype',
                            'Platinum/Palladium Print',
                            'Wet Plate Collodion',
                            'Tintype',

                            // Book & Paper Arts
                            'Bookbinding',
                            'Letterpress',
                            'Papercut',
                            'Paper Mâché',
                            'Origami',
                            'Calligraphy',

                            // Digital / Fab
                            '3D Printed',
                            'Laser Cut',
                            'CNC Milled',
                            'Generative Art',
                            'AR/VR Artwork'
                        ])

                    ],
                ];
                break;

            case 'visual':
                $traitTypes = [
                    [
                        'name' => 'Primary Color',
                        'slug' => 'primary-color',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Red',
                            'Blue',
                            'Green',
                            'Yellow',
                            'Orange',
                            'Purple',
                            'Black',
                            'White',
                            'Gray',
                            'Brown',
                            'Gold',
                            'Silver'
                        ])
                    ],
                    [
                        'name' => 'Style',
                        'slug' => 'style',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Modern',
                            'Classic',
                            'Minimalist',
                            'Abstract',
                            'Realistic',
                            'Surreal',
                            'Pop Art',
                            'Renaissance',
                            'Contemporary'
                        ])
                    ],
                    [
                        'name' => 'Mood',
                        'slug' => 'mood',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Serene',
                            'Energetic',
                            'Mysterious',
                            'Joyful',
                            'Melancholic',
                            'Dramatic',
                            'Peaceful',
                            'Intense'
                        ])
                    ],
                ];
                break;

            case 'dimensions':
                $traitTypes = [
                    [
                        'name' => 'Size',
                        'slug' => 'size',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Extra Small',
                            'Small',
                            'Medium',
                            'Large',
                            'Extra Large'
                        ])
                    ],
                    [
                        'name' => 'Weight',
                        'slug' => 'weight',
                        'display_type' => 'number',
                        'unit' => 'kg',
                        'allowed_values' => null // Free numeric input
                    ],
                    [
                        'name' => 'Height',
                        'slug' => 'height',
                        'display_type' => 'number',
                        'unit' => 'cm',
                        'allowed_values' => null
                    ],
                    [
                        'name' => 'Width',
                        'slug' => 'width',
                        'display_type' => 'number',
                        'unit' => 'cm',
                        'allowed_values' => null
                    ],
                ];
                break;

            case 'special':
                $traitTypes = [
                    [
                        'name' => 'Edition',
                        'slug' => 'edition',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            '1/1',
                            'Limited Edition',
                            'Open Edition',
                            'Artist Proof',
                            'First Edition'
                        ])
                    ],
                    [
                        'name' => 'Signature',
                        'slug' => 'signature',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Signed',
                            'Unsigned',
                            'Signed & Numbered',
                            'Certificate of Authenticity'
                        ])
                    ],
                    [
                        'name' => 'Condition',
                        'slug' => 'condition',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Mint',
                            'Excellent',
                            'Good',
                            'Fair',
                            'Restored',
                            'Vintage',
                            'New'
                        ])
                    ],
                    [
                        'name' => 'Year Created',
                        'slug' => 'year-created',
                        'display_type' => 'date',
                        'allowed_values' => null
                    ],
                ];
                break;

            case 'sustainability':
                $traitTypes = [
                    [
                        'name' => 'Recycled Content',
                        'slug' => 'recycled-content',
                        'display_type' => 'percentage',
                        'unit' => '%',
                        'allowed_values' => null
                    ],
                    [
                        'name' => 'Carbon Footprint',
                        'slug' => 'carbon-footprint',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Carbon Neutral',
                            'Low Impact',
                            'Medium Impact',
                            'Offset Compensated'
                        ])
                    ],
                    [
                        'name' => 'Eco Certification',
                        'slug' => 'eco-certification',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'FSC Certified',
                            'Organic',
                            'Fair Trade',
                            'Recycled',
                            'Biodegradable',
                            'None'
                        ])
                    ],
                    [
                        'name' => 'Sustainability Score',
                        'slug' => 'sustainability-score',
                        'display_type' => 'boost_number',
                        'allowed_values' => null
                    ],
                ];
                break;

            case 'cultural':
                $traitTypes = [
                    [
                        'name' => 'Cultural Origin',
                        'slug' => 'cultural-origin',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Florence',
                            'Tuscany',
                            'Italy',
                            'Europe',
                            'Mediterranean',
                            'Middle East',
                            'North Africa',
                            'Sub-Saharan Africa',
                            'South Asia',
                            'East Asia',
                            'Southeast Asia',
                            'Oceania',
                            'North America',
                            'South America',
                            'Indigenous',
                            'Global Contemporary',
                            'Other'
                        ])

                    ],
                    [
                        'name' => 'Thematic Focus',
                        'slug' => 'thematic-focus',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                        'Ecological Renaissance',
                        'Sacred Art',
                        'Inner Landscape',
                        'Metamorphosis',
                        'Collective Memory',
                        'Identity and Belonging',
                        'Migration and Borders',
                        'Technology and Humanity',
                        'Dreams and Myth',
                        'Ritual and Ceremony',
                        'Time and Impermanence',
                        'Resistance and Resilience',
                        'Nature and Cosmos',
                        'Body and Spirit',
                        'Other'
                    ])

                    ],
                    [
                        'name' => 'Artisan Technique',
                        'slug' => 'artisan-technique',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Inlay',
                            'Hand Embroidery',
                            'Glass Blowing',
                            'Loom Weaving',
                            '3D Modeling',
                            'Risograph Printing',
                            'Calligraphy',
                            'Ikebana',
                            'Origami',
                            'Mosaic',
                            'Tilework',
                            'Wood Carving',
                            'Stone Carving',
                            'Leather Tooling',
                            'Pottery Wheel',
                            'Tattooing',
                            'Digital Fabrication',
                            'Other'
                        ])

                    ],
                    [
                        'name' => 'Edition Type',
                        'slug' => 'edition-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Unique Piece',
                            'Limited Series',
                            'Prototype',
                            'Open Edition',
                            'Monoprint',
                            'Artist Proof'
                        ])
                    ]
                ];
               break;
        }
        foreach ($traitTypes as $type) {
            // Insert trait type directly (since we truncated)
            DB::table('trait_types')->insert(array_merge($type, [
                'category_id' => $categoryId,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}