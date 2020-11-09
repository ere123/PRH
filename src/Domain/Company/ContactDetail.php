<?php
declare(strict_types=1);

namespace Eph\Prh\Domain\Company;

use Eph\Prh\Domain\Languageable;

abstract class ContactDetail implements Languageable
{
    const TYPES = [];

    public static function isOfType(string $type): bool
    {
        return in_array($type, static::TYPES);
    }
}
