<?php

declare(strict_types=1);

namespace VentusForge\Nominatim\Service;

use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;
use VentusForge\Nominatim\Client;

/**
 * geo coordinates service
 *
 * @Flow\Scope("singleton")
 */
class GeoLocalizationService
{
    /**
     * @var VariableFrontend
     */
    protected $geoLocalizationCache;

    #[Flow\Inject]
    protected Client $client;

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
        $cacheKey = md5(json_encode([$street, $city, $postalcode, $state, $country]));
        $cachedData = $this->geoLocalizationCache->get($cacheKey);
        if ($cachedData) {
            return $cachedData;
        }

        $data = $this->client->get($street, $city, $postalcode, $state, $country);
        $this->geoLocalizationCache->set($cacheKey, $data);

        return $data;
    }

    /**
     * Search for a location by query
     * 
     * @param string $query The query to search for
     * @return array The location data
     */
    public function search(string $query): ?array {
        $cacheKey = md5($query);
        $cachedData = $this->geoLocalizationCache->get($cacheKey);
        if ($cachedData) {
            return $cachedData;
        }

        $data = $this->client->search($query);
        $this->geoLocalizationCache->set($cacheKey, $data);

        return $data;
    }
}
