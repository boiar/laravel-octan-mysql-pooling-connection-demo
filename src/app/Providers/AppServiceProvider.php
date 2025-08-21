<?php

namespace App\Providers;

use App\Services\MySQLPool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Facades\Octane;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind MySQLPool as a singleton
        $this->app->singleton(MySQLPool::class, function ($app) {
            return new MySQLPool(20); // Pool size = 20 connections
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
