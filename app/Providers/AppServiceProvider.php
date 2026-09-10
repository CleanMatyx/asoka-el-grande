<?php

namespace App\Providers;

use App\Models\AjusteSitio;
use App\Models\MenuSitio;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer(['layouts.app', 'partials.header', 'partials.footer'], function ($view): void {
            $view->with('ajustesSitio', AjusteSitio::actual());
        });

        View::composer('partials.header', function ($view): void {
            $view->with('menusCabecera', MenuSitio::query()->enUbicacion('cabecera')->get());
        });

        View::composer('partials.footer', function ($view): void {
            $view->with('menusPie', MenuSitio::query()->enUbicacion('pie')->get());
        });
    }
}
