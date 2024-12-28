<?php

namespace App\Providers;

use App\Interfaces\NewsApiServiceInterface;
use App\Interfaces\NYTimesServiceInterface;
use App\Services\NewsApiService;
use App\Services\NYTimesApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewsApiServiceInterface::class, NewsApiService::class);
        $this->app->bind(NYTimesServiceInterface::class, NYTimesApiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
