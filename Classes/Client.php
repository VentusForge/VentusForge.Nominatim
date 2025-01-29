<?php

declare(strict_types=1);

namespace VentusForge\Nominatim;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Flow\Http\Client\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Client for the Nominatim API
 */
class Client
{
    #[Flow\Inject]
    protected ServerRequestFactoryInterface $serverRequestFactory;

    #[Flow\InjectConfiguration(path: "url")]
    protected string $url;

    #[Flow\InjectConfiguration(path: "defaultQueryParams")]
    protected array $defaultQueryParams;

    protected Browser $browser;

    public function __construct()
    {
        $this->browser = new Browser();
        $this->browser->setRequestEngine(new CurlEngine());
    }

    /**
     * Create a request to the API
     * 
     * @param string $url
     * @param array $queryParams
     * @return ServerRequestInterface
     */
    protected function createRequest(string $url, array $queryParams = []): ServerRequestInterface
    {
        $url = $url . '?' . http_build_query(array_merge($this->defaultQueryParams, $queryParams));

        $request = $this->serverRequestFactory->createServerRequest('GET', $url);
        $request = $request->withAddedHeader('Content-Type', 'application/json');

        return $request;
    }

    /**
     * Parse the response body
     * 
     * @param ResponseInterface $response
     * @throws Exception
     * @return array
     */
    protected function parseBody(ResponseInterface $response): array
    {
        $responseBody = json_decode((string)$response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            throw new Exception('An error occurred during the query request. Status-Code: ' .
                $response->getStatusCode(), 1769687755);
        }

        return $responseBody;
    }

    /**
     * Get a location by street, city, postal code, state and country
     * 
     * @param string|null $street The street name
     * @param string|null $city The city name
     * @param string|null $postalcode The postal code
     * @param string|null $state The state name
     * @param string|null $country The country name
     * @return array The location data
     */
    public function get(
        ?string $street = null,
        ?string $city = null,
        ?string $postalcode = null,
        ?string $state = null,
        ?string $country = null,
    ): ?array {
        $queryParams = [];

        if (!empty($street)) {
            $queryParams['street'] = $street;
        }
        if (!empty($city)) {
            $queryParams['city'] = $city;
        }
        if (!empty($postalcode)) {
            $queryParams['postalcode'] = $postalcode;
        }
        if (!empty($state)) {
            $queryParams['state'] = $state;
        }
        if (!empty($country)) {
            $queryParams['country'] = $country;
        }

        $request = $this->createRequest($this->url, $queryParams);
        $response = $this->browser->sendRequest($request);
        return $this->parseBody($response);
    }

    /**
     * Search for a location by query
     * 
     * @param string $query The query to search for
     * @return array The location data
     */
    public function search(string $query): ?array
    {
        $request = $this->createRequest($this->url, ['q' => $query]);
        $response = $this->browser->sendRequest($request);
        return $this->parseBody($response);
    }
}
