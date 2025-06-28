<?php

namespace App\Http\Controllers;

use App\Models\Terrain;
use App\Models\TerrainAnalysis;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TerrainController extends Controller
{
    use AuthorizesRequests;
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
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'surface_m2' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'viabilised' => ['boolean'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'source_platform' => ['nullable', 'string', 'max:255'],
        ]);

        $terrain = Terrain::create([
            'title' => $request->title,
            'description' => $request->description,
            'surface_m2' => $request->surface_m2,
            'price' => $request->price,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'viabilised' => $request->viabilised ?? false,
            'source_url' => $request->source_url,
            'source_platform' => $request->source_platform,
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
     */
    public function update(Request $request, Terrain $terrain): RedirectResponse
    {
        $this->authorize('update', $terrain);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'surface_m2' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'viabilised' => ['boolean'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'source_platform' => ['nullable', 'string', 'max:255'],
        ]);

        $terrain->update([
            'title' => $request->title,
            'description' => $request->description,
            'surface_m2' => $request->surface_m2,
            'price' => $request->price,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'viabilised' => $request->viabilised ?? false,
            'source_url' => $request->source_url,
            'source_platform' => $request->source_platform,
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
     */
    private function createInitialAnalysis(Terrain $terrain): void
    {
        // Calculate basic metrics
        $price_m2 = $terrain->getPricePerM2();

        // Estimate market price (this would be replaced with actual API data)
        $market_price_m2 = 40; // Placeholder: 40€/mètre carré pour Montluçon

        // Estimate viability cost if not viabilised
        $viability_cost = $terrain->viabilised ? 0 : 10000; // Placeholder: 5000-15000€

        // Estimate possible lots (simplified calculation)
        $lots_possible = max(1, floor($terrain->surface_m2 / 500)); // Placeholder: 1 lot per 500m²

        // Estimate resale values
        $resale_estimate_min = ($market_price_m2 * $terrain->surface_m2) * 1.15; // Placeholder: 15% profit
        $resale_estimate_max = ($market_price_m2 * $terrain->surface_m2) * 1.25; // Placeholder: 25% profit

        // Calculate net margin
        $net_margin_estimate = $resale_estimate_min - $terrain->price - $viability_cost;

        // Calculate AI score (simplified)
        $ai_score = min(100, max(0, ($net_margin_estimate / $terrain->price) * 50 + 50));

        // Determine profitability label
        $profitability_label = 'Average';
        if ($ai_score >= 80) {
            $profitability_label = 'Excellent';
        } elseif ($ai_score >= 60) {
            $profitability_label = 'Good';
        } elseif ($ai_score <= 30) {
            $profitability_label = 'Poor';
        }

        // Create the analysis
        TerrainAnalysis::create([
            'terrain_id' => $terrain->id,
            'price_m2' => $price_m2,
            'market_price_m2' => $market_price_m2,
            'viability_cost' => $viability_cost,
            'lots_possible' => $lots_possible,
            'resale_estimate_min' => $resale_estimate_min,
            'resale_estimate_max' => $resale_estimate_max,
            'net_margin_estimate' => $net_margin_estimate,
            'ai_score' => $ai_score,
            'profitability_label' => $profitability_label,
            'analysis_details' => [
                'calculation_method' => 'basic',
                'market_data_source' => 'estimated',
                'division_strategy' => 'equal_lots',
            ],
            'analyzed_at' => now(),
        ]);
    }

    /**
     * Update the analysis for a terrain.
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
        $market_price_m2 = $analysis->market_price_m2; // Keep the existing market price
        $viability_cost = $terrain->viabilised ? 0 : 10000;
        $lots_possible = max(1, floor($terrain->surface_m2 / 500));

        // Update resale estimates based on new surface
        $resale_estimate_min = ($market_price_m2 * $terrain->surface_m2) * 1.15;
        $resale_estimate_max = ($market_price_m2 * $terrain->surface_m2) * 1.25;

        // Recalculate net margin
        $net_margin_estimate = $resale_estimate_min - $terrain->price - $viability_cost;

        // Recalculate AI score
        $ai_score = min(100, max(0, ($net_margin_estimate / $terrain->price) * 50 + 50));

        // Update profitability label
        $profitability_label = 'Average';
        if ($ai_score >= 80) {
            $profitability_label = 'Excellent';
        } elseif ($ai_score >= 60) {
            $profitability_label = 'Good';
        } elseif ($ai_score <= 30) {
            $profitability_label = 'Poor';
        }

        // Update the analysis
        $analysis->update([
            'price_m2' => $price_m2,
            'viability_cost' => $viability_cost,
            'lots_possible' => $lots_possible,
            'resale_estimate_min' => $resale_estimate_min,
            'resale_estimate_max' => $resale_estimate_max,
            'net_margin_estimate' => $net_margin_estimate,
            'ai_score' => $ai_score,
            'profitability_label' => $profitability_label,
            'analyzed_at' => now(),
        ]);
    }
}
