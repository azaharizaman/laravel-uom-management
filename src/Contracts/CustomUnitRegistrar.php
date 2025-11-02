<?php

namespace Azaharizaman\LaravelUomManagement\Contracts;

use Azaharizaman\LaravelUomManagement\Models\UomCustomUnit;
use Illuminate\Database\Eloquent\Model;

interface CustomUnitRegistrar
{
    /**
     * @param array<string, mixed> $attributes
     * @param Model|array<string, mixed>|null $owner
     * @param array<int, array<string, mixed>> $customConversions
     */
    public function register(array $attributes, Model|array|null $owner = null, array $customConversions = []): UomCustomUnit;
}
