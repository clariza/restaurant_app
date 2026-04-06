<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PettyCash;

class CheckPettyCashOpen
{
    public function handle(Request $request, Closure $next)
    {
        $allowedRoutes = [
            'petty-cash.create',
            'petty-cash.store',
            'petty-cash.get-open',
            'petty-cash.check-status',
            'petty-cash.check-open',
            'petty-cash.closure-data',
            'petty-cash.modal-content',
            'petty-cash.closure-modal-content',
            'petty-cash.save-closure',
            'petty-cash.close-all-open',
            'petty-cash.export.excel',
            'petty-cash.export.pdf',
            'petty-cash.print',
            'petty-cash.print-previous',
            'petty-cash.modal-closure',
            'expenses.storeFromModal',  // ✅ Agregar
            'expenses.destroy',         // ✅ Agregar
            'logout',
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        if (!PettyCash::where('status', 'open')->exists()) {
            // ✅ Si es petición AJAX/fetch, responder JSON en lugar de redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja chica abierta.',
                    'redirect' => route('petty-cash.create'),
                ], 403);
            }

            return redirect()->route('petty-cash.create')
                ->with('warning', 'Debe abrir una caja chica antes de continuar.');
        }

        return $next($request);
    }
}
