<?php

namespace MyPackage;

use Illuminate\Support\ServiceProvider;
use YourPackage\Console\GenerateRouteTests;

class MyPackageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            GenerateRouteTests::class,
        ]);
        $this->mergeConfigFrom(__DIR__ . '/../config/mypackage.php', 'mypackage');
    }


    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mypackage.php' => config_path('mypackage.php'),
        ]);
    }
}
