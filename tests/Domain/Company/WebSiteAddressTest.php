<?php
declare(strict_types=1);

namespace Eph\Prh\Tests\Domain\Company;

use Eph\Prh\Domain\Company\WebSiteAddress;
use PHPUnit\Framework\TestCase;

class WebSiteAddressTest extends TestCase
{
    public function testIsCurrent(): void
    {
        $this->assertTrue(WebSiteAddress::isCurrentAddress(1));
        $this->assertFalse(WebSiteAddress::isCurrentAddress(2));
    }

    public function testIsOfType(): void
    {
        $this->assertTrue(WebSiteAddress::isOfType(WebSiteAddress::TYPE_EN));
        $this->assertFalse(WebSiteAddress::isOfType('something else'));
    }
}
