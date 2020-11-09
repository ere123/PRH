<?php
declare(strict_types=1);

namespace Eph\Prh\Domain\Company;

use Eph\Prh\Domain\Languageable;
use stdClass;

class LineOfBusiness implements Languageable
{
    /** @var string */
    private $code;

    /** @var string */
    private $description;

    /** @var string */
    private $language;

    public function __construct(
        string $code,
        string $description,
        ?string $language = ''
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->language = $language;
    }

    public static function createFromResponseData(stdClass $data): self
    {
        return new self($data->code, $data->name, $data->language ?? '');
    }

    public static function isMainLineOfBusiness(int $order, int $version): bool
    {
        return $order === 0 && $version === 1;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
