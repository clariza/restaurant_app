<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // Solo cargar productos con gestión de inventario habilitada
        $items = MenuItem::where('manage_inventory', true)
            ->with('category')
            ->orderBy('name')
            ->get();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('inventory.index', compact('items', 'hasOpenPettyCash'));
    }

    public function updateStock(Request $request)
    {
        Log::info('updateStock llamado', ['request' => $request->all()]);
        // Validación de entrada
        $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'movement_type' => 'required|in:addition,subtraction',
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $item = MenuItem::findOrFail($request->item_id);

            // Verificar que el producto tenga gestión de inventario habilitada
            if (!$item->manage_inventory) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Este producto no tiene la gestión de inventario habilitada.']);
            }

            // Obtener usuario autenticado - CORRECCIÓN PRINCIPAL
            $user = auth()->user();
            
            // Verificar que hay un usuario autenticado
            if (!$user) {
                DB::rollBack();
                Log::error('No hay usuario autenticado al intentar actualizar stock');
                return back()->withErrors(['error' => 'Debe estar autenticado para realizar esta acción.']);
            }

            $oldStock = $item->stock;

            // Validar stock suficiente para sustracciones
            if ($request->movement_type === 'subtraction' && $request->quantity > $item->stock) {
                DB::rollBack();
                Log::warning('Validación fallida: stock insuficiente', [
                    'item_id' => $item->id,
                    'stock_actual' => $item->stock,
                    'cantidad_solicitada' => $request->quantity
                ]);
                return back()->withErrors([
                    'quantity' => 'No hay suficiente stock disponible. Stock actual: ' . $item->stock
                ]);
            }

            // Calcular nuevo stock
            $newStock = $request->movement_type === 'addition' 
                ? $item->stock + $request->quantity
                : $item->stock - $request->quantity;

            // Actualizar el item
            $item->stock = $newStock;
            $item->save();

            // Registrar el movimiento
            $movement = InventoryMovement::create([
                'menu_item_id' => $item->id,
                'user_id' => $user->id,
                'movement_type' => $request->movement_type,
                'quantity' => $request->quantity,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'notes' => $request->notes
            ]);

            DB::commit();

            Log::info('Stock actualizado correctamente', [
                'item_id' => $item->id,
                'movement_id' => $movement->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'user_id' => $user->id
            ]);

            return back()->with('success', 'Stock actualizado correctamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en updateStock', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Error al actualizar el stock: ' . $e->getMessage()]);
        }
    }

    public function getMovements($itemId)
    {
        $item = MenuItem::findOrFail($itemId);
        
        // Verificar que el producto tenga gestión de inventario habilitada
        if (!$item->manage_inventory) {
            return response()->json(['error' => 'Producto sin gestión de inventario'], 403);
        }

        $movements = $item->inventoryMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($movements);
    }

    public function movements()
    {
        // Solo mostrar movimientos de productos con gestión de inventario
        $movements = InventoryMovement::whereHas('menuItem', function($query) {
            $query->where('manage_inventory', true);
        })
        ->with(['menuItem.category', 'user'])
        ->orderBy('created_at', 'desc')
        ->paginate(50);
            
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        return view('inventory.movements', compact('movements', 'hasOpenPettyCash'));
    }

    public function itemMovements($id)
    {
        $movements = InventoryMovement::where('menu_item_id', $id)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();
            
        return response()->json($movements);
    }

    public function lowStock()
    {
        // Solo productos con gestión de inventario y bajo stock
        $items = MenuItem::where('manage_inventory', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('inventory.low-stock', compact('items'));
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Solo reportes de productos con gestión de inventario
        $movements = InventoryMovement::whereHas('menuItem', function($query) {
            $query->where('manage_inventory', true);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->with(['menuItem.category', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

        $summary = [
            'total_additions' => $movements->where('movement_type', 'addition')->sum('quantity'),
            'total_subtractions' => $movements->where('movement_type', 'subtraction')->sum('quantity'),
            'total_movements' => $movements->count(),
            'low_stock_items' => MenuItem::where('manage_inventory', true)
                ->whereRaw('stock < min_stock')
                ->count()
        ];

        return view('inventory.report', compact('movements', 'summary', 'startDate', 'endDate'));
    }
}