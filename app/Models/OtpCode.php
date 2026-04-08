<?php

namespace App\Models;

use App\Enums\OtpPurpose;
use Database\Factories\OtpCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    /** @use HasFactory<OtpCodeFactory> */
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'purpose',
        'attempts',
        'is_used',
        'expires_at',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    protected function casts(): array
    {
        return [
            'purpose' => OtpPurpose::class,
            'is_used' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }
}
