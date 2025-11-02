<?php

namespace Azaharizaman\LaravelUomManagement\Tests\Feature;

use Azaharizaman\LaravelUomManagement\Contracts\CustomUnitRegistrar;
use Azaharizaman\LaravelUomManagement\Exceptions\ConversionException;
use Azaharizaman\LaravelUomManagement\Models\UomCustomConversion;
use Azaharizaman\LaravelUomManagement\Models\UomCustomUnit;
use Azaharizaman\LaravelUomManagement\Models\UomType;
use Azaharizaman\LaravelUomManagement\Tests\TestCase;

class CustomUnitRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seedBaselineDataset();
    }

    public function test_it_registers_custom_units_with_conversions(): void
    {
        $registrar = $this->app->make(CustomUnitRegistrar::class);
        $massType = UomType::query()->where('slug', 'mass')->firstOrFail();

        $primary = $registrar->register([
            'code' => 'BX',
            'name' => 'Box',
            'uom_type_id' => $massType->getKey(),
            'conversion_factor' => '0.5',
        ]);

        $secondary = $registrar->register([
            'code' => 'CR',
            'name' => 'Crate',
            'uom_type_id' => $massType->getKey(),
            'conversion_factor' => '2',
        ], null, [
            [
                'target' => $primary->code,
                'factor' => '4',
                'is_linear' => true,
            ],
        ]);

        $this->assertInstanceOf(UomCustomUnit::class, $primary);
        $this->assertInstanceOf(UomCustomUnit::class, $secondary);

        $conversion = UomCustomConversion::query()
            ->where('source_custom_unit_id', $secondary->getKey())
            ->where('target_custom_unit_id', $primary->getKey())
            ->first();

        $this->assertNotNull($conversion);
        $this->assertSame('4.000000000000', $conversion->factor);
    }

    public function test_registering_duplicate_code_for_same_owner_throws_exception(): void
    {
        $this->expectException(ConversionException::class);

        $registrar = $this->app->make(CustomUnitRegistrar::class);
        $massType = UomType::query()->where('slug', 'mass')->firstOrFail();

        $payload = [
            'code' => 'BX',
            'name' => 'Box',
            'uom_type_id' => $massType->getKey(),
            'conversion_factor' => '1',
        ];

        $registrar->register($payload, ['owner_type' => 'inventory-item', 'owner_id' => 7]);

        $registrar->register($payload, ['owner_type' => 'inventory-item', 'owner_id' => 7]);
    }
}
