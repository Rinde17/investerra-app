<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected string $geoApiUrl = 'https://geo.api.gouv.fr/communes';

    /**
     * Get coordinates (latitude, longitude) for a given city and postal code using geo.api.gouv.fr.
     *
     * @param string $cityName Nom de la commune
     * @param string|null $zipCode Code postal de la commune
     * @return array|null ['latitude' => float, 'longitude' => float] or null if not found/error
     */
    public function getCoordinates(string $cityName, ?string $zipCode = null): ?array
    {
        $query = [
            'nom' => $cityName,
            'fields' => 'centre',
            'format' => 'json',
            'geometry' => 'centre',
        ];

        if ($zipCode) {
            $query['codePostal'] = $zipCode;
        }

        try {
            $response = Http::get($this->geoApiUrl, $query);
            $response->throw(); // Throws exception for 4xx/5xx

            $data = $response->json();

            if (!empty($data) && isset($data[0]['centre']['coordinates'])) {
                return [
                    'longitude' => (float) $data[0]['centre']['coordinates'][0],
                    'latitude' => (float) $data[0]['centre']['coordinates'][1],
                ];
            }

            Log::warning("GeocodingService: No results found for query: " . json_encode($query));
            return null;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("GeocodingService HTTP error: " . $e->getMessage() . " for query: " . json_encode($query));
            return null;
        } catch (\Exception $e) {
            Log::error("GeocodingService general error: " . $e->getMessage() . " for query: " . json_encode($query));
            return null;
        }
    }
}
