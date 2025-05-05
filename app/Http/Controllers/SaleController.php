<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Table;
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
    return view('admin.dashboard', compact('labels', 'data', 'typeLabels', 'typeData','hasOpenPettyCash'));
}
      /**
     * Muestra una lista de todas las ventas.
     */
    public function index()
    {
         // Obtener todas las ventas con sus ítems y el usuario vendedor
    $sales = Sale::with('items', 'user')->orderBy('created_at', 'desc')->get();
    $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        // Pasar las ventas a la vista
        return view('sales.index', compact('sales','hasOpenPettyCash'));
    }
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado.',
            ], 401);
        }
    
        // Validar los datos del formulario (agregar order_notes)
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order' => 'required|json',
            'order_type' => 'required|string',
            'table_number' => 'nullable|string',
            'transaction_number' => 'nullable|string',
            'payment_method' => 'required|string|in:QR,Efectivo,Tarjeta',
            'order_notes' => 'nullable|string|max:500', // Validación para order_notes
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            $openPettyCash = PettyCash::where('status', 'open')->latest()->first();
    
            if (!$openPettyCash) {
                throw new \Exception('No hay una caja chica abierta.');
            }
    
            $order = json_decode($request->order, true);
    
            if (!is_array($order)) {
                throw new \Exception('El pedido no es un array válido.');
            }
    
            $subtotal = array_reduce($order, function ($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);
    
            $taxRate = 0;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;
    
            // Crear la venta incluyendo order_notes
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'petty_cash_id' => $openPettyCash->id,
                'customer_name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'order_type' => $request->order_type,
                'table_number' => $request->table_number,
                'subtotal' => $subtotal,
                'discount' => 0,
                'service_charge' => 0,
                'tax' => $tax,
                'total' => $total,
                'transaction_number' => $request->transaction_number,
                'payment_method' => $request->payment_method,
                'order_notes' => $request->order_notes, // Incluir las notas del pedido
            ]);
    
            foreach ($order as $item) {
                $sale->items()->create([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Pedido procesado correctamente.',
                'sale_id' => $sale->id,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al procesar el pedido.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Sale $sale)
    {
    // Cargar la venta con sus ítems
        $sale->load('items','user');
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

    // Pasar la venta a la vista
        return view('sales.show', compact('sale','hasOpenPettyCash'));
    }

    public function create()
{
    $tables = Table::all();
    $deliveryServices = DeliveryService::where('is_active', true)->get();
    if ($tables->isEmpty()) {
        dd('No hay mesas en la base de datos');
    } else {
        dd($tables); // Verifica si $tables contiene datos
    }
    return view('layouts.order-details', compact('tables', 'deliveryServices'));
}
}   
    


