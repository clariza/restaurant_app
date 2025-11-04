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
    public function index()
    {
        $settings = Setting::firstOrCreate([]);
        $tablesEnabled = $settings->tables_enabled;
        $tables = $tablesEnabled ? Table::all() : collect();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        return view('tables.index', compact('tables', 'hasOpenPettyCash', 'tablesEnabled', 'settings'));
    }

    public function create()
    {
        return view('tables.create');
    }

    /**
     * ✅ CORREGIDO: Store con validación y respuesta JSON
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'number' => 'required|unique:tables,number',
                'state' => 'required|string|in:Disponible,Ocupada,Reservada,No Disponible',
            ]);

            $table = Table::create($validated);

            // Si es petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesa creada correctamente',
                    'table' => $table
                ]);
            }

            return redirect()->route('tables.index')
                ->with('success', 'Mesa creada correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear mesa: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la mesa: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al crear la mesa');
        }
    }

    public function show(Table $table)
    {
        return view('tables.show', compact('table'));
    }

    public function edit(Table $table)
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('tables.edit', compact('table', 'hasOpenPettyCash'));
    }

    /**
     * ✅ CORREGIDO: Update con validación y respuesta JSON
     */
    public function update(Request $request, Table $table)
    {
        try {
            $validated = $request->validate([
                'number' => 'required|unique:tables,number,' . $table->id,
                'state' => 'required|string|in:Disponible,Ocupada,Reservada,No Disponible',
            ]);

            $table->update($validated);

            // Si es petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesa actualizada correctamente',
                    'table' => $table->fresh()
                ]);
            }

            return redirect()->route('tables.index')
                ->with('success', 'Mesa actualizada correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar mesa: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la mesa: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al actualizar la mesa');
        }
    }

    /**
     * ✅ CORREGIDO: Delete con respuesta JSON
     */
    public function destroy(Table $table)
    {
        try {
            $table->delete();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesa eliminada correctamente'
                ]);
            }

            return redirect()->route('tables.index')
                ->with('success', 'Mesa eliminada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar mesa: ' . $e->getMessage());

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la mesa: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al eliminar la mesa');
        }
    }

    public function getAvailableTables()
    {
        $tables = Table::where('state', 'Disponible')->get();
        return response()->json($tables);
    }

    /**
     * ✅ NUEVO: Método updateState específico para el modal
     * Ruta: POST /tables/{id}/state
     */
    public function updateState(Request $request, $tableId)
    {
        try {
            Log::info('📝 updateState llamado', [
                'table_id' => $tableId,
                'request_data' => $request->all(),
                'method' => $request->method()
            ]);

            // Validar que el estado sea válido
            $request->validate([
                'state' => 'required|string|in:Disponible,Ocupada,Reservada,No Disponible'
            ]);

            // Buscar la mesa
            $table = Table::findOrFail($tableId);

            // Actualizar el estado
            $table->state = $request->state;
            $table->save();

            Log::info('✅ Mesa actualizada', [
                'table_id' => $table->id,
                'new_state' => $table->state
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de mesa actualizado correctamente',
                'table' => $table
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('❌ Mesa no encontrada', ['table_id' => $tableId]);

            return response()->json([
                'success' => false,
                'message' => 'Mesa no encontrada'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validación', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Estado inválido',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Error al actualizar estado de mesa', [
                'table_id' => $tableId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeAvailability(Request $request, $id)
    {
        try {
            $table = Table::findOrFail($id);
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
     * ✅ CORREGIDO: Método getTableStatus
     */
    public function getTableStatus($id)
    {
        try {
            $table = Table::findOrFail($id);

            return response()->json([
                'success' => true,
                'state' => $table->state,
                'table' => [
                    'id' => $table->id,
                    'number' => $table->number,
                    'state' => $table->state
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener estado de mesa: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado de la mesa'
            ], 404);
        }
    }

    public function bulkChangeState(Request $request)
    {
        try {
            $request->validate([
                'state' => 'required|in:Disponible,Ocupada,Reservada,No Disponible'
            ]);

            $newState = $request->input('state');
            $tables = Table::all();

            if ($tables->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay mesas registradas en el sistema'
                ], 404);
            }

            $updatedCount = Table::query()->update(['state' => $newState]);

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
                'message' => 'Estado inválido',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado masivo de mesas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al actualizar las mesas'
            ], 500);
        }
    }

    /**
     * ✅ CORREGIDO: Método available
     */
    public function available()
    {
        try {
            $settings = Setting::firstOrCreate([]);
            $tables = Table::orderBy('number')->get();

            return response()->json([
                'success' => true,
                'data' => $tables,
                'tables' => $tables, // Alias por compatibilidad
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

    public function getTablesStats()
    {
        try {
            $stats = Table::selectRaw('state, COUNT(*) as count')
                ->groupBy('state')
                ->get()
                ->pluck('count', 'state')
                ->toArray();

            $allStats = [];
            $validStates = ['Disponible', 'Ocupada', 'Reservada', 'No Disponible'];

            foreach ($validStates as $state) {
                $allStats[$state] = $stats[$state] ?? 0;
            }

            return response()->json([
                'success' => true,
                'stats' => $allStats,
                'total' => Table::count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de mesas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }
}
