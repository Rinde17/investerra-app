<?php

use App\Models\Team;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('team has an owner relationship', function () {
    $user = createUser();
    $team = createTeam(['owner_id' => $user->id]);

    expect($team->owner)->toBeInstanceOf(User::class)
        ->and($team->owner->id)->toBe($user->id);
});

test('team has users relationship', function () {
    $team = createTeam();
    $user = createUser();
    $team->users()->attach($user, ['role' => 'member']);

    expect($team->users)->toBeCollection()
        ->and($team->users->first())->toBeInstanceOf(User::class)
        ->and($team->users->first()->id)->toBe($user->id)
        ->and($team->users->first()->pivot->role)->toBe('member');
});

test('team has projects relationship', function () {
    $team = createTeam();
    $project = Project::factory()->create(['team_id' => $team->id]);

    expect($team->projects)->toBeCollection()
        ->and($team->projects->first())->toBeInstanceOf(Project::class)
        ->and($team->projects->first()->id)->toBe($project->id);
});

test('hasUser returns true when user is the owner', function () {
    $user = createUser();
    $team = createTeam(['owner_id' => $user->id]);

    expect($team->hasUser($user))->toBeTrue();
});

test('hasUser returns true when user is a member', function () {
    $team = createTeam();
    $user = createUser();
    $team->users()->attach($user, ['role' => 'member']);

    expect($team->hasUser($user))->toBeTrue();
});

test('hasUser returns false when user is not related to the team', function () {
    $team = createTeam();
    $user = createUser();

    expect($team->hasUser($user))->toBeFalse();
});

test('isOwner returns true when user is the owner', function () {
    $user = createUser();
    $team = createTeam(['owner_id' => $user->id]);

    expect($team->isOwner($user))->toBeTrue();
});

test('isOwner returns false when user is not the owner', function () {
    $team = createTeam();
    $user = createUser();
    $team->users()->attach($user, ['role' => 'member']);

    expect($team->isOwner($user))->toBeFalse();
});

test('getUserRole returns owner for the team owner', function () {
    $user = createUser();
    $team = createTeam(['owner_id' => $user->id]);

    expect($team->getUserRole($user))->toBe('owner');
});

test('getUserRole returns the role for a team member', function () {
    $team = createTeam();
    $user = createUser();
    $team->users()->attach($user, ['role' => 'admin']);

    expect($team->getUserRole($user))->toBe('admin');
});

test('getUserRole returns null for a user not in the team', function () {
    $team = createTeam();
    $user = createUser();

    expect($team->getUserRole($user))->toBeNull();
});
