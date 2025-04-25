<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }
        

        // Verificar si el usuario tiene el rol adecuado
        if (auth()->user()->role === $role) {
            return $next($request);
        }
        if ($request->user()->role !== $role) {
            abort(403);
        }

        // Redirigir al usuario al login si no tiene el rol adecuado
        return redirect()->route('login')->with('error', 'No tienes permiso para acceder a esta página.');
    }
}