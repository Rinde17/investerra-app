<?php

use App\Models\Terrain;
use App\Models\TerrainAnalysis;
use App\Models\Team;
use App\Services\AIAnalysisService;
use App\Services\BieniciMarketPriceM2Service;
use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

/*
test('index displays user terrains', function () {
    $user = createUser();
    $terrain1 = createTerrain(['user_id' => $user->id]);
    $terrain2 = createTerrain(['user_id' => $user->id]);

    // Mock the canAccess method
    $user->shouldReceive('canAccess')
        ->with('comparator')
        ->andReturn(true);

    $this->actingAs($user)
        ->get(route('terrains.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Terrains/Index')
            ->has('terrains', 2)
            ->where('canCompare', true)
        );
});

test('index displays team terrains', function () {
    $user = createUser();
    $team = createTeam([], $user);
    $user->current_team_id = $team->id;
    $user->save();

    $project = createProject(['team_id' => $team->id]);
    $terrain = createTerrain();
    $project->terrains()->attach($terrain);

    // Mock the canAccess method
    $user->shouldReceive('canAccess')
        ->with('comparator')
        ->andReturn(false);

    $this->actingAs($user)
        ->get(route('terrains.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Terrains/Index')
            ->has('terrains', 1)
            ->where('canCompare', false)
        );
});

test('create displays form', function () {
    $user = createUser();

    $this->actingAs($user)
        ->get(route('terrains.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Terrains/Create')
        );
});

test('store creates a new terrain', function () {
    $user = createUser();

    // Mock the GeocodingService
    $this->mock(GeocodingService::class, function (MockInterface $mock) {
        $mock->shouldReceive('getCoordinates')
            ->andReturn([
                'latitude' => 48.8566,
                'longitude' => 2.3522,
            ]);
    });

    // Mock the AIAnalysisService
    $this->mock(AIAnalysisService::class, function (MockInterface $mock) {
        $mock->shouldReceive('analyzeTerrain')
            ->andReturn(TerrainAnalysis::factory()->make());
    });

    $this->actingAs($user)
        ->post(route('terrains.store'), [
            'title' => 'Test Terrain',
            'description' => 'Test Description',
            'surface_m2' => 1000,
            'price' => 100000,
            'city' => 'Paris',
            'zip_code' => '75001',
            'viabilised' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('terrains', [
        'title' => 'Test Terrain',
        'description' => 'Test Description',
        'surface_m2' => 1000,
        'price' => 100000,
        'city' => 'Paris',
        'zip_code' => '75001',
        'viabilised' => true,
        'user_id' => $user->id,
    ]);
});

test('show displays terrain details', function () {
    $user = createUser();
    $terrain = createTerrain(['user_id' => $user->id]);
    $analysis = TerrainAnalysis::factory()->create(['terrain_id' => $terrain->id]);

    $this->actingAs($user)
        ->get(route('terrains.show', $terrain))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Terrains/Show')
            ->where('terrain.id', $terrain->id)
            ->has('analysis')
            ->has('projects')
        );
});

test('show returns 403 for unauthorized users', function () {
    $owner = createUser();
    $terrain = createTerrain(['user_id' => $owner->id]);

    $user = createUser();

    $this->actingAs($user)
        ->get(route('terrains.show', $terrain))
        ->assertStatus(403);
});

test('edit displays form with terrain data', function () {
    $user = createUser();
    $terrain = createTerrain(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('terrains.edit', $terrain))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Terrains/Edit')
            ->where('terrain.id', $terrain->id)
        );
});

test('edit returns 403 for unauthorized users', function () {
    $owner = createUser();
    $terrain = createTerrain(['user_id' => $owner->id]);

    $user = createUser();

    $this->actingAs($user)
        ->get(route('terrains.edit', $terrain))
        ->assertStatus(403);
});

test('update modifies terrain data without changing coordinates', function () {
    $user = createUser();
    $terrain = createTerrain([
        'user_id' => $user->id,
        'title' => 'Original Title',
        'description' => 'Original Description',
        'surface_m2' => 1000,
        'price' => 100000,
        'city' => 'Paris',
        'zip_code' => '75001',
        'latitude' => 48.8566,
        'longitude' => 2.3522,
    ]);

    // Mock the AIAnalysisService
    $this->mock(AIAnalysisService::class, function (MockInterface $mock) {
        $mock->shouldReceive('analyzeTerrain')
            ->andReturn(TerrainAnalysis::factory()->make());
    });

    $this->actingAs($user)
        ->put(route('terrains.update', $terrain), [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'surface_m2' => 1500,
            'price' => 150000,
            'city' => 'Paris',
            'zip_code' => '75001',
            'viabilised' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('terrains', [
        'id' => $terrain->id,
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'surface_m2' => 1500,
        'price' => 150000,
        'city' => 'Paris',
        'zip_code' => '75001',
        'viabilised' => true,
        'latitude' => 48.8566,
        'longitude' => 2.3522,
    ]);
});

test('update recalculates coordinates when city or zip code changes', function () {
    $user = createUser();
    $terrain = createTerrain([
        'user_id' => $user->id,
        'city' => 'Paris',
        'zip_code' => '75001',
        'latitude' => 48.8566,
        'longitude' => 2.3522,
    ]);

    // Mock the GeocodingService
    $this->mock(GeocodingService::class, function (MockInterface $mock) {
        $mock->shouldReceive('getCoordinates')
            ->with('Lyon', '69001')
            ->andReturn([
                'latitude' => 45.7640,
                'longitude' => 4.8357,
            ]);
    });

    // Mock the AIAnalysisService
    $this->mock(AIAnalysisService::class, function (MockInterface $mock) {
        $mock->shouldReceive('analyzeTerrain')
            ->andReturn(TerrainAnalysis::factory()->make());
    });

    $this->actingAs($user)
        ->put(route('terrains.update', $terrain), [
            'title' => $terrain->title,
            'description' => $terrain->description,
            'surface_m2' => $terrain->surface_m2,
            'price' => $terrain->price,
            'city' => 'Lyon',
            'zip_code' => '69001',
            'viabilised' => $terrain->viabilised,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('terrains', [
        'id' => $terrain->id,
        'city' => 'Lyon',
        'zip_code' => '69001',
        'latitude' => 45.7640,
        'longitude' => 4.8357,
    ]);
});

test('update returns 403 for unauthorized users', function () {
    $owner = createUser();
    $terrain = createTerrain(['user_id' => $owner->id]);

    $user = createUser();

    $this->actingAs($user)
        ->put(route('terrains.update', $terrain), [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'surface_m2' => 1500,
            'price' => 150000,
            'city' => 'Paris',
            'zip_code' => '75001',
            'viabilised' => true,
        ])
        ->assertStatus(403);
});

test('destroy deletes a terrain', function () {
    $user = createUser();
    $terrain = createTerrain(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('terrains.destroy', $terrain))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('terrains', [
        'id' => $terrain->id,
    ]);
});

test('destroy returns 403 for unauthorized users', function () {
    $owner = createUser();
    $terrain = createTerrain(['user_id' => $owner->id]);

    $user = createUser();

    $this->actingAs($user)
        ->delete(route('terrains.destroy', $terrain))
        ->assertStatus(403);
});
*/
