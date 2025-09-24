<?php

use App\Models\User;
use App\Models\Terrain;
use App\Models\TerrainAnalysis;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('dashboard redirects unauthenticated users', function () {
    $this->get('/dashboard')
        ->assertRedirect('/login');
});

test('dashboard can be rendered for authenticated users', function () {
    $user = createUser();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('mapTerrains')
            ->has('stats')
            ->has('latestTerrains')
        );
});

test('dashboard shows correct terrain count', function () {
    $user = createUser();
    $terrain1 = createTerrain(['user_id' => $user->id]);
    $terrain2 = createTerrain(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.totalTerrains', 2)
        );
});

test('dashboard shows correct profitable terrains count', function () {
    $user = createUser();

    // Create terrain with positive net margin
    $terrain1 = createTerrain(['user_id' => $user->id]);
    TerrainAnalysis::factory()->create([
        'terrain_id' => $terrain1->id,
        'net_margin_estimate' => 10000,
    ]);

    // Create terrain with negative net margin
    $terrain2 = createTerrain(['user_id' => $user->id]);
    TerrainAnalysis::factory()->create([
        'terrain_id' => $terrain2->id,
        'net_margin_estimate' => -5000,
    ]);

    // Create terrain without analysis
    createTerrain(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.profitableTerrains', 1)
        );
});

test('dashboard shows correct AI score average', function () {
    $user = createUser();

    // Create terrain with AI score 80
    $terrain1 = createTerrain(['user_id' => $user->id]);
    TerrainAnalysis::factory()->create([
        'terrain_id' => $terrain1->id,
        'ai_score' => 80,
    ]);

    // Create terrain with AI score 60
    $terrain2 = createTerrain(['user_id' => $user->id]);
    TerrainAnalysis::factory()->create([
        'terrain_id' => $terrain2->id,
        'ai_score' => 60,
    ]);

    // Create terrain without AI score
    $terrain3 = createTerrain(['user_id' => $user->id]);
    TerrainAnalysis::factory()->create([
        'terrain_id' => $terrain3->id,
        'ai_score' => null,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.aiScoreAvg', 70) // (80 + 60) / 2 = 70
        );
});

test('dashboard shows unlimited analyses for pro plan users', function () {
    $plan = createPlan(['id' => 2]); // Pro plan
    $user = createUser(['plan_id' => $plan->id]);

    // Mock the isOnProPlan method
    $user->shouldReceive('isOnProPlan')
        ->andReturn(true);

    $user->shouldReceive('isOnInvestorPlan')
        ->andReturn(false);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.analysesRemaining', 'Illimité')
        );
});

test('dashboard shows correct analyses remaining for free plan users', function () {
    $plan = createPlan(['id' => 1, 'analyses_per_week' => 5]); // Free plan
    $user = createUser(['plan_id' => $plan->id]);

    // Mock the isOnProPlan and isOnInvestorPlan methods
    $user->shouldReceive('isOnProPlan')
        ->andReturn(false);

    $user->shouldReceive('isOnInvestorPlan')
        ->andReturn(false);

    $user->shouldReceive('getAnalysesPerWeek')
        ->andReturn(5);

    // Create a terrain with analysis this week
    $terrain = createTerrain(['user_id' => $user->id]);
    TerrainAnalysis::factory()->create([
        'terrain_id' => $terrain->id,
        'created_at' => now(),
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.analysesRemaining', '4 / 5 restantes cette semaine')
        );
});

test('dashboard shows map terrains with correct data', function () {
    $user = createUser();

    // Create terrain with coordinates
    $terrain = createTerrain([
        'user_id' => $user->id,
        'title' => 'Test Terrain',
        'city' => 'Paris',
        'latitude' => 48.8566,
        'longitude' => 2.3522,
        'surface_m2' => 1000,
        'price' => 100000,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('mapTerrains', 1)
            ->where('mapTerrains.0.id', $terrain->id)
            ->where('mapTerrains.0.title', 'Test Terrain')
            ->where('mapTerrains.0.city', 'Paris')
            ->where('mapTerrains.0.latitude', 48.8566)
            ->where('mapTerrains.0.longitude', 2.3522)
            ->where('mapTerrains.0.surface_m2', 1000)
            ->where('mapTerrains.0.price', 100000)
        );
});

test('dashboard shows latest terrains with correct data', function () {
    $user = createUser();

    // Create terrain with analysis
    $terrain = createTerrain([
        'user_id' => $user->id,
        'title' => 'Latest Terrain',
        'city' => 'Lyon',
    ]);

    TerrainAnalysis::factory()->create([
        'terrain_id' => $terrain->id,
        'ai_score' => 75,
        'profitability_label' => 'Good',
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('latestTerrains', 1)
            ->where('latestTerrains.0.id', $terrain->id)
            ->where('latestTerrains.0.title', 'Latest Terrain')
            ->where('latestTerrains.0.city', 'Lyon')
            ->where('latestTerrains.0.ai_score', 75)
            ->where('latestTerrains.0.profitability_label', 'Good')
        );
});
