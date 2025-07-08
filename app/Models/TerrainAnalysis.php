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
 * @property mixed $lots_possible
 * @property mixed $profitability_label
 * @property mixed $analyzed_at
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
        'price_difference_percentage',
        'viability_cost',
        'lots_possible',
        'resale_estimate_min',
        'resale_estimate_max',
        'net_margin_estimate',
        'profit_margin_percentage',
        'ai_score',
        'profitability_label',
        'overall_risk',
        'overall_recommendation',
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
        'price_difference_percentage' => 'decimal:2',
        'viability_cost' => 'decimal:2',
        'lots_possible' => 'integer',
        'resale_estimate_min' => 'decimal:2',
        'resale_estimate_max' => 'decimal:2',
        'net_margin_estimate' => 'decimal:2',
        'profit_margin_percentage' => 'decimal:2',
        'ai_score' => 'decimal:2',
        'analysis_details' => 'array',
        'analyzed_at' => 'datetime',
        'overall_risk' => 'string',
        'overall_recommendation' => 'string',
    ];

    /**
     * Get the terrain that owns the analysis.
     */
    public function terrain(): BelongsTo
    {
        return $this->belongsTo(Terrain::class);
    }
}
