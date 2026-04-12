<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shop_id',
    'user_id',
    'ip_address',
    'user_agent',
    'viewed_at',
])]
class View extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include views from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('viewed_at', now()->toDateString());
    }

    /**
     * Get the shop that was viewed.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user who viewed the shop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
