<?php

namespace Azaharizaman\LaravelUomManagement\Database\Factories;

use Azaharizaman\LaravelUomManagement\Models\UomCompoundComponent;
use Azaharizaman\LaravelUomManagement\Models\UomCompoundUnit;
use Azaharizaman\LaravelUomManagement\Models\UomUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UomCompoundComponentFactory extends Factory
{
    protected $model = UomCompoundComponent::class;

    public function definition(): array
    {
        return [
            'compound_unit_id' => UomCompoundUnit::factory(),
            'unit_id' => UomUnit::factory(),
            'exponent' => $this->faker->randomElement([-3, -2, -1, 1, 2, 3]),
        ];
    }
}
