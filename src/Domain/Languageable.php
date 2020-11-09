<?php
declare(strict_types=1);

namespace Eph\Prh\Domain;

interface Languageable
{
    public function getLanguage(): string;
}
