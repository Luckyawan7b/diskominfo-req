<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk membatasi akses berdasarkan role user.
 *
 * Usage di route: ->middleware('role:admin')
 * atau ->middleware('role:admin,operator')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role?->name, $roles)) {
            if ($request->expectsJson()) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }

            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
