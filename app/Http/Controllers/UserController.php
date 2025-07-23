<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PettyCash;
class UserController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
        //$this->middleware('admin')->except(['index', 'show']); // Solo admin puede acceder a todo excepto index y show
    }
    // Mostrar lista de usuarios
    public function index()
    {
       // $users = User::all();
       if(auth()->user()->role === 'admin') {
            $users = User::all();
        } else {
            $users = User::where('id', auth()->id())->get();
        }
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        //return view('users.index', compact('users'));
        return view('users.index', compact('users','hasOpenPettyCash'),['showOrderDetails' => false]);
    }

    // Mostrar formulario de creación
    public function create()
    {
       // $this->authorize('create', User::class);
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
        //$this->authorize('update', $user);
        //return view('users.edit', compact('user'));
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('users.edit', compact('user','hasOpenPettyCash'),['showOrderDetails' => false]);
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        //$this->authorize('update', $user);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'sometimes|in:admin,vendedor', // Solo admin puede cambiar roles
            
        ]);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ];

          // Solo admin puede cambiar el rol
        if(auth()->user()->role === 'admin' && $request->has('role')) {
            $data['role'] = $request->role;
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario
    public function destroy(User $user)
    {
        //$this->authorize('delete', $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}