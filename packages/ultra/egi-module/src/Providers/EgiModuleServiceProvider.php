<?php

namespace Ultra\EgiModule\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log; // Per debug iniziale

class EgiModuleServiceProvider extends ServiceProvider
{

    /**
    * Log channel for this handler.
    * @var string
    */
    protected string $logChannel = 'upload'; // Default channel

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Puoi unire configurazioni qui se crei un file config/egi.php
        // $this->mergeConfigFrom(__DIR__.'/../../config/egi.php', 'egi');

        // Log::channel($this->logChannel)->debug('EgiModuleServiceProvider registered.'); // Log per verifica
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         // *** RIGA CHIAVE PER LE VISTE ***
        // Dice a Laravel:
        // - Cerca le viste in 'packages/ultra/egi-module/resources/views'
        // - Assegna a queste viste il namespace 'egimodule'
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'egimodule');

        // Aggiungi qui anche la configurazione per la pubblicazione (se vuoi)
        if ($this->app->runningInConsole()) {

            $this->publishes([
                // Pubblica il file di config del pacchetto in config/egi.php dell'app
                __DIR__.'/../../config/egi.php' => config_path('egi.php'),
            ], 'egi-config'); // Tag specifico per la config EGI

            $this->publishes([
                // Pubblica le viste in resources/views/vendor/egimodule
                __DIR__.'/../../resources/views' => resource_path('views/vendor/egimodule'),
            ], 'egi-views'); // Tag specifico per le viste EGI

            // Pubblica altri assets se necessario (config, migrations, etc.)

        }

        // *** RIGA CHIAVE PER LE ROTTE ***
        // Dice a Laravel di caricare il file di rotte specifico del pacchetto.
        // Applica automaticamente il middleware 'web' a queste rotte.
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php'); // O api.php se sono rotte API

        // Carica traduzioni se necessario
        // $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'egimodule');

        // Registra comandi console se necessario
        // $this->commands([...]);

    }
}
