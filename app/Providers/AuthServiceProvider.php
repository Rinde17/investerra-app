<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\Terrain;
use App\Policies\TeamPolicy;
use App\Policies\TerrainPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        Terrain::class => TerrainPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for team roles
        Gate::define('manage-team', function ($user, $team) {
            return $user->belongsToTeam($team) &&
                   ($team->isOwner($user) || $team->getUserRole($user) === 'admin');
        });

        Gate::define('view-team', function ($user, $team) {
            return $user->belongsToTeam($team);
        });
    }
}
