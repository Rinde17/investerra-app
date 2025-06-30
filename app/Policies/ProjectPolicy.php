<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
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
    public function view(User $user, Project $project): bool
    {
        // User can view their own projects
        if ($project->user_id === $user->id) {
            return true;
        }

        // User can view projects that belong to their current team
        if ($user->current_team_id && $project->team_id === $user->current_team_id) {
            return true;
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
    public function update(User $user, Project $project): bool
    {
        // User can update their own projects
        if ($project->user_id === $user->id) {
            return true;
        }

        // User can update projects that belong to their current team
        // if they are the team owner or an admin
        if ($user->current_team_id && $project->team_id === $user->current_team_id) {
            $team = $user->currentTeam;

            if ($team && ($team->isOwner($user) || $team->getUserRole($user) === 'admin')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // User can delete their own projects
        if ($project->user_id === $user->id) {
            return true;
        }

        // Team owner or admin can delete team projects
        if ($user->current_team_id && $project->team_id === $user->current_team_id) {
            $team = $user->currentTeam;

            if ($team && ($team->isOwner($user) || $team->getUserRole($user) === 'admin')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can add terrains to the project.
     */
    public function addTerrain(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }

    /**
     * Determine whether the user can remove terrains from the project.
     */
    public function removeTerrain(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }

    /**
     * Determine whether the user can update terrain notes in the project.
     */
    public function updateTerrainNotes(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}
