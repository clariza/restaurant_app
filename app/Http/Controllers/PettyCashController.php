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
    // ✅ NUEVO: Método para cargar el contenido del modal
    // ✅ MODIFICADO: Método para cargar el contenido del modal con ID específico
    public function modalContent(Request $request)
    {
        // 🔥 Recibir el ID de la caja abierta desde el query parameter
        $openPettyCashId = $request->query('open_petty_cash_id');



        // 🔥 Obtener la caja chica específica si se proporciona un ID
        if ($openPettyCashId && $openPettyCashId !== 'null') {
            $openPettyCash = PettyCash::where('id', $openPettyCashId)
                ->where('status', 'open')
                ->first();
        } else {
            // Si no se proporciona ID, obtener la última caja chica abierta
            $openPettyCash = PettyCash::where('status', 'open')->latest()->first();
        }

        // Calcular el total de gastos asociados a la caja chica abierta
        $totalExpenses = $openPettyCash ? $openPettyCash->expenses()->sum('amount') : 0;

        // Obtener los gastos detallados de la caja abierta
        $existingExpenses = $openPettyCash ? $openPettyCash->expenses()->get() : collect();

        // Obtener el total de ventas por tipo de pago asociados a la caja chica abierta
        $totalSalesQR = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'QR')->sum('total') : 0;
        $totalSalesCard = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Tarjeta')->sum('total') : 0;
        // $totalSalesCash = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Efectivo')->sum('total') : 0;
        $totalSalesCash = 0;

        // Obtener el valor de total_sales_cash de la última caja abierta
        $totalSalesCashFromDB = $openPettyCash ? $openPettyCash->total_sales_cash : 0;

        // Calcular el total de ventas (sin incluir el monto inicial)
        $totalSales = $totalSalesQR + $totalSalesCard + $totalSalesCash;

        // Query base con relación al usuario
        $query = PettyCash::with('user');

        // Aplicar filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Búsqueda por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Obtener todas las cajas chicas con filtros aplicados
        $pettyCashes = $query->orderBy('date', 'desc')->paginate(10);

        // Obtener todos los usuarios para el select
        $users = User::select('id', 'name')->orderBy('name')->get();

        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();



        // Retornar solo la vista parcial para el modal
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
    }
    public function getClosureData()
    {
        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->first();

        if (!$openPettyCash) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja chica abierta'
            ], 404);
        }

        // Calcular el total de gastos de esta caja chica
        $totalExpenses = Expense::where('petty_cash_id', $openPettyCash->id)
            ->sum('amount');

        // Calcular ventas por método de pago
        $totalSalesCash = Sale::where('petty_cash_id', $openPettyCash->id)
            ->where('payment_method', 'Efectivo')
            ->sum('total');

        $totalSalesQR = Sale::where('petty_cash_id', $openPettyCash->id)
            ->where('payment_method', 'QR')
            ->sum('total');

        $totalSalesCard = Sale::where('petty_cash_id', $openPettyCash->id)
            ->whereIn('payment_method', ['Tarjeta', 'Card'])
            ->sum('total');

        return response()->json([
            'success' => true,
            'petty_cash_id' => $openPettyCash->id,
            'initial_amount' => $openPettyCash->initial_amount,
            'total_expenses' => $totalExpenses,
            'total_sales_cash' => $totalSalesCash,
            'total_sales_qr' => $totalSalesQR,
            'total_sales_card' => $totalSalesCard,
        ]);
    }

    // Mostrar la lista de cierres de caja chica
    public function index(Request $request)
    {
        // Obtener la última caja chica abierta
        $openPettyCash = PettyCash::where('status', 'open')->latest()->first();

        // Calcular el total de gastos asociados a la caja chica abierta
        $totalExpenses = $openPettyCash ? $openPettyCash->expenses()->sum('amount') : 0;

        // Obtener los gastos detallados de la caja abierta
        $existingExpenses = $openPettyCash ? $openPettyCash->expenses()->get() : collect();

        // Obtener el total de ventas por tipo de pago asociados a la caja chica abierta
        $totalSalesQR = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'QR')->sum('total') : 0;
        $totalSalesCard = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Tarjeta')->sum('total') : 0;
        $totalSalesCash = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Efectivo')->sum('total') : 0;

        // Obtener el valor de total_sales_cash de la última caja abierta
        $totalSalesCashFromDB = $openPettyCash ? $openPettyCash->total_sales_cash : 0;

        // Calcular el total de ventas (sin incluir el monto inicial)
        $totalSales = $totalSalesQR + $totalSalesCard + $totalSalesCash;

        // Query base con relación al usuario
        $query = PettyCash::with('user');

        // Aplicar filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Búsqueda por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Obtener todas las cajas chicas con filtros aplicados
        $pettyCashes = $query->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderBy('date', 'desc')
            ->paginate(10);

        // Obtener todos los usuarios para el select
        $users = User::select('id', 'name')->orderBy('name')->get();

        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

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
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();

        if (PettyCash::where('status', 'open')->exists()) {
            return redirect()->route('menu.index');
        }

        return view('petty_cash.create', compact('hasOpenPettyCash'));
    }

    // Guardar un nuevo cierre de caja chica
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Crear la nueva caja chica con monto inicial 0
        PettyCash::create([
            'initial_amount' => 0,
            'current_amount' => 0,
            'date' => now()->toDateString(),
            'notes' => $request->notes,
            'status' => 'open',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('menu.index')->with('success', 'Caja chica abierta correctamente.');
    }

    // Mostrar los detalles de un cierre de caja chica
    public function show(PettyCash $pettyCash)
    {
        return view('petty_cash.show', compact('pettyCash'));
    }

    // Mostrar el formulario para editar un cierre de caja chica
    public function edit(PettyCash $pettyCash)
    {
        return view('petty_cash.edit', compact('pettyCash'));
    }

    // Actualizar un cierre de caja chica
    public function update(Request $request, PettyCash $pettyCash)
    {
        $request->validate([
            'initial_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $pettyCash->update($request->all());

        return redirect()->route('petty-cash.index')->with('success', 'Cierre de caja chica actualizado correctamente.');
    }


    // Eliminar un cierre de caja chica
    public function destroy(PettyCash $pettyCash)
    {
        $pettyCash->delete();
        return redirect()->route('petty-cash.index')->with('success', 'Cierre de caja chica eliminado correctamente.');
    }

    public function saveClosure(Request $request)
    {
        try {
            $validated = $request->validate([
                'petty_cash_id' => 'required|exists:petty_cashes,id',
                'total_expenses' => 'required|numeric|min:0',
                'cash_sales' => 'required|numeric|min:0',
                'qr_sales' => 'nullable|numeric|min:0',
                'card_sales' => 'nullable|numeric|min:0',
                'denominations' => 'nullable|array',
                'new_expenses' => 'nullable|array',
                'new_expenses.*.expense_name' => 'required_with:new_expenses|string',
                'new_expenses.*.description' => 'nullable|string',
                'new_expenses.*.amount' => 'required_with:new_expenses|numeric|min:0.01',
            ]);

            DB::beginTransaction();

            $pettyCash = PettyCash::findOrFail($validated['petty_cash_id']);

            // Verificar que la caja chica pertenece al usuario actual
            if ($pettyCash->user_id !== auth()->id()) {
                throw new \Exception('No tienes permiso para cerrar esta caja chica');
            }

            // Verificar que la caja esté abierta
            if ($pettyCash->status !== 'open') {
                throw new \Exception('Esta caja chica ya está cerrada');
            }

            // Guardar gastos adicionales (manuales) si existen
            if (!empty($validated['new_expenses'])) {
                foreach ($validated['new_expenses'] as $expense) {
                    Expense::create([
                        'expense_name' => $expense['expense_name'],
                        'description' => $expense['description'] ?? null,
                        'amount' => $expense['amount'],
                        'date' => now(),
                        'petty_cash_id' => $pettyCash->id,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            // Recalcular el total de gastos después de agregar los nuevos
            $totalExpenses = Expense::where('petty_cash_id', $pettyCash->id)->sum('amount');

            // Calcular totales
            $totalSales = $validated['cash_sales'] + ($validated['qr_sales'] ?? 0) + ($validated['card_sales'] ?? 0);
            $expectedCash = $pettyCash->initial_amount + $validated['cash_sales'] - $totalExpenses;
            $actualCash = $validated['cash_sales'];
            $difference = $actualCash - $expectedCash;

            // Actualizar caja chica
            $pettyCash->update([
                'status' => 'closed',
                'final_amount' => $actualCash,
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'cash_sales' => $validated['cash_sales'],
                'qr_sales' => $validated['qr_sales'] ?? 0,
                'card_sales' => $validated['card_sales'] ?? 0,
                'denominations' => json_encode($validated['denominations'] ?? []),
                'difference' => $difference,
                'closed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cierre de caja guardado exitosamente',
                'data' => [
                    'petty_cash_id' => $pettyCash->id,
                    'total_expenses' => $totalExpenses,
                    'total_sales' => $totalSales,
                    'difference' => $difference
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function closeAllOpen(Request $request)
    {
        try {
            PettyCash::where('status', 'open')->update(['status' => 'closed']);

            return response()->json([
                'success' => true,
                'message' => 'Todas las cajas chicas abiertas han sido cerradas.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al cerrar las cajas chicas abiertas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkOpen()
    {
        try {
            $hasOpen = PettyCash::where('status', 'open')->exists();

            if ($hasOpen) {
                return redirect()->route('petty-cash.index')->with('warning', 'Hay cajas chicas abiertas. ¿Deseas cerrarlas?');
            } else {
                return redirect()->route('petty-cash.create');
            }
        } catch (\Exception $e) {
            return redirect()->route('petty-cash.index')->with('error', 'Hubo un error al verificar las cajas chicas abiertas.');
        }
    }

    public function print(PettyCash $pettyCash)
    {

        $data = [
            'pettyCash' => $pettyCash,
            'date' => now()->format('d/m/Y'),
            'totalSales' => $pettyCash->total_sales_cash + $pettyCash->total_sales_qr + $pettyCash->total_sales_card,
            'totalExpenses' => $pettyCash->total_expenses,
            'user' => auth()->user(),
            'salesByPaymentMethod' => [
                'Efectivo' => $pettyCash->total_sales_cash,
                'QR' => $pettyCash->total_sales_qr,
                'Tarjeta' => $pettyCash->total_sales_card
            ]
        ];

        $pdf = Pdf::loadView('petty_cash.print', $data);
        return $pdf->stream('reporte-caja-chica-' . $pettyCash->date . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->getFilters($request);
        $fileName = 'reporte_caja_chica_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PettyCashExport($filters), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->getFilters($request);
        $pettyCashes = $this->getPettyCashesQuery($filters)->get();

        $pdf = Pdf::loadView('petty_cash.report-pdf', [
            'pettyCashes' => $pettyCashes,
            'filters' => $filters,
            'totalSales' => $pettyCashes->sum('total_sales_cash'),
            'totalExpenses' => $pettyCashes->sum('total_expenses')
        ]);

        return $pdf->download('reporte_caja_chica_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    private function getFilters(Request $request)
    {
        return [
            'user_id' => $request->input('user_id'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];
    }

    private function getPettyCashesQuery($filters)
    {
        $query = PettyCash::with('user');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        return $query->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderBy('date', 'desc');
    }
    public function checkStatus(Request $request)
    {
        $openPettyCash = PettyCash::where('status', 'open')
            ->where('user_id', auth()->id())
            ->first();

        return response()->json([
            'open' => $openPettyCash ? true : false,
            'petty_cash' => $openPettyCash
        ]);
    }
}
