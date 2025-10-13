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
        $categorias = Category::all(); // Obtener todas las categorías
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('purchases.create', compact('suppliers', 'hasOpenPettyCash', 'categorias'));
    }

    public function store(Request $request)
    {
        // Validación de los datos básicos
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
            // Calcular el total de la compra
            $totalAmount = collect($request->products)->sum(function ($product) {
                $unitCostAfterDiscount = $product['unit_cost'] * (1 - ($product['discount'] / 100));
                return $product['quantity'] * $unitCostAfterDiscount;
            });

            // Crear la compra
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            // Guardar los productos de la compra en el stock
            foreach ($request->products as $productData) {
                $unitCostAfterDiscount = $productData['unit_cost'] * (1 - ($productData['discount'] / 100));
                $totalCost = $productData['quantity'] * $unitCostAfterDiscount;

                Stock::create([
                    'product_id' => $productData['product_id'],
                    'purchase_id' => $purchase->id,
                    'quantity' => $productData['quantity'],
                    'unit_cost' => $productData['unit_cost'],
                    'discount' => $productData['discount'] ?? 0,
                    'total_cost' => $totalCost,
                    'selling_price' => $productData['selling_price'],
                    'expiry_date' => $productData['expiry_date'] ?? null,
                ]);

                // Actualizar el stock del producto
                $menuItem = MenuItem::find($productData['product_id']);
                if ($menuItem) {
                    $menuItem->increment('stock', $productData['quantity']);
                }
            }

            return redirect()->route('purchases.index')
                ->with('success', 'Compra registrada exitosamente. ID: ' . $purchase->id);
        } catch (\Exception $e) {
            return redirect()->route('purchases.index')
                ->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }
    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('purchases.edit', compact('purchase', 'suppliers', 'hasOpenPettyCash'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $purchaseData = $request->all();
        // Mantenemos la fecha original de la compra al actualizar
        // Si quisieras actualizar la fecha también, usarías:
        // $purchaseData['purchase_date'] = Carbon::now();

        $purchase->update($purchaseData);
        return redirect()->route('purchases.index')->with('success', 'Compra actualizada exitosamente.');
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
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Compra eliminada exitosamente.');
    }

    public function checkOpen()
    {
        try {
            // Verificar si hay cajas chicas abiertas
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
