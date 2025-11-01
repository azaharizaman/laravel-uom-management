<?php

namespace Azaharizaman\LaravelUomManagement\Exceptions;

use Azaharizaman\LaravelUomManagement\Models\UomConversion;
use Azaharizaman\LaravelUomManagement\Models\UomUnit;
use RuntimeException;
use Throwable;

class ConversionException extends RuntimeException
{
    public static function unitNotFound(string $identifier): self
    {
        return new self("Unit '{$identifier}' could not be found for conversion.");
    }

    public static function incompatibleTypes(UomUnit $from, UomUnit $to): self
    {
        return new self(sprintf(
            'Units %s and %s belong to different types and cannot be converted without explicit conversion rules.',
            $from->code,
            $to->code
        ));
    }

    public static function baseUnitMissing(int $typeId): self
    {
        return new self("No base unit is registered for unit type ID {$typeId}.");
    }

    public static function nonLinearConversion(UomConversion $conversion): self
    {
        return new self(sprintf(
            'Conversion record %d is non-linear or uses a custom formula which is not supported by the default converter.',
            $conversion->id
        ));
    }

    public static function conversionDivisionByZero(UomConversion $conversion): self
    {
        return new self(sprintf(
            'Conversion record %d specifies a zero factor which would result in division by zero.',
            $conversion->id
        ));
    }

    public static function unitHasZeroFactor(UomUnit $unit): self
    {
        return new self(sprintf(
            'Unit %s declares a zero conversion factor and cannot be used for conversion.',
            $unit->code
        ));
    }

    public static function invalidInput(mixed $value, ?Throwable $previous = null): self
    {
        $display = is_scalar($value) ? (string) $value : get_debug_type($value);

        return new self("Value '{$display}' cannot be converted to a numeric representation for conversion.", previous: $previous);
    }

    public static function pathNotFound(UomUnit $from, UomUnit $to): self
    {
        return new self(sprintf(
            'No conversion path found between %s and %s.',
            $from->code,
            $to->code
        ));
    }
}
