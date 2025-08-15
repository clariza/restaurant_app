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
        // Obtener parámetros de filtrado
        $type = $request->input('type', 'all');
        $status = $request->input('status', 'all');
        $search = $request->input('search');
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        // Consulta para órdenes de venta
        $salesQuery = Sale::with(['items', 'user'])
            ->when($type !== 'all', function($query) use ($type) {
                $query->where('order_type', $type);
            })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            });
            
        // Consulta para proformas
        $proformasQuery = Proforma::with(['items', 'user'])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            });
            
        // Si no es "all", aplicar filtro de tipo
        if ($type !== 'all' && $type !== 'proforma') {
            $proformasQuery->whereRaw('1 = 0'); // No mostrar proformas
        }
        
        // Obtener resultados combinados y paginados
        $orders = $salesQuery->orderBy('created_at', 'desc')->paginate(10);
        $proformas = ($type === 'all' || $type === 'proforma') 
        ? $proformasQuery->orderBy('created_at', 'desc')->paginate(10)
        : Proforma::whereRaw('1 = 0')->paginate(10);
            
        return view('orders.index', compact('orders', 'proformas', 'type', 'status', 'search','hasOpenPettyCash'));
    }

    public function show($id)
    {
    // Obtener la orden con sus relaciones
    $order = Sale::with(['items.menuItem', 'user'])
        ->findOrFail($id);

    // Verificar si hay caja abierta (si es necesario)
    $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

    // Pasar los datos a la vista
    return view('orders.show', compact('order', 'hasOpenPettyCash'));
    }
}