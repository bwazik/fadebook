<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if ($request->user() === null) {
            return redirect()->route('login');
        }

        if ($roles !== []) {
            $allowedRoles = collect($roles)
                ->map(fn (string $role): ?UserRole => UserRole::fromKey($role))
                ->filter();

            if (! $allowedRoles->contains($request->user()->role)) {
                return redirect()
                    ->route($request->user()->homeRouteName())
                    ->with('toast', 'مش مسموحلك تدخل هنا');
            }
        }

        return $next($request);
    }
}
