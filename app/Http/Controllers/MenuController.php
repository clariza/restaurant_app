<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Category;
use App\Models\Proforma;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Setting;

class MenuController extends Controller
{
    public function index()
    {

        // Obtener las últimas 5 órdenes normales con sus items y usuario
        $orders = Sale::with(['items', 'user'])
            ->latest()
            ->take(4)
            ->get();

        // Obtener las últimas 5 proformas con sus items y usuario
        $proformas = Proforma::with(['items', 'user'])
            ->latest()
            ->take(4)
            ->get();

        // Combinar ambas colecciones y ordenar por fecha más reciente
        $allOrders = $orders->merge($proformas)
            ->sortByDesc('created_at')
            ->take(4);

        // Contar órdenes por tipo
        $counts = [
            'all' => $allOrders->count(),
            'dine_in' => $allOrders->where('order_type', 'Comer aquí')->count(),
            'take_away' => $allOrders->where('order_type', 'Para llevar')->count(),
            'pickup' => $allOrders->where('order_type', 'Recoger')->count(),
            'proforma' => $proformas->count() // Contar solo proformas
        ];

        // Obtener todas las categorías con sus elementos del menú
        $categories = Category::with('menuItems')->get();
        $tables = Table::all();

        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        $deliveryServices = DeliveryService::where('is_active', true)->get();
        $settings = Setting::firstOrCreate([]);
        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();
        //$setting->tables_enabled = $validated['tables_enabled'];
        return view('menu.index', [
            'orders' => $allOrders,
            'counts' => $counts,
            'categories' => $categories,
            'tables' => $tables,
            'hasOpenPettyCash' => $hasOpenPettyCash,
            'showOrderDetails' => true,
            'deliveryServices' => $deliveryServices,
            'settings' => $settings,
            'openPettyCash' => $openPettyCash
        ]);
    }
    public function available()
    {
        try {
            $tables = Table::all();
            return response()->json([
                'success' => true,
                'data' => $tables
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las mesas'
            ], 500);
        }
    }
}
