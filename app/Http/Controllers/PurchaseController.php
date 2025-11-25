<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use App\Models\MenuItem;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryMovement;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier');

        // Búsqueda por referencia o proveedor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro por proveedor
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por fecha
        if ($request->filled('date')) {
            $query->whereDate('purchase_date', $request->date);
        }

        // Ordenar por fecha más reciente
        $query->orderBy('purchase_date', 'desc');

        // Paginación
        $purchases = $query->paginate(15)->appends($request->except('page'));

        // Obtener todos los proveedores para el filtro
        $suppliers = Supplier::orderBy('name')->get();

        // Verificar si hay caja abierta
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Estadísticas opcionales
        $statistics = [
            'total_purchases' => Purchase::count(),
            'total_amount' => Purchase::sum('total_amount'),
            'pending' => Purchase::where('status', 'pending')->count(),
            'completed' => Purchase::where('status', 'completed')->count(),
        ];

        return view('purchases.index', compact(
            'purchases',
            'suppliers',
            'hasOpenPettyCash',
            'statistics'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $categorias = Category::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('purchases.create', compact('suppliers', 'hasOpenPettyCash', 'categorias'));
    }

    public function store(Request $request)
    {


        // Validación
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:100',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:menu_items,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.expiry_date' => 'nullable|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            // Crear la compra
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'reference_number' => $validated['reference_number'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => 0, // Lo calculamos después
                'user_id' => Auth::id(),
            ]);

            $totalAmount = 0;

            // Procesar cada producto
            foreach ($validated['products'] as $productData) {
                $product = MenuItem::findOrFail($productData['product_id']);

                // Calcular valores
                $quantity = floatval($productData['quantity']);
                $unitCost = floatval($productData['unit_cost']);
                $discount = floatval($productData['discount'] ?? 0);
                $sellingPrice = floatval($productData['selling_price']);

                // Costo unitario después del descuento
                $unitCostAfterDiscount = $unitCost * (1 - ($discount / 100));

                // Total de la línea
                $lineTotal = $quantity * $unitCostAfterDiscount;

                // Calcular margen de utilidad
                $profitMargin = $unitCostAfterDiscount > 0
                    ? (($sellingPrice - $unitCostAfterDiscount) / $unitCostAfterDiscount) * 100
                    : 0;

                // 1. CREAR REGISTRO EN LA TABLA STOCK
                $stock = Stock::create([
                    'product_id' => $product->id,
                    'purchase_id' => $purchase->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'discount' => $discount,
                    'total_cost' => $lineTotal,
                    'selling_price' => $sellingPrice,
                    'profit_margin' => $profitMargin,
                    'expiry_date' => $productData['expiry_date'] ?? null,
                ]);


                // 2. ACTUALIZAR EL STOCK DEL PRODUCTO (si tiene gestión de inventario)
                if ($product->manage_inventory) {
                    $oldStock = $product->stock;
                    $newStock = $oldStock + $quantity;

                    $product->stock = $newStock;
                    $product->save();

                    // 3. REGISTRAR MOVIMIENTO DE INVENTARIO
                    $movement = InventoryMovement::create([
                        'menu_item_id' => $product->id,
                        'user_id' => Auth::id(),
                        'movement_type' => 'addition', // Siempre es adición en compras
                        'quantity' => $quantity,
                        'old_stock' => $oldStock,
                        'new_stock' => $newStock,
                        'notes' => "Compra #" . $purchase->id .
                            ($purchase->reference_number ? " - Ref: {$purchase->reference_number}" : "")
                    ]);
                }

                // 4. ACTUALIZAR PRECIO DE VENTA DEL PRODUCTO (opcional)
                // Si quieres actualizar automáticamente el precio de venta del producto
                if ($sellingPrice > 0) {
                    $product->price = $sellingPrice;
                    $product->save();
                }

                $totalAmount += $lineTotal;
            }

            // Actualizar el total de la compra
            $purchase->total_amount = $totalAmount;
            $purchase->save();

            DB::commit();



            return response()->json([
                'success' => true,
                'message' => 'Compra registrada exitosamente',
                'redirect_url' => route('purchases.index'),
                'purchase_id' => $purchase->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();



            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function storeWeb(Request $request)
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:menu_items,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.expiry_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = $this->calculateTotalAmount($request->products);

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            $this->processPurchaseProducts($purchase, $request->products);

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Compra registrada exitosamente. ID: ' . $purchase->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    protected function storeJson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:menu_items,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $totalAmount = $this->calculateTotalAmount($request->products);

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            $this->processPurchaseProducts($purchase, $request->products);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada exitosamente',
                'purchase_id' => $purchase->id,
                'redirect_url' => route('purchases.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function calculateTotalAmount($products)
    {
        return collect($products)->sum(function ($product) {
            $discount = isset($product['discount']) ? floatval($product['discount']) : 0;
            $unitCostAfterDiscount = floatval($product['unit_cost']) * (1 - ($discount / 100));
            return intval($product['quantity']) * $unitCostAfterDiscount;
        });
    }

    protected function processPurchaseProducts($purchase, $products)
    {
        foreach ($products as $productData) {
            $discount = isset($productData['discount']) ? floatval($productData['discount']) : 0;
            $unitCost = floatval($productData['unit_cost']);
            $quantity = intval($productData['quantity']);
            $sellingPrice = floatval($productData['selling_price']);

            // Calcular costo unitario después del descuento
            $unitCostAfterDiscount = $unitCost * (1 - ($discount / 100));
            $totalCost = $quantity * $unitCostAfterDiscount;

            // Calcular margen de utilidad
            $profitMargin = 0;
            if ($unitCostAfterDiscount > 0) {
                $profitMargin = (($sellingPrice - $unitCostAfterDiscount) / $unitCostAfterDiscount) * 100;
            }

            // Crear registro en Stock
            Stock::create([
                'product_id' => $productData['product_id'],
                'purchase_id' => $purchase->id,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'discount' => $discount,
                'total_cost' => $totalCost,
                'selling_price' => $sellingPrice,
                'profit_margin' => $profitMargin, // Guardar margen de utilidad
                'expiry_date' => isset($productData['expiry_date']) ? $productData['expiry_date'] : null,
            ]);

            // Actualizar el stock del producto
            $menuItem = MenuItem::find($productData['product_id']);
            if ($menuItem) {
                $menuItem->increment('stock', $quantity);

                // Actualizar el precio de venta del producto si es diferente
                if ($menuItem->price != $sellingPrice) {
                    $menuItem->update(['price' => $sellingPrice]);
                }
            }
        }
    }

    public function edit(Purchase $purchase)
    {
        // Verificar que la compra esté en estado pendiente
        // if ($purchase->status !== 'pending') {
        //     return redirect()->route('purchases.show', $purchase->id)
        //         ->with('error', 'Solo se pueden editar compras en estado pendiente.');
        // }

        $suppliers = Supplier::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        // Cargar la compra con sus relaciones
        $purchase->load(['supplier', 'stocks.item.category']);

        return view('purchases.edit', compact('purchase', 'suppliers', 'hasOpenPettyCash'));
    }
    public function update(Request $request, Purchase $purchase)
    {
        // Validar que la compra esté en estado pendiente
        if ($purchase->status !== 'pending') {
            return redirect()->route('purchases.index')
                ->with('error', 'Solo se pueden editar compras en estado pendiente.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:menu_items,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.expiry_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Revertir el stock de los productos antiguos
            foreach ($purchase->stocks as $stock) {
                $menuItem = MenuItem::find($stock->product_id);
                if ($menuItem) {
                    $menuItem->decrement('stock', $stock->quantity);
                }
            }

            // Eliminar los stocks antiguos
            $purchase->stocks()->delete();

            // Calcular el nuevo total
            $totalAmount = $this->calculateTotalAmount($request->products);

            // Actualizar la compra
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
            ]);

            // Procesar los nuevos productos
            $this->processPurchaseProducts($purchase, $request->products);

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', 'Compra actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function searchProducts(Request $request)
    {
        try {
            $searchTerm = $request->input('search');

            $products = MenuItem::where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%')
                ->select('id', 'name', 'price', 'description', 'category_id')
                ->with('category')
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => (float)$product->price,
                        'description' => $product->description,
                        'category' => $product->category ? $product->category->name : 'Sin categoría'
                    ];
                });

            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductDetails($id)
    {
        $product = MenuItem::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function destroy(Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            // Revertir el stock de los productos
            foreach ($purchase->stocks as $stock) {
                $menuItem = MenuItem::find($stock->product_id);
                if ($menuItem) {
                    $menuItem->decrement('stock', $stock->quantity);
                }
            }

            // Eliminar los stocks asociados
            $purchase->stocks()->delete();

            // Eliminar la compra
            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Compra eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    public function checkOpen()
    {
        try {
            $hasOpen = PettyCash::where('status', 'open')->exists();

            return response()->json([
                'hasOpen' => $hasOpen
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'hasOpen' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Purchase $purchase)
    {
        // Cargar las relaciones necesarias
        $purchase->load(['supplier', 'stocks.item.category']);

        // Verificar si hay caja abierta
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        return view('purchases.show', compact('purchase', 'hasOpenPettyCash'));
    }
}
