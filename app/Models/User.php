<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

/**
 * @property mixed $id
 * @property mixed $teams
 * @property mixed $ownedTeams
 * @property mixed $current_team_id
 * @property mixed $currentTeam
 * @property mixed $plan
 * @property mixed $plan_id
 * @property string $stripe_id
 * @property mixed $email
 * @method static where(string $string, mixed $id)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plan_id',
        'current_team_id',
        'stripe_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the plan that the user is subscribed to.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the current team of the user.
     */
    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Get all the teams that the user owns.
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    /**
     * Get all the teams that the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get all the terrains that the user has added.
     */
    public function terrains(): HasMany
    {
        return $this->hasMany(Terrain::class);
    }

    /**
     * Get all the projects that the user has created.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam(Team $team): bool
    {
        return $this->teams->contains($team) || $this->ownedTeams->contains($team);
    }

    /**
     * Get all teams that the user has access to.
     */
    public function allTeams()
    {
        return $this->ownedTeams->merge($this->teams);
    }

    /**
     * Check if the user has a specific subscription.
     *
     * @param string $name
     * @return bool
     */
    public function hasSubscription(string $name = 'default'): bool
    {
        return $this->subscribed($name);
    }

    /**
     * Check if the user is on the free plan.
     *
     * @return bool
     */
    public function isOnFreePlan(): bool
    {
        return !$this->hasSubscription() || $this->plan_id === 1; // Assuming 1 is the free plan ID
    }

    /**
     * Check if the user is on the pro plan or higher.
     *
     * @return bool
     */
    public function isOnProPlan(): bool
    {
        return $this->hasSubscription() && $this->plan_id >= 2; // Assuming 2 is the pro plan ID
    }

    /**
     * Check if the user is on the investor plan.
     *
     * @return bool
     */
    public function isOnInvestorPlan(): bool
    {
        return $this->hasSubscription() && $this->plan_id === 3; // Assuming 3 is the investor plan ID
    }

    /**
     * Check if the user can access a specific feature.
     *
     * @param string $feature
     * @return bool
     */
    public function canAccess(string $feature): bool
    {
        if (!$this->plan) {
            return false;
        }

        $plan = $this->plan;

        return match ($feature) {
            'unlimited_analyses' => $this->isOnProPlan() || $this->isOnInvestorPlan(),
            'pdf_pro' => $plan->pdf_pro,
            'comparator' => $plan->comparator,
            'pdf_expert' => $plan->pdf_expert,
            'fiscal_analysis' => $plan->fiscal_analysis,
            'custom_alerts' => $plan->custom_alerts,
            default => false,
        };
    }

    /**
     * Get the number of analyses the user can perform per week.
     *
     * @return int
     */
    public function getAnalysesPerWeek(): int
    {
        if (!$this->plan) {
            return 5; // Default to free plan limit
        }

        return $this->plan->analyses_per_week;
    }
}
