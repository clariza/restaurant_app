<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PettyCash;
class UserController extends Controller
{
    // Mostrar lista de usuarios
    public function index()
    {
        $users = User::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        //return view('users.index', compact('users'));
        return view('users.index', compact('users','hasOpenPettyCash'),['showOrderDetails' => false]);
    }

    // Mostrar formulario de creación
    public function create()
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('users.create',compact('hasOpenPettyCash'), ['showOrderDetails' => false]);
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,vendedor', // Asegura que el rol sea 'admin' o 'vendedor'
        ]);
    
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role, // Guarda el rol seleccionado
        ]);
        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(User $user)
    {
        //return view('users.edit', compact('user'));
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('users.edit', compact('user','hasOpenPettyCash'),['showOrderDetails' => false]);
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}