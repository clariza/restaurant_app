<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::orderBy('created_at', 'desc')->paginate(10);

        // Si es una petición AJAX, devolver JSON
        if ($request->ajax() || $request->query('json')) {
            return response()->json([
                'success' => true,
                'clients' => $clients
            ]);
        }

        // Si no, devolver la vista normal
        return view('clients.index', compact('clients'));
    }
    // public function index()
    // {
    //     $clients = Client::orderBy('created_at', 'desc')->paginate(10);
    //     return view('clients.index', compact('clients'));
    // }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:clients,email',
            'document_type' => 'required|in:CI,NIT,Pasaporte',
            'document_number' => 'nullable|string|max:50|unique:clients,document_number',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'document_type' => 'required|in:CI,NIT,Pasaporte',
            'document_number' => 'nullable|string|max:50|unique:clients,document_number,' . $client->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }

    public function toggleStatus(Client $client)
    {
        $client->is_active = !$client->is_active;
        $client->save();

        return response()->json([
            'success' => true,
            'is_active' => $client->is_active,
            'message' => $client->is_active ? 'Cliente activado' : 'Cliente desactivado'
        ]);
    }
}
