<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            // Scheduling task
            $schedule->command('news:fetch')
                     ->hourly()
                     ->description('Fetch news from all active API sources');
        });
    }
}
