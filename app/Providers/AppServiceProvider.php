<?php

namespace App\Providers;

use App\Repositories\ClientRepository;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\DealRepository;
use App\Repositories\DealRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ClientRepositoryInterface::class,
            ClientRepository::class,
        );
        $this->app->bind(
            DealRepositoryInterface::class,
            DealRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
