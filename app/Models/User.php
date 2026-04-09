<?php

namespace App\Models;

use App\Enums\ShopStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'password',
        'role',
        'status',
        'no_show_strike_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class, 'owner_id');
    }

    public function homeRouteName(): string
    {
        return match ($this->role) {
            UserRole::Client => 'marketplace',
            UserRole::BarberOwner => $this->ownerHomeRouteName(),
            UserRole::SuperAdmin => 'admin.dashboard',
            UserRole::BarberStaff => 'marketplace',
        };
    }

    protected function ownerHomeRouteName(): string
    {
        $shop = $this->shops()->latest('id')->first();

        if ($shop === null) {
            return 'onboarding';
        }

        return match ($shop->status) {
            ShopStatus::Active => 'owner.dashboard',
            ShopStatus::Pending, ShopStatus::Suspended => 'owner.pending',
            ShopStatus::Rejected => 'onboarding',
        };
    }

    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'status' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
