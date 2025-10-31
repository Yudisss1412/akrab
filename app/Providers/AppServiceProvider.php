<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

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
        View::addExtension('blade.view', 'blade');
    }
    
    /**
     * Register any application services after loading.
     */
    public function registerFacades(): void
    {
        // Register Intervention Image Facade if needed
        Facade::clearResolvedInstances();
    }
}
