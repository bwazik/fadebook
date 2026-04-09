<?php

namespace App\Models;

use App\Enums\WhatsAppQueueType;
use App\Enums\WhatsAppStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'shop_id',
    'phone',
    'template',
    'queue_type',
    'data',
    'status',
    'error_message',
    'attempts',
    'sent_at',
])]
class WhatsAppMessage extends Model
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
            'queue_type' => WhatsAppQueueType::class,
            'status' => WhatsAppStatus::class,
            'data' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the user this message was sent to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shop this message is associated with.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
