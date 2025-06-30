<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FetchTerrainsBienIci extends Command
{
    protected $signature = 'bienici:fetch-terrains';
    protected $description = 'Estimation du prix moyen au m² des terrains viabilisés à Montluçon (à titre indicatif)';

    public function handle()
    {
        $this->info("🔍 Récupération des terrains à Montluçon...");

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
                "zoneIds" => ["-110282"]
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
        ])->get($url, $query);

        if (!$response->successful()) {
            $this->error('❌ Erreur lors de la récupération des données : ' . $response->status());
            return CommandAlias::FAILURE;
        }

        $data = $response->json();
        $ads = $data['realEstateAds'] ?? [];
        $totalAds = count($ads);

        $totalViabilised = 0;
        $sumPricePerM2 = 0;

        foreach ($ads as $ad) {
            $description = strtolower($ad['description'] ?? '');
            $isViabilise = true;

            // ❌ Exclusions : signes clairs de non-viabilisation
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

            // Si terrain présumé viabilisé ET possède pricePerSquareMeter
            if ($isViabilise === true && isset($ad['pricePerSquareMeter']) && is_numeric($ad['pricePerSquareMeter'])) {
                $totalViabilised++;
                $sumPricePerM2 += $ad['pricePerSquareMeter'];
            }
        }

        $prixM2 = $totalViabilised > 0 ? $sumPricePerM2 / $totalViabilised : null;

        $this->info("\n📊 Résumé des données :");
        $this->line("🧾 Nombre total d'annonces reçues : $totalAds");
        $this->line("📌 Annonces utilisées pour le calcul (terrains viabilisés avec prix au m²) : $totalViabilised");

        if ($prixM2 !== null) {
            $this->line("💶 Prix moyen estimé au m² (commune, terrains viabilisés) : " . round($prixM2, 2) . " € / m²");
            $this->warn("⚠️ Estimation indicative à partir des annonces en ligne (pas de garantie de fiabilité)");
        } else {
            $this->line("❓ Pas assez de données pour estimer le prix moyen au m².");
        }

        return CommandAlias::SUCCESS;
    }
}
