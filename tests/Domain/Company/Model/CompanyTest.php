<?php
declare(strict_types=1);

namespace Eph\Prh\Tests\Domain\Company\Model;

use Eph\Prh\Domain\Company\Address;
use Eph\Prh\Domain\Company\LineOfBusiness;
use Eph\Prh\Domain\Company\Model\Company;
use Eph\Prh\Domain\Company\WebSiteAddress;
use Eph\Prh\Domain\Languageable;
use PHPUnit\Framework\TestCase;

class CompanyTest extends TestCase
{
    private function getCompany(): Company
    {
        return new Company(
            'TestCompany',
            [
                new LineOfBusiness('1', 'Kuvaus', 'FI'),
                new LineOfBusiness('1', 'Description', 'EN'),
            ],
            [
                new Address('Katu', 'Hki', '01234', 'FI'),
                new Address('Street', 'Hki', '01234', 'EN'),
            ],
            [
                new WebSiteAddress('www.isolta.fi', 'FI'),
                new WebSiteAddress('www.isolta.com', 'EN'),
            ]
        );
    }

    public function testGetBusinessLinesByLanguage(): void
    {
        $this->getLanguageablesByLanguage($this->getCompany()->getBusinessLines('FI'));
    }

    public function testGetAddressesByLanguage(): void
    {
        $this->getLanguageablesByLanguage($this->getCompany()->getCurrentAddresses('FI'));
    }

    public function testGetWebSiteAddressesByLanguage(): void
    {
        $this->getLanguageablesByLanguage($this->getCompany()->getWebSiteAddresses('FI'));
    }

    /**
     * @param Languageable[] $languageables
     */
    private function getLanguageablesByLanguage(array $languageables): void
    {
        foreach ($languageables as $languageable) {
            $this->assertEquals('FI', $languageable->getLanguage());
        }
    }
}
