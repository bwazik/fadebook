<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\Barber;

class BarberObserver
{
    /**
     * Handle the Barber "created" event.
     */
    public function created(Barber $barber): void
    {
        $user = $barber->user;

        if ($user && $user->role === UserRole::Client) {
            $user->update(['role' => UserRole::Barber]);
        }
    }

    /**
     * Handle the Barber "deleted" event.
     */
    public function deleted(Barber $barber): void
    {
        $user = $barber->user;

        if ($user && $user->role === UserRole::Barber) {
            // Check if this user is still a barber in any other shop
            $stillBarber = Barber::where('user_id', $user->id)->exists();

            if (! $stillBarber) {
                $user->update(['role' => UserRole::Client]);
            }
        }
    }

    /**
     * Handle the Barber "restored" event.
     */
    public function restored(Barber $barber): void
    {
        $user = $barber->user;

        if ($user && $user->role === UserRole::Client) {
            $user->update(['role' => UserRole::Barber]);
        }
    }
}
