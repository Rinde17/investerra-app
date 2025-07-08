<?php

namespace App\Http\Controllers;

use App\Http\Requests\TerrainRequest;
use App\Models\Terrain;
use App\Services\AIAnalysisService;
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
    protected AIAnalysisService $aiAnalysisService;

    public function __construct(
        GeocodingService $geocodingService,
        BieniciMarketPriceM2Service $bieniciMarketPriceM2Service,
        AIAnalysisService $aiAnalysisService
    ) {
        $this->geocodingService = $geocodingService;
        $this->bieniciMarketPriceM2Service = $bieniciMarketPriceM2Service;
        $this->aiAnalysisService = $aiAnalysisService;
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
        $this->aiAnalysisService->analyzeTerrain($terrain);

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
            $this->aiAnalysisService->analyzeTerrain($terrain);
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


}
