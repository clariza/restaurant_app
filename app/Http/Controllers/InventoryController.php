<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchMenuItemStock;
use App\Models\InventoryMovement;
use App\Models\MenuItem;
use App\Models\PettyCash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    // En el controlador index():
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id',
                    Branch::where('is_main', true)->first()?->id);

        $branches = Branch::where('is_active', true)->get();

        $items = MenuItem::where('manage_inventory', true)
            ->with(['category', 'branchStocks' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($item) use ($branchId) {
            // Adjuntar el stock de la sucursal como atributo virtual
            $item->branch_stock = $item->branchStocks->first()?->stock ?? 0;
            $item->branch_min_stock = $item->branchStocks->first()?->min_stock
                                      ?? $item->min_stock;
            return $item;
        });

        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        return view('inventory.index', compact(
        'items', 'branches', 'branchId', 'hasOpenPettyCash'
        ));
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'item_id'       => 'required|exists:menu_items,id',
            'branch_id'     => 'required|exists:branches,id',  // nuevo
            'quantity'      => 'required|numeric|min:0.01',
            'movement_type' => 'required|in:addition,subtraction',
            'notes'         => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $item = MenuItem::findOrFail($request->item_id);

        if (!$item->manage_inventory) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Producto sin gestión de inventario.']);
        }

        $user = auth()->user();
        if (!$user) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Debe estar autenticado.']);
        }

        // Obtener o crear el registro de stock para esa sucursal
        $branchStock = BranchMenuItemStock::firstOrCreate(
            ['branch_id' => $request->branch_id, 'menu_item_id' => $item->id],
            ['stock' => $item->stock, 'min_stock' => $item->min_stock]
        );

        // Validar stock suficiente
        if ($request->movement_type === 'subtraction'
            && $request->quantity > $branchStock->stock) {
            DB::rollBack();
            return back()->withErrors([
                'quantity' => 'Stock insuficiente en esta sucursal. Stock actual: '
                              . $branchStock->stock
            ]);
        }

        $oldStock = $branchStock->stock;

        $newStock = $request->movement_type === 'addition'
            ? $branchStock->stock + $request->quantity
            : $branchStock->stock - $request->quantity;

        $branchStock->stock = $newStock;
        $branchStock->save();

        InventoryMovement::create([
            'menu_item_id'  => $item->id,
            'branch_id'     => $request->branch_id,
            'user_id'       => $user->id,
            'movement_type' => $request->movement_type,
            'quantity'      => $request->quantity,
            'old_stock'     => $oldStock,
            'new_stock'     => $newStock,
            'notes'         => $request->notes,
        ]);

        DB::commit();

        return back()->with('success', 'Stock actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
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
        $movements = InventoryMovement::whereHas('menuItem', function ($query) {
            $query->where('manage_inventory', true);
        })
            ->with(['menuItem.category', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        return view('inventory.movements', compact('movements', 'hasOpenPettyCash'));
    }

    // Mantener solo itemMovements y eliminar getMovements, o viceversa.
    // El que uses debe incluir branch_id en la respuesta:

    public function itemMovements($id)
    {
        try {
            $item = MenuItem::find($id);

            if (!$item) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            if (!$item->manage_inventory) {
                return response()->json(['error' => 'Sin gestión de inventario'], 403);
            }

            $movements = InventoryMovement::where('menu_item_id', $id)
                ->with(['user', 'menuItem', 'branch']) // ← agregar branch
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json($movements);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar movimientos: ' . $e->getMessage()
            ], 500);
        }
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
        $movements = InventoryMovement::whereHas('menuItem', function ($query) {
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
    // Agregar esta ruta y método para que el menú pueda consultar stock actualizado
    public function getItemsStock(Request $request)
    {
        $branchId = $request->get(
            'branch_id',
            Branch::where('is_main', true)->first()?->id
        );

        $items = MenuItem::where('is_available', true)
            ->where('manage_inventory', true)
            ->with(['branchStocks' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->get()
            ->map(fn($item) => [
                'id'         => $item->id,
                'stock'      => $item->branch_stock,      
                'min_stock'  => $item->branch_min_stock,  
                'stock_type' => $item->branch_stock_type,
                'stock_unit' => $item->branch_stock_unit,
            ]);

        return response()->json(['success' => true, 'data' => $items]);
    }
}
