<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchMenuItemStock;
use App\Models\InventoryMovement;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // ─── Branch activo ────────────────────────────────────────────────────
    private function getActiveBranchId(): ?int
    {
        // Primero intentar desde la sesión (igual que SaleController)
        return session('branch_id')
            ?? Branch::where('is_main', true)->first()?->id
            ?? Branch::first()?->id;
    }

    // ─── ADD ──────────────────────────────────────────────────────────────
    public function add(Request $request)
    {
        $request->validate([
            'item_id'   => 'required|exists:menu_items,id',
            'quantity'  => 'required|integer|min:1',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $branchId = $request->branch_id ?? $this->getActiveBranchId();
        $item     = MenuItem::findOrFail($request->item_id);

        // Sin gestión de inventario → OK sin tocar BD
        if (!$item->manage_inventory) {
            return response()->json([
                'success'   => true,
                'new_stock' => null,
                'message'   => 'Ítem sin gestión de inventario',
            ]);
        }

        try {
            DB::beginTransaction();

            $branchStock = BranchMenuItemStock::firstOrCreate(
                ['branch_id' => $branchId, 'menu_item_id' => $item->id],
                ['stock' => $item->stock, 'min_stock' => $item->min_stock]
            );

            if ($branchStock->stock < $request->quantity) {
                DB::rollBack();
                return response()->json([
                    'success'   => false,
                    'message'   => "Stock insuficiente para {$item->name}. Disponible: {$branchStock->stock}",
                    'new_stock' => $branchStock->stock,
                ], 422);
            }

            $oldStock = $branchStock->stock;
            $newStock = $oldStock - $request->quantity;

            $branchStock->stock = $newStock;
            $branchStock->save();

            InventoryMovement::create([
                'menu_item_id'  => $item->id,
                'branch_id'     => $branchId,
                'user_id'       => auth()->id(),
                'movement_type' => 'subtraction',
                'quantity'      => $request->quantity,
                'old_stock'     => $oldStock,
                'new_stock'     => $newStock,
                'notes'         => 'Reserva de carrito — ' . auth()->user()->name,
            ]);

            DB::commit();

            Log::info("🛒 Carrito: +{$request->quantity} {$item->name} reservado (branch: {$branchId})");

            return response()->json([
                'success'   => true,
                'new_stock' => $newStock,
                'min_stock' => $branchStock->min_stock,
                'message'   => 'Stock reservado correctamente',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CartController@add: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al reservar stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── REMOVE ───────────────────────────────────────────────────────────
    public function remove(Request $request)
    {
        $request->validate([
            'item_id'   => 'required|exists:menu_items,id',
            'quantity'  => 'required|integer|min:1',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $branchId = $request->branch_id ?? $this->getActiveBranchId();
        $item     = MenuItem::findOrFail($request->item_id);

        if (!$item->manage_inventory) {
            return response()->json([
                'success'   => true,
                'new_stock' => null,
                'message'   => 'Ítem sin gestión de inventario',
            ]);
        }

        try {
            DB::beginTransaction();

            $branchStock = BranchMenuItemStock::firstOrCreate(
                ['branch_id' => $branchId, 'menu_item_id' => $item->id],
                ['stock' => $item->stock, 'min_stock' => $item->min_stock]
            );

            $oldStock = $branchStock->stock;
            $newStock = $oldStock + $request->quantity;

            $branchStock->stock = $newStock;
            $branchStock->save();

            InventoryMovement::create([
                'menu_item_id'  => $item->id,
                'branch_id'     => $branchId,
                'user_id'       => auth()->id(),
                'movement_type' => 'addition',
                'quantity'      => $request->quantity,
                'old_stock'     => $oldStock,
                'new_stock'     => $newStock,
                'notes'         => 'Devolución de carrito — ' . auth()->user()->name,
            ]);

            DB::commit();

            Log::info("🛒 Carrito: -{$request->quantity} {$item->name} devuelto (branch: {$branchId})");

            return response()->json([
                'success'   => true,
                'new_stock' => $newStock,
                'min_stock' => $branchStock->min_stock,
                'message'   => 'Stock restaurado correctamente',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CartController@remove: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── CLEAR ────────────────────────────────────────────────────────────
    public function clear(Request $request)
    {
        $request->validate([
            'items'             => 'required|array',
            'items.*.item_id'  => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'branch_id'        => 'nullable|exists:branches,id',
        ]);

        $branchId = $request->branch_id ?? $this->getActiveBranchId();

        try {
            DB::beginTransaction();

            foreach ($request->items as $cartItem) {
                $item = MenuItem::find($cartItem['item_id']);

                if (!$item || !$item->manage_inventory) continue;

                $branchStock = BranchMenuItemStock::firstOrCreate(
                    ['branch_id' => $branchId, 'menu_item_id' => $item->id],
                    ['stock' => $item->stock, 'min_stock' => $item->min_stock]
                );

                $oldStock = $branchStock->stock;
                $newStock = $oldStock + $cartItem['quantity'];

                $branchStock->stock = $newStock;
                $branchStock->save();

                InventoryMovement::create([
                    'menu_item_id'  => $item->id,
                    'branch_id'     => $branchId,
                    'user_id'       => auth()->id(),
                    'movement_type' => 'addition',
                    'quantity'      => $cartItem['quantity'],
                    'old_stock'     => $oldStock,
                    'new_stock'     => $newStock,
                    'notes'         => 'Limpieza de carrito — ' . auth()->user()->name,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Carrito limpiado y stock restaurado',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CartController@clear: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar carrito: ' . $e->getMessage(),
            ], 500);
        }
    }
}
