<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PettyCash;

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
                'purchase_date' => 'required|date',
            ]);
    
            Purchase::create($request->all());
            return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
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
                'purchase_date' => 'required|date',
            ]);
    
            $purchase->update($request->all());
            return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
        }
    
        public function destroy(Purchase $purchase)
        {
            $purchase->delete();
            return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
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
