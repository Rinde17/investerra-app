<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Team;
use App\Models\Terrain;
use App\Models\TerrainAnalysis;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('project has a user relationship', function () {
    // createProject crée déjà un utilisateur par défaut si non spécifié
    $project = createProject();

    expect($project->user)->toBeInstanceOf(User::class)
        ->and($project->user->id)->toBe($project->user_id); // L'ID de l'utilisateur est directement sur le projet
});

test('project has a team relationship', function () {
    $team = createTeam();
    // Utilisation de l'attribut team_id lors de la création du projet
    $project = createProject(['team_id' => $team->id]);

    expect($project->team)->toBeInstanceOf(Team::class)
        ->and($project->team->id)->toBe($team->id);
});

test('project has terrains relationship', function () {
    $project = createProject();
    // createTerrain peut maintenant attacher le terrain au projet directement
    $terrain = createTerrain([], $project, ['notes' => 'Test notes']);

    // Recharger la relation terrains pour s'assurer que les données pivot sont là
    $project->load('terrains');

    expect($project->terrains)->toBeCollection()
        ->and($project->terrains->first())->toBeInstanceOf(Terrain::class)
        ->and($project->terrains->first()->id)->toBe($terrain->id)
        ->and($project->terrains->first()->pivot->notes)->toBe('Test notes');
});

test('getTotalInvestmentCost returns sum of terrain prices and viability costs', function () {
    $project = createProject();

    // Création des terrains avec leurs analyses et attachement au projet via le helper
    $terrain1 = createTerrain([
        'price' => 10000,
    ], $project);
    // Création de l'analyse séparément et attachement au terrain
    createTerrainAnalysis([
        'terrain_id' => $terrain1->id,
        'viability_cost' => 5000,
    ]);


    $terrain2 = createTerrain([
        'price' => 15000,
    ], $project);
    createTerrainAnalysis([
        'terrain_id' => $terrain2->id,
        'viability_cost' => 3000,
    ]);

    // Recharger la relation terrains pour que le modèle Project ait les dernières données
    $project->refresh();

    // Expected total: 10000 (terrain1 price) + 5000 (analysis1 viability_cost) + 15000 (terrain2 price) + 3000 (analysis2 viability_cost) = 33000
    expect($project->getTotalInvestmentCost())->toEqual(33000);
});

test('getTotalInvestmentCost handles terrains without analysis', function () {
    $project = createProject();

    // Création d'un terrain sans analyse et attachement au projet
    $terrain = createTerrain([
        'price' => 10000,
    ], $project);

    $project->refresh(); // Recharger pour s'assurer que le projet a le terrain attaché

    // Expected total: 10000 (terrain price)
    expect($project->getTotalInvestmentCost())->toEqual(10000);
});

test('getTotalEstimatedProfit returns sum of terrain analysis net margins', function () {
    $project = createProject();

    // Création des terrains avec leurs analyses et attachement au projet
    $terrain1 = createTerrain([], $project);
    createTerrainAnalysis([
        'terrain_id' => $terrain1->id,
        'net_margin_estimate' => 20000,
    ]);

    $terrain2 = createTerrain([], $project);
    createTerrainAnalysis([
        'terrain_id' => $terrain2->id,
        'net_margin_estimate' => 15000,
    ]);

    $project->refresh(); // Recharger la relation terrains

    // Expected total: 20000 (analysis1 net_margin_estimate) + 15000 (analysis2 net_margin_estimate) = 35000
    expect($project->getTotalEstimatedProfit())->toEqual(35000);
});

test('getTotalEstimatedProfit handles terrains without analysis', function () {
    $project = createProject();

    // Création d'un terrain sans analyse et attachement au projet
    $terrain = createTerrain([], $project);

    $project->refresh(); // Recharger pour s'assurer que le projet a le terrain attaché

    // Expected total: 0 (no analysis)
    expect($project->getTotalEstimatedProfit())->toEqual(0);
});

test('getAverageAiScore returns average of terrain analysis ai scores', function () {
    $project = createProject();

    // Création des terrains avec analyses et attachement au projet
    $terrain1 = createTerrain([], $project);
    createTerrainAnalysis([
        'terrain_id' => $terrain1->id,
        'ai_score' => 80,
    ]);

    $terrain2 = createTerrain([], $project);
    createTerrainAnalysis([
        'terrain_id' => $terrain2->id,
        'ai_score' => 60,
    ]);

    $project->refresh(); // Recharger la relation terrains

    // Expected average: (80 + 60) / 2 = 70
    expect($project->getAverageAiScore())->toEqual(70);
});

test('getAverageAiScore returns 0 when no terrains have analysis', function () {
    $project = createProject();

    // Création d'un terrain sans analyse et attachement au projet
    $terrain = createTerrain([], $project);

    $project->refresh(); // Recharger pour s'assurer que le projet a le terrain attaché

    // Expected average: 0 (no analysis)
    expect($project->getAverageAiScore())->toEqual(0);
});

test('getAverageAiScore handles null ai_score values', function () {
    $project = createProject();

    // Création des terrains avec analyses et attachement au projet
    $terrain1 = createTerrain([], $project);
    createTerrainAnalysis([
        'terrain_id' => $terrain1->id,
        'ai_score' => 80,
    ]);

    $terrain2 = createTerrain([], $project);
    createTerrainAnalysis([
        'terrain_id' => $terrain2->id,
        'ai_score' => null,
    ]);

    $project->refresh(); // Recharger la relation terrains

    // Expected average: 80 / 1 = 80 (only counting non-null scores)
    expect($project->getAverageAiScore())->toEqual(80);
});
