<?php

use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use App\Models\Terrain;
use App\Models\TerrainAnalysis;
use App\Models\Plan;
use App\Models\Device;
use App\Models\ContactMessage;

/**
 * Create a user with optional attributes
 */
function createUser(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

/**
 * Create an admin user
 */
function createAdmin(): User
{
    return User::factory()->create([
        'is_admin' => true,
    ]);
}

/**
 * Create a team with optional attributes and owner
 */
function createTeam(array $attributes = [], ?User $owner = null): Team
{
    $owner = $owner ?? createUser();

    return Team::factory()->create(array_merge([
        'owner_id' => $owner->id, // Correction ici, il s'agit de owner_id et non user_id
    ], $attributes));
}

/**
 * Create a project with optional attributes and owner
 */
function createProject(array $attributes = [], ?User $owner = null): Project
{
    $owner = $owner ?? createUser();

    return Project::factory()->create(array_merge([
        'user_id' => $owner->id,
    ], $attributes));
}

/**
 * Create a terrain with optional attributes and optionally attach to a project
 *
 * @param array $attributes Attributes for the Terrain model
 * @param \App\Models\Project|null $project The project to attach the terrain to
 * @param array $pivotAttributes Attributes for the project_terrain pivot table
 * @return \App\Models\Terrain
 */
function createTerrain(array $attributes = [], ?Project $project = null, array $pivotAttributes = []): Terrain
{
    // Ensure a user is associated with the terrain if not provided
    if (!isset($attributes['user_id'])) {
        $attributes['user_id'] = createUser()->id;
    }

    $terrain = Terrain::factory()->create($attributes);

    // Attach the terrain to the project if provided
    if ($project) {
        $project->terrains()->attach($terrain->id, $pivotAttributes);
    }

    return $terrain;
}

/**
 * Create a terrain analysis with optional attributes and terrain
 */
function createTerrainAnalysis(array $attributes = [], ?Terrain $terrain = null): TerrainAnalysis
{
    $terrain = $terrain ?? createTerrain();

    return TerrainAnalysis::factory()->create(array_merge([
        'terrain_id' => $terrain->id,
    ], $attributes));
}

/**
 * Create a plan with optional attributes
 */
function createPlan(array $attributes = []): Plan
{
    return Plan::factory()->create($attributes);
}

/**
 * Create a device with optional attributes and user
 */
function createDevice(array $attributes = [], ?User $user = null): Device
{
    $user = $user ?? createUser();

    return Device::factory()->create(array_merge([
        'user_id' => $user->id,
    ], $attributes));
}

/**
 * Create a contact message with optional attributes
 */
function createContactMessage(array $attributes = []): ContactMessage
{
    return ContactMessage::factory()->create($attributes);
}

/**
 * Login as a user
 */
function loginAs(User $user)
{
    return test()->actingAs($user);
}
