<?php

namespace App\Providers;

use App\Http\Middleware\AdminMiddleware;
use App\View\Components\AdminLayout;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;

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
        $router = $this->app->make(Router::class);

        // Daftarkan middleware dengan nama 'admin'
        $router->aliasMiddleware('admin', AdminMiddleware::class);

        Blade::component('admin-layout', AdminLayout::class);
    }
}
