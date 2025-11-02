<?php

namespace Azaharizaman\LaravelUomManagement\Contracts;

use Azaharizaman\LaravelUomManagement\Models\UomCompoundUnit;
use Brick\Math\BigDecimal;

interface CompoundUnitConverter
{
    public function convert(BigDecimal|int|float|string $value, UomCompoundUnit|int|string $from, UomCompoundUnit|int|string $to, ?int $precision = null): BigDecimal;
}
