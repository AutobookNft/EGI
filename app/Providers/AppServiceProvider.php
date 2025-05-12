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
use Illuminate\Support\Facades\View;

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

        // $this->app->singleton(WalletService::class, function ($app) {
        //     return new WalletService();
        // });


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        // dd(View::getFinder()->getHints());
        
        // Registriamo un driver nominato "custom_database"
        Notification::extend('custom_database', function ($app) {
            return new CustomDatabaseChannel();
        });
    }
}
