<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PettyCash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Si ya está autenticado y no tiene caja abierta, redirigir a apertura
        if (Auth::check() && !PettyCash::where('status', 'open')->exists()) {
            return redirect()->route('petty-cash.create');
        }
        
        return view('auth.login', ['showOrderDetails' => true]);
        //return view('auth.login', ['showOrderDetails' => true]);
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();
    //         // Verificar si hay una caja chica abierta
    //         $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

    //         return redirect()->intended(route('menu.index'));
    //     }

    //     return back()->withErrors([
    //         'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    //     ]);
    // }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
           // Verificar si hay una caja chica abierta
           if (!PettyCash::where('status', 'open')->exists()) {
            return redirect()->route('petty-cash.create')
                ->with('warning', 'Debe abrir una caja chica antes de continuar.');
        }

            return redirect()->intended(route('menu.index'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        
        
        // Limpiar el pedido del cliente antes de cerrar sesión
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirigir a login con mensaje
        return redirect('login');
    }
}