<?php

namespace Database\Seeders;

use App\Models\BarContextSummarie;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BarContextsSummariesSeeder extends Seeder
{
    public function run()
    {
        $summaries = [

            // Title Dashboard
            [
                'position' => 1,
                'context' => 'dashboard',
                'summary' => 'dashboard',
                'permission' => 'view_dashboard',
                'tip' => 'back_to_dashboard',
                'route' => 'dashboard',
                'icon' =>  'dashboard',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Title Collection
            [
                'position' => 1,
                'context' => 'collections',
                'summary' => 'collections',
                'permission' => 'view_collection',
                'tip' => 'manage_collection',
                'route' => 'collections',
                'icon' =>  'art_therapy',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Title Teams
            [
                'position' => 1,
                'context' => 'teams',
                'summary' => 'teams',
                'permission' => 'view_team',
                'tip' => 'manage_team',
                'route' => 'teams',
                'icon' =>  'teams',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // --------------------------------------------------------

            // Summary Permisions & Roles
            [
                'position' => 2,
                'context' => 'dashboard',
                'summary' => 'authorizations',
                'permission' => 'manage_roles',
                'tip' => 'permissions_roles',
                'route' => '',
                'icon' =>  'permissions_roles',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Summary Gestione Collezioni
            [
                'position' => 3,
                'context' => 'dashboard',
                'summary' => 'collection_handling',
                'permission' => 'view_collection',
                'tip' => 'manage_collection',
                'route' => '',
                'icon' =>  'art_therapy',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Summary Gestione Collezioni
            [
                'position' => 3,
                'context' => 'collections',
                'summary' => 'dashboard',
                'permission' => 'view_dashboard',
                'tip' => 'back_to_dashboard',
                'route' => 'dashboard',
                'icon' =>  'back',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Title Profile
            // [
            //     'context' => 'profile',
            //     'summary' => 'profile',
            //     'permission' => 'view_user',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],

        ];

        foreach ($summaries as $summary) {
            BarContextSummarie::updateOrCreate(
                ['context' => $summary['context'], 'summary' => $summary['summary']],
                $summary
            );
        }
    }
}
