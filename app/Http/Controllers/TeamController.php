<?php

namespace App\Http\Controllers;

use App\Mail\MemberAddedToTeam;
use App\Mail\MemberRemovedFromTeam;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the teams.
     */
    public function index(): Response
    {
        $user = $this->user();
        $teams = $user->allTeams();

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
            'currentTeam' => $user->currentTeam,
        ]);
    }

    /**
     * Show the form for creating a new team.
     */
    public function create(): Response
    {
        return Inertia::render('Teams/Create');
    }

    /**
     * Store a newly created team in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $this->user();

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $user->id,
        ]);

        // Set as the current team if user doesn't have one
        if (!$user->current_team_id) {
            $user->current_team_id = $team->id;
            $user->save();
        }

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified team.
     * @throws AuthorizationException
     */
    public function show(Team $team): Response
    {
        $this->authorize('view', $team);

        $members = $team->users()->with('plan')->get();
        $projects = $team->projects()->with('terrains')->get();

        return Inertia::render('Teams/Show', [
            'team' => $team,
            'members' => $members,
            'projects' => $projects,
            'userRole' => $team->getUserRole($this->user()),
        ]);
    }

    /**
     * Show the form for editing the specified team.
     * @throws AuthorizationException
     */
    public function edit(Team $team): Response
    {
        $this->authorize('update', $team);

        return Inertia::render('Teams/Edit', [
            'team' => $team,
        ]);
    }

    /**
     * Update the specified team in storage.
     * @throws AuthorizationException
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified team from storage.
     * @throws AuthorizationException
     */
    public function destroy(Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        // Update current_team_id for users who have this team as their current team
        User::where('current_team_id', $team->id)
            ->update(['current_team_id' => null]);

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully.');
    }

    /**
     * Switch the user's current team.
     */
    public function switchTeam(Team $team): RedirectResponse
    {
        $user = $this->user();

        // Check if the user belongs to the team
        if (!$user->belongsToTeam($team)) {
            return redirect()->route('teams.index')
                ->with('error', 'You do not belong to this team.');
        }

        $user->current_team_id = $team->id;
        $user->save();

        return redirect()->route('teams.show', $team)
            ->with('success', 'Current team switched successfully.');
    }

    /**
     * Show the form for adding a member to the team.
     * @throws AuthorizationException
     */
    public function addMemberForm(Team $team): Response
    {
        $this->authorize('update', $team);

        return Inertia::render('Teams/AddMember', [
            'team' => $team,
        ]);
    }

    /**
     * Add a member to the team.
     * @throws AuthorizationException
     */
    public function addMember(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', 'string', Rule::in(['member', 'admin'])],
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if the user is already a member of the team
        if ($team->hasUser($user)) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'This user is already a member of the team.');
        }

        try {
            $team->users()->attach($user, ['role' => $request->role]);

            // 🔥 ENVOI DU MAIL ICI
            $inviter = $request->user();
            $members = $team->users()->get();

            Mail::to($user->email)->queue(
                new MemberAddedToTeam($inviter, $team, $members)
            );

            return redirect()->route('teams.show', $team)
                ->with('success', 'Member added successfully.');
        } catch (Exception $e) {
            return redirect()->route('teams.show', $team)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update a team member's role.
     * @throws AuthorizationException
     */
    public function updateMemberRole(Request $request, Team $team, User $user): RedirectResponse
    {
        $this->authorize('update', $team);

        $request->validate([
            'role' => ['required', 'string', Rule::in(['member', 'admin'])],
        ]);

        // Check if the user is a member of the team
        if (!$team->hasUser($user)) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'This user is not a member of the team.');
        }

        // Cannot change the role of the team owner
        if ($team->isOwner($user)) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'Cannot change the role of the team owner.');
        }

        $team->users()->updateExistingPivot($user->id, ['role' => $request->role]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Member role updated successfully.');
    }

    /**
     * Remove a member from the team.
     * @throws AuthorizationException
     */
    public function removeMember(Team $team, User $user): RedirectResponse
    {
        $this->authorize('update', $team);

        // Check if the user is a member of the team
        if (!$team->hasUser($user)) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'This user is not a member of the team.');
        }

        // Cannot remove the team owner
        if ($team->isOwner($user)) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'Cannot remove the team owner.');
        }

        try {
            $team->users()->detach($user);

            if ($user->current_team_id === $team->id) {
                $user->current_team_id = null;
                $user->save();
            }

            // 🔥 Envoi du mail en file d’attente
            $remover = request()->user();
            $members = $team->users()->get();

            Mail::to($user->email)->queue(
                new MemberRemovedFromTeam($remover, $team, $members)
            );
        } catch (Exception $e) {
            return redirect()->route('teams.show', $team)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('teams.show', $team)
            ->with('success', 'Member removed successfully.');
    }
}
