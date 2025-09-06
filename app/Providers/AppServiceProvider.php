<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar la zona horaria de Carbon para que coincida con la configuración
        Carbon::setLocale(config('app.locale', 'es'));
        
        // Establecer la zona horaria por defecto de PHP
        date_default_timezone_set(config('app.timezone'));
        
        // También configurar Carbon para usar la misma zona horaria
        config(['app.timezone' => config('app.timezone', date_default_timezone_get())]);
    }
}