<?php

namespace App\Http\Controllers;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\PettyCash;
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
        return view('items.index', compact('productos','hasOpenPettyCash'));
    }

    /**
     * Show the form for creating a new resource.
     */
    
    // Mostrar formulario para crear un nuevo producto
    public function create()
    {
        $categorias = Category::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('items.create', compact('categorias','hasOpenPettyCash'));
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
             'image' => 'nullable|string',
             'category_id' => 'required|exists:categories,id',
         ]);
 
         $data = $request->all();
         if ($request->hasFile('image')) {
             $data['image'] = $request->file('image')->store('items', 'public');
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
       return view('items.edit', compact('item', 'categorias','hasOpenPettyCash'));
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
       ]);

       $data = $request->all();
       if ($request->hasFile('image')) {
           $data['image'] = $request->file('image')->store('items', 'public');
       }

       $item->update($data);
       return redirect()->route('items.index')->with('success', 'Producto actualizado correctamente.');
   }

   public function search(Request $request)
    {
        $query = $request->input('query');
        $products = MenuItem::with('category')
        ->where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'name', 'price', 'category_id']);
    
        return response()->json($products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'category' => $product->category
            ];
        }));
    }

   // Eliminar un producto
   public function destroy(MenuItem $item)
   {
       $item->delete();
       return redirect()->route('items.index')->with('success', 'Producto eliminado correctamente.');
   }
}
