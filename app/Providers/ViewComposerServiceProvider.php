<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * 🎯 Condivide le statistiche di impatto globale con tutte le viste
         * 📡 Interrogabile: fornisce dati di impatto ambientale a livello globale
         *
         * @package ViewComposers
         */
        View::composer('*', function ($view) {
            // MVP: Valore hardcoded per le statistiche di impatto
            // TODO: In futuro, recuperare questo valore da un servizio o repository
            $totalPlasticRecovered = 5241.38;

            $view->with('totalPlasticRecovered', $totalPlasticRecovered);
        });
    }
}
