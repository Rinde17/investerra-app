<?php

use App\Http\Controllers\TeamController;
use App\Http\Controllers\TerrainController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Team routes
Route::middleware(['auth'])->group(function () {
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::post('/teams/{team}/switch', [TeamController::class, 'switchTeam'])->name('teams.switch');

    // Team members
    Route::get('/teams/{team}/members/add', [TeamController::class, 'addMemberForm'])->name('teams.members.add');
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.members.store');
    Route::put('/teams/{team}/members/{user}', [TeamController::class, 'updateMemberRole'])->name('teams.members.update');
    Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('teams.members.destroy');

    // Terrain routes
    Route::get('/terrains', [TerrainController::class, 'index'])->name('terrains.index');
    Route::get('/terrains/create', [TerrainController::class, 'create'])->name('terrains.create');
    Route::post('/terrains', [TerrainController::class, 'store'])->name('terrains.store');
    Route::get('/terrains/{terrain}', [TerrainController::class, 'show'])->name('terrains.show');
    Route::get('/terrains/{terrain}/edit', [TerrainController::class, 'edit'])->name('terrains.edit');
    Route::put('/terrains/{terrain}', [TerrainController::class, 'update'])->name('terrains.update');
    Route::delete('/terrains/{terrain}', [TerrainController::class, 'destroy'])->name('terrains.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
