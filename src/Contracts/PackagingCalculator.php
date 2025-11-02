<?php

namespace Azaharizaman\LaravelUomManagement\Contracts;

use Azaharizaman\LaravelUomManagement\Models\UomPackaging;
use Azaharizaman\LaravelUomManagement\Models\UomUnit;
use Brick\Math\BigDecimal;

interface PackagingCalculator
{
    public function resolvePackaging(UomUnit|string|int $base, UomUnit|string|int $package): UomPackaging;

    public function packagesToBase(BigDecimal|int|float|string $packages, UomPackaging|int $packaging, ?int $precision = null): BigDecimal;

    public function baseToPackages(BigDecimal|int|float|string $baseQuantity, UomPackaging|int $packaging, ?int $precision = null): BigDecimal;
}
