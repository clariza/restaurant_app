<?php

// app/Http/Controllers/DeliveryServiceController.php
namespace App\Http\Controllers;

use App\Models\DeliveryService;
use Illuminate\Http\Request;
use App\Models\PettyCash;

class DeliveryServiceController extends Controller
{
    public function index()
    {
        $services = DeliveryService::where('is_active', true)->get();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('deliveries.index', compact('services','hasOpenPettyCash'));
    }

    public function create()
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('deliveries.create',compact('hasOpenPettyCash'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        DeliveryService::create($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Servicio creado exitosamente');
    }

    public function edit(DeliveryService $delivery)
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('deliveries.edit', compact('delivery','hasOpenPettyCash'));
    }

    public function update(Request $request, DeliveryService $deliveryService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $deliveryService->update($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Servicio actualizado exitosamente');
    }

    public function destroy(DeliveryService $delivery)
    {
        $delivery->update(['is_active' => false]);
        
        return redirect()->route('deliveries.index')
            ->with('success', 'Servicio desactivado exitosamente');
    }
}