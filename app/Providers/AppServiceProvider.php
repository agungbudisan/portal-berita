<?php

namespace App\Providers;

use App\Models\Comment;
use App\Policies\CommentPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        /// Default string length untuk MySQL
        Schema::defaultStringLength(191);

        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Mendaftarkan CommentPolicy
        Gate::policy(Comment::class, CommentPolicy::class);

        // Query logging untuk debugging
        if (config('app.debug')) {
            DB::listen(function($query) {
                if ($query->time > 100) {
                    Log::channel('stderr')->info(
                        'Slow query: ' . $query->sql,
                        ['bindings' => $query->bindings, 'time' => $query->time]
                    );
                }
            });
        }
    }
}
