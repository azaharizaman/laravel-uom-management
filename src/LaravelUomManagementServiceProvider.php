<?php

namespace Azaharizaman\LaravelUomManagement;

use Azaharizaman\LaravelUomManagement\Contracts\UnitConverter as UnitConverterContract;
use Azaharizaman\LaravelUomManagement\Database\Seeders\UomDatabaseSeeder;
use Azaharizaman\LaravelUomManagement\Services\DefaultUnitConverter;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelUomManagementServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-uom-management')
            ->hasConfigFile('uom')
            ->hasMigration('create_uom_tables');
    }

    public function bootingPackage(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/seeders/UomDatabaseSeeder.php' => database_path('seeders/UomDatabaseSeeder.php'),
            ], 'laravel-uom-management-seeders');
        }
    }

    public function registeringPackage(): void
    {
        $this->app->bindIf('uom.database.seeder', fn () => UomDatabaseSeeder::class);

        $this->app->singleton(UnitConverterContract::class, function ($app) {
            return new DefaultUnitConverter($app->make(ConfigRepository::class));
        });

        $this->app->alias(UnitConverterContract::class, 'uom.converter');
    }
}
