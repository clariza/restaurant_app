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

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sellerId = $request->get('seller_id', 'all');

        // Query base para órdenes (orden ASCENDENTE)
        $ordersQuery = Sale::with(['items.menuItem', 'user'])
            ->orderBy('created_at', 'asc')  // Orden ascendente por fecha
            ->orderBy('id', 'asc');          // Desempate por ID

        // Query base para proformas (orden ASCENDENTE)
        $proformasQuery = Proforma::with(['items', 'user'])
            ->orderBy('created_at', 'asc')  // Orden ascendente por fecha
            ->orderBy('id', 'asc');          // Desempate por ID

        // Aplicar filtro de tipo
        if ($type !== 'all') {
            if ($type === 'proforma') {
                // Solo mostrar proformas
                $ordersQuery->whereRaw('1 = 0'); // No mostrar órdenes
            } else {
                // Filtrar por tipo de orden específico
                $ordersQuery->where('order_type', $type);
                $proformasQuery->whereRaw('1 = 0'); // No mostrar proformas
            }
        }

        // Aplicar filtro de fecha desde
        if ($dateFrom) {
            try {
                $dateFromCarbon = Carbon::parse($dateFrom)->startOfDay();
                $ordersQuery->where('created_at', '>=', $dateFromCarbon);
                $proformasQuery->where('created_at', '>=', $dateFromCarbon);
            } catch (\Exception $e) {
                // Si hay error en el parsing de fecha, ignorar el filtro
            }
        }

        // Aplicar filtro de fecha hasta
        if ($dateTo) {
            try {
                $dateToCarbon = Carbon::parse($dateTo)->endOfDay();
                $ordersQuery->where('created_at', '<=', $dateToCarbon);
                $proformasQuery->where('created_at', '<=', $dateToCarbon);
            } catch (\Exception $e) {
                // Si hay error en el parsing de fecha, ignorar el filtro
            }
        }

        // Aplicar filtro de vendedor
        if ($sellerId !== 'all') {
            $ordersQuery->where('user_id', $sellerId);
            $proformasQuery->where('user_id', $sellerId);
        }

        // Aplicar búsqueda
        if ($search) {
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('transaction_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('daily_order_number', 'like', "%{$search}%");
            });

            $proformasQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Verificar caja abierta
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Obtener lista de vendedores (usuarios que han realizado ventas)
        $sellers = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('sales')
                ->whereNotNull('user_id')
                ->distinct();
        })
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        // Obtener resultados paginados
        $orders = $ordersQuery->paginate(15)->appends($request->all());
        $proformas = $proformasQuery->paginate(15)->appends($request->all());

        return view('orders.index', compact('orders', 'proformas', 'hasOpenPettyCash', 'sellers'));
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

                // Eliminar la orden
                $orderNumber = $order->transaction_number;
                $order->delete();

                // Actualizar el total de la caja chica
                $openPettyCash->update();

                DB::commit();

                Log::info("Orden eliminada exitosamente: {$orderNumber} por usuario " . auth()->user()->name);

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "La orden {$orderNumber} ha sido eliminada exitosamente."
                    ]);
                }

                return redirect()->route('orders.index')->with('success', "La orden {$orderNumber} ha sido eliminada exitosamente.");
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("Error al eliminar orden: " . $e->getMessage());

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
