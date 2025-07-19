<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon; // Importer Carbon pour la manipulation des dates

// Assurez-vous d'importer le modèle Terrain si ce n'est pas déjà fait
use App\Models\Terrain;
use App\Models\TerrainAnalysis; // <-- Importez TerrainAnalysis pour le comptage direct si applicable


class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function __invoke(Request $request): Response
    {
        $user = $this->user();

        // Récupérer les terrains de l'utilisateur avec leur analyse associée
        // CORRECTION : Utiliser 'analysis' comme nom de la relation
        $terrains = $user->terrains()
            ->with('analysis') // <-- C'est ici la correction principale
            ->latest()
            ->get();

        // Préparer les données pour la carte
        $mapTerrains = $terrains->filter(function ($terrain) {
            // Utiliser latitude et longitude directement de l'objet Terrain
            return $terrain->latitude !== null && $terrain->longitude !== null;
        })->map(function ($terrain) {
            return [
                'id'        => $terrain->id,
                'title'     => $terrain->title,
                'city'      => $terrain->city,
                'latitude'  => (float) $terrain->latitude, // Assurez-vous que c'est un float
                'longitude' => (float) $terrain->longitude, // Assurez-vous que c'est un float
                'surface_m2' => (float) $terrain->surface_m2,
                'price'     => (float) $terrain->price,
            ];
        })->values();

        // Calculer les statistiques clés
        $totalTerrains = $terrains->count();
        $profitableTerrains = $terrains->filter(function ($terrain) {
            return $terrain->analysis && $terrain->analysis->net_margin_estimate > 0;
        })->count();

        $aiScoreAvg = $terrains->filter(function ($terrain) {
            return $terrain->analysis && $terrain->analysis->ai_score !== null;
        })->avg('analysis.ai_score');

        // Récupérer les 3-5 derniers terrains pour la section "Activité récente"
        $latestTerrains = $terrains->take(5)->map(function ($terrain) {
            return [
                'id'        => $terrain->id,
                'title'     => $terrain->title,
                'city'      => $terrain->city,
                'ai_score'  => $terrain->analysis ? $terrain->analysis->ai_score : null,
                'profitability_label' => $terrain->analysis ? $terrain->analysis->profitability_label : null,
            ];
        });

        // Logique pour 'analysesRemaining'
        if ($user->isOnProPlan() || $user->isOnInvestorPlan()) {
            $analysesRemainingMessage = 'Illimité';
        } else {
            $maxAnalysesPerWeek = $user->getAnalysesPerWeek();

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            // Puisque TerrainAnalysis n'a pas de user_id direct,
            // nous devons compter les analyses via la relation terrain->analysis
            $analysesThisWeek = 0;
            foreach ($terrains as $terrain) {
                if ($terrain->analysis && $terrain->analysis->created_at instanceof Carbon && $terrain->analysis->created_at->between($startOfWeek, $endOfWeek)) {
                    $analysesThisWeek++;
                }
            }

            $remainingAnalyses = max(0, $maxAnalysesPerWeek - $analysesThisWeek);
            $analysesRemainingMessage = "{$remainingAnalyses} / {$maxAnalysesPerWeek} restantes cette semaine";
        }

        return Inertia::render('Dashboard', [
            'mapTerrains' => $mapTerrains,
            'stats' => [
                'totalTerrains'      => $totalTerrains,
                'profitableTerrains' => $profitableTerrains,
                'aiScoreAvg'         => round($aiScoreAvg ?? 0),
                'analysesRemaining'  => $analysesRemainingMessage, // <-- Passez le message calculé
            ],
            'latestTerrains'     => $latestTerrains,
        ]);
    }
}
