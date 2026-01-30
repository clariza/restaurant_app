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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


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
        // 🔥 CRÍTICO: Log de entrada para debugging
        Log::info('📥 REQUEST RECIBIDO EN STORE:', [
            'converting_from_proforma' => $request->input('converting_from_proforma'),
            'customer_name' => $request->input('customer_name'),
            'order_type' => $request->input('order_type'),
            'all_data' => $request->all()
        ]);

        // Validar autenticación
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado.',
            ], 401);
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
            'converting_from_proforma' => 'nullable|integer|exists:proformas,id', // ✅ Validación
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
            $tax = $subtotal * 0;
            $total = $subtotal + $tax;

            // Generar número de pedido
            $orderNumber = Sale::generateOrderNumber();

            // 🔥 CREAR LA VENTA CON PROFORMA_ID SI CORRESPONDE
            $saleData = [
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
                'proforma_id' => $sale->proforma_id ?? 'no asignado'
            ]);

            // ✅ Procesar items y stock
            foreach ($order as $item) {
                $menuItem = MenuItem::where('name', $item['name'])->first();

                if (!$menuItem) {
                    throw new \Exception("El ítem '{$item['name']}' no existe en el menú.");
                }

                // Verificar stock disponible
                if ($menuItem->manage_inventory && $menuItem->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para {$menuItem->name}. Disponible: {$menuItem->stock}, Requerido: {$item['quantity']}");
                }

                $sale->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Crear movimiento de inventario
                InventoryMovement::create([
                    'menu_item_id' => $menuItem->id,
                    'user_id' => Auth::id(),
                    'movement_type' => 'subtraction',
                    'quantity' => $item['quantity'],
                    'old_stock' => $menuItem->stock,
                    'new_stock' => $menuItem->stock - $item['quantity'],
                    'notes' => "Venta #{$orderNumber}" . ($convertingFromProforma ? " (Proforma #{$convertingFromProforma})" : '')
                ]);

                // Actualizar stock
                if ($menuItem->manage_inventory) {
                    $menuItem->decrement('stock', $item['quantity']);
                    Log::info("Stock actualizado para {$menuItem->name}: -{$item['quantity']}");
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
                    // Solo registrar el error
                }
            } else {
                Log::info('ℹ️ No se está convirtiendo desde proforma');
            }

            // ✅ COMMIT FINAL
            DB::commit();

            Log::info('✅ TRANSACCIÓN COMPLETADA', [
                'sale_id' => $sale->id,
                'daily_order_number' => $sale->daily_order_number,
                'proforma_converted' => $proformaConverted
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $sale->id,
                'order_number' => $sale->transaction_number,
                'daily_order_number' => $sale->daily_order_number,
                'message' => 'Pedido procesado correctamente.',
                'converted_from_proforma' => $proformaConverted // ✅ Informar si se convirtió
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
    // En SalesController.php


    /**
     * Obtiene el siguiente número de pedido del día
     */
    /**
     * Obtiene el siguiente número de pedido del día
     */
    /**
     * Obtiene el siguiente número de pedido del día con formato PED-00001
     */
    public function getNextOrderNumber()
    {
        try {
            $today = Carbon::today();

            // Buscar el último pedido del día actual
            $lastSale = Sale::whereDate('order_date', $today)
                ->whereNotNull('daily_order_number')
                ->orderBy('daily_order_number', 'desc')
                ->first();

            // Extraer el número del último pedido
            if ($lastSale && $lastSale->daily_order_number) {
                // Extraer solo los dígitos del formato "PED-00001"
                if (preg_match('/PED-(\d+)/', $lastSale->daily_order_number, $matches)) {
                    $lastNumber = (int) $matches[1];
                    $nextNumber = $lastNumber + 1;
                } else {
                    // Si no tiene el formato esperado, empezar en 1
                    $nextNumber = 1;
                }
            } else {
                // Si no hay pedidos hoy, empezar en 1
                $nextNumber = 1;
            }

            // Formatear con prefijo y ceros a la izquierda (5 dígitos)
            $formattedOrderNumber = 'PED-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'next_order_number' => $formattedOrderNumber,
                'next_number_raw' => $nextNumber,
                'date' => $today->toDateString()
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
