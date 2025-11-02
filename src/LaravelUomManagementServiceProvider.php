<?php

namespace Azaharizaman\LaravelUomManagement;

use Azaharizaman\LaravelUomManagement\Console\Commands\UomConvertCommand;
use Azaharizaman\LaravelUomManagement\Console\Commands\UomListUnitsCommand;
use Azaharizaman\LaravelUomManagement\Console\Commands\UomSeedCommand;
use Azaharizaman\LaravelUomManagement\Contracts\AliasResolver as AliasResolverContract;
use Azaharizaman\LaravelUomManagement\Contracts\CompoundUnitConverter as CompoundUnitConverterContract;
use Azaharizaman\LaravelUomManagement\Contracts\CustomUnitRegistrar as CustomUnitRegistrarContract;
use Azaharizaman\LaravelUomManagement\Contracts\PackagingCalculator as PackagingCalculatorContract;
use Azaharizaman\LaravelUomManagement\Contracts\UnitConverter as UnitConverterContract;
use Azaharizaman\LaravelUomManagement\Database\Seeders\UomDatabaseSeeder;
use Azaharizaman\LaravelUomManagement\Services\DefaultAliasResolver;
use Azaharizaman\LaravelUomManagement\Services\DefaultCompoundUnitConverter;
use Azaharizaman\LaravelUomManagement\Services\DefaultCustomUnitRegistrar;
use Azaharizaman\LaravelUomManagement\Services\DefaultPackagingCalculator;
use Azaharizaman\LaravelUomManagement\Services\DefaultUnitConverter;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Database\DatabaseManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelUomManagementServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-uom-management')
            ->hasConfigFile('uom')
            ->hasMigration('create_uom_tables')
            ->hasCommands([
                UomSeedCommand::class,
                UomConvertCommand::class,
                UomListUnitsCommand::class,
            ]);
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

        $this->app->singleton(AliasResolverContract::class, DefaultAliasResolver::class);
        $this->app->alias(AliasResolverContract::class, 'uom.aliases');

        $this->app->singleton(CompoundUnitConverterContract::class, function ($app) {
            return new DefaultCompoundUnitConverter(
                $app->make(UnitConverterContract::class),
                $app->make(ConfigRepository::class)
            );
        });
        $this->app->alias(CompoundUnitConverterContract::class, 'uom.compound');

        $this->app->singleton(PackagingCalculatorContract::class, function ($app) {
            return new DefaultPackagingCalculator(
                $app->make(AliasResolverContract::class),
                $app->make(ConfigRepository::class)
            );
        });
        $this->app->alias(PackagingCalculatorContract::class, 'uom.packaging');

        $this->app->singleton(CustomUnitRegistrarContract::class, function ($app) {
            return new DefaultCustomUnitRegistrar(
                $app->make(DatabaseManager::class),
                $app->make(ConfigRepository::class)
            );
        });
        $this->app->alias(CustomUnitRegistrarContract::class, 'uom.custom-units');
    }
}
