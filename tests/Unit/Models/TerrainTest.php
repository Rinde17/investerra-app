<?php

use App\Models\Terrain;
use App\Models\User;
use App\Models\Project;
use App\Models\TerrainAnalysis;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('terrain has a user relationship', function () {
    $user = createUser();
    $terrain = createTerrain(['user_id' => $user->id]);

    expect($terrain->user)->toBeInstanceOf(User::class)
        ->and($terrain->user->id)->toBe($user->id);
});

test('terrain has an analysis relationship', function () {
    $terrain = createTerrain();
    $analysis = TerrainAnalysis::factory()->create(['terrain_id' => $terrain->id]);

    expect($terrain->analysis)->toBeInstanceOf(TerrainAnalysis::class)
        ->and($terrain->analysis->id)->toBe($analysis->id);
});

test('terrain has projects relationship', function () {
    $terrain = createTerrain();
    $project = createProject();

    $project->terrains()->attach($terrain, ['notes' => 'Test notes']);

    expect($terrain->projects)->toBeCollection()
        ->and($terrain->projects->first())->toBeInstanceOf(Project::class)
        ->and($terrain->projects->first()->id)->toBe($project->id)
        ->and($terrain->projects->first()->pivot->notes)->toBe('Test notes');
});

test('getPricePerM2 returns price divided by surface area', function () {
    $terrain = createTerrain([
        'price' => 100000,
        'surface_m2' => 500,
    ]);

    // Expected: 100000 / 500 = 200
    expect($terrain->getPricePerM2())->toEqual(200);
});

test('getPricePerM2 returns 0 when surface area is 0', function () {
    $terrain = createTerrain([
        'price' => 100000,
        'surface_m2' => 0,
    ]);

    expect($terrain->getPricePerM2())->toEqual(0);
});

test('getFullAddress returns city and zip code', function () {
    $terrain = createTerrain([
        'city' => 'Paris',
        'zip_code' => '75001',
    ]);

    expect($terrain->getFullAddress())->toBe('Paris, 75001');
});
