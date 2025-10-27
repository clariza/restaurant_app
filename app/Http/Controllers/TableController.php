<?php

namespace App\Http\Controllers;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PettyCash;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Mostrar lista de mesas
    public function index()
    {
        $settings = Setting::firstOrCreate([]);
        $tablesEnabled = $settings->tables_enabled;
        $tables = $tablesEnabled ? Table::all() : collect();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        $settings = Setting::firstOrCreate([]);
        return view('tables.index', compact('tables','hasOpenPettyCash','tablesEnabled','settings'));
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

        public function getAvailableTables()
        {
            $tables = Table::where('state', 'Disponible')->get();
            return response()->json($tables);
        }
         /**
     * Cambiar estado de una mesa
     */
    public function changeTableState(Request $request, Table $table)
    {
        $request->validate([
            'state' => 'required|in:Disponible,Ocupada,Reservada'
        ]);

        $table->update(['state' => $request->state]);
        return response()->json(['success' => true]);
    }
     /**
     * Cambiar estado de una mesa
     */
    public function updateState(Request $request, $tableId)
    {
        try {
            $table = Table::findOrFail($tableId);
            $table->state = $request->state;
            $table->save();
        
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
/**
 * Cambiar el estado de disponibilidad de una mesa
 */
public function changeAvailability(Request $request, $id)
{
    try {
        $table = Table::findOrFail($id);
        
        // Cambiar entre Disponible y No Disponible
        $newState = $table->state === 'Disponible' ? 'No Disponible' : 'Disponible';
        
        $table->update(['state' => $newState]);

        return response()->json([
            'success' => true,
            'new_state' => $newState,
            'message' => 'Estado de mesa actualizado correctamente'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cambiar el estado de la mesa'
        ], 500);
    }
}

/**
 * Obtener el estado actual de una mesa
 */
public function getTableStatus($id)
{
    try {
        $table = Table::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'state' => $table->state
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener el estado de la mesa'
        ], 500);
    }
}
public function bulkChangeState(Request $request)
{
    try {
        // Validar el estado recibido
        $request->validate([
            'state' => 'required|in:' . implode(',', Table::$validStates)
        ]);

        $newState = $request->input('state');
        
        // Obtener todas las mesas
        $tables = Table::all();
        
        if ($tables->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay mesas registradas en el sistema'
            ], 404);
        }
        
        // Actualizar todas las mesas al nuevo estado
        $updatedCount = Table::query()->update(['state' => $newState]);
        
        // Log de la operación (opcional)
        //\Illuminate\Support\Facades\Log::info("Estado de todas las mesas cambiado a: {$newState} por usuario: " . auth()->user()->name ?? 'Sistema');
        
        return response()->json([
            'success' => true,
            'message' => "Estado de todas las mesas actualizado correctamente",
            'new_state' => $newState,
            'updated_count' => $updatedCount,
            'tables' => Table::all(['id', 'number', 'state'])
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Estado inválido. Los estados permitidos son: ' . implode(', ', Table::$validStates),
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error al cambiar estado masivo de mesas: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor al actualizar las mesas'
        ], 500);
    }
}
/**
 * Obtener todas las mesas (para el modal de configuración)
 */
public function available()
{
    try {
        // Verificar si las mesas están habilitadas
        $settings = Setting::firstOrCreate([]);
        
        // Obtener todas las mesas ordenadas por número
        $tables = Table::orderBy('number')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tables,
            'tables_enabled' => $settings->tables_enabled
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error al obtener mesas: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener las mesas: ' . $e->getMessage(),
            'data' => []
        ], 500);
    }
}
// También puedes agregar un método para obtener estadísticas de mesas
public function getTablesStats()
{
    try {
        $stats = Table::selectRaw('state, COUNT(*) as count')
                     ->groupBy('state')
                     ->get()
                     ->pluck('count', 'state')
                     ->toArray();
        
        // Asegurar que todos los estados aparezcan, incluso si tienen 0 mesas
        $allStats = [];
        foreach (Table::$validStates as $state) {
            $allStats[$state] = $stats[$state] ?? 0;
        }
        
        return response()->json([
            'success' => true,
            'stats' => $allStats,
            'total' => Table::count()
        ]);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error al obtener estadísticas de mesas: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener estadísticas'
        ], 500);
    }
}
    

}
