<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float|mixed $price_m2
 * @property float|mixed $market_price_m2
 * @property float|mixed $viability_cost
 * @property float|mixed $resale_estimate_min
 * @property float|mixed $resale_estimate_max
 * @property float|mixed $net_margin_estimate
 * @property float|mixed $ai_score
 * @property Terrain|null $terrain
 * @method static create(array $array)
 */
class TerrainAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'terrain_id',
        'price_m2',
        'market_price_m2',
        'viability_cost',
        'lots_possible',
        'resale_estimate_min',
        'resale_estimate_max',
        'net_margin_estimate',
        'ai_score',
        'profitability_label',
        'analysis_details',
        'analyzed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_m2' => 'decimal:2',
        'market_price_m2' => 'decimal:2',
        'viability_cost' => 'decimal:2',
        'lots_possible' => 'integer',
        'resale_estimate_min' => 'decimal:2',
        'resale_estimate_max' => 'decimal:2',
        'net_margin_estimate' => 'decimal:2',
        'ai_score' => 'decimal:2',
        'analysis_details' => 'array',
        'analyzed_at' => 'datetime',
    ];

    /**
     * Get the terrain that owns the analysis.
     */
    public function terrain(): BelongsTo
    {
        return $this->belongsTo(Terrain::class);
    }

    /**
     * Calculate the profit margin percentage.
     */
    public function getProfitMarginPercentage(): float
    {
        $terrain = $this->terrain;

        if (!$terrain || $terrain->price <= 0) {
            return 0;
        }

        return ($this->net_margin_estimate / $terrain->price) * 100;
    }

    /**
     * Get the total investment cost (purchase price and viability cost).
     */
    public function getTotalInvestmentCost(): float
    {
        $terrain = $this->terrain;

        if (!$terrain) {
            return 0;
        }

        return $terrain->price + ($this->viability_cost ?? 0);
    }

    /**
     * Get the average resale estimate.
     */
    public function getAverageResaleEstimate(): float
    {
        if ($this->resale_estimate_min === null || $this->resale_estimate_max === null) {
            return 0;
        }

        return ($this->resale_estimate_min + $this->resale_estimate_max) / 2;
    }
}
