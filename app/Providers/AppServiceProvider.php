<?php

namespace LeaseTracker\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('LeaseTracker\Services\Vehicle\VehicleServiceInterface', 'LeaseTracker\Services\Vehicle\VehicleService');
        $this->app->bind('LeaseTracker\Services\Mileage\MileageServiceInterface', 'LeaseTracker\Services\Mileage\MileageService');
        $this->app->bind('LeaseTracker\Services\Calculation\CalculationServiceInterface', 'LeaseTracker\Services\Calculation\CalculationService');
        $this->app->bind('LeaseTracker\Services\VehicleImage\VehicleImageServiceInterface', 'LeaseTracker\Services\VehicleImage\GoogleVehicleImageService');
        $this->app->bind('LeaseTracker\Repositories\VehicleRepositoryInterface', 'LeaseTracker\Repositories\VehicleRepository');
        $this->app->bind('LeaseTracker\Repositories\MileEntryRepositoryInterface', 'LeaseTracker\Repositories\MileEntryRepository');
    }
}
