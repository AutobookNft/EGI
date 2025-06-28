<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewDebugServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $relativePath = str_replace(
                base_path('resources/views/') . '/',
                '',
                $view->getPath()
            );

            view()->share('__current_view_path', $relativePath);

            // if (app()->environment('local')) {
            //     logger()->info('Current view: ' . $relativePath);
            // }
        });
    }
}