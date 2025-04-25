<?php

namespace App\Http\Controllers;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Models\PettyCash;
class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Mostrar lista de mesas
    public function index()
    {
        $tables = Table::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('tables.index', compact('tables','hasOpenPettyCash'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // Mostrar formulario para crear una nueva mesa
    public function create()
    {
        return view('tables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // Guardar una nueva mesa
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|unique:tables',
            'state' => 'required|string',
        ]);

        Table::create($request->all());
        return redirect()->route('tables.index')->with('success', 'Mesa creada correctamente.');
    }


    /**
     * Display the specified resource.
     */
    // Mostrar detalles de una mesa
    public function show(Table $table)
    {
        return view('tables.show', compact('table'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // Mostrar formulario para editar una mesa
    public function edit(Table $table)
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('tables.edit', compact('table','hasOpenPettyCash'));
    }
    /**
     * Update the specified resource in storage.
     */
       // Actualizar una mesa
    public function update(Request $request, Table $table)
    {
        $request->validate([
            'number' => 'required|integer|unique:tables,number,' . $table->id,
            'state' => 'required|string',
        ]);

        $table->update($request->all());
        return redirect()->route('tables.index')->with('success', 'Mesa actualizada correctamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
      // Eliminar una mesa
      public function destroy(Table $table)
      {
          $table->delete();
          return redirect()->route('tables.index')->with('success', 'Mesa eliminada correctamente.');
      }
}
