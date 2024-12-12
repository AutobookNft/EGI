<?php

namespace App\Providers;


use App\Repositories\IconRepository;
use App\Services\FileStorageService;
use Illuminate\Support\ServiceProvider;


use App\Models\User;
use App\Models\TeamWallet as Wallet;
use App\Models\Collection;
use App\Models\Egi;
use App\Policies\ProfilePolicy;
use App\Policies\TeamWalletPolicy as WalletPolicy;
use App\Policies\CollectionPolicy;
use App\Policies\EgiPolicy;

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
        $this->app->singleton(FileStorageService::class, function () {
            return new FileStorageService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
