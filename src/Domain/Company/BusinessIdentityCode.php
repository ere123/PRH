<?php
declare(strict_types=1);

namespace Eph\Prh\Domain\Company;

use Eph\Prh\Exceptions\Domain\Company\InvalidBusinessIdentityCodeException;

class BusinessIdentityCode
{
    /** @var string */
    private $businessIdentityCode;

    public function __construct(string $businessIdentityCode)
    {
        if (!$this->validate($businessIdentityCode)) {
            throw new InvalidBusinessIdentityCodeException('Invalid business identity code');
        }
        $this->businessIdentityCode = $businessIdentityCode;
    }

    /**
     * https://github.com/pear/Validate_FI
     * https://tarkistusmerkit.teppovuori.fi/tarkmerk.htm
     * @param string $businessIdentityCode
     * @return bool
     */
    private function validate(string $businessIdentityCode): bool
    {
        if (preg_match('/^[0-9]{6,7}-[0-9]{1}$/', $businessIdentityCode)) {
            list($num, $control) = preg_split('[-]', $businessIdentityCode);
            // Add leading zeros if number is < 7
            $num = str_pad($num, 7, '0', STR_PAD_LEFT);
            $controlSum = 0;
            $controlSum += (int)substr($num, 0, 1)*7;
            $controlSum += (int)substr($num, 1, 1)*9;
            $controlSum += (int)substr($num, 2, 1)*10;
            $controlSum += (int)substr($num, 3, 1)*5;
            $controlSum += (int)substr($num, 4, 1)*8;
            $controlSum += (int)substr($num, 5, 1)*4;
            $controlSum += (int)substr($num, 6, 1)*2;
            $controlSum = $controlSum%11;
            if ($controlSum === 0) {
                return ($controlSum == $control);
            } elseif ($controlSum >= 2 && $controlSum <= 10) {
                return ((11 - $controlSum) == $control);
            }
        }

        return false;
    }

    public function __toString(): string
    {
        return $this->businessIdentityCode;
    }
}
