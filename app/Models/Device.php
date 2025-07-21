<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'user_agent',
        'ip_address',
        'name',
        'last_used_at',
        'is_trusted',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'is_trusted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
