<?php

declare(strict_types=1);

namespace Azaharizaman\LaravelUomManagement\Tests\Unit\Exceptions;

use Azaharizaman\LaravelUomManagement\Exceptions\ConversionException;
use Azaharizaman\LaravelUomManagement\Models\UomCompoundUnit;
use Azaharizaman\LaravelUomManagement\Models\UomConversion;
use Azaharizaman\LaravelUomManagement\Models\UomPackaging;
use Azaharizaman\LaravelUomManagement\Models\UomUnit;
use Azaharizaman\LaravelUomManagement\Tests\TestCase;

class ConversionExceptionTest extends TestCase
{
    public function testStaticFactoriesProduceExpectedMessages(): void
    {
        $unit = UomUnit::factory()->create(['code' => 'KG']);
        $target = UomUnit::factory()->create(['code' => 'LB']);

        $this->assertStringContainsString('could not be found', ConversionException::unitNotFound('missing')->getMessage());
        $this->assertStringContainsString('cannot be converted', ConversionException::incompatibleTypes($unit, $target)->getMessage());
        $this->assertStringContainsString('No base unit is registered', ConversionException::baseUnitMissing(99)->getMessage());
    }

    public function testCompoundHelpersHandleInvalidArguments(): void
    {
        $compound = UomCompoundUnit::factory()->create(['symbol' => 'AB/CD']);

        $mismatch = ConversionException::compoundStructureMismatch($compound, $compound);
        $this->assertStringContainsString('do not share the same dimensional structure', $mismatch->getMessage());

    /** @var mixed $invalidCompound */ $invalidCompound = 'invalid';
        $invalid = ConversionException::compoundComponentMissingType($invalidCompound);
        $this->assertStringContainsString('could not be validated', $invalid->getMessage());

    /** @var mixed $invalidFrom */ $invalidFrom = 'a';
    /** @var mixed $invalidTo */ $invalidTo = 'b';
    $invalidStructure = ConversionException::compoundStructureMismatch($invalidFrom, $invalidTo);
        $this->assertStringContainsString('invalid arguments', $invalidStructure->getMessage());
    }

    public function testConversionRecordExceptions(): void
    {
        /** @var \Azaharizaman\LaravelUomManagement\Models\UomConversion $conversion */
        $conversion = UomConversion::factory()->create();

        $this->assertStringContainsString('non-linear', ConversionException::nonLinearConversion($conversion)->getMessage());
        $this->assertStringContainsString('division by zero', ConversionException::conversionDivisionByZero($conversion)->getMessage());
    }

    public function testPathAndPackagingExceptionFactories(): void
    {
        /** @var \Azaharizaman\LaravelUomManagement\Models\UomUnit $base */
        $base = UomUnit::factory()->create(['code' => 'BASE']);
        /** @var \Azaharizaman\LaravelUomManagement\Models\UomUnit $target */
        $target = UomUnit::factory()->create(['code' => 'ALT']);

        $this->assertStringContainsString('No conversion path found', ConversionException::pathNotFound($base, $target)->getMessage());
        $this->assertStringContainsString('could not be found', ConversionException::packagingRecordNotFound(123)->getMessage());

        /** @var \Azaharizaman\LaravelUomManagement\Models\UomPackaging $packaging */
        $packaging = UomPackaging::factory()->for($base, 'baseUnit')->for($target, 'packageUnit')->create();
        $this->assertStringContainsString('No packaging relationship exists', ConversionException::packagingPathNotFound($packaging->baseUnit, $packaging->packageUnit)->getMessage());
    }
}
