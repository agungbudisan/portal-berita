<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NewsApiService;

class NewsApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NewsApiService::class, function ($app) {
            return new NewsApiService();
        });
    }

    public function boot()
    {
        //
    }
}
