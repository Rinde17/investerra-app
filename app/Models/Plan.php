<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $stripe_price_id
 * @property mixed $id
 * @property mixed $name
 * @method static create(array $array)
 * @method static where(string $string, mixed $stripePriceId)
 * @method static find(mixed $new_plan_id)
 */
class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price_monthly',
        'features',
        'stripe_price_id',
        'description',
        'analyses_per_week',
        'pdf_pro',
        'comparator',
        'pdf_expert',
        'fiscal_analysis',
        'custom_alerts',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'pdf_pro' => 'boolean',
        'comparator' => 'boolean',
        'pdf_expert' => 'boolean',
        'fiscal_analysis' => 'boolean',
        'custom_alerts' => 'boolean',
    ];

    /**
     * Get the users that belong to the plan.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
