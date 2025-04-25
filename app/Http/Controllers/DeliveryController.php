<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::all();
        return view('deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        return view('deliveries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Delivery::create($request->all());

        return redirect()->route('deliveries.index')
            ->with('success', 'Servicio de delivery creado exitosamente.');
    }

    public function show(Delivery $delivery)
    {
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        return view('deliveries.edit', compact('delivery'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $delivery->update($request->all());

        return redirect()->route('deliveries.index')
            ->with('success', 'Servicio de delivery actualizado exitosamente.');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Servicio de delivery eliminado exitosamente.');
    }
}