<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')->get();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('purchases.index', compact('purchases','hasOpenPettyCash'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('purchases.create', compact('suppliers','hasOpenPettyCash'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $purchaseData = $request->all();
        $purchaseData['purchase_date'] = Carbon::now(); // Asigna la fecha actual automáticamente

        Purchase::create($purchaseData);
        return redirect()->route('purchases.index')->with('success', 'Compra creada exitosamente.');
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('purchases.edit', compact('purchase', 'suppliers','hasOpenPettyCash'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $purchaseData = $request->all();
        // Mantenemos la fecha original de la compra al actualizar
        // Si quisieras actualizar la fecha también, usarías:
        // $purchaseData['purchase_date'] = Carbon::now();

        $purchase->update($purchaseData);
        return redirect()->route('purchases.index')->with('success', 'Compra actualizada exitosamente.');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Compra eliminada exitosamente.');
    }

    public function checkOpen()
    {
        try {
            // Verificar si hay cajas chicas abiertas
            $hasOpen = PettyCash::where('status', 'open')->exists();

            return response()->json([
                'hasOpen' => $hasOpen
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'hasOpen' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}