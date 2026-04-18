<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    /**
     * Scope a query to only include client-facing notifications (exclude admin notifications).
     */
    public function scopeClientOnly(Builder $query): Builder
    {
        return $query->where('type', 'not like', 'App\\\\Notifications\\\\Admin\\\\%');
    }
}
