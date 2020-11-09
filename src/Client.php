<?php
declare(strict_types=1);

namespace Eph\Prh;

use Eph\Prh\Domain\Company\BusinessIdentityCode;
use Eph\Prh\Domain\Company\Model\Company;
use Eph\Prh\Exceptions\NotFoundException;
use Eph\Prh\Exceptions\PrhClientException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use stdClass;
use JsonException;

class Client
{
    /** @var string */
    private $baseUri = 'https://avoindata.prh.fi/bis/v1';

    /** @var ClientInterface */
    private $httpClient;

    /** @var LoggerInterface|null */
    private $logger;

    public function __construct(?ClientInterface $client = null)
    {
        if ($client === null) {
            $client = new HttpClient([
                'base_uri' => $this->baseUri,
                'allow_redirects' => false,
                'timeout' => 5,
                'connection_timeout' => 5,
            ]);
        }
        $this->setHttpClient($client);
    }

    /**
     * Returns a Company if found, null if not found
     * Throws exception when something went wrong
     * @param BusinessIdentityCode $businessIdentityCode
     * @return Company|null
     * @throws PrhClientException
     */
    public function getCompany(BusinessIdentityCode $businessIdentityCode): ?Company
    {
        try {
            $response = $this->sendRequest('GET', $this->baseUri.'/'.$businessIdentityCode);
        } catch (NotFoundException $e) {
            return null;
        }

        if ($response !== null && $response->getStatusCode() === 200) {
            $data = $this->getResponseResults($response);
            $companyData = $data[0];

            return Company::createFromResponseData($companyData);
        } else {
            $this->throwInvalidStatusException($response);
        }

        return null;
    }

    /**
     * @param string $method
     * @param string $uri
     * @return ResponseInterface|null
     * @throws NotFoundException
     * @throws PrhClientException
     */
    protected function sendRequest(string $method, string $uri): ?ResponseInterface
    {
        try {
            return $this->httpClient->request($method, $uri);
        } catch (ClientException $ex) {
            $response = $ex->getResponse();
            if ($response->getStatusCode() === 404) {
                throw new NotFoundException('Resource not found');
            }

            $this->logError($ex->getMessage());
            $this->throwInvalidStatusException($response);
        } catch (GuzzleException $ex) {
            $this->logError($ex->getMessage());
            throw new PrhClientException('Got GuzzleException: '.$ex->getMessage(), 0, $ex);
        }

        return null;
    }

    /**
     * Get data from JSON
     * @param ResponseInterface $response
     * @return stdClass
     * @throws PrhClientException
     */
    protected function getResponseData(ResponseInterface $response): stdClass
    {
        try {
            $data = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
            //We assume the result is something resembling correct if we get an stdClass
            if ($data instanceof stdClass) {
                return $data;
            }

            throw new PrhClientException('Invalid data in response');
        } catch (JsonException $ex) {
            $this->logError($ex->getMessage());
            throw new PrhClientException('Invalid data in response: '.$ex->getMessage());
        }
    }

    /**
     * @param ResponseInterface $response
     * @return stdClass[]
     * @throws PrhClientException
     */
    protected function getResponseResults(ResponseInterface $response): array
    {
        return $this->getResultsFromData($this->getResponseData($response));
    }

    /**
     * Data is returned under JSON results-key
     * @param stdClass $data
     * @return stdClass[]
     */
    protected function getResultsFromData(stdClass $data): array
    {
        return $data->results;
    }

    /**
     * @param ResponseInterface $response
     * @throws PrhClientException
     */
    private function throwInvalidStatusException(ResponseInterface $response): void
    {
        throw new PrhClientException(
            'Got an invalid response status: '.$response->getStatusCode().' '.$response->getBody()
        );
    }

    protected function logError(string $message): void
    {
        if ($this->logger !== null) {
            $this->logger->error($message);
        }
    }

    public function setHttpClient(ClientInterface $client): void
    {
        $this->httpClient = $client;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
