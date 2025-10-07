<?php

use App\Models\Project;
use App\Models\Terrain;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

/*
test('index displays user projects', function () {
    $user = createUser();
    $project1 = createProject([], $user);
    $project2 = createProject([], $user);

    $this->actingAs($user)
        ->get(route('projects.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Index')
            ->has('projects', 2)
            ->where('projects.0.id', $project2->id) // Most recent first
            ->where('projects.1.id', $project1->id)
        );
});

test('index displays team projects', function () {
    $user = createUser();
    $team = createTeam([], $user);
    $user->current_team_id = $team->id;
    $user->save();

    $userProject = createProject([], $user);
    $teamProject = createProject(['team_id' => $team->id]);

    $this->actingAs($user)
        ->get(route('projects.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Index')
            ->has('projects', 2)
        );
});

test('create displays form with teams and terrains', function () {
    $user = createUser();
    $team = createTeam([], $user);
    $terrain = createTerrain(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('projects.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Create')
            ->has('teams')
            ->has('terrains')
        );
});

test('store creates a new project', function () {
    $user = createUser();
    $terrain = createTerrain(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('projects.store'), [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'terrains' => [$terrain->id],
            'notes' => 'Test Notes',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'notes' => 'Test Notes',
        'is_saved' => true,
    ]);

    $project = Project::where('name', 'Test Project')->first();
    $this->assertTrue($project->terrains->contains($terrain->id));
});

test('show displays project details', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain = createTerrain();
    $project->terrains()->attach($terrain);

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Show')
            ->where('project.id', $project->id)
            ->has('terrains')
            ->has('totalInvestment')
            ->has('totalProfit')
            ->has('averageScore')
        );
});

test('show returns 403 for unauthorized users', function () {
    $owner = createUser();
    $project = createProject([], $owner);

    $user = createUser();

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertStatus(403);
});

test('edit displays form with project data', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain = createTerrain();
    $project->terrains()->attach($terrain);

    $this->actingAs($user)
        ->get(route('projects.edit', $project))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Edit')
            ->where('project.id', $project->id)
            ->has('teams')
            ->has('terrains')
            ->has('projectTerrainIds')
        );
});

test('edit returns 403 for unauthorized users', function () {
    $owner = createUser();
    $project = createProject([], $owner);

    $user = createUser();

    $this->actingAs($user)
        ->get(route('projects.edit', $project))
        ->assertStatus(403);
});

test('update modifies project data', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain1 = createTerrain();
    $terrain2 = createTerrain();
    $project->terrains()->attach($terrain1);

    $this->actingAs($user)
        ->put(route('projects.update', $project), [
            'name' => 'Updated Project',
            'description' => 'Updated Description',
            'terrains' => [$terrain2->id],
            'notes' => 'Updated Notes',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Project',
        'description' => 'Updated Description',
        'notes' => 'Updated Notes',
    ]);

    $project->refresh();
    $this->assertFalse($project->terrains->contains($terrain1->id));
    $this->assertTrue($project->terrains->contains($terrain2->id));
});

test('update returns 403 for unauthorized users', function () {
    $owner = createUser();
    $project = createProject([], $owner);

    $user = createUser();

    $this->actingAs($user)
        ->put(route('projects.update', $project), [
            'name' => 'Updated Project',
            'description' => 'Updated Description',
        ])
        ->assertStatus(403);
});

test('destroy deletes a project', function () {
    $user = createUser();
    $project = createProject([], $user);

    $this->actingAs($user)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('destroy returns 403 for unauthorized users', function () {
    $owner = createUser();
    $project = createProject([], $owner);

    $user = createUser();

    $this->actingAs($user)
        ->delete(route('projects.destroy', $project))
        ->assertStatus(403);
});

test('addTerrainForm displays form with available terrains', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain1 = createTerrain(['user_id' => $user->id]);
    $terrain2 = createTerrain(['user_id' => $user->id]);

    // Add terrain1 to the project
    $project->terrains()->attach($terrain1);

    $this->actingAs($user)
        ->get(route('projects.add-terrain-form', $project))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Projects/AddTerrain')
            ->where('project.id', $project->id)
            ->has('terrains')
        );
});

test('addTerrain adds a terrain to a project', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain = createTerrain(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('projects.add-terrain', $project), [
            'terrain_id' => $terrain->id,
            'notes' => 'Terrain Notes',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertTrue($project->terrains()->where('terrain_id', $terrain->id)->exists());
    $this->assertEquals('Terrain Notes', $project->terrains()->find($terrain->id)->pivot->notes);
});

test('addTerrain returns error when terrain already in project', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain = createTerrain(['user_id' => $user->id]);

    // Add terrain to the project
    $project->terrains()->attach($terrain);

    $this->actingAs($user)
        ->post(route('projects.add-terrain', $project), [
            'terrain_id' => $terrain->id,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('removeTerrain removes a terrain from a project', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain = createTerrain(['user_id' => $user->id]);

    // Add terrain to the project
    $project->terrains()->attach($terrain);

    $this->actingAs($user)
        ->delete(route('projects.remove-terrain', [$project, $terrain]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertFalse($project->terrains()->where('terrain_id', $terrain->id)->exists());
});

test('updateTerrainNotes updates notes for a terrain in a project', function () {
    $user = createUser();
    $project = createProject([], $user);
    $terrain = createTerrain(['user_id' => $user->id]);

    // Add terrain to the project
    $project->terrains()->attach($terrain, [
        'notes' => 'Original Notes',
    ]);

    $this->actingAs($user)
        ->put(route('projects.update-terrain-notes', [$project, $terrain]), [
            'notes' => 'Updated Notes',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertEquals('Updated Notes', $project->terrains()->find($terrain->id)->pivot->notes);
});
*/
