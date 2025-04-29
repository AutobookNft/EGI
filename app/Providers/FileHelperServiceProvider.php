<?php


namespace App\Providers;

use App\Helpers\FileHelper;
use Illuminate\Support\ServiceProvider;

class FileHelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        // $this->app->singleton('file-helper', function () {
        //     return new FileHelper();
        // });
    }
}
