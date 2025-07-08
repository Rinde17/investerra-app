<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BieniciMarketPriceM2Service
{
    /**
     * Récupère le prix moyen au m² des terrains viabilisés depuis l'API Bienici.
     *
     * @param string|null $zoneId Identifiant de la zone pour filtrer (ex: "-110282")
     * @return ?float Retourne un float ou null en cas d'erreur.
     * @throws ConnectionException
     */
    public function getMarketPriceM2(string $city, string $zip): ?float
    {
        try {
            $cityWithPostalCode = $this->formatCityAndZip($city, $zip);
            $zoneId = $this->getZoneIdsForCity($cityWithPostalCode);

            if ($zoneId === null) {
                Log::warning("Impossible de trouver le zoneId pour $city, $zip");
                return null;
            }

            $url = 'https://www.bienici.com/realEstateAds.json';
            $filters = [
                "size" => 24,
                "from" => 0,
                "showAllModels" => false,
                "filterType" => "buy",
                "propertyType" => ["terrain"],
                "page" => 1,
                "sortBy" => "relevance",
                "sortOrder" => "desc",
                "onTheMarket" => [true],
                "zoneIdsByTypes" => [
                    "zoneIds" => [$zoneId]
                ]
            ];

            $query = [
                'filters' => json_encode($filters),
                'extensionType' => 'extendedIfNoResult',
                'enableGoogleStructuredDataAggregates' => 'true',
                'leadingCount' => 2,
                'access_token' => 'jsjsSuW6Sf6a55yBM6KZ1XwQV29JlEVdg3NErvagNJI=:686221d90407ed00b9cce92d',
                'id' => '686221d90407ed00b9cce92d'
            ];

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0'
            ])
            ->timeout(5) // 5 seconds timeout
            ->retry(3, 1000) // Retry 3 times with 1 second delay between attempts
            ->get($url, $query);

            if (!$response->successful()) {
                Log::error('Erreur lors de la récupération des données Bienici : HTTP status ' . $response->status());
                return null;
            }

            $data = $response->json();
            $ads = $data['realEstateAds'] ?? [];

            $totalViabilised = 0;
            $sumPricePerM2 = 0;

            foreach ($ads as $ad) {
                $description = strtolower($ad['description'] ?? '');
                $isViabilise = true;

                // Exclusions : signes clairs de non-viabilisation
                $negations = [
                    'non viabilisé', 'pas viabilisé', 'à viabiliser',
                    'viabilisation à prévoir', 'viabilisation est à prévoir',
                    'pas encore viabilisé', 'reste à viabiliser', 'viabilisation du terrain est à prévoir',
                    'viabilisation du terrain à prévoir'
                ];
                foreach ($negations as $neg) {
                    if (str_contains($description, $neg)) {
                        $isViabilise = false;
                        break;
                    }
                }

                if ($isViabilise && isset($ad['pricePerSquareMeter']) && is_numeric($ad['pricePerSquareMeter'])) {
                    $totalViabilised++;
                    $sumPricePerM2 += $ad['pricePerSquareMeter'];
                }
            }

            $prixM2 = $totalViabilised > 0 ? $sumPricePerM2 / $totalViabilised : null;

            return $prixM2 !== null ? round($prixM2, 2) : null;
        } catch (\Exception $e) {
            Log::error('Exception lors de la récupération des données Bienici: ' . $e->getMessage());
            return null;
        }
    }

    protected function getZoneIdsForCity(string $cityWithPostalCode): ?string {
        try {
            $url = 'https://res.bienici.com/place.json';
            $query = [
                'q' => $cityWithPostalCode,
                'type' => 'city,delegated-city,department,postalCode,region',
                'prefix' => 'no',
            ];

            $response = Http::timeout(5) // 5 seconds timeout
                ->retry(3, 1000) // Retry 3 times with 1 second delay between attempts
                ->get($url, $query);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['zoneIds'][0]) && $data['zoneIds'][0] !== null) {
                    return $data['zoneIds'][0];
                }

                Log::warning("ZoneId introuvable pour la ville/code postal : $cityWithPostalCode");
            } else {
                Log::error('Erreur lors de la récupération du zoneId : HTTP status ' . $response->status());
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception lors de la récupération du zoneId: ' . $e->getMessage());
            return null;
        }
    }

    protected function formatCityAndZip(string $city, string $zip): string
    {
        // 1. Mettre en minuscules
        $city = mb_strtolower($city, 'UTF-8');

        // 2. Supprimer les accents (ex : ç -> c, é -> e, etc.)
        $city = Str::ascii($city);

        // 3. Supprimer espaces et caractères non alphanumériques sauf tirets
        $city = preg_replace('/[^a-z0-9-]/', '', $city);

        // 4. Assembler avec le code postal
        return $city . '-' . $zip;
    }
}
