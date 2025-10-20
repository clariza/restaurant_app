<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Proforma;
use Illuminate\Http\Request;
use App\Models\PettyCash;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $type = $request->get('type', 'all');
        $status = $request->get('status', 'all');
        $search = $request->get('search');

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

        // Aplicar filtro de estado (si tienes campo status)
        // if ($status !== 'all' && \Schema::hasColumn('sales', 'status')) {
        //     $ordersQuery->where('status', $status);
        // }

        // Aplicar búsqueda
        if ($search) {
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('transaction_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });

            $proformasQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Verificar caja abierta
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Obtener resultados paginados
        $orders = $ordersQuery->paginate(15)->appends($request->all());
        $proformas = $proformasQuery->paginate(15)->appends($request->all());

        return view('orders.index', compact('orders', 'proformas', 'hasOpenPettyCash'));
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
}
