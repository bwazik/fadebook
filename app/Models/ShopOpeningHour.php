<?php

namespace App\Models;

use Database\Factories\ShopOpeningHourFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOpeningHour extends Model
{
    /** @use HasFactory<ShopOpeningHourFactory> */
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'day_of_week',
        'is_closed',
        'open_time',
        'close_time',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
        ];
    }
}
