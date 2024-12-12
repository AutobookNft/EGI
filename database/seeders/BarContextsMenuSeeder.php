<?php

namespace Database\Seeders;

use App\Models\BarContextMenu;
use App\Models\BarContextSummarie;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BarContextsMenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [

             // Menu per il summary 'Permisions & Roles'
             [
                'position' => 1,
                'context' => 'dashboard',
                'summary' => 'authorizations', // ID del summary 'Admin'
                'name' => 'permissions_roles',
                'route' => 'admin.roles.index',
                'permission' => 'manage_roles',
                'tip' => 'permissions_roles',
                'icon' => 'permissions_roles',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Menu per il summary 'Permisions & Roles'
            [
                'position' => 2,
                'context' => 'dashboard',
                'summary' => 'authorizations',
                'name' => 'assign_roles',
                'route' => 'admin.assign.role.form',
                'permission' => 'manage_roles',
                'tip' => 'assign_roles',
                'icon' => 'assign_roles',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Menu per il summary 'Permisions & Roles'
            [
                'position' => 3,
                'context' => 'dashboard',
                'summary' => 'authorizations',
                'name' => 'assign_permissions',
                'route' => 'admin.assign.permissions.form',
                'permission' => 'manage_roles',
                'tip' => 'manage_roles',
                'icon' => 'assign_permissions',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],


            // Menu per il summary 'Gestione Collezioni'
            [
                'position' => 1,
                'context' => 'dashboard',
                'summary' => 'collection_handling', // ID del summary 'Gestione Collezioni'
                'name' => 'open_collection',
                'route' => 'collections.carousel',
                'permission' => 'view_collection',
                'tip' => 'collection_handling',
                'icon' => 'open',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],


        ];

        foreach ($menus as $menu) {
            $summary = BarContextSummarie::where('context', $menu['context'])
                                         ->where('summary', $menu['summary'])
                                         ->first();

            if ($summary) {
                BarContextMenu::updateOrCreate(
                    [
                        'context' => $menu['context'],
                        'summary' => $menu['summary'],
                        'name' => $menu['name'],
                    ],
                    $menu
                );
            }
        }
    }
}
