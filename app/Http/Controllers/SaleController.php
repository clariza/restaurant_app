<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\MenuItem;
use App\Models\InventoryMovement;
use App\Models\Table;
use App\Models\DeliveryService;
use App\Models\PettyCash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    /**
     * Muestra el dashboard con los reportes estadísticos.
     */
    public function dashboard()
    {
        // Obtener las ventas agrupadas por mes
        $salesByMonth = Sale::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total) as total_sales')
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Formatear los datos para el gráfico de ventas por período
        $labels = [];
        $data = [];

        foreach ($salesByMonth as $sale) {
            $labels[] = date('F Y', mktime(0, 0, 0, $sale->month, 1, $sale->year)); // Formato: "Mes Año"
            $data[] = $sale->total_sales;
        }

        // Obtener las ventas agrupadas por tipo
        $salesByType = Sale::select(
            'order_type',
            DB::raw('SUM(total) as total_sales')
        )
            ->groupBy('order_type')
            ->get();

        // Formatear los datos para el gráfico de comparación de tipos de ventas
        $typeLabels = [];
        $typeData = [];

        foreach ($salesByType as $sale) {
            $typeLabels[] = $sale->order_type; // Tipos de venta: "Para llevar", "Para comer aquí", "Recoger"
            $typeData[] = $sale->total_sales;
        }
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Pasar los datos a la vista
        return view('admin.dashboard', compact('labels', 'data', 'typeLabels', 'typeData', 'hasOpenPettyCash'));
    }
    /**
     * Muestra una lista de todas las ventas.
     */
    public function index()
    {
        // Obtener todas las ventas con sus ítems y el usuario vendedor
        // Ordenadas por fecha de creación de forma ASCENDENTE (más antiguas primero)
        $sales = Sale::with('items', 'user')
            ->orderBy('created_at', 'asc')  // Orden ascendente por fecha
            ->orderBy('id', 'asc')           // Desempate por ID si tienen la misma fecha
            ->get();

        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Pasar las ventas a la vista
        return view('sales.index', compact('sales', 'hasOpenPettyCash'));
    }
    public function store(Request $request)
    {
        // Validar autenticación
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado.',
            ], 401);
        }

        // Validación mejorada
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order' => 'required|json',
            'order_type' => 'required|string',
            'table_number' => 'nullable|string',
            'transaction_number' => 'nullable|string',
            'payment_method' => 'required|string|in:QR,Efectivo,Tarjeta',
            'order_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Verificar caja chica abierta
            $openPettyCash = PettyCash::where('status', 'open')->latest()->first();
            if (!$openPettyCash) {
                throw new \Exception('No hay una caja chica abierta.');
            }

            // Procesar el pedido
            $order = json_decode($request->order, true);
            if (!is_array($order)) {
                throw new \Exception('El pedido no es un array válido.');
            }

            // Calcular totales
            $subtotal = array_reduce($order, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
            $taxRate = 0;
            $tax = $subtotal * 0; // 0% de impuesto
            $total = $subtotal + $tax;

            // Generar número de pedido
            $orderNumber = Sale::generateOrderNumber();
            $today = now()->toDateString();
            // Crear la venta
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'petty_cash_id' => $openPettyCash->id,
                'customer_name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
                'order_type' => $request->order_type,
                'table_number' => $request->table_number,
                'subtotal' => $subtotal,
                'discount' => 0,
                'service_charge' => 0,
                'tax' => $tax,
                'total' => $total,
                'transaction_number' => $request->transaction_number,
                'payment_method' => $request->payment_method,
                'order_notes' => $request->order_notes,
                'daily_order_number' => $orderNumber,
                'order_date' => now()->toDateString(),
            ]);

            foreach ($order as $item) {
                // Buscar el MenuItem por nombre (asegúrate de que el nombre coincida exactamente)
                $menuItem = MenuItem::where('name', $item['name'])->first();

                if (!$menuItem) {
                    throw new \Exception("El ítem '{$item['name']}' no existe en el menú.");
                }

                // Crear el SaleItem CON menu_item_id
                $saleItem = $sale->items()->create([
                    'menu_item_id' => $menuItem->id, // Campo crítico
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Resto de la lógica (movimiento de inventario, etc.)
                InventoryMovement::create([
                    'menu_item_id' => $menuItem->id,
                    'user_id' => Auth::id(),
                    'movement_type' => 'subtraction',
                    'quantity' => $item['quantity'],
                    'old_stock' => $menuItem->stock,
                    'new_stock' => $menuItem->stock - $item['quantity'],
                    'notes' => "Venta #{$orderNumber}"
                ]);

                $menuItem->stock -= $item['quantity'];
                $menuItem->save();
            }

            // DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido procesado correctamente.',
                'sale_id' => $sale->id,
                'daily_order_number' => $sale->daily_order_number,
            ]);
        } catch (\Exception $e) {
            // DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al procesar el pedido.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        // Obtener la venta actual con sus relaciones
        $sale = Sale::with(['items', 'user'])->findOrFail($id);

        // Verificar si hay caja abierta
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Obtener la venta anterior (fecha anterior más cercana)
        $previousSale = Sale::where('created_at', '<', $sale->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        // Si hay ventas con la misma fecha/hora, usar ID como desempate
        if (!$previousSale) {
            $previousSale = Sale::where('created_at', '=', $sale->created_at)
                ->where('id', '<', $sale->id)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Obtener la venta siguiente (fecha posterior más cercana)
        $nextSale = Sale::where('created_at', '>', $sale->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        // Si hay ventas con la misma fecha/hora, usar ID como desempate
        if (!$nextSale) {
            $nextSale = Sale::where('created_at', '=', $sale->created_at)
                ->where('id', '>', $sale->id)
                ->orderBy('id', 'asc')
                ->first();
        }

        // Retornar la vista con todas las variables
        return view('sales.show', compact('sale', 'previousSale', 'nextSale', 'hasOpenPettyCash'));
    }
    public function create()
    {
        $tables = Table::all();
        $deliveryServices = DeliveryService::where('is_active', true)->get();
        if ($tables->isEmpty()) {
            dd('No hay mesas en la base de datos');
        } else {
            dd($tables);
        }
        return view('layouts.order-details', compact('tables', 'deliveryServices'));
    }

    // En SaleController
    public function checkStock(Request $request)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $menuItem = MenuItem::find($item['name']);
            if (!$menuItem || $menuItem->stock < $item['quantity']) {
                return response()->json([
                    'available' => false,
                    'itemName' => $menuItem ? $menuItem->name : 'Ítem no encontrado'
                ]);
            }
        }

        return response()->json(['available' => true]);
    }
}
