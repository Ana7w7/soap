<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ProductService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar el ProductService en el contenedor de Laravel
        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService();
        });
    }

    public function boot()
    {
        //
    }
}

?>