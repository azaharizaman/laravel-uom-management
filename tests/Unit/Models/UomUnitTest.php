<?php

namespace Azaharizaman\LaravelUomManagement\Tests\Unit\Models;

use Azaharizaman\LaravelUomManagement\Models\UomAlias;
use Azaharizaman\LaravelUomManagement\Models\UomType;
use Azaharizaman\LaravelUomManagement\Models\UomUnit;
use Azaharizaman\LaravelUomManagement\Models\UomUnitGroup;
use Azaharizaman\LaravelUomManagement\Tests\TestCase;

class UomUnitTest extends TestCase
{
    public function test_factory_creates_unit_with_relationships(): void
    {
    $type = UomType::factory()->create(['slug' => 'test-type']);
        $group = UomUnitGroup::factory()->create();

        $unit = UomUnit::factory()->for($type, 'type')->create(['code' => 'UNITX']);
        $alias = UomAlias::factory()->for($unit, 'unit')->create(['alias' => 'unitx-alt']);
        $group->units()->attach($unit->getKey());

        $unit->refresh();

        $this->assertTrue($unit->type->is($type));
        $this->assertTrue($unit->aliases->contains($alias));
        $this->assertTrue($unit->groups->contains($group));
    }
}
