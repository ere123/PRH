# Usage
```php
try {
    $businessIdentityCode = new \Eph\Prh\Domain\Company\BusinessIdentityCode('1854047-8'); //Throws InvalidBusinessIdentityCodeException
    $client = new \Eph\Prh\Client();
    $company = $client->getCompany($businessIdentityCode); //Throws PrhClientException
} catch (\Eph\Prh\Exceptions\Domain\Company\InvalidBusinessIdentityCodeException $ex) {
    // Failed validation
} catch (\Eph\Prh\Exceptions\PrhClientException $ex) {
    // Other failures
}

```
## Not found:
If business identity code were to pass validation and still not be found in PRH, Client::getCompany() will return null.
