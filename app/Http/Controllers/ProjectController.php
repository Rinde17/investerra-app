<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Terrain;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the projects.
     */
    public function index(): Response
    {
        $user = $this->user();
        $projects = Project::where('user_id', $user->id)
            ->with(['terrains', 'team'])
            ->orderBy('created_at', 'desc')
            ->get();

        // If a user has a current team, also get team projects
        if ($user->current_team_id) {
            $teamProjects = Project::where('team_id', $user->current_team_id)
                ->with(['terrains', 'team'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Merge user projects with team projects
            $projects = $projects->merge($teamProjects)->unique('id');
        }

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): Response
    {
        $user = $this->user();
        $teams = $user->allTeams();
        $terrains = Terrain::where('user_id', $user->id)
            ->with('analysis')
            ->orderBy('created_at', 'desc')
            ->get();

        // If a user has a current team, also get terrains from team projects
        if ($user->current_team_id) {
            $teamTerrains = $user->currentTeam->projects()
                ->with(['terrains.analysis'])
                ->get()
                ->pluck('terrains')
                ->flatten()
                ->unique('id');

            // Merge user terrains with team terrains
            $terrains = $terrains->merge($teamTerrains)->unique('id');
        }

        return Inertia::render('Projects/Create', [
            'teams' => $teams,
            'terrains' => $terrains,
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'terrains' => ['nullable', 'array'],
            'terrains.*' => ['exists:terrains,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'team_id' => $request->team_id,
            'notes' => $request->notes,
            'is_saved' => true,
        ]);

        // Attach terrains if provided
        if ($request->terrains) {
            $project->terrains()->attach($request->terrains);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     * @throws AuthorizationException
     */
    public function show(Project $project): Response
    {
        $this->authorize('view', $project);

        $project->load(['terrains.analysis', 'team', 'user']);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'terrains' => $project->terrains,
            'team' => $project->team,
            'totalInvestment' => $project->getTotalInvestmentCost(),
            'totalProfit' => $project->getTotalEstimatedProfit(),
            'averageScore' => $project->getAverageAiScore(),
        ]);
    }

    /**
     * Show the form for editing the specified project.
     * @throws AuthorizationException
     */
    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        $user = $this->user();
        $teams = $user->allTeams();

        // Get all terrains the user has access to
        $terrains = Terrain::where('user_id', $user->id)
            ->with('analysis')
            ->orderBy('created_at', 'desc')
            ->get();

        // If user has a current team, also get terrains from team projects
        if ($user->current_team_id) {
            $teamTerrains = $user->currentTeam->projects()
                ->with(['terrains.analysis'])
                ->get()
                ->pluck('terrains')
                ->flatten()
                ->unique('id');

            // Merge user terrains with team terrains
            $terrains = $terrains->merge($teamTerrains)->unique('id');
        }

        // Get the IDs of terrains already in the project
        $projectTerrainIds = $project->terrains->pluck('id')->toArray();

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'teams' => $teams,
            'terrains' => $terrains,
            'projectTerrainIds' => $projectTerrainIds,
        ]);
    }

    /**
     * Update the specified project in storage.
     * @throws AuthorizationException
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'terrains' => ['nullable', 'array'],
            'terrains.*' => ['exists:terrains,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'team_id' => $request->team_id,
            'notes' => $request->notes,
        ]);

        // Sync terrains if provided
        if ($request->has('terrains')) {
            $project->terrains()->sync($request->terrains);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project from storage.
     * @throws AuthorizationException
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Show the form for adding a terrain to the project.
     * @throws AuthorizationException
     */
    public function addTerrainForm(Project $project): Response
    {
        $this->authorize('update', $project);

        $user = $this->user();

        // Get all terrains the user has access to
        $terrains = Terrain::where('user_id', $user->id)
            ->with('analysis')
            ->orderBy('created_at', 'desc')
            ->get();

        // If a user has a current team, also get terrains from team projects
        if ($user->current_team_id) {
            $teamTerrains = $user->currentTeam->projects()
                ->with(['terrains.analysis'])
                ->get()
                ->pluck('terrains')
                ->flatten()
                ->unique('id');

            // Merge user terrains with team terrains
            $terrains = $terrains->merge($teamTerrains)->unique('id');
        }

        // Get the IDs of terrains already in the project
        $projectTerrainIds = $project->terrains->pluck('id')->toArray();

        // Filter out terrains that are already in the project
        $availableTerrains = $terrains->filter(function ($terrain) use ($projectTerrainIds) {
            return !in_array($terrain->id, $projectTerrainIds);
        });

        return Inertia::render('Projects/AddTerrain', [
            'project' => $project,
            'terrains' => $availableTerrains,
        ]);
    }

    /**
     * Add a terrain to the project.
     * @throws AuthorizationException
     */
    public function addTerrain(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'terrain_id' => ['required', 'exists:terrains,id'],
            'notes' => ['nullable', 'string'],
        ]);

        // Check if the terrain is already in the project
        if ($project->terrains()->where('terrain_id', $request->terrain_id)->exists()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'This terrain is already in the project.');
        }

        // Attach the terrain to the project
        $project->terrains()->attach($request->terrain_id, [
            'notes' => $request->notes,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Terrain added to project successfully.');
    }

    /**
     * Remove a terrain from the project.
     * @throws AuthorizationException
     */
    public function removeTerrain(Project $project, Terrain $terrain): RedirectResponse
    {
        $this->authorize('update', $project);

        // Detach the terrain from the project
        $project->terrains()->detach($terrain->id);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Terrain removed from project successfully.');
    }

    /**
     * Update the notes for a terrain in the project.
     * @throws AuthorizationException
     */
    public function updateTerrainNotes(Request $request, Project $project, Terrain $terrain): RedirectResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        // Update the pivot table with the new notes
        $project->terrains()->updateExistingPivot($terrain->id, [
            'notes' => $request->notes,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Terrain notes updated successfully.');
    }
}
