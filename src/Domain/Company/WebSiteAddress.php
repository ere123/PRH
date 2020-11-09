<?php
declare(strict_types=1);

namespace Eph\Prh\Domain\Company;

use stdClass;

class WebSiteAddress extends ContactDetail
{
    const TYPE_EN = 'Website address';
    const TYPE_FI = 'Kotisivun www-osoite';
    const TYPE_SE = 'www-address';

    const TYPES = [
        self::TYPE_EN,
        self::TYPE_FI,
        self::TYPE_SE
    ];

    /** @var string */
    private $webSiteAddress;

    /** @var string */
    private $language;

    public function __construct(
        string $webSiteAddress,
        string $language = ''
    ) {
        $this->webSiteAddress = $webSiteAddress;
        $this->language = $language;
    }

    public static function isCurrentAddress(int $version): bool
    {
        return $version === 1;
    }

    public static function createFromResponseData(stdClass $data): self
    {
        return new self($data->value, $data->language ?? '');
    }

    public function getWebSiteAddress(): string
    {
        return $this->webSiteAddress;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
