<?php

use App\Models\User;
use App\Models\Team;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Terrain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface; // Assurez-vous d'importer MockInterface

uses(RefreshDatabase::class);

// --- Tests pour les relations du modèle User ---

test('user has a plan relationship', function () {
    $plan = createPlan();
    $user = createUser(['plan_id' => $plan->id]);

    expect($user->plan)->toBeInstanceOf(Plan::class)
        ->and($user->plan->id)->toBe($plan->id);
});

test('user has a current team relationship', function () {
    $team = createTeam();
    $user = createUser(['current_team_id' => $team->id]);

    expect($user->currentTeam)->toBeInstanceOf(Team::class)
        ->and($user->currentTeam->id)->toBe($team->id);
});

test('user has owned teams relationship', function () {
    $user = createUser();
    $team = createTeam([], $user); // createTeam with owner

    expect($user->ownedTeams)->toBeCollection()
        ->and($user->ownedTeams->first())->toBeInstanceOf(Team::class)
        ->and($user->ownedTeams->first()->id)->toBe($team->id);
});

test('user has teams relationship', function () {
    $user = createUser();
    $team = createTeam();
    $team->users()->attach($user, ['role' => 'member']);
    $user->load('teams'); // Recharger la relation pour s'assurer que le pivot est là

    expect($user->teams)->toBeCollection()
        ->and($user->teams->first())->toBeInstanceOf(Team::class)
        ->and($user->teams->first()->id)->toBe($team->id)
        ->and($user->teams->first()->pivot->role)->toBe('member');
});

test('user has terrains relationship', function () {
    $user = createUser();
    // createTerrain gère déjà l'association de l'utilisateur
    $terrain = createTerrain(['user_id' => $user->id]);
    $user->load('terrains'); // Recharger la relation terrains

    expect($user->terrains)->toBeCollection()
        ->and($user->terrains->first())->toBeInstanceOf(Terrain::class)
        ->and($user->terrains->first()->id)->toBe($terrain->id);
});

test('user has projects relationship', function () {
    $user = createUser();
    $project = createProject([], $user); // createProject with owner
    $user->load('projects'); // Recharger la relation projects

    expect($user->projects)->toBeCollection()
        ->and($user->projects->first())->toBeInstanceOf(Project::class)
        ->and($user->projects->first()->id)->toBe($project->id);
});

// --- Tests pour les méthodes du modèle User ---

test('belongsToTeam returns true when user owns the team', function () {
    $user = createUser();
    $team = createTeam([], $user);

    expect($user->belongsToTeam($team))->toBeTrue();
});

test('belongsToTeam returns true when user is a member of the team', function () {
    $user = createUser();
    $team = createTeam();
    $team->users()->attach($user, ['role' => 'member']);
    $user->load('teams'); // Recharger la relation teams

    expect($user->belongsToTeam($team))->toBeTrue();
});

test('belongsToTeam returns false when user is not related to the team', function () {
    $user = createUser();
    $team = createTeam();

    expect($user->belongsToTeam($team))->toBeFalse();
});

test('allTeams returns both owned and member teams', function () {
    $user = createUser();
    $ownedTeam = createTeam([], $user);
    $memberTeam = createTeam();
    $memberTeam->users()->attach($user, ['role' => 'member']);
    $user->load('ownedTeams', 'teams'); // Recharger les relations

    $allTeams = $user->allTeams();

    expect($allTeams)->toHaveCount(2)
        ->and($allTeams->contains($ownedTeam))->toBeTrue()
        ->and($allTeams->contains($memberTeam))->toBeTrue();
});

test('hasSubscription returns true when user has a subscription', function () {
    // Créer un utilisateur réel pour avoir les attributs en base de données
    $realUser = createUser(['stripe_id' => 'cus_123']);

    // Créer un mock partiel de la CLASSE User
    $user = $this->partialMock(User::class, function (MockInterface $mock) use ($realUser) {
        // Mocker la méthode 'getAttribute' pour qu'elle retourne les attributs du vrai utilisateur
        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($realUser) {
                return $realUser->getAttribute($key);
            });

        // Mocker la méthode 'subscribed' comme avant
        $mock->shouldReceive('subscribed')
            ->once()
            ->with('default')
            ->andReturn(true);
    });

    // Assigner l'ID du vrai utilisateur au mock si la méthode testée en dépend
    $user->id = $realUser->id;

    expect($user->hasSubscription())->toBeTrue();
});

test('isOnFreePlan returns true when user has no subscription', function () {
    $realUser = createUser(['plan_id' => null]);

    $user = $this->partialMock(User::class, function (MockInterface $mock) use ($realUser) {
        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($realUser) {
                // Si la méthode testée accède à $this->plan, assurez-vous qu'elle est null ici
                if ($key === 'plan') {
                    return null;
                }
                return $realUser->getAttribute($key);
            });

        $mock->shouldReceive('hasSubscription')
            ->once()
            ->andReturn(false);
    });

    $user->id = $realUser->id;

    expect($user->isOnFreePlan())->toBeTrue();
});

