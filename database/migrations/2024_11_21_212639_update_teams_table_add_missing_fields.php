<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTeamsTableAddMissingFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('show')->index()->nullable()->after('name');
            $table->char('creator')->index()->nullable()->after('personal_team');
            $table->char('owner_wallet')->index()->nullable()->after('creator');
            $table->string('address', 100)->index()->nullable()->after('owner_wallet');
            $table->bigInteger('epp_id')->nullable()->after('address');
            $table->index('epp_id', 'epp_id');
            $table->bigInteger('EGI_asset_id')->nullable()->after('epp_id');
            $table->index('EGI_asset_id', 'EGI_asset_id');
            $table->string('collection_name')->nullable()->after('EGI_asset_id');
            $table->text('description')->nullable()->after('collection_name');
            $table->string('type', 10)->index()->nullable()->after('description');
            $table->string('path_image_banner', 1024)->nullable()->after('type');
            $table->string('path_image_card', 1024)->nullable()->after('path_image_banner');
            $table->string('path_image_avatar', 1024)->nullable()->after('path_image_card');
            $table->string('path_image_EGI', 1024)->nullable()->after('path_image_avatar');
            $table->string('url_collection_site')->nullable()->after('path_image_EGI');
            $table->integer('position')->index()->nullable()->after('url_collection_site');
            $table->string('token')->index()->nullable()->after('position');
            $table->foreignId('owner_id')->index()->nullable()->after('token');
            $table->integer('EGI_number')->nullable()->after('owner_id');
            $table->text('EGI_asset_roles')->nullable()->after('EGI_number');
            $table->float('floor_price')->nullable()->after('EGI_asset_roles');
            $table->string('path_image_to_ipfs')->nullable()->after('floor_price');
            $table->string('url_image_ipfs')->nullable()->after('path_image_to_ipfs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'show',
                'creator',
                'owner_wallet',
                'address',
                'epp_id',
                'EGI_asset_id',
                'collection_name',
                'description',
                'type',
                'path_image_banner',
                'path_image_card',
                'path_image_avatar',
                'path_image_EGI',
                'url_collection_site',
                'position',
                'token',
                'owner_id',
                'EGI_number',
                'EGI_asset_roles',
                'floor_price',
                'path_image_to_ipfs',
                'url_image_ipfs',
            ]);
        });
    }
}
