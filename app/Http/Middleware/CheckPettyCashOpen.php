<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PettyCash;

class CheckPettyCashOpen
{
    public function handle(Request $request, Closure $next)
    {
        // Rutas permitidas sin caja abierta
        $allowedRoutes = [
            'petty-cash.create',
            'petty-cash.store',
            'logout'
        ];
         // Si la ruta actual está permitida, continuar
         if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Verificar si hay una caja chica abierta
        if (!PettyCash::where('status', 'open')->exists()) {
            return redirect()->route('petty-cash.create')
                ->with('warning', 'Debe abrir una caja chica antes de continuar.');
        }

        return $next($request);
    }
}