<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function getTablesStatus()
    {
        try {
            // Obtener la configuración de mesas
            $settings = Setting::first();

            // Si no existe configuración, crear una por defecto
            if (!$settings) {
                $settings = Setting::create([
                    'tables_enabled' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'tables_enabled' => (bool)$settings->tables_enabled
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener estado de mesas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'tables_enabled' => false,
                'error' => 'Error al obtener configuración de mesas'
            ], 500);
        }
    }
    public function update(Request $request)
    {
        try {
            $settings = Setting::firstOrCreate([]);

            // Actualizar tables_enabled si se envía
            if ($request->has('tables_enabled')) {
                $settings->tables_enabled = $request->input('tables_enabled') == '1';
            }

            // Actualizar estados de mesas si se envían
            if ($request->has('tables')) {
                $tablesData = json_decode($request->input('tables'), true);

                if (is_array($tablesData)) {
                    foreach ($tablesData as $tableData) {
                        if (isset($tableData['id']) && isset($tableData['state'])) {
                            \App\Models\Table::where('id', $tableData['id'])
                                ->update(['state' => $tableData['state']]);
                        }
                    }
                }
            }

            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la configuración'
            ], 500);
        }
    }
}
