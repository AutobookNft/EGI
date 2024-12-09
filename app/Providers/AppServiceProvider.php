<?php

namespace App\Providers;


use App\Repositories\IconRepository;
use App\Services\FileStorageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
