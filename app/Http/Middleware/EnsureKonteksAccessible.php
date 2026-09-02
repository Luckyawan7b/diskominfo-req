<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKonteksAccessible
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $konteks = $request->route('konteks');

        if (! $konteks) {
            return $next($request);
        }

        $user = $request->user();

        // Admin selalu lolos
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Operator hanya lolos jika desa_id cocok
        if ($user && $user->isOperator() && $konteks->desa_id === $user->desa_id) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke konteks risiko desa lain.');
    }
}
