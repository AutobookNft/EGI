<?php

namespace Ultra\EgiModule\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log; // Per debug iniziale

class EgiModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Puoi unire configurazioni qui se crei un file config/egi.php
        // $this->mergeConfigFrom(__DIR__.'/../../config/egi.php', 'egi');

        Log::debug('EgiModuleServiceProvider registered.'); // Log per verifica
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Log::debug('EgiModuleServiceProvider booted.'); // Log per verifica

        // Registra rotte se le definisci nel pacchetto
        // $this->loadRoutesFrom(__DIR__.'/../../routes/egi.php');

        // Registra migration se le definisci nel pacchetto
        // $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Registra traduzioni se le definisci nel pacchetto
        // $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'egimodule');

        // Registra viste se le definisci nel pacchetto
        // $this->loadViewsFrom(__DIR__.'/../../resources/views', 'egimodule');

        // Rendi pubblicabili le risorse (config, assets, etc.)
        // if ($this->app->runningInConsole()) {
        //     $this->publishes([
        //       __DIR__.'/../../config/egi.php' => config_path('egi.php'),
        //     ], 'egi-config');
        //
        //     $this->publishes([
        //         __DIR__.'/../../resources/assets' => public_path('vendor/egimodule'),
        //     ], 'egi-assets');
        // }
    }
}
