<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use App\Models\PettyCash;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PettyCashController extends Controller
{
    // Mostrar la lista de cierres de caja chica
public function index()
{
    // Obtener la última caja chica abierta
    $openPettyCash = PettyCash::where('status', 'open')->latest()->first();

    // Calcular el total de gastos asociados a la caja chica abierta
    $totalExpenses = $openPettyCash ? $openPettyCash->expenses()->sum('amount') : 0;

    // Obtener el total de ventas por tipo de pago asociados a la caja chica abierta
    $totalSalesQR = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'QR')->sum('total') : 0;
    $totalSalesCard = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Tarjeta')->sum('total') : 0;
    $totalSalesCash = $openPettyCash ? $openPettyCash->sales()->where('payment_method', 'Efectivo')->sum('total') : 0;

    // Obtener el valor de total_sales_cash de la última caja abierta
    $totalSalesCashFromDB = $openPettyCash ? $openPettyCash->total_sales_cash : 0;

    // Calcular el total de ventas (sin incluir el monto inicial)
    $totalSales = $totalSalesQR + $totalSalesCard + $totalSalesCash;

    // Obtener todas las cajas chicas
    $pettyCashes = PettyCash::orderBy('date', 'desc')->get();
    $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
    
    return view('petty_cash.index', compact(
        'pettyCashes',
        'totalExpenses',
        'totalSalesQR',
        'totalSalesCard',
        'totalSalesCash',
        'totalSalesCashFromDB',
        'totalSales',
        'hasOpenPettyCash'
    ));
}

    // Mostrar el formulario para crear un nuevo cierre de caja chica
    public function create()
    {
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
    
        if (PettyCash::where('status', 'open')->exists()) {
            return redirect()->route('menu.index');
        }

        return view('petty_cash.create',compact('hasOpenPettyCash')); 
    }

    // Guardar un nuevo cierre de caja chica
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string', // Solo validamos las notas ahora
        ]);
        
        // Crear la nueva caja chica con monto inicial 0
        PettyCash::create([
            'initial_amount' => 0, // Establecemos monto inicial en 0
            'current_amount' => 0, // También el monto actual en 0
            'date' => now()->toDateString(),
            'notes' => $request->notes,
            'status' => 'open',
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
        $request->validate([
            'petty_cash_id' => 'required|exists:petty_cash,id',
            'total_sales_cash' => 'required|numeric|min:0',
            'total_sales_qr' => 'required|numeric|min:0',
            'total_sales_card' => 'required|numeric|min:0',
            'total_expenses' => 'required|numeric|min:0',
        ]);
    
        try {
            DB::beginTransaction();
    
            $pettyCash = PettyCash::findOrFail($request->petty_cash_id);
            
            if ($pettyCash->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'La caja chica ya está cerrada'
                ], 400);
            }
    
            $totalSales = $request->total_sales_cash + $request->total_sales_qr + $request->total_sales_card;
            $totalGeneral = $totalSales - $request->total_expenses;
    
            $pettyCash->update([
                'total_sales_cash' => $request->total_sales_cash,
                'total_sales_qr' => $request->total_sales_qr,
                'total_sales_card' => $request->total_sales_card,
                'total_expenses' => $request->total_expenses,
                'total_general' => $totalGeneral,
                'current_amount' => $totalGeneral,
                'closed_at' => now(),
                'status' => 'closed',
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Cierre de caja guardado correctamente',
                'data' => $pettyCash
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el cierre: ' . $e->getMessage()
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
    // Obtener los datos necesarios para el reporte
    $data = [
        'pettyCash' => $pettyCash,
        'date' => now()->format('d/m/Y'),
        'totalSales' => $pettyCash->total_sales_cash + $pettyCash->total_sales_qr + $pettyCash->total_sales_card,
        'totalExpenses' => $pettyCash->total_expenses,
        'user' => auth()->user(), // O el vendedor asociado a esta caja chica
        'salesByPaymentMethod' => [
            'Efectivo' => $pettyCash->total_sales_cash,
            'QR' => $pettyCash->total_sales_qr,
            'Tarjeta' => $pettyCash->total_sales_card
        ]
    ];

    $pdf = Pdf::loadView('petty_cash.print', $data);
    return $pdf->stream('reporte-caja-chica-'.$pettyCash->date.'.pdf');
}
}