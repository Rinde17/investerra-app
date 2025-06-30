<?php

namespace App\Http\Controllers;

use App\Http\Requests\TerrainRequest;
use App\Models\Terrain;
use App\Models\TerrainAnalysis;
use App\Services\BieniciMarketPriceM2Service;
use App\Services\GeocodingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TerrainController extends Controller
{
    use AuthorizesRequests;

    protected GeocodingService $geocodingService;
    protected BieniciMarketPriceM2Service $bieniciMarketPriceM2Service;

    public function __construct(GeocodingService $geocodingService, BieniciMarketPriceM2Service $bieniciMarketPriceM2Service)
    {
        $this->geocodingService = $geocodingService;
        $this->bieniciMarketPriceM2Service = $bieniciMarketPriceM2Service;
    }

    /**
     * Display a listing of the terrains.
     */
    public function index(): Response
    {
        $user = $this->user();
        $terrains = Terrain::where('user_id', $user->id)
            ->with('analysis')
            ->orderBy('created_at', 'desc')
            ->get();

        // If a user has a current team, also get terrains from team projects
        if ($user->current_team_id) {
            $teamTerrains = $user->currentTeam->projects()
                ->with(['terrains.analysis'])
                ->get()
                ->pluck('terrains')
                ->flatten()
                ->unique('id');

            // Merge user terrains with team terrains
            $terrains = $terrains->merge($teamTerrains)->unique('id');
        }

        return Inertia::render('Terrains/Index', [
            'terrains' => $terrains,
        ]);
    }

    /**
     * Show the form for creating a new terrain.
     */
    public function create(): Response
    {
        return Inertia::render('Terrains/Create');
    }

    /**
     * Store a newly created terrain in storage.
     * @throws ConnectionException
     */
    public function store(TerrainRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $coordinates = $this->geocodingService->getCoordinates($data['city'], $data['zip_code']);

        $terrain = Terrain::create([
            ...$data,
            'latitude' => $data['latitude'] ?? $coordinates['latitude'],
            'longitude' => $data['longitude'] ?? $coordinates['longitude'],
            'user_id' => $this->user()->id,
        ]);

        // Create initial analysis
        $this->createInitialAnalysis($terrain);

        return redirect()->route('terrains.show', $terrain)
            ->with('success', 'Terrain created successfully.');
    }

    /**
     * Display the specified terrain.
     * @throws AuthorizationException
     */
    public function show(Terrain $terrain): Response
    {
        $this->authorize('view', $terrain);

        $terrain->load('analysis', 'projects');

        return Inertia::render('Terrains/Show', [
            'terrain' => $terrain,
            'analysis' => $terrain->analysis,
            'projects' => $terrain->projects,
        ]);
    }

    /**
     * Show the form for editing the specified terrain.
     * @throws AuthorizationException
     */
    public function edit(Terrain $terrain): Response
    {
        $this->authorize('update', $terrain);

        return Inertia::render('Terrains/Edit', [
            'terrain' => $terrain,
        ]);
    }

    /**
     * Update the specified terrain in storage.
     * @throws AuthorizationException
     * @throws ConnectionException
     */
    public function update(TerrainRequest $request, Terrain $terrain): RedirectResponse
    {
        $this->authorize('update', $terrain);

        $data = $request->validated();

        // Déterminer s’il faut recalculer les coordonnées
        $shouldUpdateCoordinates =
            !$terrain->latitude || !$terrain->longitude ||
            $terrain->city !== $data['city'] ||
            $terrain->zip_code !== $data['zip_code'];

        $coordinates = null;
        if ($shouldUpdateCoordinates) {
            $coordinates = $this->geocodingService->getCoordinates($data['city'], $data['zip_code']);
        }

        $terrain->update([
            ...$data,
            'latitude' => $data['latitude'] ?? $coordinates['latitude'],
            'longitude' => $data['longitude'] ?? $coordinates['longitude'],
        ]);

        // Update analysis if terrain data has changed
        if ($terrain->wasChanged(['price', 'surface_m2', 'viabilised'])) {
            $this->updateAnalysis($terrain);
        }

        return redirect()->route('terrains.show', $terrain)
            ->with('success', 'Terrain updated successfully.');
    }

    /**
     * Remove the specified terrain from storage.
     * @throws AuthorizationException
     */
    public function destroy(Terrain $terrain): RedirectResponse
    {
        $this->authorize('delete', $terrain);

        $terrain->delete();

        return redirect()->route('terrains.index')
            ->with('success', 'Terrain deleted successfully.');
    }

    /**
     * Create an initial analysis for a terrain.
     * @throws ConnectionException
     */
    private function createInitialAnalysis(Terrain $terrain): void
    {
        // Calculate basic metrics
        $price_m2 = $terrain->getPricePerM2();

        // Estimate market price (this would be replaced with actual API data)
        $market_price_m2 = $this->bieniciMarketPriceM2Service->getMarketPriceM2($terrain->city, $terrain->zip_code);

        $metrics = $this->calculateAnalysisMetrics($terrain, $market_price_m2);

        TerrainAnalysis::create([
                'terrain_id' => $terrain->id,
                'price_m2' => $price_m2,
                'market_price_m2' => $market_price_m2,
                'analyzed_at' => now(),
                'analysis_details' => [
                    'calculation_method' => 'basic',
                    'market_data_source' => 'estimated',
                    'division_strategy' => 'equal_lots',
                ],
            ] + $metrics);
    }

    /**
     * Update the analysis for a terrain.
     * @throws ConnectionException
     */
    private function updateAnalysis(Terrain $terrain): void
    {
        $analysis = $terrain->analysis;

        if (!$analysis) {
            $this->createInitialAnalysis($terrain);
            return;
        }

        // Recalculate metrics
        $price_m2 = $terrain->getPricePerM2();
        $market_price_m2 = $analysis->market_price_m2; // Keep existing market price

        $metrics = $this->calculateAnalysisMetrics($terrain, $market_price_m2);

        $analysis->update([
                'price_m2' => $price_m2,
                'analyzed_at' => now(),
            ] + $metrics);
    }

    private function calculateAnalysisMetrics(Terrain $terrain, float $market_price_m2): array
    {
        $viability_cost = $terrain->viabilised ? 0 : 10000; // Placeholder: 5000-15000€
        $lots_possible = max(1, floor($terrain->surface_m2 / 500)); // Placeholder: 1 lot per 500m²

        $resale_estimate_min = ($market_price_m2 * $terrain->surface_m2) * 0.85; // -15% en dessous du marché
        $resale_estimate_max = ($market_price_m2 * $terrain->surface_m2) * 1.15; // +15% au dessus du marché

        $net_margin_estimate = $resale_estimate_min - $terrain->price - $viability_cost;

        $ai_score = min(100, max(0, ($net_margin_estimate / $terrain->price) * 50 + 50));

        if ($ai_score >= 80) {
            $profitability_label = 'Excellent';
        } elseif ($ai_score >= 60) {
            $profitability_label = 'Good';
        } elseif ($ai_score <= 30) {
            $profitability_label = 'Poor';
        } else {
            $profitability_label = 'Average';
        }

        return compact(
            'viability_cost',
            'lots_possible',
            'resale_estimate_min',
            'resale_estimate_max',
            'net_margin_estimate',
            'ai_score',
            'profitability_label'
        );
    }

}
