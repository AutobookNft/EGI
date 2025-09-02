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
            ['name' => 'Minecraft', 'slug' => 'minecraft', 'icon' => '🟫', 'color' => '#4A7C59', 'is_system' => true, 'sort_order' => 7],
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

            case 'minecraft':
                $traitTypes = [
                    /**
                     * MATERIALS & RESOURCES
                     * Core materials that can be combined with tools, weapons, armor
                     */
                    [
                        'name' => 'Material Type',
                        'slug' => 'material-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Basic Materials
                            'Wood',
                            'Stone',
                            'Iron',
                            'Gold',
                            'Diamond',
                            'Netherite',
                            // Armor Materials
                            'Leather',
                            'Chainmail',
                            'Turtle Shell',
                            // Special Materials
                            'Bamboo',
                            'Bone',
                            'Flint',
                            'String',
                            'Feather',
                            'Paper',
                            'Phantom Membrane',
                            'Rabbit Hide',
                            'Scute',
                            'Copper',
                            'Amethyst',
                            'Quartz',
                            'Prismarine',
                            'End Crystal',
                            'Crying Obsidian',
                            'Ancient Debris'
                        ])
                    ],

                    /**
                     * TOOL CATEGORIES
                     * Basic tools for mining, farming, and utility
                     */
                    [
                        'name' => 'Tool Type',
                        'slug' => 'tool-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Pickaxe',
                            'Axe',
                            'Shovel',
                            'Hoe',
                            'Shears',
                            'Flint and Steel',
                            'Fishing Rod',
                            'Carrot on a Stick',
                            'Warped Fungus on a Stick',
                            'Bucket',
                            'Water Bucket',
                            'Lava Bucket',
                            'Milk Bucket',
                            'Powder Snow Bucket',
                            'Compass',
                            'Recovery Compass',
                            'Clock',
                            'Map',
                            'Explorer Map',
                            'Treasure Map',
                            'Spyglass',
                            'Lead',
                            'Name Tag',
                            'Brush'
                        ])
                    ],

                    /**
                     * WEAPON CATEGORIES
                     * Offensive equipment
                     */
                    [
                        'name' => 'Weapon Type',
                        'slug' => 'weapon-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Sword',
                            'Bow',
                            'Crossbow',
                            'Trident',
                            'Mace',
                            'TNT',
                            'End Crystal',
                            'Fire Charge',
                            'Snowball',
                            'Egg',
                            'Ender Pearl',
                            'Eye of Ender',
                            'Splash Potion',
                            'Lingering Potion',
                            'Arrow',
                            'Spectral Arrow',
                            'Tipped Arrow',
                            'Firework Rocket'
                        ])
                    ],

                    /**
                     * ARMOR PIECES
                     * Defensive equipment slots
                     */
                    [
                        'name' => 'Armor Piece',
                        'slug' => 'armor-piece',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Helmet',
                            'Chestplate',
                            'Leggings',
                            'Boots',
                            'Shield',
                            'Elytra',
                            'Horse Armor',
                            'Wolf Armor'
                        ])
                    ],

                    /**
                     * BLOCK CATEGORIES
                     * Main block types without specific variants
                     */
                    [
                        'name' => 'Block Category',
                        'slug' => 'block-category',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Natural Blocks
                            'Stone',
                            'Cobblestone',
                            'Mossy Cobblestone',
                            'Dirt',
                            'Coarse Dirt',
                            'Podzol',
                            'Grass Block',
                            'Mycelium',
                            'Sand',
                            'Red Sand',
                            'Gravel',
                            'Clay',
                            'Terracotta',
                            // Rocks & Minerals
                            'Granite',
                            'Diorite',
                            'Andesite',
                            'Deepslate',
                            'Tuff',
                            'Calcite',
                            'Dripstone',
                            'Pointed Dripstone',
                            // Nether Blocks
                            'Netherrack',
                            'Soul Sand',
                            'Soul Soil',
                            'Magma Block',
                            'Basalt',
                            'Blackstone',
                            'Crimson Nylium',
                            'Warped Nylium',
                            'Shroomlight',
                            // End Blocks
                            'End Stone',
                            'End Stone Bricks',
                            'Purpur Block',
                            'Chorus Plant',
                            'Chorus Flower',
                            // Functional Blocks
                            'Obsidian',
                            'Crying Obsidian',
                            'Bedrock',
                            'Spawner',
                            'Command Block',
                            'Structure Block',
                            'Jigsaw Block',
                            'Barrier',
                            'Light Block'
                        ])
                    ],

                    /**
                     * WOOD VARIANTS
                     * All wood types in the game
                     */
                    [
                        'name' => 'Wood Type',
                        'slug' => 'wood-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Oak',
                            'Spruce',
                            'Birch',
                            'Jungle',
                            'Acacia',
                            'Dark Oak',
                            'Mangrove',
                            'Cherry',
                            'Bamboo',
                            'Crimson',
                            'Warped',
                            'Stripped Oak',
                            'Stripped Spruce',
                            'Stripped Birch',
                            'Stripped Jungle',
                            'Stripped Acacia',
                            'Stripped Dark Oak',
                            'Stripped Mangrove',
                            'Stripped Cherry',
                            'Stripped Crimson',
                            'Stripped Warped'
                        ])
                    ],

                    /**
                     * ORE TYPES
                     * Mineable resources
                     */
                    [
                        'name' => 'Ore Type',
                        'slug' => 'ore-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Coal Ore',
                            'Deepslate Coal Ore',
                            'Iron Ore',
                            'Deepslate Iron Ore',
                            'Copper Ore',
                            'Deepslate Copper Ore',
                            'Gold Ore',
                            'Deepslate Gold Ore',
                            'Nether Gold Ore',
                            'Redstone Ore',
                            'Deepslate Redstone Ore',
                            'Emerald Ore',
                            'Deepslate Emerald Ore',
                            'Lapis Lazuli Ore',
                            'Deepslate Lapis Ore',
                            'Diamond Ore',
                            'Deepslate Diamond Ore',
                            'Nether Quartz Ore',
                            'Ancient Debris',
                            'Amethyst Cluster',
                            'Budding Amethyst'
                        ])
                    ],

                    /**
                     * FOOD ITEMS
                     * Consumable items
                     */
                    [
                        'name' => 'Food Type',
                        'slug' => 'food-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Raw Foods
                            'Apple',
                            'Golden Apple',
                            'Enchanted Golden Apple',
                            'Carrot',
                            'Golden Carrot',
                            'Potato',
                            'Baked Potato',
                            'Poisonous Potato',
                            'Beetroot',
                            'Sweet Berries',
                            'Glow Berries',
                            'Melon Slice',
                            'Glistering Melon',
                            // Meats
                            'Raw Beef',
                            'Steak',
                            'Raw Porkchop',
                            'Cooked Porkchop',
                            'Raw Mutton',
                            'Cooked Mutton',
                            'Raw Chicken',
                            'Cooked Chicken',
                            'Raw Rabbit',
                            'Cooked Rabbit',
                            'Rabbit Stew',
                            'Raw Cod',
                            'Cooked Cod',
                            'Raw Salmon',
                            'Cooked Salmon',
                            'Tropical Fish',
                            'Pufferfish',
                            // Baked Goods
                            'Bread',
                            'Cookie',
                            'Cake',
                            'Pumpkin Pie',
                            // Special Foods
                            'Mushroom Stew',
                            'Suspicious Stew',
                            'Beetroot Soup',
                            'Honey Bottle',
                            'Dried Kelp',
                            'Chorus Fruit',
                            'Spider Eye',
                            'Fermented Spider Eye',
                            'Rotten Flesh',
                            'Milk'
                        ])
                    ],

                    /**
                     * MOB CATEGORIES
                     * Grouped by behavior type
                     */
                    [
                        'name' => 'Mob Category',
                        'slug' => 'mob-category',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Passive',
                            'Neutral',
                            'Hostile',
                            'Boss',
                            'Tameable',
                            'Breedable',
                            'Villager',
                            'Illager',
                            'Undead',
                            'Arthropod',
                            'Aquatic',
                            'Flying',
                            'Nether',
                            'End'
                        ])
                    ],

                    /**
                     * SPECIFIC MOB TYPES
                     * Individual mob species
                     */
                    [
                        'name' => 'Mob Type',
                        'slug' => 'mob-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Passive
                            'Allay',
                            'Axolotl',
                            'Bat',
                            'Camel',
                            'Cat',
                            'Chicken',
                            'Cod',
                            'Cow',
                            'Donkey',
                            'Frog',
                            'Glow Squid',
                            'Horse',
                            'Mooshroom',
                            'Mule',
                            'Ocelot',
                            'Parrot',
                            'Pig',
                            'Pufferfish',
                            'Rabbit',
                            'Salmon',
                            'Sheep',
                            'Skeleton Horse',
                            'Sniffer',
                            'Snow Golem',
                            'Squid',
                            'Strider',
                            'Tadpole',
                            'Tropical Fish',
                            'Turtle',
                            'Villager',
                            'Wandering Trader',
                            // Neutral
                            'Bee',
                            'Cave Spider',
                            'Dolphin',
                            'Enderman',
                            'Fox',
                            'Goat',
                            'Iron Golem',
                            'Llama',
                            'Trader Llama',
                            'Panda',
                            'Piglin',
                            'Polar Bear',
                            'Spider',
                            'Wolf',
                            'Zombified Piglin',
                            // Hostile
                            'Blaze',
                            'Breeze',
                            'Creeper',
                            'Drowned',
                            'Elder Guardian',
                            'Endermite',
                            'Evoker',
                            'Ghast',
                            'Guardian',
                            'Hoglin',
                            'Husk',
                            'Magma Cube',
                            'Phantom',
                            'Piglin Brute',
                            'Pillager',
                            'Ravager',
                            'Shulker',
                            'Silverfish',
                            'Skeleton',
                            'Slime',
                            'Stray',
                            'Vex',
                            'Vindicator',
                            'Warden',
                            'Witch',
                            'Wither Skeleton',
                            'Zoglin',
                            'Zombie',
                            'Zombie Villager',
                            // Bosses
                            'Ender Dragon',
                            'Wither',
                            'Elder Guardian'
                        ])
                    ],

                    /**
                     * ENCHANTMENTS
                     * Magical enhancements
                     */
                    [
                        'name' => 'Enchantment Type',
                        'slug' => 'enchantment-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Armor Enchantments
                            'Protection',
                            'Fire Protection',
                            'Feather Falling',
                            'Blast Protection',
                            'Projectile Protection',
                            'Respiration',
                            'Aqua Affinity',
                            'Thorns',
                            'Depth Strider',
                            'Frost Walker',
                            'Soul Speed',
                            'Swift Sneak',
                            // Weapon Enchantments
                            'Sharpness',
                            'Smite',
                            'Bane of Arthropods',
                            'Knockback',
                            'Fire Aspect',
                            'Looting',
                            'Sweeping Edge',
                            'Impaling',
                            // Tool Enchantments
                            'Efficiency',
                            'Silk Touch',
                            'Unbreaking',
                            'Fortune',
                            'Mending',
                            // Bow Enchantments
                            'Power',
                            'Punch',
                            'Flame',
                            'Infinity',
                            // Crossbow Enchantments
                            'Quick Charge',
                            'Multishot',
                            'Piercing',
                            // Trident Enchantments
                            'Loyalty',
                            'Riptide',
                            'Channeling',
                            // Fishing Rod
                            'Luck of the Sea',
                            'Lure',
                            // Curses
                            'Curse of Binding',
                            'Curse of Vanishing'
                        ])
                    ],

                    /**
                     * POTION EFFECTS
                     * Status effects from potions
                     */
                    [
                        'name' => 'Potion Effect',
                        'slug' => 'potion-effect',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Positive Effects
                            'Speed',
                            'Haste',
                            'Strength',
                            'Instant Health',
                            'Jump Boost',
                            'Regeneration',
                            'Resistance',
                            'Fire Resistance',
                            'Water Breathing',
                            'Invisibility',
                            'Night Vision',
                            'Health Boost',
                            'Absorption',
                            'Saturation',
                            'Glowing',
                            'Levitation',
                            'Luck',
                            'Slow Falling',
                            'Conduit Power',
                            'Dolphins Grace',
                            'Hero of the Village',
                            // Negative Effects
                            'Slowness',
                            'Mining Fatigue',
                            'Instant Damage',
                            'Nausea',
                            'Blindness',
                            'Hunger',
                            'Weakness',
                            'Poison',
                            'Wither',
                            'Bad Luck',
                            'Bad Omen',
                            'Darkness',
                            'Infested',
                            'Oozing',
                            'Raid Omen',
                            'Trial Omen',
                            'Weaving',
                            'Wind Charged'
                        ])
                    ],

                    /**
                     * BIOMES
                     * World generation regions
                     */
                    [
                        'name' => 'Biome',
                        'slug' => 'biome',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Overworld - Temperate
                            'Plains',
                            'Sunflower Plains',
                            'Forest',
                            'Flower Forest',
                            'Birch Forest',
                            'Dark Forest',
                            'Swamp',
                            'Mangrove Swamp',
                            'Meadow',
                            'Cherry Grove',
                            // Overworld - Cold
                            'Taiga',
                            'Snowy Taiga',
                            'Old Growth Pine Taiga',
                            'Old Growth Spruce Taiga',
                            'Snowy Plains',
                            'Ice Spikes',
                            'Frozen River',
                            'Frozen Ocean',
                            'Deep Frozen Ocean',
                            'Grove',
                            'Snowy Slopes',
                            'Frozen Peaks',
                            'Jagged Peaks',
                            // Overworld - Dry
                            'Desert',
                            'Savanna',
                            'Savanna Plateau',
                            'Windswept Savanna',
                            'Badlands',
                            'Wooded Badlands',
                            'Eroded Badlands',
                            // Overworld - Ocean
                            'Ocean',
                            'Deep Ocean',
                            'Warm Ocean',
                            'Lukewarm Ocean',
                            'Cold Ocean',
                            'Deep Cold Ocean',
                            'Mushroom Fields',
                            'Beach',
                            'Stony Shore',
                            // Overworld - Mountain
                            'Windswept Hills',
                            'Windswept Gravelly Hills',
                            'Windswept Forest',
                            'Stony Peaks',
                            // Overworld - Cave
                            'Dripstone Caves',
                            'Lush Caves',
                            'Deep Dark',
                            // Jungle
                            'Jungle',
                            'Sparse Jungle',
                            'Bamboo Jungle',
                            // River
                            'River',
                            // Nether
                            'Nether Wastes',
                            'Soul Sand Valley',
                            'Crimson Forest',
                            'Warped Forest',
                            'Basalt Deltas',
                            // End
                            'The End',
                            'Small End Islands',
                            'End Midlands',
                            'End Highlands',
                            'End Barrens'
                        ])
                    ],

                    /**
                     * STRUCTURES
                     * Generated structures
                     */
                    [
                        'name' => 'Structure Type',
                        'slug' => 'structure-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Overworld Structures
                            'Village',
                            'Plains Village',
                            'Desert Village',
                            'Savanna Village',
                            'Taiga Village',
                            'Snowy Village',
                            'Pillager Outpost',
                            'Woodland Mansion',
                            'Swamp Hut',
                            'Igloo',
                            'Desert Pyramid',
                            'Jungle Pyramid',
                            'Ancient City',
                            'Trail Ruins',
                            'Trial Chambers',
                            // Underground
                            'Mineshaft',
                            'Stronghold',
                            'Dungeon',
                            'Geode',
                            'Fossil',
                            // Ocean
                            'Ocean Monument',
                            'Ocean Ruins',
                            'Shipwreck',
                            'Buried Treasure',
                            // Nether
                            'Nether Fortress',
                            'Bastion Remnant',
                            'Ruined Portal',
                            // End
                            'End City',
                            'End Ship',
                            'End Gateway',
                            // Special
                            'Desert Well',
                            'Witch Hut'
                        ])
                    ],

                    /**
                     * REDSTONE COMPONENTS
                     * Technical/mechanical items
                     */
                    [
                        'name' => 'Redstone Component',
                        'slug' => 'redstone-component',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Redstone Dust',
                            'Redstone Torch',
                            'Redstone Block',
                            'Redstone Repeater',
                            'Redstone Comparator',
                            'Piston',
                            'Sticky Piston',
                            'Observer',
                            'Hopper',
                            'Dropper',
                            'Dispenser',
                            'Note Block',
                            'Jukebox',
                            'TNT',
                            'Redstone Lamp',
                            'Tripwire Hook',
                            'Trapped Chest',
                            'Daylight Detector',
                            'Iron Door',
                            'Iron Trapdoor',
                            'Lever',
                            'Button',
                            'Pressure Plate',
                            'Weighted Pressure Plate',
                            'Target',
                            'Sculk Sensor',
                            'Calibrated Sculk Sensor',
                            'Sculk Shrieker',
                            'Rail',
                            'Powered Rail',
                            'Detector Rail',
                            'Activator Rail',
                            'Minecart',
                            'Minecart with Chest',
                            'Minecart with Hopper',
                            'Minecart with TNT',
                            'Minecart with Furnace',
                            'Crafter'
                        ])
                    ],

                    /**
                     * DECORATIVE BLOCKS
                     * Aesthetic building blocks
                     */
                    [
                        'name' => 'Decorative Block',
                        'slug' => 'decorative-block',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            // Glass
                            'Glass',
                            'Stained Glass',
                            'Glass Pane',
                            'Stained Glass Pane',
                            'Tinted Glass',
                            // Wool & Carpets
                            'White Wool',
                            'Orange Wool',
                            'Magenta Wool',
                            'Light Blue Wool',
                            'Yellow Wool',
                            'Lime Wool',
                            'Pink Wool',
                            'Gray Wool',
                            'Light Gray Wool',
                            'Cyan Wool',
                            'Purple Wool',
                            'Blue Wool',
                            'Brown Wool',
                            'Green Wool',
                            'Red Wool',
                            'Black Wool',
                            'Carpet',
                            // Concrete
                            'Concrete',
                            'Concrete Powder',
                            // Glazed Terracotta
                            'Glazed Terracotta',
                            // Flowers
                            'Dandelion',
                            'Poppy',
                            'Blue Orchid',
                            'Allium',
                            'Azure Bluet',
                            'Red Tulip',
                            'Orange Tulip',
                            'White Tulip',
                            'Pink Tulip',
                            'Oxeye Daisy',
                            'Cornflower',
                            'Lily of the Valley',
                            'Wither Rose',
                            'Torchflower',
                            'Pitcher Plant',
                            'Sunflower',
                            'Lilac',
                            'Rose Bush',
                            'Peony',
                            // Coral
                            'Tube Coral',
                            'Brain Coral',
                            'Bubble Coral',
                            'Fire Coral',
                            'Horn Coral',
                            // Candles
                            'Candle',
                            'Sea Pickle',
                            // Banners & Patterns
                            'Banner',
                            'Banner Pattern',
                            // Pottery
                            'Decorated Pot',
                            'Pottery Sherd'
                        ])
                    ],

                    /**
                     * MUSIC DISCS
                     * Collectible music items
                     */
                    [
                        'name' => 'Music Disc',
                        'slug' => 'music-disc',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            '13',
                            'Cat',
                            'Blocks',
                            'Chirp',
                            'Far',
                            'Mall',
                            'Mellohi',
                            'Stal',
                            'Strad',
                            'Ward',
                            'Wait',
                            '11',
                            'Otherside',
                            '5',
                            'Pigstep',
                            'Relic',
                            'Creator',
                            'Creator (Music Box)',
                            'Precipice'
                        ])
                    ],

                    /**
                     * DYE COLORS
                     * Color variants for items
                     */
                    [
                        'name' => 'Dye Color',
                        'slug' => 'dye-color',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'White',
                            'Orange',
                            'Magenta',
                            'Light Blue',
                            'Yellow',
                            'Lime',
                            'Pink',
                            'Gray',
                            'Light Gray',
                            'Cyan',
                            'Purple',
                            'Blue',
                            'Brown',
                            'Green',
                            'Red',
                            'Black'
                        ])
                    ],

                    /**
                     * CRAFTING STATIONS
                     * Functional blocks for crafting
                     */
                    [
                        'name' => 'Crafting Station',
                        'slug' => 'crafting-station',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Crafting Table',
                            'Furnace',
                            'Blast Furnace',
                            'Smoker',
                            'Campfire',
                            'Soul Campfire',
                            'Brewing Stand',
                            'Cauldron',
                            'Anvil',
                            'Chipped Anvil',
                            'Damaged Anvil',
                            'Grindstone',
                            'Enchanting Table',
                            'Bookshelf',
                            'Chiseled Bookshelf',
                            'Lectern',
                            'Cartography Table',
                            'Fletching Table',
                            'Smithing Table',
                            'Stonecutter',
                            'Loom',
                            'Composter',
                            'Barrel',
                            'Chest',
                            'Trapped Chest',
                            'Ender Chest',
                            'Shulker Box',
                            'Beacon',
                            'Conduit',
                            'Lodestone'
                        ])
                    ],

                    /**
                     * VILLAGER PROFESSIONS
                     * NPC job types
                     */
                    [
                        'name' => 'Villager Profession',
                        'slug' => 'villager-profession',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Unemployed',
                            'Nitwit',
                            'Armorer',
                            'Butcher',
                            'Cartographer',
                            'Cleric',
                            'Farmer',
                            'Fisherman',
                            'Fletcher',
                            'Leatherworker',
                            'Librarian',
                            'Mason',
                            'Shepherd',
                            'Toolsmith',
                            'Weaponsmith',
                            'Wandering Trader'
                        ])
                    ],

                    /**
                     * ACHIEVEMENT CATEGORIES
                     * In-game achievements/advancements
                     */
                    [
                        'name' => 'Achievement Category',
                        'slug' => 'achievement-category',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Minecraft',
                            'Nether',
                            'The End',
                            'Adventure',
                            'Husbandry'
                        ])
                    ],

                    /**
                     * GAME MODES
                     */
                    [
                        'name' => 'Game Mode',
                        'slug' => 'game-mode',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Survival',
                            'Creative',
                            'Adventure',
                            'Spectator',
                            'Hardcore',
                            'Peaceful',
                            'Easy',
                            'Normal',
                            'Hard'
                        ])
                    ],

                    /**
                     * DIMENSIONS
                     */
                    [
                        'name' => 'Dimension',
                        'slug' => 'dimension',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Overworld',
                            'The Nether',
                            'The End'
                        ])
                    ],

                    /**
                     * RARITY LEVELS
                     */
                    [
                        'name' => 'Rarity Level',
                        'slug' => 'rarity-level',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Common',
                            'Uncommon',
                            'Rare',
                            'Epic',
                            'Legendary',
                            'Mythic',
                            'Unique',
                            'Artifact'
                        ])
                    ],

                    /**
                     * PARTICLE EFFECTS
                     * Visual effects
                     */
                    [
                        'name' => 'Particle Effect',
                        'slug' => 'particle-effect',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Flame',
                            'Smoke',
                            'Heart',
                            'Bubble',
                            'Water Splash',
                            'Explosion',
                            'Magic',
                            'Enchantment',
                            'Critical Hit',
                            'Note',
                            'Portal',
                            'Villager Happy',
                            'Villager Angry',
                            'Redstone',
                            'Lava Drip',
                            'Water Drip',
                            'Honey Drip',
                            'Snowflake',
                            'End Rod',
                            'Dragon Breath',
                            'Totem of Undying',
                            'Soul',
                            'Soul Fire',
                            'Sculk Soul',
                            'Sculk Charge',
                            'Sonic Boom',
                            'Cherry Leaves',
                            'Egg Crack',
                            'Gust',
                            'Trial Spawner'
                        ])
                    ],

                    /**
                     * SOUND CATEGORIES
                     * Audio effect types
                     */
                    [
                        'name' => 'Sound Category',
                        'slug' => 'sound-category',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Master',
                            'Music',
                            'Records',
                            'Weather',
                            'Blocks',
                            'Hostile',
                            'Neutral',
                            'Players',
                            'Ambient',
                            'Voice'
                        ])
                    ],

                    /**
                     * NUMERIC PROPERTIES
                     */

                    // Y-Level (height in world)
                    [
                        'name' => 'Y Level',
                        'slug' => 'y-level',
                        'display_type' => 'number',
                        'unit' => 'blocks',
                        'allowed_values' => null // Range: -64 to 320
                    ],

                    // Durability
                    [
                        'name' => 'Durability',
                        'slug' => 'durability',
                        'display_type' => 'number',
                        'unit' => 'uses',
                        'allowed_values' => null // Various values per item
                    ],

                    // Stack Size
                    [
                        'name' => 'Stack Size',
                        'slug' => 'stack-size',
                        'display_type' => 'number',
                        'unit' => 'items',
                        'allowed_values' => json_encode([1, 16, 64])
                    ],

                    // Light Level
                    [
                        'name' => 'Light Level',
                        'slug' => 'light-level',
                        'display_type' => 'number',
                        'unit' => 'level',
                        'allowed_values' => json_encode(range(0, 15))
                    ],

                    // Enchantment Level
                    [
                        'name' => 'Enchantment Level',
                        'slug' => 'enchantment-level',
                        'display_type' => 'number',
                        'unit' => 'level',
                        'allowed_values' => json_encode([1, 2, 3, 4, 5])
                    ],

                    // Food Value
                    [
                        'name' => 'Food Value',
                        'slug' => 'food-value',
                        'display_type' => 'number',
                        'unit' => 'hunger points',
                        'allowed_values' => json_encode(range(1, 20))
                    ],

                    // Saturation
                    [
                        'name' => 'Saturation',
                        'slug' => 'saturation',
                        'display_type' => 'number',
                        'unit' => 'points',
                        'allowed_values' => null // Float values
                    ],

                    // Attack Damage
                    [
                        'name' => 'Attack Damage',
                        'slug' => 'attack-damage',
                        'display_type' => 'number',
                        'unit' => 'damage',
                        'allowed_values' => null // Various per weapon
                    ],

                    // Attack Speed
                    [
                        'name' => 'Attack Speed',
                        'slug' => 'attack-speed',
                        'display_type' => 'number',
                        'unit' => 'speed',
                        'allowed_values' => null // Float values
                    ],

                    // Armor Points
                    [
                        'name' => 'Armor Points',
                        'slug' => 'armor-points',
                        'display_type' => 'number',
                        'unit' => 'points',
                        'allowed_values' => json_encode(range(1, 20))
                    ],

                    // Armor Toughness
                    [
                        'name' => 'Armor Toughness',
                        'slug' => 'armor-toughness',
                        'display_type' => 'number',
                        'unit' => 'toughness',
                        'allowed_values' => json_encode([0, 2, 3, 4])
                    ],

                    // Blast Resistance
                    [
                        'name' => 'Blast Resistance',
                        'slug' => 'blast-resistance',
                        'display_type' => 'number',
                        'unit' => 'resistance',
                        'allowed_values' => null // Various per block
                    ],

                    // Hardness
                    [
                        'name' => 'Hardness',
                        'slug' => 'hardness',
                        'display_type' => 'number',
                        'unit' => 'hardness',
                        'allowed_values' => null // Various per block
                    ],

                    // Mining Speed
                    [
                        'name' => 'Mining Speed',
                        'slug' => 'mining-speed',
                        'display_type' => 'number',
                        'unit' => 'speed',
                        'allowed_values' => null // Multiplier values
                    ],

                    // Redstone Power
                    [
                        'name' => 'Redstone Power',
                        'slug' => 'redstone-power',
                        'display_type' => 'number',
                        'unit' => 'power',
                        'allowed_values' => json_encode(range(0, 15))
                    ],

                    /**
                     * VERSION & UPDATE INFO
                     */
                    [
                        'name' => 'Version Added',
                        'slug' => 'version-added',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Classic',
                            'Pre-Classic',
                            'Indev',
                            'Infdev',
                            'Alpha',
                            'Beta',
                            '1.0 - Adventure Update',
                            '1.1',
                            '1.2 - Jungle Update',
                            '1.3 - Trading Update',
                            '1.4 - Pretty Scary Update',
                            '1.5 - Redstone Update',
                            '1.6 - Horse Update',
                            '1.7 - The Update that Changed the World',
                            '1.8 - Bountiful Update',
                            '1.9 - Combat Update',
                            '1.10 - Frostburn Update',
                            '1.11 - Exploration Update',
                            '1.12 - World of Color Update',
                            '1.13 - Update Aquatic',
                            '1.14 - Village & Pillage',
                            '1.15 - Buzzy Bees',
                            '1.16 - Nether Update',
                            '1.17 - Caves & Cliffs Part I',
                            '1.18 - Caves & Cliffs Part II',
                            '1.19 - The Wild Update',
                            '1.20 - Trails & Tales',
                            '1.21 - Tricky Trials'
                        ])
                    ],

                    /**
                     * SPECIAL PROPERTIES
                     */
                    [
                        'name' => 'Special Property',
                        'slug' => 'special-property',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Fireproof',
                            'Waterlogged',
                            'Renewable',
                            'Stackable',
                            'Flammable',
                            'Luminous',
                            'Transparent',
                            'Gravity Affected',
                            'Pushable by Piston',
                            'Blast Resistant',
                            'Naturally Generated',
                            'Can Despawn',
                            'Tradeable',
                            'Compostable',
                            'Smeltable',
                            'Fuel Source',
                            'Breedable Item',
                            'Potion Ingredient',
                            'Enchantable',
                            'Repairable',
                            'Dyeable',
                            'Named',
                            'Cursed',
                            'Treasure Only',
                            'Two-Handed',
                            'Projectile',
                            'Placeable',
                            'Wearable',
                            'Consumable',
                            'Throwable'
                        ])
                    ],

                    /**
                     * TRIM PATTERNS
                     * Armor customization patterns (1.20+)
                     */
                    [
                        'name' => 'Armor Trim',
                        'slug' => 'armor-trim',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Sentry',
                            'Dune',
                            'Coast',
                            'Wild',
                            'Ward',
                            'Eye',
                            'Vex',
                            'Tide',
                            'Snout',
                            'Rib',
                            'Wayfinder',
                            'Shaper',
                            'Silence',
                            'Raiser',
                            'Host',
                            'Flow',
                            'Bolt'
                        ])
                    ],

                    /**
                     * TRIM MATERIALS
                     * Materials for armor trims
                     */
                    [
                        'name' => 'Trim Material',
                        'slug' => 'trim-material',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Iron',
                            'Copper',
                            'Gold',
                            'Lapis',
                            'Emerald',
                            'Diamond',
                            'Netherite',
                            'Redstone',
                            'Amethyst',
                            'Quartz'
                        ])
                    ]
                ];
                break;
                $traitTypes = [
                    [
                        'name' => 'Block Type',
                        'slug' => 'block-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Stone',
                            'Cobblestone',
                            'Dirt',
                            'Grass Block',
                            'Sand',
                            'Gravel',
                            'Wood',
                            'Oak Wood',
                            'Birch Wood',
                            'Spruce Wood',
                            'Jungle Wood',
                            'Acacia Wood',
                            'Dark Oak Wood',
                            'Cherry Wood',
                            'Mangrove Wood',
                            'Coal Ore',
                            'Iron Ore',
                            'Gold Ore',
                            'Diamond Ore',
                            'Emerald Ore',
                            'Lapis Lazuli Ore',
                            'Redstone Ore',
                            'Copper Ore',
                            'Obsidian',
                            'Bedrock',
                            'Water',
                            'Lava',
                            'Glass',
                            'Ice',
                            'Snow',
                            'Clay',
                            'Netherrack',
                            'Soul Sand',
                            'End Stone',
                            'Purpur Block',
                            'Prismarine',
                            'Sea Lantern',
                            'Glowstone',
                            'Beacon',
                            'Enchanting Table',
                            'Anvil',
                            'Crafting Table',
                            'Furnace',
                            'Chest',
                            'Shulker Box'
                        ])
                    ],
                    [
                        'name' => 'Biome',
                        'slug' => 'biome',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Plains',
                            'Forest',
                            'Taiga',
                            'Swamp',
                            'Mountains',
                            'Desert',
                            'Tundra',
                            'Jungle',
                            'Savanna',
                            'Badlands',
                            'Ocean',
                            'Deep Ocean',
                            'Frozen Ocean',
                            'River',
                            'Beach',
                            'Mushroom Fields',
                            'Nether Wastes',
                            'Crimson Forest',
                            'Warped Forest',
                            'Soul Sand Valley',
                            'Basalt Deltas',
                            'The End',
                            'End Highlands',
                            'End Midlands',
                            'Small End Islands',
                            'End Barrens',
                            'Dripstone Caves',
                            'Lush Caves',
                            'Deep Dark',
                            'Mangrove Swamp',
                            'Cherry Grove'
                        ])
                    ],
                    [
                        'name' => 'Tool Type',
                        'slug' => 'tool-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Wooden Pickaxe',
                            'Stone Pickaxe',
                            'Iron Pickaxe',
                            'Gold Pickaxe',
                            'Diamond Pickaxe',
                            'Netherite Pickaxe',
                            'Wooden Axe',
                            'Stone Axe',
                            'Iron Axe',
                            'Gold Axe',
                            'Diamond Axe',
                            'Netherite Axe',
                            'Wooden Shovel',
                            'Stone Shovel',
                            'Iron Shovel',
                            'Gold Shovel',
                            'Diamond Shovel',
                            'Netherite Shovel',
                            'Wooden Hoe',
                            'Stone Hoe',
                            'Iron Hoe',
                            'Gold Hoe',
                            'Diamond Hoe',
                            'Netherite Hoe',
                            'Wooden Sword',
                            'Stone Sword',
                            'Iron Sword',
                            'Gold Sword',
                            'Diamond Sword',
                            'Netherite Sword',
                            'Bow',
                            'Crossbow',
                            'Trident',
                            'Shears',
                            'Flint and Steel',
                            'Fishing Rod',
                            'Bucket',
                            'Compass',
                            'Clock',
                            'Map',
                            'Elytra'
                        ])
                    ],
                    [
                        'name' => 'Armor Type',
                        'slug' => 'armor-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            "Wooden Hoe",
                            "Stone Hoe",
                            "Iron Hoe",
                            "Golden Hoe",
                            "Diamond Hoe",
                            "Netherite Hoe",
                            'Leather Helmet',
                            'Leather Chestplate',
                            'Leather Leggings',
                            'Leather Boots',
                            'Chainmail Helmet',
                            'Chainmail Chestplate',
                            'Chainmail Leggings',
                            'Chainmail Boots',
                            'Iron Helmet',
                            'Iron Chestplate',
                            'Iron Leggings',
                            'Iron Boots',
                            'Gold Helmet',
                            'Gold Chestplate',
                            'Gold Leggings',
                            'Gold Boots',
                            'Diamond Helmet',
                            'Diamond Chestplate',
                            'Diamond Leggings',
                            'Diamond Boots',
                            'Netherite Helmet',
                            'Netherite Chestplate',
                            'Netherite Leggings',
                            'Netherite Boots',
                            'Turtle Shell',
                            'Shield'
                        ])
                    ],
                    [
                        'name' => 'Mob Type',
                        'slug' => 'mob-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Zombie',
                            'Skeleton',
                            'Creeper',
                            'Spider',
                            'Cave Spider',
                            'Enderman',
                            'Witch',
                            'Slime',
                            'Magma Cube',
                            'Ghast',
                            'Blaze',
                            'Wither Skeleton',
                            'Zombified Piglin',
                            'Piglin',
                            'Piglin Brute',
                            'Hoglin',
                            'Zoglin',
                            'Strider',
                            'Shulker',
                            'Endermite',
                            'Silverfish',
                            'Guardian',
                            'Elder Guardian',
                            'Phantom',
                            'Drowned',
                            'Husk',
                            'Stray',
                            'Wither',
                            'Ender Dragon',
                            'Chicken',
                            'Cow',
                            'Pig',
                            'Sheep',
                            'Horse',
                            'Donkey',
                            'Mule',
                            'Llama',
                            'Wolf',
                            'Cat',
                            'Parrot',
                            'Rabbit',
                            'Bat',
                            'Squid',
                            'Glow Squid',
                            'Dolphin',
                            'Turtle',
                            'Polar Bear',
                            'Panda',
                            'Fox',
                            'Bee',
                            'Villager',
                            'Wandering Trader',
                            'Iron Golem',
                            'Snow Golem',
                            'Allay',
                            'Frog',
                            'Tadpole',
                            'Warden',
                            'Axolotl',
                            'Goat',
                            'Glow Berries'
                        ])
                    ],
                    [
                        'name' => 'Enchantment',
                        'slug' => 'enchantment',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Sharpness',
                            'Smite',
                            'Bane of Arthropods',
                            'Looting',
                            'Fire Aspect',
                            'Knockback',
                            'Sweeping Edge',
                            'Efficiency',
                            'Fortune',
                            'Silk Touch',
                            'Unbreaking',
                            'Mending',
                            'Power',
                            'Punch',
                            'Flame',
                            'Infinity',
                            'Piercing',
                            'Quick Charge',
                            'Multishot',
                            'Protection',
                            'Fire Protection',
                            'Blast Protection',
                            'Projectile Protection',
                            'Feather Falling',
                            'Thorns',
                            'Respiration',
                            'Aqua Affinity',
                            'Depth Strider',
                            'Frost Walker',
                            'Soul Speed',
                            'Swift Sneak',
                            'Loyalty',
                            'Impaling',
                            'Riptide',
                            'Channeling',
                            'Luck of the Sea',
                            'Lure',
                            'Curse of Binding',
                            'Curse of Vanishing'
                        ])
                    ],
                    [
                        'name' => 'Structure Type',
                        'slug' => 'structure-type',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Village',
                            'Pillager Outpost',
                            'Woodland Mansion',
                            'Ocean Monument',
                            'Buried Treasure',
                            'Shipwreck',
                            'Ruined Portal',
                            'Dungeon',
                            'Mineshaft',
                            'Stronghold',
                            'End City',
                            'End Ship',
                            'Nether Fortress',
                            'Bastion Remnant',
                            'Desert Pyramid',
                            'Jungle Pyramid',
                            'Igloo',
                            'Swamp Hut',
                            'Ancient City',
                            'Trail Ruins',
                            'Cherry Village',
                            'Mangrove Village'
                        ])
                    ],
                    [
                        'name' => 'Dimension',
                        'slug' => 'dimension',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Overworld',
                            'The Nether',
                            'The End'
                        ])
                    ],
                    [
                        'name' => 'Rarity Level',
                        'slug' => 'rarity-level',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Common',
                            'Uncommon',
                            'Rare',
                            'Epic',
                            'Legendary',
                            'Mythic'
                        ])
                    ],
                    [
                        'name' => 'Game Mode',
                        'slug' => 'game-mode',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Survival',
                            'Creative',
                            'Adventure',
                            'Spectator',
                            'Hardcore'
                        ])
                    ],
                    [
                        'name' => 'Y Level',
                        'slug' => 'y-level',
                        'display_type' => 'number',
                        'unit' => 'blocks',
                        'allowed_values' => null // Range from -64 to 320
                    ],
                    [
                        'name' => 'Durability',
                        'slug' => 'durability',
                        'display_type' => 'number',
                        'unit' => 'uses',
                        'allowed_values' => null
                    ],
                    [
                        'name' => 'Stack Size',
                        'slug' => 'stack-size',
                        'display_type' => 'number',
                        'unit' => 'items',
                        'allowed_values' => json_encode([
                            1,
                            16,
                            64
                        ])
                    ],
                    [
                        'name' => 'Light Level',
                        'slug' => 'light-level',
                        'display_type' => 'number',
                        'unit' => 'level',
                        'allowed_values' => json_encode([
                            0,
                            1,
                            2,
                            3,
                            4,
                            5,
                            6,
                            7,
                            8,
                            9,
                            10,
                            11,
                            12,
                            13,
                            14,
                            15
                        ])
                    ],
                    [
                        'name' => 'Potion Effect',
                        'slug' => 'potion-effect',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Speed',
                            'Slowness',
                            'Haste',
                            'Mining Fatigue',
                            'Strength',
                            'Instant Health',
                            'Instant Damage',
                            'Jump Boost',
                            'Nausea',
                            'Regeneration',
                            'Resistance',
                            'Fire Resistance',
                            'Water Breathing',
                            'Invisibility',
                            'Blindness',
                            'Night Vision',
                            'Hunger',
                            'Weakness',
                            'Poison',
                            'Wither',
                            'Health Boost',
                            'Absorption',
                            'Saturation',
                            'Glowing',
                            'Levitation',
                            'Luck',
                            'Bad Luck',
                            'Slow Falling',
                            'Conduit Power',
                            'Dolphins Grace',
                            'Bad Omen',
                            'Hero of the Village',
                            'Darkness'
                        ])
                    ],
                    [
                        'name' => 'Food Value',
                        'slug' => 'food-value',
                        'display_type' => 'number',
                        'unit' => 'hunger points',
                        'allowed_values' => null
                    ],
                    [
                        'name' => 'Saturation',
                        'slug' => 'saturation',
                        'display_type' => 'number',
                        'unit' => 'points',
                        'allowed_values' => null
                    ],
                    [
                        'name' => 'Blast Resistance',
                        'slug' => 'blast-resistance',
                        'display_type' => 'number',
                        'unit' => 'resistance',
                        'allowed_values' => null
                    ],
                    [
                        'name' => 'Hardness',
                        'slug' => 'hardness',
                        'display_type' => 'number',
                        'unit' => 'hardness',
                        'allowed_values' => null
                    ],
                    [
                        'name' => 'Version Added',
                        'slug' => 'version-added',
                        'display_type' => 'text',
                        'allowed_values' => json_encode([
                            'Classic',
                            'Alpha',
                            'Beta',
                            '1.0',
                            '1.1',
                            '1.2',
                            '1.3',
                            '1.4',
                            '1.5',
                            '1.6',
                            '1.7',
                            '1.8',
                            '1.9',
                            '1.10',
                            '1.11',
                            '1.12',
                            '1.13',
                            '1.14',
                            '1.15',
                            '1.16',
                            '1.17',
                            '1.18',
                            '1.19',
                            '1.20',
                            '1.21'
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