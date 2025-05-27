<?php

namespace App\Providers;

use App\Domain\Interfaces\FavoriteProductRepositoryInterface;
use App\Domain\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Repositories\ExternalProductRepository;
use App\Infrastructure\Repositories\FavoriteProductRepository;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ExternalProductRepository::class);
        $this->app->bind(FavoriteProductRepositoryInterface::class, FavoriteProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
