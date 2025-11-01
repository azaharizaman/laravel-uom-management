<?php

namespace Azaharizaman\LaravelUomManagement\Database\Factories;

use Azaharizaman\LaravelUomManagement\Models\UomAlias;
use Azaharizaman\LaravelUomManagement\Models\UomUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UomAliasFactory extends Factory
{
    protected $model = UomAlias::class;

    public function definition(): array
    {
        return [
            'unit_id' => UomUnit::factory(),
            'alias' => Str::upper($this->faker->unique()->lexify('??')),
            'is_preferred' => $this->faker->boolean(20),
        ];
    }
}
