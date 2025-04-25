<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\PettyCash;
class CategoryController extends Controller
{
    // Mostrar lista de categorías
    public function index()
    {
        $categorias = Category::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('categories.index', compact('categorias','hasOpenPettyCash'));
    }

    // Mostrar formulario para crear una nueva categoría
    public function create()
    {
        return view('categories.create');
    }

    // Guardar una nueva categoría
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories',
            'icon' => 'nullable|string',
        ]);

        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'Categoría creada correctamente.');
    }

    // Mostrar detalles de una categoría
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    // Mostrar formulario para editar una categoría
    public function edit(Category $category)
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('categories.edit', compact('category','hasOpenPettyCash'));
    }

   // Actualizar una categoría
   public function update(Request $request, Category $category)
   {
       $request->validate([
           'name' => 'required|string|unique:categories,name,' . $category->id,
           'icon' => 'nullable|string',
       ]);
      
       //$categoria->update($request->all());
       $category->update([
        'name' => $request->name,
        'icon' => $request->icon,
       ]);
       return redirect()->route('categories.index')->with('success', 'Categoría actualizada correctamente.');
   }
    // Eliminar una categoría
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}