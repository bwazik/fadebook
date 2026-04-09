<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Database\Factories\AreaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'uuid',
    'name',
    'slug',
    'is_active',
])]

class Area extends Model
{
    /** @use HasFactory<AreaFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    /**
     * Scope a query to only include active areas.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the shops in this area.
     */
    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
