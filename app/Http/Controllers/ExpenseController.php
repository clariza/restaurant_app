<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\PettyCash;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
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
            ->with('pettyCash.branch')
            ->orderBy('date', 'desc')
            ->get();

        // ← AGREGAR ESTO
        if ($request->wantsJson() || $request->has('json')) {
            return response()->json([
                'expenses'      => $expenses,
                'petty_cash_id' => $openPettyCash?->id,
                'openPettyCash' => (bool) $openPettyCash,
            ]);
        }

        return view('expenses.index', compact('expenses', 'openPettyCash'));
    }

    // ── MODAL DE GASTOS (JSON) ────────────────────────────────────────
    public function modalExpenses()
    {
        $branchId = session('branch_id');
        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->first();

        $expenses = $openPettyCash
            ? Expense::where('petty_cash_id', $openPettyCash->id)
            ->where('source', 'modal')          // ← solo del modal
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

    /**
     * Guardar un gasto desde el formulario/submenu tradicional.
     * Comportamiento original intacto — NO modificar.
     *
     * POST /expenses
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if (!$openPettyCash) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No hay caja chica abierta'], 422);
            }
            return redirect()->back()->with('error', 'No hay caja chica abierta');
        }

        $expense = Expense::create([
            'expense_name'  => $validated['expense_name'],
            'description'   => $validated['description'],
            'amount'        => $validated['amount'],
            'date'          => now(),
            'petty_cash_id' => $openPettyCash->id,
            'user_id'       => auth()->id(),
            'source'        => 'form',              // ← creado desde el formulario
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'expense' => $expense], 201);
        }

        return redirect()->route('expenses.index')->with('success', 'Gasto creado exitosamente');
    }

    /**
     * Guardar un gasto desde el modal de cierre de caja (AJAX exclusivo).
     * Recibe `petty_cash_id` explícito — no busca la caja del usuario.
     * Admin puede registrar en cualquier caja; cajero solo en la propia.
     *
     * POST /expenses/modal
     */
    public function storeFromModal(Request $request)
    {
        try {
            $request->merge([
                'description'   => $request->description ?: null,
                'petty_cash_id' => (int) $request->petty_cash_id,
            ]);

            $validated = $request->validate([
                'petty_cash_id' => 'required|integer|exists:petty_cash,id',
                'expense_name'  => 'required|string|max:255',
                'description'   => 'nullable|string|max:500',
                'amount'        => 'required|numeric|min:0.01',
            ]);

            $pettyCash = PettyCash::find($validated['petty_cash_id']);

            if (!$pettyCash) {
                return response()->json(['success' => false, 'message' => 'Caja no encontrada'], 404);
            }
            if ($pettyCash->status !== 'open') {
                return response()->json(['success' => false, 'message' => 'Caja cerrada'], 422);
            }
            if (auth()->user()->role !== 'admin' && $pettyCash->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $expense = Expense::create([
                'expense_name'  => $validated['expense_name'],
                'description'   => $validated['description'] ?? null,
                'amount'        => $validated['amount'],
                'date'          => now(),
                'petty_cash_id' => $pettyCash->id,
                'user_id'       => auth()->id(),
                'source'        => 'modal',         // ← creado desde el modal de cierre
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gasto guardado correctamente.',
                'expense' => [
                    'id'           => $expense->id,
                    'expense_name' => $expense->expense_name,
                    'description'  => $expense->description,
                    'amount'       => $expense->amount,
                    'date'         => $expense->date,
                    'source'       => $expense->source,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', array_merge(...array_values($e->errors()))),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function show(Expense $expense)
    {
        if (auth()->user()->role !== 'admin') {
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
        if (auth()->user()->role !== 'admin') {
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
        if (auth()->user()->role !== 'admin') {
            $isFromOpenPettyCash = PettyCash::where('id', $expense->petty_cash_id)
                ->where('status', 'open')
                ->exists();
            if (!$isFromOpenPettyCash) {
                return $this->jsonOrRedirect(
                    $request,
                    false,
                    'No tienes permiso para actualizar este gasto.',
                    403
                );
            }
        }

        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'amount'       => 'required|numeric|min:0.01',
            'date'         => 'required|date',
        ]);

        $expense->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gasto actualizado exitosamente.',
                'expense' => $expense,
            ]);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto actualizado exitosamente.');
    }

    /**
     * Eliminar un gasto.
     * Solo administradores pueden eliminar.
     * Soporta tanto peticiones AJAX (JSON) como formulario tradicional.
     *
     * DELETE /expenses/{expense}
     */
    public function destroy(Request $request, Expense $expense)
    {
        if (auth()->user()->role !== 'admin') {
            return $this->jsonOrRedirect(
                $request,
                false,
                'No tienes permisos para eliminar gastos.',
                403
            );
        }

        $expense->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gasto eliminado exitosamente.',
            ]);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto eliminado exitosamente.');
    }

    // ── Helper privado ────────────────────────────────────────────────

    /**
     * Devuelve JSON o redirección según el tipo de petición.
     */
    private function jsonOrRedirect(Request $request, bool $success, string $message, int $status = 422)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $status);
        }
        $type = $success ? 'success' : 'error';
        return redirect()->back()->with($type, $message);
    }
}