/*
test('isOnFreePlan returns true when user has plan_id 1', function () {
    $plan = createPlan(['id' => 1]);
    $realUser = createUser(['plan_id' => $plan->id]);

    // Créer un mock pour l'objet Plan qui sera retourné par $user->plan
    $mockedPlan = Mockery::mock(Plan::class);
    $mockedPlan->id = $plan->id;
    // Si isOnFreePlan vérifie d'autres attributs du plan
    // $mockedPlan->some_feature = true;

    $user = $this->partialMock(User::class, function (MockInterface $mock) use ($realUser, $mockedPlan) {
        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($realUser, $mockedPlan) {
                if ($key === 'plan') {
                    return $mockedPlan; // Retourne le plan mocké quand la relation 'plan' est accédée
                }
                return $realUser->getAttribute($key);
            });

        $mock->shouldReceive('hasSubscription')
            ->once()
            ->andReturn(true);
    });

    $user->id = $realUser->id;

    expect($user->isOnFreePlan())->toBeTrue();
});
*/

/*
test('isOnProPlan returns true when user has subscription and plan_id >= 2', function () {
    $plan = createPlan(['id' => 2]);
    $realUser = createUser(['plan_id' => $plan->id]);

    $mockedPlan = Mockery::mock(Plan::class);
    $mockedPlan->id = $plan->id;

    $user = $this->partialMock(User::class, function (MockInterface $mock) use ($realUser, $mockedPlan) {
        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($realUser, $mockedPlan) {
                if ($key === 'plan') {
                    return $mockedPlan;
                }
                return $realUser->getAttribute($key);
            });

        $mock->shouldReceive('hasSubscription')
            ->once()
            ->andReturn(true);
    });

    $user->id = $realUser->id;

    expect($user->isOnProPlan())->toBeTrue();
});
*/

/*
test('isOnInvestorPlan returns true when user has subscription and plan_id = 3', function () {
    $plan = createPlan(['id' => 3]);
    $realUser = createUser(['plan_id' => $plan->id]);

    $mockedPlan = Mockery::mock(Plan::class);
    $mockedPlan->id = $plan->id;

    $user = $this->partialMock(User::class, function (MockInterface $mock) use ($realUser, $mockedPlan) {
        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($realUser, $mockedPlan) {
                if ($key === 'plan') {
                    return $mockedPlan;
                }
                return $realUser->getAttribute($key);
            });

        $mock->shouldReceive('hasSubscription')
            ->once()
            ->andReturn(true);
    });

    $user->id = $realUser->id;

    expect($user->isOnInvestorPlan())->toBeTrue();
});
*/

test('canAccess returns false when user has no plan', function () {
    $realUser = createUser(['plan_id' => null]);

    $user = $this->partialMock(User::class, function (MockInterface $mock) use ($realUser) {
        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($realUser) {
                if ($key === 'plan') {
                    return null; // Pas de plan pour ce test
                }
                return $realUser->getAttribute($key);
            });

        // Mocker les méthodes isOnProPlan et isOnInvestorPlan si canAccess les appelle
        $mock->shouldReceive('isOnProPlan')->andReturn(false);
        $mock->shouldReceive('isOnInvestorPlan')->andReturn(false);
    });

    $user->id = $realUser->id;

    expect($user->canAccess('unlimited_analyses'))->toBeFalse();
});

/*
test('canAccess unlimited_analyses returns true for pro and investor plans', function () {
    $realUser = createUser();

    // Créer un mock pour l'objet Plan qui sera retourné par $user->plan
    $mockedPlan = Mockery::mock(Plan::class);
    $mockedPlan->id = 2;

    $user = $this->partialMock(User::class, function (MockInterface $mock) use ($realUser, $mockedPlan) {
        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($realUser, $mockedPlan) {
                if ($key === 'plan') {
                    return $mockedPlan; // Retourne le plan mocké quand la relation 'plan' est accédée
                }
                return $realUser->getAttribute($key);
            });

        $mock->shouldReceive('isOnProPlan')
            ->once()
            ->andReturn(true);

        $mock->shouldReceive('isOnInvestorPlan')
            ->once()
            ->andReturn(false);
    });

    $user->id = $realUser->id;

    expect($user->canAccess('unlimited_analyses'))->toBeTrue();
});
*/

test('canAccess feature returns true when plan has the feature', function () {
    $plan = createPlan(['pdf_pro' => true]);
    $user = createUser(['plan_id' => $plan->id]);
    $user->load('plan'); // Charger la relation plan pour que canAccess puisse y accéder

    expect($user->canAccess('pdf_pro'))->toBeTrue();
});

test('canAccess feature returns false when plan does not have the feature', function () {
    $plan = createPlan(['pdf_pro' => false]);
    $user = createUser(['plan_id' => $plan->id]);
    $user->load('plan'); // Charger la relation plan

    expect($user->canAccess('pdf_pro'))->toBeFalse();
});

test('getAnalysesPerWeek returns default 5 when user has no plan', function () {
    $user = createUser(['plan_id' => null]);

    expect($user->getAnalysesPerWeek())->toBe(5);
});

test('getAnalysesPerWeek returns plan analyses_per_week value', function () {
    $plan = createPlan(['analyses_per_week' => 10]);
    $user = createUser(['plan_id' => $plan->id]);
    $user->load('plan'); // Charger la relation plan

    expect($user->getAnalysesPerWeek())->toBe(10);
});

