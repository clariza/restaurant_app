<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::orderBy('created_at', 'desc');

        // Filtro por mes específico
        if ($request->filled('birthday_month')) {
            $query->whereMonth('birthdays', $request->birthday_month);
        }

        // Filtros rápidos
        if ($request->filled('birthday_filter')) {
            $today = now();
            match ($request->birthday_filter) {
                'today'      => $query->whereMonth('birthdays', $today->month)
                    ->whereDay('birthdays', $today->day),
                'this_week'  => $query->whereRaw('DATE_FORMAT(birthdays, "%m-%d") BETWEEN ? AND ?', [
                    $today->format('m-d'),
                    $today->addDays(7)->format('m-d'),
                ]),
                'this_month' => $query->whereMonth('birthdays', $today->month),
                default      => null,
            };
        }

        $clients = $query->paginate(10)->withQueryString();

        if ($request->ajax() || $request->query('json')) {
            return response()->json(['success' => true, 'clients' => $clients]);
        }

        return view('clients.index', compact('clients'));
    }
    // public function index()
    // {
    //     $clients = Client::orderBy('created_at', 'desc')->paginate(10);
    //     return view('clients.index', compact('clients'));
    // }

    public function create()
    {
        $branches = \App\Models\Branch::active()->orderBy('name')->get();
        return view('clients.create', compact('branches'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:clients,email',
            'document_type' => 'required|in:CI,DNI,Pasaporte',
            'document_number' => 'nullable|string|max:50|unique:clients,document_number',
            'birthdays' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
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
        if (request()->expectsJson() || request()->query('json')) {
            return response()->json(['client' => $client]);
        }

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $branches = \App\Models\Branch::active()->orderBy('name')->get();
        return view('clients.edit', compact('client', 'branches'));
    }

    public function update(Request $request, Client $client)
    {
        if ($request->filled('birthdays')) {
            try {
                $request->merge([
                    'birthdays' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->birthdays)->format('Y-m-d')
                ]);
            } catch (\Exception $e) {
                // Si el formato ya es correcto o viene vacío, lo dejamos pasar
            }
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'document_type' => 'required|in:CI,DNI,Pasaporte',
            'document_number' => 'nullable|string|max:50|unique:clients,document_number,' . $client->id,
            'birthdays' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }
    /**
     * Buscar clientes por nombre, teléfono o documento (para autocompletado)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['clients' => []]);
        }

        $clients = Client::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%")
                    ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$query}%"])
                    ->orWhere('phone', 'LIKE', "%{$query}%")
                    ->orWhere('document_number', 'LIKE', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn($c) => [
                'id'              => $c->id,
                'name'            => $c->name,
                'last_name'       => $c->last_name,
                'full_name'       => trim("{$c->name} {$c->last_name}"),
                'email'           => $c->email,
                'phone'           => $c->phone,
                'document_type'   => $c->document_type,
                'document_number' => $c->document_number,
                'address'         => $c->address,
                'city'            => $c->city,
                'birthday'        => $c->birthdays,
                'notes'           => $c->notes,
                'is_active'       => $c->is_active,
            ]);

        return response()->json(['clients' => $clients]);
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
