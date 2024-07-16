<?php

namespace App\Providers;

use App\Repositories\CampaignRepository;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\PIDMigrationService;
use App\Services\Contracts\PIDMigrationServiceInterface;
use App\Services\ProductService;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Support\ServiceProvider;

class  AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CampaignRepositoryInterface::class, CampaignRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(PIDMigrationServiceInterface::class, PIDMigrationService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
