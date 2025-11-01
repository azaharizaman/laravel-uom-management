<?php

namespace Azaharizaman\LaravelUomManagement\Database\Factories;

use Azaharizaman\LaravelUomManagement\Models\UomItem;
use Azaharizaman\LaravelUomManagement\Models\UomItemPackaging;
use Azaharizaman\LaravelUomManagement\Models\UomPackaging;
use Illuminate\Database\Eloquent\Factories\Factory;

class UomItemPackagingFactory extends Factory
{
    protected $model = UomItemPackaging::class;

    public function definition(): array
    {
        return [
            'item_id' => UomItem::factory(),
            'packaging_id' => UomPackaging::factory(),
        ];
    }
}
