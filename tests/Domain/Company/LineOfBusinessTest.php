<?php
declare(strict_types=1);

namespace Eph\Prh\Tests\Domain\Company;

use Eph\Prh\Domain\Company\LineOfBusiness;
use PHPUnit\Framework\TestCase;

class LineOfBusinessTest extends TestCase
{
    public function testIsMainLineOfBusiness(): void
    {
        $this->assertTrue(LineOfBusiness::isMainLineOfBusiness(0, 1));
        $this->assertFalse(LineOfBusiness::isMainLineOfBusiness(1, 0));
    }
}
