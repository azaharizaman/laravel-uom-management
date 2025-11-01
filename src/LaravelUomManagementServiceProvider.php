<?php

namespace Azaharizaman\LaravelUomManagement;

use Azaharizaman\LaravelUomManagement\Database\Seeders\UomDatabaseSeeder;
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
    }
}
