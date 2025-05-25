<?php

namespace App\Providers;


use App\Repositories\IconRepository;
use App\Services\FileStorageService;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Collection;
use App\Policies\ProfilePolicy;
use App\Policies\TeamWalletPolicy as WalletPolicy;
use App\Policies\CollectionPolicy;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Channels\CustomDatabaseChannel;
use App\Services\Notifications\WalletService;
use App\View\Components\EppHighlight;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Ultra\UltraLogManager\UltraLogManager;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        User::class => ProfilePolicy::class,
        Wallet::class => WalletPolicy::class,
        Collection::class => CollectionPolicy::class,
        // Egi::class => EgiPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IconRepository::class);

        // Registra il servizio di storage dei file
        $this->app->singleton(FileStorageService::class, function ($app) {
            return new FileStorageService();
        });

        $this->app->singleton(IconRepository::class, function ($app) {
            return new IconRepository(
                $app->make(UltraLogManager::class)
            );
        });

        // $this->app->singleton(WalletService::class, function ($app) {
        //     return new WalletService();
        // });


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Blade::component('epp-highlight', EppHighlight::class);
        Blade::component('collections-carousel', \App\View\Components\CollectionsCarousel::class);
        Blade::component('environmental-stats', \App\View\Components\EnvironmentalStats::class);
        Blade::component('egi-carousel', \App\View\Components\EgiCarousel::class);
        Blade::component('egi-stat-card', \App\View\Components\EgiStatCard::class);
        Blade::component('EgiStatsSection', \App\View\Components\EgiStatsSection::class);
        Blade::component('gdpr-layout', \App\View\Components\GdprLayout::class);

        // Registriamo un driver nominato "custom_database"
        Notification::extend('custom_database', function ($app) {
            return new CustomDatabaseChannel();
        });
    }
}
