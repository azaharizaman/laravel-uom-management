<?php

declare(strict_types=1);

namespace Azaharizaman\LaravelUomManagement\Tests\Unit;

use Azaharizaman\LaravelUomManagement\LaravelUomManagementServiceProvider;
use Azaharizaman\LaravelUomManagement\Services\DefaultAliasResolver;
use Azaharizaman\LaravelUomManagement\Services\DefaultCompoundUnitConverter;
use Azaharizaman\LaravelUomManagement\Services\DefaultCustomUnitRegistrar;
use Azaharizaman\LaravelUomManagement\Services\DefaultPackagingCalculator;
use Azaharizaman\LaravelUomManagement\Services\DefaultUnitConverter;
use Azaharizaman\LaravelUomManagement\Tests\TestCase;
use Illuminate\Support\ServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function testContainerBindingsAreRegistered(): void
    {
        $this->assertInstanceOf(DefaultUnitConverter::class, $this->app->make('uom.converter'));
        $this->assertSame($this->app->make('uom.converter'), $this->app->make('uom.converter'));

        $this->assertInstanceOf(DefaultAliasResolver::class, $this->app->make('uom.aliases'));
        $this->assertInstanceOf(DefaultCompoundUnitConverter::class, $this->app->make('uom.compound'));
        $this->assertInstanceOf(DefaultPackagingCalculator::class, $this->app->make('uom.packaging'));
        $this->assertInstanceOf(DefaultCustomUnitRegistrar::class, $this->app->make('uom.custom-units'));
    }

    public function testSeederPublishingIsConfigured(): void
    {
        $provider = new LaravelUomManagementServiceProvider($this->app);
        $provider->bootingPackage();

    $paths = ServiceProvider::pathsToPublish(LaravelUomManagementServiceProvider::class, 'laravel-uom-management-seeders');

    $destination = database_path('seeders/UomDatabaseSeeder.php');

    $this->assertNotEmpty($paths);
    $this->assertContains($destination, array_values($paths));
    }
}
