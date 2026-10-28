<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\MataKuliahRepository;
use App\Repositories\ConfigMataKuliahRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MataKuliahRepository::class, \App\Repositories\JsonMataKuliahRepository::class);
        $this->app->bind(\App\Repositories\KurikulumRepository::class, \App\Repositories\JsonKurikulumRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
