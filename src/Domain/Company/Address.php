<?php
declare(strict_types=1);

namespace Eph\Prh\Domain\Company;

use Eph\Prh\Domain\Languageable;
use stdClass;

class Address implements Languageable
{
    /** @var string */
    private $street;

    /** @var string */
    private $city;

    /** @var string */
    private $postalCode;

    /** @var string */
    private $language;

    public function __construct(
        ?string $street = '',
        ?string $city = '',
        ?string $postalCode = '',
        ?string $language = ''
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->language = $language;
    }

    public static function isCurrentAddress(int $version): bool
    {
        return $version === 1;
    }

    public static function createFromResponseData(stdClass $data): self
    {
        return new self(
            $data->street ?? '',
            $data->city ?? '',
            $data->postCode ?? '',
            $data->language ?? ''
        );
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
