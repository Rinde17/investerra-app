<?php

namespace App\Policies;

use App\Models\Terrain;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TerrainPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Terrain $terrain): bool
    {
        // User can view their own terrains
        if ($terrain->user_id === $user->id) {
            return true;
        }

        // User can view terrains that belong to projects in their current team
        if ($user->current_team_id) {
            $teamProjects = $user->currentTeam->projects()->pluck('id')->toArray();

            foreach ($terrain->projects as $project) {
                if (in_array($project->id, $teamProjects)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Terrain $terrain): bool
    {
        // User can update their own terrains
        if ($terrain->user_id === $user->id) {
            return true;
        }

        // User can update terrains that belong to projects in their current team
        // if they are the team owner or an admin
        if ($user->current_team_id) {
            $team = $user->currentTeam;

            if ($team && ($team->isOwner($user) || $team->getUserRole($user) === 'admin')) {
                $teamProjects = $team->projects()->pluck('id')->toArray();

                foreach ($terrain->projects as $project) {
                    if (in_array($project->id, $teamProjects)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Terrain $terrain): bool
    {
        // Only the owner of the terrain can delete it
        return $terrain->user_id === $user->id;
    }

    /**
     * Determine whether the user can analyze the model.
     */
    public function analyze(User $user, Terrain $terrain): bool
    {
        // User can analyze terrains they can view
        return $this->view($user, $terrain);
    }

    /**
     * Determine whether the user can add the terrain to a project.
     */
    public function addToProject(User $user, Terrain $terrain): bool
    {
        // User can add terrains they can view to their projects
        return $this->view($user, $terrain);
    }
}
