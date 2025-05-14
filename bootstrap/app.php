<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\FetchNewsFromApi;
use Illuminate\Support\Facades\URL;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        // Tambahkan service provider Anda di sini
        App\Providers\ScheduleServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Deteksi admin section untuk mengoptimalkan request
        if (defined('ADMIN_SECTION') && ADMIN_SECTION === true) {
            $middleware->prepend(\App\Http\Middleware\OptimizeAdminRequests::class);
        }

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'optimize.admin' => \App\Http\Middleware\OptimizeAdminRequests::class,
        ]);
    })
    ->withCommands([
        FetchNewsFromApi::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // Tambahkan custom exception handling untuk admin
        $exceptions->reportable(function (\Throwable $e) {
            if (request()->is('admin/*') || defined('ADMIN_SECTION')) {
                \Illuminate\Support\Facades\Log::channel('stderr')
                    ->error('Admin Error: ' . $e->getMessage(), [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
            }
        });

        // Render custom admin error page
        $exceptions->renderable(function (\Throwable $e) {
            if ((request()->is('admin/*') || defined('ADMIN_SECTION')) &&
                !app()->environment('local')) {
                return response()->view('admin.dashboard-error', [
                    'error' => config('app.debug') ? $e->getMessage() : 'An error occurred loading the dashboard'
                ], 500);
            }
        });
    })
    ->create();
