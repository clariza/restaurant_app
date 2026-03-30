<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\PettyCash;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $branchId = session('branch_id');

        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->first();

        $expenses = Expense::whereHas('pettyCash', function ($q) use ($branchId) {
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
        })
            ->with('pettyCash')
            ->orderBy('date', 'desc')
            ->get();

        return view('expenses.index', compact('expenses', 'openPettyCash'));
    }
    // ── MODAL DE GASTOS (JSON) ────────────────────────────────────────
    // Solo gastos de la caja abierta del usuario en su sucursal activa
    public function modalExpenses()
    {
        $branchId = session('branch_id');

        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->first();

        $expenses = $openPettyCash
            ? Expense::where('petty_cash_id', $openPettyCash->id)
            ->orderBy('date', 'desc')
            ->get()
            : collect();

        return response()->json([
            'expenses'      => $expenses,
            'openPettyCash' => (bool) $openPettyCash,
            'petty_cash_id' => $openPettyCash?->id,
        ]);
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
            ->latest()  // ✅ toma la más reciente, no la más antigua
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
        // Verificar permisos para usuarios no administradores
        if (auth()->user()->role !== 'admin') {
            // Verificar si el gasto pertenece a una caja chica abierta
            $isFromOpenPettyCash = PettyCash::where('id', $expense->petty_cash_id)
                ->where('status', 'open')
                ->exists();

            if (!$isFromOpenPettyCash) {
                abort(403, 'No tienes permiso para ver este gasto.');
            }
        }

        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        // Verificar permisos para usuarios no administradores
        if (auth()->user()->role !== 'admin') {
            // Verificar si el gasto pertenece a una caja chica abierta
            $isFromOpenPettyCash = PettyCash::where('id', $expense->petty_cash_id)
                ->where('status', 'open')
                ->exists();

            if (!$isFromOpenPettyCash) {
                abort(403, 'No tienes permiso para editar este gasto.');
            }
        }

        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        return view('expenses.edit', compact('expense', 'hasOpenPettyCash'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Verificar permisos para usuarios no administradores
        if (auth()->user()->role !== 'admin') {
            // Verificar si el gasto pertenece a una caja chica abierta
            $isFromOpenPettyCash = PettyCash::where('id', $expense->petty_cash_id)
                ->where('status', 'open')
                ->exists();

            if (!$isFromOpenPettyCash) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para actualizar este gasto'
                    ], 403);
                }
                abort(403, 'No tienes permiso para actualizar este gasto.');
            }
        }

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
        // Verificar que el usuario sea administrador
        if (auth()->user()->role !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar gastos'
                ], 403);
            }
            return redirect()->route('expenses.index')
                ->with('error', 'No tienes permisos para eliminar gastos');
        }

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
