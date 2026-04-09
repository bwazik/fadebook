<?php

namespace App\Models;

use App\Enums\ShopStatus;
use Database\Factories\ShopFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    /** @use HasFactory<ShopFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'area_id',
        'name',
        'address',
        'phone',
        'logo_path',
        'status',
        'rejection_reason',
        'basic_services',
        'barbers_count',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function openingHours(): HasMany
    {
        return $this->hasMany(ShopOpeningHour::class);
    }

    protected function casts(): array
    {
        return [
            'status' => ShopStatus::class,
            'basic_services' => 'array',
        ];
    }
}
