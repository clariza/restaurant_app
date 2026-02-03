<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use Illuminate\Support\Facades\Storage;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Mostrar lista de productos
    public function index()
    {
        $productos = MenuItem::with('category')->get();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('items.index', compact('productos', 'hasOpenPettyCash'));
    }

    /**
     * Show the form for creating a new resource.
     */

    // Mostrar formulario para crear un nuevo producto
    public function create()
    {
        $categorias = Category::all();
        $branches = Branch::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('items.create', compact('categorias', 'branches', 'hasOpenPettyCash'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // Guardar un nuevo producto
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url|max:500',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            // Validaciones de inventario
            'stock' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'stock_type' => 'nullable|in:discrete,continuous',
            'stock_unit' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'name',
            'description',
            'price',
            'category_id',
            'branch_id',
            'stock',
            'min_stock',
            'stock_type',
            'stock_unit',
        ]);

        $imagePath = null;

        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('items', 'public');
        }
        // Si no hay archivo, usar URL
        elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $data['image'] = $imagePath;

        // Convertir manage_inventory a booleano
        $data['manage_inventory'] = $request->has('manage_inventory') ? true : false;

        // Si no se gestiona inventario, establecer valores predeterminados
        if (!$data['manage_inventory']) {
            $data['stock'] = 0;
            $data['min_stock'] = 0;
            $data['stock_type'] = 'discrete';
            $data['stock_unit'] = 'unidades';
        }

        MenuItem::create($data);

        return redirect()->route('items.index')->with('success', 'Producto creado correctamente.');
    }

    // Mostrar detalles de un producto
    public function show(MenuItem $item)
    {
        return view('items.show', compact('producto'));
    }

    // Mostrar formulario para editar un producto
    public function edit(MenuItem $item)
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        $categorias = Category::all();
        $branches = Branch::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();
        return view('items.edit', compact('item', 'categorias', 'branches', 'hasOpenPettyCash'));
    }

    // Actualizar un producto
    public function update(Request $request, MenuItem $item)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url|max:500',
            // Validaciones de inventario
            'stock' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'stock_type' => 'nullable|in:discrete,continuous',
            'stock_unit' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'name',
            'description',
            'price',
            'category_id',
            'branch_id',
            'stock',
            'min_stock',
            'stock_type',
            'stock_unit',
        ]);

        // Manejo de la imagen
        $imagePath = $item->image; // Mantener imagen actual por defecto

        // Si se subió un nuevo archivo
        if ($request->hasFile('image_file')) {
            // Eliminar imagen anterior si era un archivo local
            if ($item->image && !filter_var($item->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = $request->file('image_file')->store('items', 'public');
        }
        // Si se proporcionó una nueva URL
        elseif ($request->filled('image_url')) {
            // Eliminar imagen anterior si era un archivo local
            if ($item->image && !filter_var($item->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = $request->image_url;
        }

        $data['image'] = $imagePath;

        // Convertir manage_inventory a booleano
        $data['manage_inventory'] = $request->has('manage_inventory') ? true : false;

        // Si se desactiva la gestión de inventario, mantener valores actuales o establecer predeterminados
        if (!$data['manage_inventory']) {
            $data['stock'] = 0;
            $data['min_stock'] = 0;
            $data['stock_type'] = 'discrete';
            $data['stock_unit'] = 'unidades';
        }

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = MenuItem::with('category')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'price', 'category_id']);

        return response()->json($products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'category' => $product->category
            ];
        }));
    }


    public function destroy(MenuItem $item)
    {
        // Eliminar imagen si es un archivo local
        if ($item->image && !filter_var($item->image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Producto eliminado correctamente.');
    }
}
