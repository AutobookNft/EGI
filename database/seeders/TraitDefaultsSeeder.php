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
        // Default Categories
        $categories = [
            ['name' => 'Materials', 'slug' => 'materials', 'icon' => '📦', 'is_system' => true, 'sort_order' => 1],
            ['name' => 'Visual', 'slug' => 'visual', 'icon' => '🎨', 'is_system' => true, 'sort_order' => 2],
            ['name' => 'Dimensions', 'slug' => 'dimensions', 'icon' => '📐', 'is_system' => true, 'sort_order' => 3],
            ['name' => 'Special', 'slug' => 'special', 'icon' => '⚡', 'is_system' => true, 'sort_order' => 4],
            ['name' => 'Sustainability', 'slug' => 'sustainability', 'icon' => '🌿', 'is_system' => true, 'sort_order' => 5],
            ['name' => 'Cultural', 'slug' => 'cultural', 'icon' => '🏛️', 'is_system' => true, 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            // Check if category exists, if not insert it
            $existingCategory = DB::table('trait_categories')
                ->where('slug', $category['slug'])
                ->first();

            if ($existingCategory) {
                $categoryId = $existingCategory->id;
                // Update existing category with new data if needed
                DB::table('trait_categories')
                    ->where('id', $categoryId)
                    ->update(array_merge($category, ['updated_at' => now()]));
            } else {
                $categoryId = DB::table('trait_categories')->insertGetId(
                    array_merge($category, ['created_at' => now(), 'updated_at' => now()])
                );
            }

            // Add trait types for each category (only if they don't exist)
            $this->seedTraitTypes($categoryId, $category['slug']);
        }
    }

    /**
     * Seed trait types for each category
     */
    private function seedTraitTypes(int $categoryId, string $categorySlug): void {
        $traitTypes = [];

        switch ($categorySlug) {
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
                            'Mixed Media'
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
                            'Oil Paint',
                            'Acrylic',
                            'Watercolor',
                            'Digital Print',
                            '3D Printed',
                            'Hand Carved',
                            'Cast',
                            'Welded',
                            'Sewn'
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
                            'Collective Memory'
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
                            'Risograph Printing'
                        ])
                    ],
                    [
                        'name' => 'Edition Type',
                        'slug' => 'edition-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Unique Piece',
                            'Limited Series',
                            'Prototype'
                        ])
                    ]
                ];
                break;
        }

        foreach ($traitTypes as $type) {
            // Check if trait type already exists for this category
            $existingType = DB::table('trait_types')
                ->where('slug', $type['slug'])
                ->where('category_id', $categoryId)
                ->first();

            if (!$existingType) {
                DB::table('trait_types')->insert(array_merge($type, [
                    'category_id' => $categoryId,
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }
    }
}