<?php
declare(strict_types=1);

namespace Eph\Prh\Tests\Domain\Company;

use Eph\Prh\Domain\Company\BusinessIdentityCode;
use Eph\Prh\Exceptions\Domain\Company\InvalidBusinessIdentityCodeException;
use PHPUnit\Framework\TestCase;

class BusinessIdentityCodeTest extends TestCase
{
    /**
     * @return string[][]
     */
    public function validValues(): array
    {
        return [
            ['1854047-8'],
            ['1980908-8']
        ];
    }

    /**
     * @dataProvider validValues
     * @param string $validValue
     * @throws InvalidBusinessIdentityCodeException
     */
    public function testIsValid(string $validValue): void
    {
        $this->assertInstanceOf(BusinessIdentityCode::class, new BusinessIdentityCode($validValue));
    }

    /**
     * @return string[][]
     */
    public function invalidValues(): array
    {
        return [
            ['0737546-1'],
            ['0737546-11'],
            ['0737545-2'],
            ['57286XX-0'],
            ['0'],
            ['-1'],
            ['valid']
        ];
    }

    /**
     * @dataProvider invalidValues
     * @param string $invalidValue
     * @throws InvalidBusinessIdentityCodeException
     */
    public function testIsNotValid(string $invalidValue): void
    {
        $this->expectException(InvalidBusinessIdentityCodeException::class);
        new BusinessIdentityCode($invalidValue);
    }
}
