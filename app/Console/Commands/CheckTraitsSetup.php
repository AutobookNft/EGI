<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\TraitCategory;
use App\Models\TraitType;

class CheckTraitsSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traits:check-setup {--fix : Automatically fix missing data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check traits setup and optionally fix missing categories/types';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking traits setup...');
        
        // Check if tables exist
        if (!Schema::hasTable('trait_categories')) {
            $this->error('Table trait_categories does not exist. Run migrations first.');
            return 1;
        }
        
        if (!Schema::hasTable('trait_types')) {
            $this->error('Table trait_types does not exist. Run migrations first.');
            return 1;
        }
        
        // Check categories
        $categoriesCount = TraitCategory::count();
        $systemCategoriesCount = TraitCategory::where('is_system', true)->count();
        
        $this->info("Categories in database: {$categoriesCount}");
        $this->info("System categories: {$systemCategoriesCount}");
        
        if ($categoriesCount === 0) {
            $this->warn('No categories found in database!');
            
            if ($this->option('fix')) {
                $this->info('Creating default categories...');
                $this->createDefaultCategories();
                $this->info('Default categories created!');
            } else {
                $this->info('Run with --fix to create default categories');
            }
        } else {
            $this->info('Categories check: OK');
            
            // Show existing categories
            $categories = TraitCategory::all();
            $this->table(['ID', 'Name', 'Slug', 'Is System', 'Collection ID'], 
                $categories->map(fn($c) => [
                    $c->id, 
                    $c->name, 
                    $c->slug, 
                    $c->is_system ? 'Yes' : 'No',
                    $c->collection_id ?? 'null'
                ])
            );
        }
        
        // Check types
        $typesCount = TraitType::count();
        $this->info("Trait types in database: {$typesCount}");
        
        if ($typesCount === 0 && $categoriesCount > 0) {
            $this->warn('No trait types found but categories exist!');
            
            if ($this->option('fix')) {
                $this->info('Creating default trait types...');
                $this->createDefaultTypes();
                $this->info('Default trait types created!');
            } else {
                $this->info('Run with --fix to create default trait types');
            }
        }
        
        return 0;
    }
    
    private function createDefaultCategories()
    {
        $categories = [
            ['name' => 'Materials', 'slug' => 'materials', 'icon' => '📦', 'sort_order' => 1],
            ['name' => 'Visual', 'slug' => 'visual', 'icon' => '🎨', 'sort_order' => 2],
            ['name' => 'Dimensions', 'slug' => 'dimensions', 'icon' => '📐', 'sort_order' => 3],
            ['name' => 'Special', 'slug' => 'special', 'icon' => '⚡', 'sort_order' => 4],
            ['name' => 'Sustainability', 'slug' => 'sustainability', 'icon' => '🌿', 'sort_order' => 5],
        ];
        
        foreach ($categories as $categoryData) {
            TraitCategory::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'icon' => $categoryData['icon'],
                'sort_order' => $categoryData['sort_order'],
                'is_system' => true,
                'collection_id' => null
            ]);
        }
    }
    
    private function createDefaultTypes()
    {
        $materials = TraitCategory::where('slug', 'materials')->first();
        $visual = TraitCategory::where('slug', 'visual')->first();
        $dimensions = TraitCategory::where('slug', 'dimensions')->first();
        
        if ($materials) {
            TraitType::create([
                'category_id' => $materials->id,
                'name' => 'Primary Material',
                'slug' => 'primary_material',
                'display_type' => 'text',
                'is_system' => true
            ]);
        }
        
        if ($visual) {
            TraitType::create([
                'category_id' => $visual->id,
                'name' => 'Color',
                'slug' => 'color',
                'display_type' => 'text',
                'is_system' => true
            ]);
        }
        
        if ($dimensions) {
            TraitType::create([
                'category_id' => $dimensions->id,
                'name' => 'Height',
                'slug' => 'height',
                'display_type' => 'number',
                'unit' => 'cm',
                'is_system' => true
            ]);
        }
    }
}
