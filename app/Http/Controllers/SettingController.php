<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'tables_enabled' => 'required|boolean'
            ]);

            // Obtener o crear configuración
            $setting = Setting::firstOrNew([]);
            $setting->tables_enabled = $validated['tables_enabled'];
            $setting->save();

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente',
                'tables_enabled' => $setting->tables_enabled
            ]);

        } catch (\Exception $e) {
            Log::error('Error en SettingController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuración'
            ], 500);
        }
    }
}