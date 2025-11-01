<?php

namespace Azaharizaman\LaravelUomManagement\Database\Factories;

use Azaharizaman\LaravelUomManagement\Models\UomPackaging;
use Azaharizaman\LaravelUomManagement\Models\UomUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UomPackagingFactory extends Factory
{
    protected $model = UomPackaging::class;

    public function definition(): array
    {
        return [
            'base_unit_id' => UomUnit::factory(),
            'package_unit_id' => UomUnit::factory(),
            'quantity' => $this->faker->numberBetween(2, 48),
            'label' => $this->faker->optional()->lexify('Pack ??'),
            'metadata' => null,
        ];
    }
}
