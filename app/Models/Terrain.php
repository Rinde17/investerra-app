<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property TerrainAnalysis|null $analysis
 * @property Project|null $projects
 * @property mixed $price
 * @property mixed $surface_m2
 * @property mixed $city
 * @property mixed $zip_code
 * @property mixed $viabilised
 * @property mixed $id
 * @property mixed $latitude
 * @property mixed $longitude
 * @method static where(string $string, mixed $id)
 * @method static create(array $array)
 */
class Terrain extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'surface_m2',
        'price',
        'city',
        'zip_code',
        'latitude',
        'longitude',
        'viabilised',
        'source_url',
        'source_platform',
        'is_scraped',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'surface_m2' => 'decimal:2',
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'viabilised' => 'boolean',
        'is_scraped' => 'boolean',
    ];

    /**
     * Get the user that owns the terrain.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the analysis for the terrain.
     */
    public function analysis(): HasOne
    {
        return $this->hasOne(TerrainAnalysis::class);
    }

    /**
     * Get the projects that the terrain belongs to.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Calculate the price per square meter.
     */
    public function getPricePerM2(): float
    {
        return $this->surface_m2 > 0 ? $this->price / $this->surface_m2 : 0;
    }

    /**
     * Get the full address of the terrain.
     */
    public function getFullAddress(): string
    {
        return "$this->city, $this->zip_code";
    }
}
