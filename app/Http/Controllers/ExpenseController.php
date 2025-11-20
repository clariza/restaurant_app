<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\PettyCash;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::orderBy('date', 'desc')->get();
        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->first();

        // Si la petición solicita JSON
        if ($request->wantsJson() || $request->has('json')) {
            return response()->json([
                'expenses' => $expenses,  // ✅ Asegúrate que sea un array
                'openPettyCash' => $openPettyCash ? true : false
            ]);
        }

        // Vista normal
        return view('expenses.index', compact('expenses', 'openPettyCash'));
    }

    public function create()
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        if (!$hasOpenPettyCash) {
            return redirect()->route('expenses.index')
                ->with('error', 'No hay una caja chica abierta. Abre una caja chica antes de registrar gastos.');
        }

        return view('expenses.create', compact('hasOpenPettyCash'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Verificar que hay caja chica abierta
        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->first();

        if (!$openPettyCash) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja chica abierta'
                ], 422);
            }
            return redirect()->back()->with('error', 'No hay caja chica abierta');
        }

        $expense = Expense::create([
            'expense_name' => $validated['expense_name'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => now(), // 🔥 Fecha y hora actual
            'petty_cash_id' => $openPettyCash->id,
            'user_id' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gasto creado exitosamente',
                'expense' => $expense
            ], 201);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto creado exitosamente');
    }


    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('expenses.edit', compact('expense', 'hasOpenPettyCash'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        $expense->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gasto actualizado exitosamente',
                'expense' => $expense
            ]);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto actualizado exitosamente');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $expense->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gasto eliminado exitosamente'
            ]);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto eliminado exitosamente');
    }
}
