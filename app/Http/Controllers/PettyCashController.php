<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\PettyCash;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PettyCashExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class PettyCashController extends Controller
{
    /**
     * Helper: Obtener branch_id de la sesión o abortar
     */
    private function getBranchId()
    {
        return session('branch_id');
    }

    // ✅ MODIFICADO: Método para cargar el contenido del modal con ID específico
    public function modalContent(Request $request)
    {
        try {
            $branchId = $this->getBranchId();

            // Recibir el ID de la caja abierta desde el query parameter
            $openPettyCashId = $request->query('open_petty_cash_id');

            // Obtener la caja chica específica si se proporciona un ID
            if ($openPettyCashId && $openPettyCashId !== 'null') {
                $query = PettyCash::where('id', $openPettyCashId)->where('status', 'open');
                if ($branchId) $query->where('branch_id', $branchId);
                $openPettyCash = $query->first();
            } else {
                // Si no se proporciona ID, obtener la última caja chica abierta
                $query = PettyCash::where('status', 'open');
                if ($branchId) $query->where('branch_id', $branchId);
                $openPettyCash = $query->latest()->first();
            }

            // Calcular totales solo si hay caja abierta
            $totalExpenses         = 0;
            $existingExpenses      = collect();
            $totalSalesQR          = 0;
            $totalSalesCard        = 0;
            $totalSalesCash        = 0;
            $totalSalesCashFromDB  = 0;
            $totalSales            = 0;

            if ($openPettyCash) {
                $totalExpenses        = $openPettyCash->expenses()->sum('amount') ?? 0;
                $existingExpenses     = $openPettyCash->expenses()->get() ?? collect();
                $totalSalesQR         = $openPettyCash->sales()->where('payment_method', 'QR')->sum('total') ?? 0;
                $totalSalesCard       = $openPettyCash->sales()->where('payment_method', 'Tarjeta')->sum('total') ?? 0;
                $totalSalesCash       = 0; // Se calculará con las denominaciones
                $totalSalesCashFromDB = $openPettyCash->total_sales_cash ?? 0;
                $totalSales           = $totalSalesQR + $totalSalesCard + $totalSalesCash;
            }

            // Query base con relación al usuario
            $query = PettyCash::with('user');
            if ($branchId) $query->where('branch_id', $branchId);

            // Aplicar filtros
            if ($request->filled('user_id'))   $query->where('user_id', $request->user_id);
            if ($request->filled('date_from'))  $query->whereDate('date', '>=', $request->date_from);
            if ($request->filled('date_to'))    $query->whereDate('date', '<=', $request->date_to);
            if ($request->filled('status'))     $query->where('status', $request->status);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'LIKE', "%{$search}%"));
                });
            }

            $pettyCashes     = $query->orderBy('date', 'desc')->paginate(10);
            $users           = User::select('id', 'name')->orderBy('name')->get();
            $hasOpenPettyCash = PettyCash::where('status', 'open')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->exists();

            return view('petty_cash.modal-content', compact(
                'pettyCashes',
                'openPettyCash',
                'totalExpenses',
                'totalSalesQR',
                'totalSalesCard',
                'totalSalesCash',
                'totalSalesCashFromDB',
                'totalSales',
                'hasOpenPettyCash',
                'users',
                'existingExpenses'
            ));
        } catch (\Exception $e) {
            return response()->view('errors.modal-error', ['error' => $e->getMessage()], 500);
        }
    }

    public function getClosureData()
    {
        try {
            $branchId = $this->getBranchId();

            // ✅ CORREGIDO: filtrar también por branch_id
            $openPettyCash = PettyCash::where('status', 'open')
                ->where('user_id', auth()->id())
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->first();

            if (!$openPettyCash) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja chica abierta para esta sucursal'
                ], 404);
            }

            $totalExpenses  = Expense::where('petty_cash_id', $openPettyCash->id)->sum('amount') ?? 0;
            $totalSalesCash = Sale::where('petty_cash_id', $openPettyCash->id)->where('payment_method', 'Efectivo')->sum('total') ?? 0;
            $totalSalesQR   = Sale::where('petty_cash_id', $openPettyCash->id)->where('payment_method', 'QR')->sum('total') ?? 0;
            $totalSalesCard = Sale::where('petty_cash_id', $openPettyCash->id)->whereIn('payment_method', ['Tarjeta', 'Card'])->sum('total') ?? 0;

            return response()->json([
                'success'         => true,
                'petty_cash_id'   => $openPettyCash->id,
                'initial_amount'  => $openPettyCash->initial_amount ?? 0,
                'total_expenses'  => $totalExpenses,
                'total_sales_cash' => $totalSalesCash,
                'total_sales_qr'  => $totalSalesQR,
                'total_sales_card' => $totalSalesCard,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener datos: ' . $e->getMessage()], 500);
        }
    }

    // Mostrar la lista de cierres de caja chica
    public function index(Request $request)
    {
        $branchId = $this->getBranchId();

        // ✅ CORREGIDO: filtrar por branch_id
        $openPettyCash = PettyCash::where('status', 'open')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->first();

        $totalExpenses        = $openPettyCash ? $openPettyCash->expenses()->sum('amount') : 0;
        $existingExpenses     = $openPettyCash ? $openPettyCash->expenses()->get() : collect();
        $totalSalesQR         = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'QR')->sum('total') : 0;
        $totalSalesCard       = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Tarjeta')->sum('total') : 0;
        $totalSalesCash       = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Efectivo')->sum('total') : 0;
        $totalSalesCashFromDB = $openPettyCash ? $openPettyCash->total_sales_cash : 0;
        $totalSales           = $totalSalesQR + $totalSalesCard + $totalSalesCash;

        // Query base filtrada por sucursal
        $query = PettyCash::with('user');
        if ($branchId) $query->where('branch_id', $branchId);

        if ($request->filled('user_id'))   $query->where('user_id', $request->user_id);
        if ($request->filled('date_from'))  $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('status'))     $query->where('status', $request->status);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'LIKE', "%{$search}%"));
            });
        }

        $pettyCashes = $query->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderBy('date', 'desc')
            ->paginate(10);

        $users = User::select('id', 'name')->orderBy('name')->get();

        // ✅ CORREGIDO: filtrar por branch_id
        $hasOpenPettyCash = PettyCash::where('status', 'open')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->exists();

        return view('petty_cash.index', compact(
            'pettyCashes',
            'totalExpenses',
            'totalSalesQR',
            'totalSalesCard',
            'totalSalesCash',
            'totalSalesCashFromDB',
            'totalSales',
            'hasOpenPettyCash',
            'users',
            'existingExpenses',
            'openPettyCash'
        ));
    }

    // Mostrar el formulario para crear un nuevo cierre de caja chica
    public function create()
    {
        $branchId = $this->getBranchId();

        // ✅ CORREGIDO: filtrar por branch_id
        $hasOpenPettyCash = PettyCash::where('status', 'open')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->exists();

        if ($hasOpenPettyCash) {
            return redirect()->route('menu.index');
        }

        return view('petty_cash.create', compact('hasOpenPettyCash'));
    }

    // ✅ CORREGIDO: Guardar nueva caja chica con branch_id
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $branchId = $this->getBranchId();

        if (!$branchId) {
            return redirect()->back()
                ->with('error', 'No hay sucursal activa. Por favor, inicia sesión nuevamente.');
        }

        // Evitar duplicados para el mismo usuario y sucursal
        $existing = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->where('branch_id', $branchId)
            ->first();

        if ($existing) {
            return redirect()->route('menu.index')
                ->with('warning', 'Ya tienes una caja chica abierta para esta sucursal.');
        }

        PettyCash::create([
            'initial_amount' => 0,
            'current_amount' => 0,
            'date'           => now()->toDateString(),
            'notes'          => $request->notes,
            'status'         => 'open',
            'user_id'        => auth()->id(),
            'branch_id'      => $branchId, // ✅ CORREGIDO
        ]);

        return redirect()->route('menu.index')->with('success', 'Caja chica abierta correctamente.');
    }

    public function show(PettyCash $pettyCash)
    {
        return view('petty_cash.show', compact('pettyCash'));
    }

    public function edit(PettyCash $pettyCash)
    {
        return view('petty_cash.edit', compact('pettyCash'));
    }

    public function update(Request $request, PettyCash $pettyCash)
    {
        $request->validate([
            'initial_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'date'           => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        $pettyCash->update($request->all());

        return redirect()->route('petty-cash.index')->with('success', 'Cierre de caja chica actualizado correctamente.');
    }

    public function destroy(PettyCash $pettyCash)
    {
        $pettyCash->delete();
        return redirect()->route('petty-cash.index')->with('success', 'Cierre de caja chica eliminado correctamente.');
    }

    public function saveClosure(Request $request)
    {
        try {
            $validated = $request->validate([
                'petty_cash_id'          => 'required|integer',
                'total_expenses'         => 'required|numeric|min:0',
                'total_sales_cash'       => 'required|numeric|min:0',
                'total_sales_qr'         => 'nullable|numeric|min:0',
                'total_sales_card'       => 'nullable|numeric|min:0',
                'expenses'               => 'nullable|array',
                'expenses.*.name'        => 'required_with:expenses|string|max:255',
                'expenses.*.description' => 'nullable|string|max:500',
                'expenses.*.amount'      => 'required_with:expenses|numeric|min:0.01',
            ]);

            DB::beginTransaction();

            $pettyCash = PettyCash::find($validated['petty_cash_id']);

            if (!$pettyCash) throw new \Exception('Caja chica no encontrada');
            if ($pettyCash->user_id !== auth()->id()) throw new \Exception('No tienes permiso para cerrar esta caja chica');
            if ($pettyCash->status !== 'open') throw new \Exception('Esta caja chica ya está cerrada');

            $newExpensesCount = 0;
            if (!empty($validated['expenses'])) {
                foreach ($validated['expenses'] as $expense) {
                    Expense::create([
                        'expense_name'  => $expense['name'],
                        'description'   => $expense['description'] ?? null,
                        'amount'        => $expense['amount'],
                        'date'          => now(),
                        'petty_cash_id' => $pettyCash->id,
                        'user_id'       => auth()->id(),
                    ]);
                    $newExpensesCount++;
                }
            }

            $totalExpenses  = Expense::where('petty_cash_id', $pettyCash->id)->sum('amount');
            $totalGeneral   = $validated['total_sales_cash'] + ($validated['total_sales_qr'] ?? 0) + ($validated['total_sales_card'] ?? 0);
            $currentAmount  = $pettyCash->initial_amount + $validated['total_sales_cash'] - $totalExpenses;

            $pettyCash->update([
                'status'          => 'closed',
                'current_amount'  => $currentAmount,
                'total_sales_cash' => $validated['total_sales_cash'],
                'total_sales_qr'  => $validated['total_sales_qr'] ?? 0,
                'total_sales_card' => $validated['total_sales_card'] ?? 0,
                'total_expenses'  => $totalExpenses,
                'total_general'   => $totalGeneral,
                'closed_at'       => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cierre de caja guardado exitosamente',
                'data'    => [
                    'petty_cash_id'      => $pettyCash->id,
                    'total_expenses'     => $totalExpenses,
                    'total_general'      => $totalGeneral,
                    'current_amount'     => $currentAmount,
                    'new_expenses_count' => $newExpensesCount,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function closeAllOpen(Request $request)
    {
        try {
            PettyCash::where('status', 'open')->update(['status' => 'closed']);
            return response()->json(['success' => true, 'message' => 'Todas las cajas chicas abiertas han sido cerradas.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Hubo un error al cerrar las cajas chicas abiertas.', 'error' => $e->getMessage()], 500);
        }
    }

    public function checkOpen()
    {
        try {
            $branchId = $this->getBranchId();
            $hasOpen  = PettyCash::where('status', 'open')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->exists();

            if ($hasOpen) {
                return redirect()->route('petty-cash.index')->with('warning', 'Hay cajas chicas abiertas. ¿Deseas cerrarlas?');
            }

            return redirect()->route('petty-cash.create');
        } catch (\Exception $e) {
            return redirect()->route('petty-cash.index')->with('error', 'Hubo un error al verificar las cajas chicas abiertas.');
        }
    }

    public function print(PettyCash $pettyCash)
    {
        $data = [
            'pettyCash'            => $pettyCash,
            'date'                 => now()->format('d/m/Y'),
            'totalSales'           => $pettyCash->total_sales_cash + $pettyCash->total_sales_qr + $pettyCash->total_sales_card,
            'totalExpenses'        => $pettyCash->total_expenses,
            'user'                 => auth()->user(),
            'salesByPaymentMethod' => [
                'Efectivo' => $pettyCash->total_sales_cash,
                'QR'       => $pettyCash->total_sales_qr,
                'Tarjeta'  => $pettyCash->total_sales_card,
            ]
        ];

        $pdf = Pdf::loadView('petty_cash.print', $data);
        return $pdf->stream('reporte-caja-chica-' . $pettyCash->date . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters  = $this->getFilters($request);
        $fileName = 'reporte_caja_chica_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PettyCashExport($filters), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $filters     = $this->getFilters($request);
        $pettyCashes = $this->getPettyCashesQuery($filters)->get();

        $pdf = Pdf::loadView('petty_cash.report-pdf', [
            'pettyCashes'   => $pettyCashes,
            'filters'       => $filters,
            'totalSales'    => $pettyCashes->sum('total_sales_cash'),
            'totalExpenses' => $pettyCashes->sum('total_expenses'),
        ]);

        return $pdf->download('reporte_caja_chica_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    public function closureModalContent(Request $request)
    {
        try {
            $branchId = $this->getBranchId();

            // ✅ CORREGIDO: filtrar por branch_id
            $openPettyCash = PettyCash::where('status', 'open')
                ->where('user_id', auth()->id())
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->latest()
                ->first();

            $totalExpenses  = 0;
            $totalSalesQR   = 0;
            $totalSalesCard = 0;

            if ($openPettyCash) {
                $totalExpenses  = Expense::where('petty_cash_id', $openPettyCash->id)->sum('amount') ?? 0;
                $totalSalesQR   = Sale::where('petty_cash_id', $openPettyCash->id)->where('payment_method', 'QR')->sum('total') ?? 0;
                $totalSalesCard = Sale::where('petty_cash_id', $openPettyCash->id)->whereIn('payment_method', ['Tarjeta', 'Card'])->sum('total') ?? 0;
            }

            return view('petty_cash.modal-content', compact('openPettyCash', 'totalExpenses', 'totalSalesQR', 'totalSalesCard'));
        } catch (\Exception $e) {
            return response()->view('errors.modal-error', ['error' => 'Error al cargar el modal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ✅ CORREGIDO: Obtener la caja chica abierta del usuario y sucursal actual
     */
    public function getOpenPettyCash()
    {
        $branchId = $this->getBranchId();

        $openPettyCash = PettyCash::where('user_id', auth()->id())
            ->where('status', 'open')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->first();

        if ($openPettyCash) {
            return response()->json([
                'success'        => true,
                'petty_cash_id'  => $openPettyCash->id,
                'date'           => $openPettyCash->date,
                'initial_amount' => $openPettyCash->initial_amount,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No hay caja chica abierta']);
    }

    public function getModalClosure($id)
    {
        $pettyCash = PettyCash::findOrFail($id);

        if ($pettyCash->user_id !== auth()->id()) abort(403, 'No autorizado');
        if ($pettyCash->status !== 'open') abort(400, 'Esta caja ya está cerrada');

        $totalSalesQR   = Sale::where('petty_cash_id', $id)->where('payment_method', 'qr')->sum('total');
        $totalSalesCard = Sale::where('petty_cash_id', $id)->where('payment_method', 'tarjeta')->sum('total');
        $totalExpenses  = Expense::where('petty_cash_id', $id)->sum('amount');

        return view('petty-cash.partials.closure-modal', compact('pettyCash', 'totalSalesQR', 'totalSalesCard', 'totalExpenses'));
    }

    /**
     * ✅ CORREGIDO: Imprimir reporte de la caja anterior del usuario y sucursal actual
     */
    public function printPrevious()
    {
        $branchId = $this->getBranchId();

        $previousPettyCash = PettyCash::where('user_id', auth()->id())
            ->where('status', 'closed')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('closed_at', 'desc')
            ->first();

        if (!$previousPettyCash) {
            return back()->with('error', 'No se encontró ninguna caja cerrada anterior para esta sucursal.');
        }

        $data = [
            'pettyCash'            => $previousPettyCash,
            'date'                 => $previousPettyCash->closed_at
                ? $previousPettyCash->closed_at->format('d/m/Y')
                : now()->format('d/m/Y'),
            'totalSales'           => $previousPettyCash->total_sales_cash + $previousPettyCash->total_sales_qr + $previousPettyCash->total_sales_card,
            'totalExpenses'        => $previousPettyCash->total_expenses,
            'user'                 => auth()->user(),
            'salesByPaymentMethod' => [
                'Efectivo' => $previousPettyCash->total_sales_cash,
                'QR'       => $previousPettyCash->total_sales_qr,
                'Tarjeta'  => $previousPettyCash->total_sales_card,
            ]
        ];

        $pdf = Pdf::loadView('petty_cash.print', $data);
        return $pdf->stream('reporte-caja-anterior-' . $previousPettyCash->date . '.pdf');
    }

    /**
     * ✅ CORREGIDO: checkStatus filtra por branch_id
     */
    public function checkStatus(Request $request)
    {
        $branchId = $this->getBranchId();

        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->first();

        return response()->json([
            'open'       => (bool) $openPettyCash,
            'petty_cash' => $openPettyCash,
        ]);
    }

    private function getFilters(Request $request)
    {
        return [
            'user_id'   => $request->input('user_id'),
            'status'    => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
        ];
    }

    private function getPettyCashesQuery($filters)
    {
        $branchId = $this->getBranchId();
        $query    = PettyCash::with('user');

        if ($branchId) $query->where('branch_id', $branchId);
        if (!empty($filters['user_id']))   $query->where('user_id', $filters['user_id']);
        if (!empty($filters['status']))    $query->where('status', $filters['status']);
        if (!empty($filters['date_from'])) $query->whereDate('date', '>=', $filters['date_from']);
        if (!empty($filters['date_to']))   $query->whereDate('date', '<=', $filters['date_to']);

        return $query->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")->orderBy('date', 'desc');
    }
}
