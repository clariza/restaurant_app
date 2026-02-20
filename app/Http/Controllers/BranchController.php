<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\PettyCash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
    public function index()
    {
        $branches = Branch::orderBy('is_main', 'desc')
            ->orderBy('name')
            ->paginate(10);

        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_main' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Si esta sucursal será la principal, desactivar otras como principales
            if ($request->is_main) {
                Branch::where('is_main', true)->update(['is_main' => false]);
            }

            $branch = Branch::create($validated);

            DB::commit();

            return redirect()->route('branches.index')
                ->with('success', 'Sucursal creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al crear la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch)
    {
        $branch->load(['users', 'orders' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('branches')->ignore($branch->id)],
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_main' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Si esta sucursal será la principal, desactivar otras como principales
            if ($request->is_main && !$branch->is_main) {
                Branch::where('is_main', true)->update(['is_main' => false]);
            }

            $branch->update($validated);

            DB::commit();

            return redirect()->route('branches.index')
                ->with('success', 'Sucursal actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al actualizar la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified branch from storage.
     */
    public function destroy(Branch $branch)
    {
        // No permitir eliminar la sucursal principal
        if ($branch->is_main) {
            return back()->with('error', 'No se puede eliminar la sucursal principal.');
        }

        // Verificar si tiene usuarios asignados
        if ($branch->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una sucursal con usuarios asignados.');
        }

        try {
            $branch->delete();
            return redirect()->route('branches.index')
                ->with('success', 'Sucursal eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Toggle branch active status.
     */
    public function toggleStatus(Branch $branch)
    {
        // No permitir desactivar la sucursal principal
        if ($branch->is_main && $branch->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede desactivar la sucursal principal.'
            ], 400);
        }

        $branch->is_active = !$branch->is_active;
        $branch->save();

        return response()->json([
            'success' => true,
            'is_active' => $branch->is_active,
            'message' => 'Estado actualizado exitosamente.'
        ]);
    }
    public function showLoginForm()
    {
        // Si ya está autenticado y no tiene caja abierta, redirigir a apertura
        if (Auth::check() && !PettyCash::where('status', 'open')->exists()) {
            return redirect()->route('petty-cash.create');
        }

        // Obtener sucursales activas ordenadas por principal primero
        $branches = Branch::where('is_active', true)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();

        return view('auth.login', [
            'showOrderDetails' => false,
            'branches' => $branches
        ]);
    }
}
