<?php
declare(strict_types=1);

namespace Eph\Prh\Domain\Company\Model;

use Eph\Prh\Domain\Company\Address;
use Eph\Prh\Domain\Company\LineOfBusiness;
use Eph\Prh\Domain\Company\WebSiteAddress;
use Eph\Prh\Domain\Languageable;
use stdClass;

class Company
{
    /** @var string */
    private $name;

    /** @var WebSiteAddress[] */
    private $webSiteAddresses;

    /** @var Address[] */
    private $currentAddresses;

    /** @var LineOfBusiness[] */
    private $businessLines;

    /**
     * Company constructor.
     * @param string $name
     * @param LineOfBusiness[]|null $businessLines
     * @param Address[]|null $currentAddresses
     * @param WebSiteAddress[]|null $webSiteAddresses
     */
    public function __construct(
        string $name,
        ?array $businessLines = [],
        ?array $currentAddresses = [],
        ?array $webSiteAddresses = []
    ) {
        $this->name = $name;
        $this->businessLines = $businessLines;
        $this->currentAddresses = $currentAddresses;
        $this->webSiteAddresses = $webSiteAddresses;
    }

    public static function createFromResponseData(stdClass $data): self
    {
        $businessLines = [];
        foreach ($data->businessLines as $lineOfBusinessData) {
            if (LineOfBusiness::isMainLineOfBusiness($lineOfBusinessData->order, $lineOfBusinessData->version)) {
                $businessLines[] = LineOfBusiness::createFromResponseData($lineOfBusinessData);
            }
        }

        $addresses = [];
        foreach ($data->addresses as $addressData) {
            if (Address::isCurrentAddress($addressData->version)) {
                $addresses[] = Address::createFromResponseData($addressData);
            }
        }

        $webSiteAddresses = [];
        foreach ($data->contactDetails as $contactDetailData) {
            if (WebSiteAddress::isOfType($contactDetailData->type) && WebSiteAddress::isCurrentAddress($contactDetailData->version)) {
                $webSiteAddresses[] = WebSiteAddress::createFromResponseData($contactDetailData);
            }
        }

        return new self(
            $data->name,
            $businessLines,
            $addresses,
            $webSiteAddresses
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $language
     * @return WebSiteAddress[]
     */
    public function getWebSiteAddresses(?string $language = null): array
    {
        if ($language !== null) {
            return $this->getByLanguage($this->webSiteAddresses, $language);
        }

        return $this->webSiteAddresses;
    }

    /**
     * @param string|null $language
     * @return Address[]
     */
    public function getCurrentAddresses(?string $language = null): array
    {
        if ($language !== null) {
            return $this->getByLanguage($this->currentAddresses, $language);
        }

        return $this->currentAddresses;
    }

    /**
     * @param string|null $language
     * @return LineOfBusiness[]
     */
    public function getBusinessLines(?string $language = null): array
    {
        if ($language !== null) {
            return $this->getByLanguage($this->businessLines, $language);
        }

        return $this->businessLines;
    }

    /**
     * @param Languageable[] $objects
     * @param string $language
     * @return mixed[]
     */
    private function getByLanguage(array $objects, string $language): array
    {
        return array_filter($objects, static function (Languageable $object) use ($language) {
            return $object->getLanguage() === $language;
        });
    }
}
