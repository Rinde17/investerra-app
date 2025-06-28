<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
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
    public function view(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
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
    public function update(User $user, Team $team): bool
    {
        // Team owner can update the team
        if ($team->isOwner($user)) {
            return true;
        }

        // Team admin can update the team
        $teamUser = $team->users()->where('user_id', $user->id)->first();
        return $teamUser && $teamUser->pivot->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $team->isOwner($user);
    }

    /**
     * Determine whether the user can add team members.
     */
    public function addMember(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }

    /**
     * Determine whether the user can update team member roles.
     */
    public function updateMemberRole(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }

    /**
     * Determine whether the user can remove team members.
     */
    public function removeMember(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }
}
