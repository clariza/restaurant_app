<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\MenuItem;
use App\Models\Proforma;
use App\Models\InventoryMovement;
use App\Models\Table;
use App\Models\DeliveryService;
use App\Models\PettyCash;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SaleController extends Controller
{
    /**
     * Muestra el dashboard con los reportes estadísticos.
     */
    public function dashboard()
    {
        // Obtener branch_id de la sesión
        $branchId = session('branch_id');

        // Query base
        $salesQuery = Sale::query();

        // Filtrar por sucursal si existe
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }

        // Obtener las ventas agrupadas por mes
        $salesByMonth = (clone $salesQuery)
            ->select(
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
            $labels[] = date('F Y', mktime(0, 0, 0, $sale->month, 1, $sale->year));
            $data[] = $sale->total_sales;
        }

        // Obtener las ventas agrupadas por tipo
        $salesByType = (clone $salesQuery)
            ->select(
                'order_type',
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy('order_type')
            ->get();

        // Formatear los datos para el gráfico de comparación de tipos de ventas
        $typeLabels = [];
        $typeData = [];

        foreach ($salesByType as $sale) {
            $typeLabels[] = $sale->order_type;
            $typeData[] = $sale->total_sales;
        }

        // Verificar caja chica abierta para esta sucursal y usuario
        $hasOpenPettyCashQuery = PettyCash::where('status', 'open')
            ->where('user_id', Auth::id());

        if ($branchId) {
            $hasOpenPettyCashQuery->where('branch_id', $branchId);
        }

        $hasOpenPettyCash = $hasOpenPettyCashQuery->exists();

        // Obtener información de la sucursal actual
        $currentBranch = $branchId ? Branch::find($branchId) : null;

        // Pasar los datos a la vista
        return view('admin.dashboard', compact(
            'labels',
            'data',
            'typeLabels',
            'typeData',
            'hasOpenPettyCash',
            'currentBranch'
        ));
    }

    /**
     * Muestra una lista de todas las ventas.
     */
    public function index(Request $request)
    {
        // Obtener branch_id de la sesión
        $branchId = session('branch_id');

        // Query base con relaciones
        $salesQuery = Sale::with('items', 'user', 'branch');

        // Filtrar por sucursal si existe
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }
        // REEMPLAZAR desde los filtros hasta el ->get()

        if ($request->filled('branch_filter') && Auth::user()->role === 'admin') {
            $salesQuery->where('branch_id', $request->branch_filter);
        }
        if ($request->filled('date_from')) {
            $salesQuery->whereDate('order_date', '>=', $request->date_from);
        }
    if ($request->filled('date_to')) {
        $salesQuery->whereDate('order_date', '<=', $request->date_to);
    }
    if ($request->filled('order_type')) {
        $salesQuery->where('order_type', $request->order_type); 
    }
if ($request->filled('payment_method')) {
    $salesQuery->where('payment_method', $request->payment_method);
}
if ($request->filled('search')) {
    $search = $request->search;
    $salesQuery->where(function ($q) use ($search) {
        $q->where('customer_name', 'like', "%{$search}%")
          ->orWhere('daily_order_number', 'like', "%{$search}%")
          ->orWhere('transaction_number', 'like', "%{$search}%");
    });
}

$sales = $salesQuery
    ->orderBy('order_date', 'desc')
    ->orderBy('id', 'desc')
    ->paginate(20)
    ->withQueryString();

        // Verificar caja chica abierta para esta sucursal y usuario
        $hasOpenPettyCashQuery = PettyCash::where('status', 'open')
            ->where('user_id', Auth::id());

        if ($branchId) {
            $hasOpenPettyCashQuery->where('branch_id', $branchId);
        }

        $hasOpenPettyCash = $hasOpenPettyCashQuery->exists();

        // Obtener todas las sucursales para el filtro (solo si el usuario es admin)
        $branches = collect();
        if (Auth::user()->role === 'admin') {
            $branches = Branch::where('is_active', true)
                ->orderBy('is_main', 'desc')
                ->orderBy('name')
                ->get();
        }

        // Información de la sucursal actual
        $currentBranch = $branchId ? Branch::find($branchId) : null;

        // Pasar las ventas a la vista
        return view('sales.index', compact(
            'sales',
            'hasOpenPettyCash',
            'branches',
            'currentBranch'
        ));
    }

    public function store(Request $request)
    {
        // 🔥 CRÍTICO: Log de entrada para debugging
        Log::info('📥 REQUEST RECIBIDO EN STORE:', [
            'converting_from_proforma' => $request->input('converting_from_proforma'),
            'customer_name' => $request->input('customer_name'),
            'order_type' => $request->input('order_type'),
            'branch_id_session' => session('branch_id'),
            'all_data' => $request->all()
        ]);

        // Validar autenticación
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado.',
            ], 401);
        }

        // Obtener branch_id de la sesión
        $branchId = session('branch_id');

        if (!$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'No se ha seleccionado una sucursal. Por favor, cierre sesión e inicie sesión nuevamente.',
            ], 400);
        }

        // Validación
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order' => 'required|string', // JSON string
            'order_type' => 'required|string',
            'table_number' => 'nullable|string',
            'transaction_number' => 'nullable|string',
            'payment_method' => 'required|string|in:QR,Efectivo,Tarjeta,Transferencia',
            'order_notes' => 'nullable|string|max:500',
            'delivery_service' => 'nullable|string|max:255',
            'pickup_notes' => 'nullable|string|max:500',
            'converting_from_proforma' => 'nullable|integer|exists:proformas,id',
        ]);

        if ($validator->fails()) {
            Log::error('❌ Validación fallida:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // ✅ OBTENER ID DE PROFORMA AL INICIO
            $convertingFromProforma = $request->input('converting_from_proforma');

            Log::info('🔍 DEBUGGING PROFORMA:', [
                'converting_from_proforma_raw' => $request->input('converting_from_proforma'),
                'converting_from_proforma_parsed' => $convertingFromProforma,
                'is_null' => is_null($convertingFromProforma),
                'is_numeric' => is_numeric($convertingFromProforma),
                'type' => gettype($convertingFromProforma),
                'value' => $convertingFromProforma
            ]);

            // 🔥 CRÍTICO: Validar y obtener proforma ANTES de crear la venta
            $proforma = null;
            if ($convertingFromProforma) {
                $proforma = Proforma::find($convertingFromProforma);

                if (!$proforma) {
                    Log::error('❌ PROFORMA NO ENCONTRADA', [
                        'proforma_id' => $convertingFromProforma
                    ]);
                    throw new \Exception("Proforma ID {$convertingFromProforma} no encontrada");
                }

                Log::info('📋 Proforma encontrada:', [
                    'id' => $proforma->id,
                    'customer' => $proforma->customer_name,
                    'converted_to_order' => $proforma->converted_to_order ?? 'campo no existe',
                    'status' => $proforma->status
                ]);

                // Verificar que no esté ya convertida
                if (isset($proforma->converted_to_order) && $proforma->converted_to_order) {
                    Log::warning('⚠️ Proforma ya convertida anteriormente', [
                        'proforma_id' => $convertingFromProforma,
                        'converted_order_id' => $proforma->converted_order_id ?? 'no definido'
                    ]);
                    throw new \Exception("Esta proforma ya fue convertida anteriormente");
                }
            }

            // Verificar caja chica abierta para esta sucursal y usuario
            $openPettyCash = PettyCash::where('status', 'open')
                ->where('user_id', Auth::id())
                ->where('branch_id', $branchId)
                ->latest()
                ->first();

            if (!$openPettyCash) {
                throw new \Exception('No hay una caja chica abierta para esta sucursal.');
            }

            // Procesar el pedido
            $order = json_decode($request->order, true);
            if (!is_array($order)) {
                throw new \Exception('El pedido no es un array válido.');
            }

            // Calcular totales
            $subtotal = array_reduce($order, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
            $tax = $subtotal * 0;
            $total = $subtotal + $tax;

            // Generar número de pedido
            $orderNumber = Sale::generateOrderNumber();

            // 🔥 CREAR LA VENTA CON BRANCH_ID Y PROFORMA_ID SI CORRESPONDE
            $saleData = [
                'user_id' => Auth::id(),
                'petty_cash_id' => $openPettyCash->id,
                'branch_id' => $branchId, // ✅ AGREGAR BRANCH_ID
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
            ];

            // ✅ AGREGAR proforma_id SI EXISTE LA COLUMNA Y HAY CONVERSIÓN
            if ($convertingFromProforma && Schema::hasColumn('sales', 'proforma_id')) {
                $saleData['proforma_id'] = $convertingFromProforma;
                Log::info('✅ Agregando proforma_id a la venta', ['proforma_id' => $convertingFromProforma]);
            }

            $sale = Sale::create($saleData);

            Log::info('✅ Sale creada:', [
                'sale_id' => $sale->id,
                'transaction_number' => $sale->transaction_number,
                'branch_id' => $sale->branch_id,
                'proforma_id' => $sale->proforma_id ?? 'no asignado'
            ]);

        
            // Busca este foreach en tu código actual:

foreach ($order as $item) {
    $menuItem = MenuItem::where('name', $item['name'])->first();

    if (!$menuItem) {
        throw new \Exception("El ítem '{$item['name']}' no existe en el menú.");
    }

    // 🔥 SOLO VERIFICAR STOCK SI NO VIENE DE PROFORMA
    if (!$convertingFromProforma) {
        if ($menuItem->manage_inventory && $menuItem->stock < $item['quantity']) {
            throw new \Exception("Stock insuficiente para {$menuItem->name}. Disponible: {$menuItem->stock}, Requerido: {$item['quantity']}");
        }
    }

    $sale->items()->create([
        'menu_item_id' => $menuItem->id,
        'name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'total' => $item['price'] * $item['quantity'],
    ]);

    // 🔥 SOLO DESCONTAR STOCK SI NO VIENE DE PROFORMA
    if (!$convertingFromProforma) {
        InventoryMovement::create([
            'menu_item_id' => $menuItem->id,
            'user_id' => Auth::id(),
            'movement_type' => 'subtraction',
            'quantity' => $item['quantity'],
            'old_stock' => $menuItem->stock,
            'new_stock' => $menuItem->stock - $item['quantity'],
            'notes' => "Venta #{$orderNumber} - Sucursal: " . session('branch_name', 'N/A')
        ]);

        if ($menuItem->manage_inventory) {
            $menuItem->decrement('stock', $item['quantity']);
            Log::info("Stock actualizado para {$menuItem->name}: -{$item['quantity']}");
        }
    } else {
        Log::info("✅ Venta de proforma - Stock ya descontado", [
            'menu_item' => $menuItem->name,
            'quantity' => $item['quantity'],
            'proforma_id' => $convertingFromProforma
        ]);
    }
}

            // ✅ MARCAR PROFORMA COMO CONVERTIDA
            $proformaConverted = false;

            if ($proforma) {
                Log::info('🔄 MARCANDO PROFORMA COMO CONVERTIDA', [
                    'proforma_id' => $proforma->id,
                    'sale_id' => $sale->id
                ]);

                try {
                    // Verificar qué campos existen en la proforma
                    $fillableFields = $proforma->getFillable();
                    Log::info('📋 Campos fillable de Proforma:', ['fields' => $fillableFields]);

                    // Preparar datos para actualizar
                    $updateData = ['status' => 'completed'];

                    // ✅ Usar los campos correctos según tu modelo
                    if (in_array('converted_to_order', $fillableFields)) {
                        $updateData['converted_to_order'] = true;
                    }
                    if (in_array('is_converted', $fillableFields)) {
                        $updateData['is_converted'] = true;
                    }
                    if (in_array('converted_order_id', $fillableFields)) {
                        $updateData['converted_order_id'] = $sale->id;
                    }
                    if (in_array('converted_at', $fillableFields)) {
                        $updateData['converted_at'] = now();
                    }

                    Log::info('📝 Datos a actualizar en proforma:', $updateData);

                    // Actualizar la proforma
                    $updateResult = $proforma->update($updateData);

                    // Recargar para verificar
                    $proforma->refresh();

                    Log::info('🔍 DESPUÉS DEL UPDATE:', [
                        'update_result' => $updateResult,
                        'proforma_id' => $proforma->id,
                        'status' => $proforma->status,
                        'converted_to_order' => $proforma->converted_to_order ?? 'campo no existe',
                        'is_converted' => $proforma->is_converted ?? 'campo no existe',
                        'converted_order_id' => $proforma->converted_order_id ?? 'campo no existe',
                    ]);

                    if ($updateResult) {
                        $proformaConverted = true;
                        Log::info('✅ PROFORMA CONVERTIDA EXITOSAMENTE', [
                            'proforma_id' => $proforma->id,
                            'sale_id' => $sale->id
                        ]);
                    } else {
                        Log::error('❌ UPDATE RETORNÓ FALSE', [
                            'proforma_id' => $proforma->id
                        ]);
                    }
                } catch (\Exception $updateError) {
                    Log::error('❌ ERROR AL ACTUALIZAR PROFORMA:', [
                        'error' => $updateError->getMessage(),
                        'trace' => $updateError->getTraceAsString()
                    ]);
                    // No lanzar excepción para no revertir la venta
                }
            } else {
                Log::info('ℹ️ No se está convirtiendo desde proforma');
            }

            // ✅ COMMIT FINAL
            DB::commit();

            Log::info('✅ TRANSACCIÓN COMPLETADA', [
                'sale_id' => $sale->id,
                'daily_order_number' => $sale->daily_order_number,
                'branch_id' => $sale->branch_id,
                'branch_name' => session('branch_name'),
                'proforma_converted' => $proformaConverted
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $sale->id,
                'order_number' => $sale->transaction_number,
                'daily_order_number' => $sale->daily_order_number,
                'branch_name' => session('branch_name'),
                'message' => 'Pedido procesado correctamente.',
                'converted_from_proforma' => $proformaConverted
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ ERROR EN STORE:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al procesar el pedido.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        // Obtener branch_id de la sesión
        $branchId = session('branch_id');

        // Obtener la venta actual con sus relaciones
        $saleQuery = Sale::with(['items', 'user', 'branch']);

        // Filtrar por sucursal si existe
        if ($branchId) {
            $saleQuery->where('branch_id', $branchId);
        }

        $sale = $saleQuery->findOrFail($id);

        // Verificar si hay caja abierta para esta sucursal
        $hasOpenPettyCashQuery = PettyCash::where('status', 'open')
            ->where('user_id', Auth::id());

        if ($branchId) {
            $hasOpenPettyCashQuery->where('branch_id', $branchId);
        }

        $hasOpenPettyCash = $hasOpenPettyCashQuery->exists();

        // Obtener la venta anterior (misma sucursal)
        $previousSaleQuery = Sale::where('created_at', '<', $sale->created_at);
        if ($branchId) {
            $previousSaleQuery->where('branch_id', $branchId);
        }
        $previousSale = $previousSaleQuery->orderBy('created_at', 'desc')->first();

        if (!$previousSale) {
            $previousSaleQuery = Sale::where('created_at', '=', $sale->created_at)
                ->where('id', '<', $sale->id);
            if ($branchId) {
                $previousSaleQuery->where('branch_id', $branchId);
            }
            $previousSale = $previousSaleQuery->orderBy('id', 'desc')->first();
        }

        // Obtener la venta siguiente (misma sucursal)
        $nextSaleQuery = Sale::where('created_at', '>', $sale->created_at);
        if ($branchId) {
            $nextSaleQuery->where('branch_id', $branchId);
        }
        $nextSale = $nextSaleQuery->orderBy('created_at', 'asc')->first();

        if (!$nextSale) {
            $nextSaleQuery = Sale::where('created_at', '=', $sale->created_at)
                ->where('id', '>', $sale->id);
            if ($branchId) {
                $nextSaleQuery->where('branch_id', $branchId);
            }
            $nextSale = $nextSaleQuery->orderBy('id', 'asc')->first();
        }

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

    /**
     * Obtiene el siguiente número de pedido del día con formato PED-00001
     */
    public function getNextOrderNumber()
    {
        try {
            $today = Carbon::today();
            $branchId = session('branch_id');

            // Buscar el último pedido del día actual para esta sucursal
            $lastSaleQuery = Sale::whereDate('order_date', $today)
                ->whereNotNull('daily_order_number');

            if ($branchId) {
                $lastSaleQuery->where('branch_id', $branchId);
            }

            $lastSale = $lastSaleQuery->orderBy('daily_order_number', 'desc')->first();

            // Extraer el número del último pedido
            if ($lastSale && $lastSale->daily_order_number) {
                if (preg_match('/PED-(\d+)/', $lastSale->daily_order_number, $matches)) {
                    $lastNumber = (int) $matches[1];
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }
            } else {
                $nextNumber = 1;
            }

            // Formatear con prefijo y ceros a la izquierda (5 dígitos)
            $formattedOrderNumber = 'PED-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'next_order_number' => $formattedOrderNumber,
                'next_number_raw' => $nextNumber,
                'date' => $today->toDateString(),
                'branch_id' => $branchId,
                'branch_name' => session('branch_name')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el número de pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
