<?php

namespace Azaharizaman\LaravelUomManagement\Database\Factories;

use Azaharizaman\LaravelUomManagement\Models\UomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UomTypeFactory extends Factory
{
    protected $model = UomType::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->optional()->sentence(8),
        ];
    }
}
