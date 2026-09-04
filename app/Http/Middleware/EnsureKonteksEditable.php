<?php

namespace App\Http\Middleware;

use App\Models\MrKonteks;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan konteks masih bisa diedit.
 *
 * - Admin: selalu bisa akses
 * - Operator: hanya jika status konteks = draft / rejected
 *
 * Route harus memiliki parameter {konteks} (route model binding).
 */
class EnsureKonteksEditable
{
    public function handle(Request $request, Closure $next): Response
    {
        $konteks = $request->route('konteks');

        // Route model binding: jika string, cari manual
        if (! $konteks instanceof MrKonteks) {
            $konteks = MrKonteks::findOrFail($konteks);
        }

        $user = $request->user();

        // Admin selalu bisa akses
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Operator hanya bisa edit jika draft/rejected
        if (! $konteks->isEditableByOperator()) {
            return redirect()->route('konteks.index')
                ->with('error', 'Dokumen ini tidak bisa diedit karena statusnya: ' . $konteks->status);
        }

        // Operator hanya boleh akses konteks desanya sendiri
        if ($konteks->dinas_id !== $user->dinas_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen dinas lain.');
        }

        return $next($request);
    }
}
