<?php

namespace Ultra\EgiModule\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\EgiModule\Services\CollectionService;
use Ultra\EgiModule\Services\UserRoleService;
use Ultra\EgiModule\Services\WalletService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\Contracts\UltraLoggerInterface;
use Ultra\UltraLogManager\UltraLogManager;// Per debug iniziale

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

        // 1. Registra il binding per UltraLoggerInterface
        // $this->app->bind(UltraLoggerInterface::class, UltraLogManager::class);

        // 2. Registra il binding per WalletServiceInterface
        $this->app->bind(WalletServiceInterface::class, WalletService::class);

        // 3. Registra il binding per UserRoleServiceInterface
        $this->app->bind(UserRoleServiceInterface::class, UserRoleService::class);


        // 4. Registra WalletService con le sue dipendenze
        $this->app->bind(WalletService::class, function ($app) {
            return new WalletService(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLogManager::class)
            );
        });

        // 5. Registra CollectionService con le sue dipendenze
        // Ora possiamo usare le interfacce qui, perché abbiamo i binding
        $this->app->bind(CollectionService::class, function ($app) {
            return new CollectionService(
                $app->make(UltraLoggerInterface::class), // Usa l'interfaccia!
                $app->make(WalletServiceInterface::class), // Usa l'interfaccia!
                $app->make(UserRoleServiceInterface::class), // Usa l'interfaccia!
                'florenceegi'
            );
        });

        // Register UserRoleService
        $this->app->bind(UserRoleServiceInterface::class, function ($app) {
            return new UserRoleService(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLoggerInterface::class)
            );
        });

        // 6. Crea alias per accesso più semplice
        $this->app->alias(CollectionService::class, 'egi.collection_service');
        $this->app->alias(UserRoleServiceInterface::class, 'egi.user_role_service');

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
