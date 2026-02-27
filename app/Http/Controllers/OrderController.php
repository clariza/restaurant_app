<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Proforma;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        
        $type     = $request->get('type', 'all');
        $search   = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $sellerId = $request->get('seller_id', 'all');
        $branchId = $request->get('branch_id', 'all'); // ← NUEVO

        // ─── Query de órdenes ───────────────────────────────────────
        $ordersQuery = Sale::with(['items.menuItem', 'user', 'branch'])
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');

        // ─── Query de proformas ──────────────────────────────────────
        $proformasQuery = Proforma::with(['items', 'user'])
            ->where(function ($q) {
                $q->where('converted_to_order', '!=', 1)
                  ->orWhereNull('converted_to_order');
            })
            ->where(function ($q) {
                $q->where('is_converted', '!=', 1)
                  ->orWhereNull('is_converted');
            })
        ->orderBy('created_at', 'asc')
        ->orderBy('id', 'asc');

    // ─── Filtro: sucursal (solo para órdenes) ───────────────────
    // ─── Filtro: sucursal ────────────────────────────────────────
$sessionBranchId = session('branch_id');

if ($sessionBranchId) {
    $ordersQuery->where('branch_id', $sessionBranchId);
    $proformasQuery->where('branch_id', $sessionBranchId); // ← AGREGAR
} elseif ($branchId !== 'all') {
    $ordersQuery->where('branch_id', $branchId);
    $proformasQuery->where('branch_id', $branchId); // ← AGREGAR
}
   

    // ─── Filtro: tipo ────────────────────────────────────────────
    if ($type !== 'all') {
        if ($type === 'proforma') {
            $ordersQuery->whereRaw('1 = 0');
        } else {
            $ordersQuery->where('order_type', $type);
            $proformasQuery->whereRaw('1 = 0');
        }
    }

    // ─── Filtro: fechas ──────────────────────────────────────────
    if ($dateFrom) {
        try {
            $from = Carbon::parse($dateFrom)->startOfDay();
            $ordersQuery->where('created_at', '>=', $from);
            $proformasQuery->where('created_at', '>=', $from);
        } catch (\Exception $e) {}
    }
    if ($dateTo) {
        try {
            $to = Carbon::parse($dateTo)->endOfDay();
            $ordersQuery->where('created_at', '<=', $to);
            $proformasQuery->where('created_at', '<=', $to);
        } catch (\Exception $e) {}
    }

    // ─── Filtro: vendedor ────────────────────────────────────────
    if ($sellerId !== 'all') {
        $ordersQuery->where('user_id', $sellerId);
        $proformasQuery->where('user_id', $sellerId);
    }

    // ─── Filtro: búsqueda ────────────────────────────────────────
    if ($search) {
        $ordersQuery->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('transaction_number', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('daily_order_number', 'like', "%{$search}%");
        });
        $proformasQuery->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    // ─── Datos auxiliares ────────────────────────────────────────
    $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

    $sellers = User::whereIn('id', function ($q) {
        $q->select('user_id')->from('sales')
          ->whereNotNull('user_id')->distinct();
    })->orderBy('name')->get(['id', 'name']);

    // Solo admin ve el filtro de sucursales
    $branches = collect();
    $branches = \App\Models\Branch::where('is_active', true)
    ->orderBy('is_main', 'desc')
    ->orderBy('name')
    ->get();
    $currentBranch = $sessionBranchId
        ? \App\Models\Branch::find($sessionBranchId)
        : null;

    $orders    = $ordersQuery->paginate(15)->appends($request->all());
    $proformas = $proformasQuery->paginate(15)->appends($request->all());

    return view('orders.index', compact(
        'orders', 'proformas', 'hasOpenPettyCash',
        'sellers', 'branches', 'currentBranch'
    ));
}

    public function print($id)
    {
        // Obtener la orden con sus relaciones
        $order = Sale::with(['items.menuItem', 'user'])->findOrFail($id);

        // Retornar vista de impresión
        return view('orders.print', compact('order'));
    }

    public function show($id)
    {
        // Obtener la orden actual con sus relaciones
        $order = Sale::with(['items.menuItem', 'user'])->findOrFail($id);

        // Verificar si hay caja abierta
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Obtener la orden anterior (fecha anterior más cercana)
        $previousOrder = Sale::where('created_at', '<', $order->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        // Si hay órdenes con la misma fecha/hora, usar ID como desempate
        if (!$previousOrder) {
            $previousOrder = Sale::where('created_at', '=', $order->created_at)
                ->where('id', '<', $order->id)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Obtener la orden siguiente (fecha posterior más cercana)
        $nextOrder = Sale::where('created_at', '>', $order->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        // Si hay órdenes con la misma fecha/hora, usar ID como desempate
        if (!$nextOrder) {
            $nextOrder = Sale::where('created_at', '=', $order->created_at)
                ->where('id', '>', $order->id)
                ->orderBy('id', 'asc')
                ->first();
        }

        // Retornar la vista con todas las variables
        return view('orders.show', compact('order', 'previousOrder', 'nextOrder', 'hasOpenPettyCash'));
    }

    public function destroy($id)
    {
        try {
            // Buscar la orden
            $order = Sale::with(['items.menuItem'])->findOrFail($id);

            // Verificar que exista una caja chica abierta
            $openPettyCash = PettyCash::where('status', 'open')->first();

            if (!$openPettyCash) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay una caja chica abierta. No se puede eliminar la orden.'
                    ], 400);
                }

                return redirect()->back()->with('error', 'No hay una caja chica abierta. No se puede eliminar la orden.');
            }

            // Verificar que la orden pertenezca a la caja chica actual
            if ($order->petty_cash_id !== $openPettyCash->id) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Esta orden pertenece a otra caja chica y no puede ser eliminada.'
                    ], 400);
                }

                return redirect()->back()->with('error', 'Esta orden pertenece a otra caja chica y no puede ser eliminada.');
            }

            DB::beginTransaction();

            try {
                // 🔥 CRÍTICO: Si esta orden vino de una proforma, desmarcarla como convertida
                $proformaId = null;

                // Verificar si existe la columna proforma_id en la tabla sales
                if (Schema::hasColumn('sales', 'proforma_id') && $order->proforma_id) {
                    $proformaId = $order->proforma_id;

                    Log::info('🔍 Orden viene de proforma, buscando para desmarcar:', [
                        'order_id' => $order->id,
                        'proforma_id' => $proformaId
                    ]);

                    $proforma = Proforma::find($proformaId);

                    if ($proforma) {
                        Log::info('📋 Proforma encontrada, desmarcando conversión:', [
                            'proforma_id' => $proforma->id,
                            'current_status' => $proforma->status,
                            'converted_to_order' => $proforma->converted_to_order ?? 'campo no existe',
                            'is_converted' => $proforma->is_converted ?? 'campo no existe'
                        ]);

                        // Preparar datos para desmarcar
                        $fillableFields = $proforma->getFillable();
                        $updateData = [];

                        // Desmarcar según los campos disponibles
                        if (in_array('converted_to_order', $fillableFields)) {
                            $updateData['converted_to_order'] = false;
                        }
                        if (in_array('is_converted', $fillableFields)) {
                            $updateData['is_converted'] = false;
                        }
                        if (in_array('converted_order_id', $fillableFields)) {
                            $updateData['converted_order_id'] = null;
                        }
                        if (in_array('converted_at', $fillableFields)) {
                            $updateData['converted_at'] = null;
                        }
                        if (in_array('status', $fillableFields)) {
                            $updateData['status'] = 'reservado'; // Volver al estado original
                        }

                        if (!empty($updateData)) {
                            $updateResult = $proforma->update($updateData);

                            // Recargar para verificar
                            $proforma->refresh();

                            Log::info('✅ Proforma desmarcada:', [
                                'proforma_id' => $proforma->id,
                                'update_result' => $updateResult,
                                'new_status' => $proforma->status,
                                'converted_to_order' => $proforma->converted_to_order ?? 'campo no existe',
                                'is_converted' => $proforma->is_converted ?? 'campo no existe',
                                'converted_order_id' => $proforma->converted_order_id ?? 'campo no existe'
                            ]);
                        } else {
                            Log::warning('⚠️ No hay campos para desmarcar en la proforma');
                        }
                    } else {
                        Log::warning('⚠️ Proforma no encontrada para desmarcar', [
                            'proforma_id' => $proformaId
                        ]);
                    }
                } else {
                    Log::info('ℹ️ Orden no viene de proforma o columna proforma_id no existe');
                }

                // Revertir el stock de los items
                foreach ($order->items as $item) {
                    if ($item->menuItem) {
                        // Incrementar el stock del producto
                        $item->menuItem->increment('stock', $item->quantity);

                        Log::info("Stock revertido para producto ID {$item->menu_item_id}: +{$item->quantity}");
                    }
                }

                // Eliminar los items de la orden
                $order->items()->delete();

                // Guardar número de orden antes de eliminar
                $orderNumber = $order->transaction_number;

                // Eliminar la orden
                $order->delete();

                // Actualizar el total de la caja chica
                $openPettyCash->update();

                DB::commit();

                $logMessage = "Orden eliminada exitosamente: {$orderNumber} por usuario " . auth()->user()->name;
                if ($proformaId) {
                    $logMessage .= " (Proforma #{$proformaId} desmarcada)";
                }
                Log::info($logMessage);

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "La orden {$orderNumber} ha sido eliminada exitosamente.",
                        'proforma_unmarked' => !is_null($proformaId) // Informar si se desmarcó proforma
                    ]);
                }

                return redirect()->route('orders.index')->with('success', "La orden {$orderNumber} ha sido eliminada exitosamente.");
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("Error al eliminar orden: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la orden: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }
}
