# VentusForge.Nominatim

API connector for the Nominatim API (OpenStreetMap).

This package provides a simple HTTP client and a service to retrieve geocoordinates and address data via Nominatim search. Responses are cached by default using a Neos cache frontend.

## Installation

Install with Composer:

```bash
composer require ventusforge/neos-nominatim
```

## Configuration

You can configure the base URL and default query parameters in `Configuration/Settings.yaml`. Example (the package includes these defaults):

```yaml
VentusForge:
  Nominatim:
    url: 'https://nominatim.openstreetmap.org/search.php'
    defaultQueryParams:
      format: 'jsonv2'
      accept-language: 'de-DE'
      countrycodes: 'de'
      addressdetails: 1
```

The `defaultQueryParams` are appended to every request sent to Nominatim (for example: language, output format, country codes, address details).

## Public classes / API

- `VentusForge\Nominatim\Client`
  - `get(?string $street = null, ?string $city = null, ?string $postalcode = null, ?string $state = null, ?string $country = null): ?array`
    - Builds a Nominatim request using individual address components (street, city, postalcode, state, country).
  - `search(string $query): ?array`
    - Performs a free-text search (`q`) against Nominatim.

- `VentusForge\Nominatim\Service\GeoLocalizationService`
  - A wrapper around `Client` that uses Neos `VariableFrontend` caching and exposes the same `get(...)` and `search(...)` methods.
  - Cache keys are generated from the request parameters (MD5); cached results are returned when available.

## Examples (Neos/Flow)

Inject the `GeoLocalizationService` into a Flow controller or service:

```php
use VentusForge\Nominatim\Service\GeoLocalizationService;
use Neos\Flow\Annotations\Flow;

class SomeController {
    #[Flow\Inject]
    protected GeoLocalizationService $geoLocalizationService;

    public function exampleAction(): void
    {
        // Free-text search
        $result = $this->geoLocalizationService->search('Brandenburg Gate, Berlin');

        // Search by address components
        $result2 = $this->geoLocalizationService->get('Unter den Linden 6', 'Berlin', '10117', 'Berlin', 'DE');

        // $result / $result2 are arrays (or null) as returned by Nominatim
    }
}
```

If you prefer to call the API directly, you can use the `Client` class:

```php
use VentusForge\Nominatim\Client;

// Assuming the Client is injected by Flow
$result = $this->client->search('Cologne Cathedral');
```

## Caching

`GeoLocalizationService` uses a Neos `VariableFrontend` cache (`geoLocalizationCache`) to avoid repeated external requests. Adjust your cache configuration in your project if necessary.

## Error handling

On HTTP errors the client throws an exception (`Neos\Flow\Http\Client\Exception`). Check status codes and handle potential exceptions in your application code.

## License

This package is licensed under the terms described in the repository `LICENSE` file.
