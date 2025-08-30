<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder for default trait categories and types
 * 
 * @package FlorenceEGI\Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Traits System)
 * @date 2024-12-27
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
        ];

        foreach ($categories as $category) {
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
        }

        foreach ($traitTypes as $type) {
            DB::table('trait_types')->insert(array_merge($type, [
                'category_id' => $categoryId,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
