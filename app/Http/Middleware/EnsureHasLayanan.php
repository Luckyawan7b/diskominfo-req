<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasLayanan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if ($user && $user->isOperator()) {
            $hasLayanan = \App\Models\Layanan::where('dinas_id', $user->dinas_id)->exists();
            
            $allowedRoutes = [
                'layanan.create', 
                'logout', 
                'livewire.update', 
                'livewire.upload-file',
                'livewire.preview-file'
            ];
            
            $routeName = $request->route() ? $request->route()->getName() : null;
            
            if (!$hasLayanan && !in_array($routeName, $allowedRoutes)) {
                return redirect()->route('layanan.create')
                    ->with('error', 'Anda wajib menambahkan minimal 1 (satu) layanan digital terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
