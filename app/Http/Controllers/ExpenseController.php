<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\PettyCash;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $openPettyCash = PettyCash::where('status', 'open')->latest()->first();
        $expenses = Expense::with('pettyCash')->latest()->get();
        $totalExpenses = $openPettyCash ? $openPettyCash->expenses()->sum('amount') : 0;
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        return view('expenses.index', compact('expenses', 'openPettyCash', 'totalExpenses','hasOpenPettyCash'));
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
        $request->validate([
            'expense_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $openPettyCash = PettyCash::where('status', 'open')->latest()->first();
    
        if (!$openPettyCash) {
            return back()->with('error', 'No hay una caja chica abierta. Abre una caja chica antes de registrar gastos.');
        }

        $expense = new Expense();
        $expense->expense_name = $request->expense_name;
        $expense->description = $request->description;
        $expense->amount = $request->amount;
        $expense->date = now(); // Asigna la fecha y hora actual
        $expense->petty_cash_id = $openPettyCash->id;
        $expense->save();

        return redirect()->route('expenses.index')->with('success', 'Gasto creado exitosamente.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('expenses.edit', compact('expense','hasOpenPettyCash'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Gasto actualizado exitosamente.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado exitosamente.');
    }
}