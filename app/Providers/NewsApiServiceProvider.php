<?php

namespace App\Providers;

use App\Services\NewsApiService;
use Illuminate\Support\ServiceProvider;

class NewsApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NewsApiService::class, function ($app) {
            return new NewsApiService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
