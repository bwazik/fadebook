<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Traits\WithToast;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    use WithToast;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = UserRole::tryFrom(Auth::user()->role->value ?? Auth::user()->role);

        $requiredRole = match ($role) {
            'client' => UserRole::Client,
            'barber_owner' => UserRole::BarberOwner,
            'super_admin' => UserRole::SuperAdmin,
            default => null,
        };

        if ($userRole !== $requiredRole) {
            $this->flashToastError(__('messages.unauthorized'));

            return redirect()->route('home');
        }

        return $next($request);
    }
}
