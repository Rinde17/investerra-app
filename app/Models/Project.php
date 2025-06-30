<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static where(string $string, mixed $id)
 * @method static create(array $array)
 * @property mixed $terrains
 * @property mixed $team
 */
class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'team_id',
        'notes',
        'is_saved',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_saved' => 'boolean',
    ];

    /**
     * Get the user that owns the project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team that the project belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the terrains that belong to the project.
     */
    public function terrains(): BelongsToMany
    {
        return $this->belongsToMany(Terrain::class)
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Get the total investment cost of all terrains in the project.
     */
    public function getTotalInvestmentCost(): float
    {
        $total = 0;

        foreach ($this->terrains as $terrain) {
            $total += $terrain->price;

            if ($terrain->analysis) {
                $total += $terrain->analysis->viability_cost ?? 0;
            }
        }

        return $total;
    }

    /**
     * Get the total estimated profit of all terrains in the project.
     */
    public function getTotalEstimatedProfit(): float
    {
        $total = 0;

        foreach ($this->terrains as $terrain) {
            if ($terrain->analysis) {
                $total += $terrain->analysis->net_margin_estimate ?? 0;
            }
        }

        return $total;
    }

    /**
     * Get the average AI score of all terrains in the project.
     */
    public function getAverageAiScore(): float
    {
        $terrains = $this->terrains()->whereHas('analysis')->get();

        if ($terrains->isEmpty()) {
            return 0;
        }

        $totalScore = 0;
        $count = 0;

        foreach ($terrains as $terrain) {
            if ($terrain->analysis && $terrain->analysis->ai_score !== null) {
                $totalScore += $terrain->analysis->ai_score;
                $count++;
            }
        }

        return $count > 0 ? $totalScore / $count : 0;
    }
}
