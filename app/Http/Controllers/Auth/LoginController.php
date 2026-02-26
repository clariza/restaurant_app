<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PettyCash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected string $redirectTo = '/admin/dashboard';

    /**
     * Mostrar el formulario de login con las sucursales disponibles
     */
    public function showLoginForm()
    {
        // Redirigir si ya está autenticado
        if (Auth::check()) {
            return redirect($this->redirectTo);
        }

        $branches = Branch::where('is_active', true)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();

        Log::info('📋 Cargando login con sucursales:', [
            'total_branches' => $branches->count()
        ]);

        return view('auth.login', compact('branches'));
    }

    /**
     * Manejar el intento de autenticación
     */
    public function login(Request $request)
    {
        // 🔥 VALIDACIÓN
        $request->validate([
            'email'     => 'required|string|email',
            'password'  => 'required|string',
            'branch_id' => 'required|integer|exists:branches,id',
        ], [
            'branch_id.required' => 'Por favor selecciona una sucursal',
            'branch_id.exists'   => 'La sucursal seleccionada no es válida',
        ]);

        // 🔥 PASO 1: INTENTAR AUTENTICACIÓN
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            Log::warning('❌ Credenciales inválidas para:', ['email' => $request->email]);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        Log::info('🔐 === INICIO DE AUTENTICACIÓN ===');

        $user = Auth::user();

        Log::info('Usuario autenticado:', [
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'email'     => $user->email,
        ]);

        // 🔥 PASO 2: OBTENER Y VALIDAR LA SUCURSAL
        $branchId = $request->input('branch_id');

        $branch = Branch::where('id', $branchId)
            ->where('is_active', true)
            ->first();

        if (! $branch) {
            Log::error('❌ Sucursal no encontrada o inactiva:', ['branch_id' => $branchId]);

            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['branch_id' => 'La sucursal seleccionada no es válida'])
                ->withInput($request->only('email'));
        }

        Log::info('✅ Sucursal encontrada:', [
            'branch_id'   => $branch->id,
            'branch_name' => $branch->name,
            'branch_code' => $branch->code,
            'is_main'     => $branch->is_main,
        ]);

        // 🔥 PASO 3: GUARDAR EN SESIÓN
        $request->session()->put([
            'branch_id'   => $branch->id,
            'branch_name' => $branch->name,
            'branch_code' => $branch->code,
        ]);

        $request->session()->save();

        Log::info('💾 Sesión guardada');

        // 🔥 PASO 4: VERIFICACIÓN INMEDIATA
        $verificacion = session('branch_id');

        Log::info('🔍 VERIFICACIÓN INMEDIATA:', [
            'branch_id_en_sesion' => $verificacion,
            'session_all'         => session()->all(),
        ]);

        if (! $verificacion) {
            Log::error('❌ VERIFICACIÓN FALLÓ - La sesión NO se guardó correctamente');
        } else {
            Log::info('✅ VERIFICACIÓN EXITOSA - branch_id está en sesión');
        }

        // 🔥 PASO 5: ACTUALIZAR USUARIO EN BD
        try {
            $user->branch_id = $branch->id;
            //$user->save();

            Log::info('✅ Usuario actualizado con branch_id en BD:', [
                'user_id'   => $user->id,
                'branch_id' => $user->branch_id,
            ]);
        } catch (\Exception $e) {
            Log::warning('⚠️ No se pudo actualizar usuario (campo branch_id puede no existir):', [
                'error' => $e->getMessage(),
            ]);
        }

        // 🔥 PASO 6: VERIFICAR CAJA CHICA
        try {
            $openPettyCash = PettyCash::where('status', 'open')
                ->where('user_id', $user->id)
                ->where('branch_id', $branch->id)
                ->first();

            if ($openPettyCash) {
                Log::info('✅ Caja chica abierta encontrada:', [
                    'petty_cash_id'   => $openPettyCash->id,
                    'opening_balance' => $openPettyCash->opening_balance,
                ]);
            } else {
                Log::warning('⚠️ No hay caja chica abierta para esta sucursal');
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Error al verificar caja chica:', [
                'error' => $e->getMessage(),
            ]);
        }

        // 🔥 PASO 7: REGENERAR SESIÓN (seguridad)
        $request->session()->regenerate();

        Log::info('=== FIN DE AUTENTICACIÓN ===');

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Cerrar sesión y limpiar datos de sucursal
     */
    public function logout(Request $request)
    {
        Log::info('🚪 Cerrando sesión:', [
            'user_id'   => Auth::id(),
            'branch_id' => session('branch_id'),
        ]);

        $request->session()->forget(['branch_id', 'branch_name', 'branch_code']);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
