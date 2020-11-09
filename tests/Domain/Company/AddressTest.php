<?php
declare(strict_types=1);

namespace Eph\Prh\Tests\Domain\Company;

use Eph\Prh\Domain\Company\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testIsCurrent(): void
    {
        $this->assertTrue(Address::isCurrentAddress(1));
        $this->assertFalse(Address::isCurrentAddress(0));
    }
}
