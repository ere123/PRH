<?php
declare(strict_types=1);

namespace Eph\Prh\Tests;

use Eph\Prh\Client;
use Eph\Prh\Domain\Company\BusinessIdentityCode;
use Eph\Prh\Domain\Company\WebSiteAddress;
use Eph\Prh\Exceptions\PrhClientException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function createClient(Response $response): Client
    {
        return $this->createMockClient($response);
    }

    public function createExceptionClient(GuzzleException $exception): Client
    {
        return $this->createMockClient($exception);
    }

    /**
     * @param Response|GuzzleException $data
     * @return Client
     */
    private function createMockClient($data): Client
    {
        $mock = new MockHandler([
            $data
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handlerStack]);
        return new Client($client);
    }

    public function testInvalidJsonException(): void
    {
        $this->expectException(PrhClientException::class);

        $client = $this->createClient(new Response());
        $client->getCompany($this->getBusinessIdentityCode());
    }

    /**
     * @return mixed[]
     */
    public function invalidJsonData(): array
    {
        return [
            ['null'],
            [''],
            ['abc'],
            ['0'],
            ['-1'],
            [1],
            [0],
            [-1],
            [false],
            [true]
        ];
    }

    /**
     * @dataProvider invalidJsonData
     * @param mixed $invalidData
     */
    public function testInvalidJsonData($invalidData): void
    {
        $this->expectException(PrhClientException::class);

        $client = $this->createClient(new Response(200, [], $invalidData));
        $client->getCompany($this->getBusinessIdentityCode());
    }

    public function testNonErrorStatusCode(): void
    {
        $this->expectException(PrhClientException::class);

        $client = $this->createClient(new Response(399, []));
        $client->getCompany($this->getBusinessIdentityCode());
    }

    /**
     * @return int[][]
     */
    public function clientExceptionCodes(): array
    {
        return [
            [400],
            [401]
        ];
    }

    /**
     * @dataProvider clientExceptionCodes
     * @param int $code
     */
    public function testClientException(int $code): void
    {
        $this->expectException(PrhClientException::class);

        $client = $this->createExceptionClient(
            new ClientException('', new Request('GET', ''), new Response($code, [], '{}'))
        );
        $client->getCompany($this->getBusinessIdentityCode());
    }

    public function testGuzzleException(): void
    {
        $this->expectException(PrhClientException::class);

        $client = $this->createExceptionClient(
            new TransferException()
        );
        $client->getCompany($this->getBusinessIdentityCode());
    }

    public function testNotFoundException(): void
    {
        $client = $this->createExceptionClient(
            new ClientException('', new Request('GET', ''), new Response(404, [], '{}'))
        );
        $company = $client->getCompany($this->getBusinessIdentityCode());
        $this->assertNull($company);
    }

    public function testNon200Success(): void
    {
        $this->expectException(PrhClientException::class);

        $client = $this->createExceptionClient(
            new ClientException('', new Request('GET', ''), new Response(201, [], '{}'))
        );
        $client->getCompany($this->getBusinessIdentityCode());
    }

    public function testGetCompany(): void
    {
        $companyName = 'Isolta Oy';
        $businessLine = 'Software';
        $address = 'Kalevankatu 4';
        $webSite = 'www.isolta.com';

        $client = $this->createClient(
            new Response(200, [], json_encode([
                'results' => [
                    [
                        'name' => $companyName,
                        'businessLines' => [
                            [
                                'order' => 0,
                                'version' => 1,
                                'name' => $businessLine,
                                'code' => '1234',
                            ]
                        ],
                        'addresses' => [
                            [
                                'version' => 1,
                                'street' => $address
                            ]
                        ],
                        'contactDetails' => [
                            [
                                'version' => 1,
                                'type' => WebSiteAddress::TYPE_EN,
                                'value' => $webSite,
                            ]
                        ]
                    ]
                ]
            ]))
        );

        $company = $client->getCompany($this->getBusinessIdentityCode());

        $this->assertEquals($companyName, $company->getName());
        $this->assertEquals($webSite, $company->getWebSiteAddresses()[0]->getWebSiteAddress());
        $this->assertEquals($address, $company->getCurrentAddresses()[0]->getStreet());
        $this->assertEquals($businessLine, $company->getBusinessLines()[0]->getDescription());
    }

    private function getBusinessIdentityCode(): BusinessIdentityCode
    {
        return new BusinessIdentityCode('1854047-8');
    }
}
