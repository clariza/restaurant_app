<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Proforma;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sellerId = $request->get('seller_id', 'all');
        $branchFilter = $request->get('branch_id', 'all');
        // Obtener branch_id de la sesión
        $branchId = session('branch_id');

        // Verificar si el usuario es admin
        $isAdmin = auth()->user()->role === 'admin';

        // Query base para órdenes (orden DESCENDENTE — más recientes primero)
        $ordersQuery = Sale::with(['items.menuItem', 'user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Query base para proformas (orden DESCENDENTE — más recientes primero)
        // ✅ EXCLUIR proformas ya convertidas
        $proformasQuery = Proforma::with(['items', 'user', 'branch'])
            ->where(function ($query) {
                $query->where('converted_to_order', '!=', 1)
                    ->orWhereNull('converted_to_order');
            })
            ->where(function ($query) {
                $query->where('is_converted', '!=', 1)
                    ->orWhereNull('is_converted');
            })
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Aplicar filtro de sucursal
        if (!$isAdmin || ($branchFilter !== 'all' && $branchFilter)) {
            $filterBranchId = ($branchFilter !== 'all' && $branchFilter) ? $branchFilter : $branchId;

            if ($filterBranchId) {
                $ordersQuery->where('branch_id', $filterBranchId);
                $proformasQuery->where('branch_id', $filterBranchId);
            }
        }
        // Aplicar filtro de tipo
        if ($type !== 'all') {
            if ($type === 'proforma') {
                $ordersQuery->whereRaw('1 = 0');
            } else {
                $ordersQuery->where('order_type', $type);
                $proformasQuery->whereRaw('1 = 0');
            }
        }
        // Aplicar filtro de fecha desde
        if ($dateFrom) {
            try {
                $dateFromCarbon = Carbon::parse($dateFrom)->startOfDay();
                $ordersQuery->where('created_at', '>=', $dateFromCarbon);
                $proformasQuery->where('created_at', '>=', $dateFromCarbon);
            } catch (\Exception $e) {
            }
        }
        // Aplicar filtro de fecha hasta
        if ($dateTo) {
            try {
                $dateToCarbon = Carbon::parse($dateTo)->endOfDay();
                $ordersQuery->where('created_at', '<=', $dateToCarbon);
                $proformasQuery->where('created_at', '<=', $dateToCarbon);
            } catch (\Exception $e) {
            }
        }
        // Aplicar filtro de vendedor
        if ($sellerId !== 'all') {
            $ordersQuery->where('user_id', $sellerId);
            $proformasQuery->where('user_id', $sellerId);
        }
        // Aplicar búsqueda
        if ($search) {
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('transaction_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('daily_order_number', 'like', "%{$search}%");
            });
            $proformasQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }
        // Verificar caja abierta
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        // Obtener lista de vendedores
        $sellers = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('sales')
                ->whereNotNull('user_id')
                ->distinct();
        })
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        // Obtener lista de sucursales (solo para admin)
        $branches = collect();
        if ($isAdmin) {
            $branches = \App\Models\Branch::where('is_active', true)
                ->orderBy('is_main', 'desc')
                ->orderBy('name', 'asc')
                ->get();
        }
        // Obtener resultados paginados
        $orders = $ordersQuery->paginate(15)->appends($request->all());
        $proformas = $proformasQuery->paginate(15)->appends($request->all());
        return view('orders.index', compact('orders', 'proformas', 'hasOpenPettyCash', 'sellers', 'branches', 'isAdmin'));
    }
    public function print($id)
    {
        $order = Sale::with(['items.menuItem', 'user'])->findOrFail($id);

        if (request()->boolean('modal') || request()->expectsJson()) {
            $orderNumber = $order->daily_order_number ?: $order->transaction_number;
            $typeText = $order->order_type;

            if (($order->order_type ?? '') === 'Comer aquí' && $order->table_number) {
                $typeText .= ' ' . $order->table_number;
            }

            $ticket = [
                'title' => 'RESTAURANTE MIQUNA',
                'date' => $order->created_at->format('j/n/Y H:i'),
                'seller' => $order->user->name ?? 'Usuario',
                'order_number' => $orderNumber,
                'type' => $typeText,
                'customer' => $order->customer_name,
                'items' => $order->items->map(function ($item) {
                    return [
                        'quantity' => $item->quantity,
                        'name' => Str::limit($item->name ?? ($item->menuItem->name ?? 'Producto'), 20, ''),
                        'amount' => (float) ($item->price * $item->quantity),
                    ];
                })->values()->all(),
                'subtotal' => (float) ($order->subtotal ?? $order->total),
                'tax' => (float) ($order->tax ?? 0),
                'total' => (float) $order->total,
                'payments' => [[
                    'label' => $order->payment_method ?: 'Efectivo',
                    'amount' => (float) $order->total,
                ]],
                'notes' => $order->order_notes,
            ];

            return response()->json([
                'success' => true,
                'ticket' => $ticket,
            ]);
        }

        // Retornar vista de impresión
        return view('orders.print', compact('order'));
    }
    public function show($id)
    {
        $order = Sale::with(['items.menuItem', 'user'])->findOrFail($id);
        $hasOpenPettyCash = PettyCash::where('status', 'open')->exists();
        // Orden anterior (más reciente que la actual al navegar hacia "atrás")
        $previousOrder = Sale::where('created_at', '>', $order->created_at)
            ->orderBy('created_at', 'asc')
            ->first();
        if (!$previousOrder) {
            $previousOrder = Sale::where('created_at', '=', $order->created_at)
                ->where('id', '>', $order->id)
                ->orderBy('id', 'asc')
                ->first();
        }
        // Orden siguiente (más antigua que la actual al navegar hacia "adelante")
        $nextOrder = Sale::where('created_at', '<', $order->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$nextOrder) {
            $nextOrder = Sale::where('created_at', '=', $order->created_at)
                ->where('id', '<', $order->id)
                ->orderBy('id', 'desc')
                ->first();
        }
        return view('orders.show', compact('order', 'previousOrder', 'nextOrder', 'hasOpenPettyCash'));
    }
    public function ticketPdf($id)
    {
        $order = Sale::with(['items.menuItem', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('orders.ticket-pdf', compact('order'))
            ->setPaper([0, 0, 226.77, 800], 'portrait')
            ->setOptions([
                'defaultFont'     => 'Courier',
                'isRemoteEnabled' => false,
                'dpi'             => 96,
                'margin_top'      => 3,
                'margin_bottom'   => 3,
                'margin_left'     => 6,
                'margin_right'    => 6,
            ]);

        return $pdf->stream("ticket-orden-{$order->id}.pdf");
    }
    public function destroy($id)
    {
        try {
            $order = Sale::with(['items.menuItem'])->findOrFail($id);
            $openPettyCash = PettyCash::where('status', 'open')->first();
            if (!$openPettyCash) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay una caja chica abierta. No se puede eliminar la orden.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'No hay una caja chica abierta. No se puede eliminar la orden.');
            }
            if ($order->petty_cash_id !== $openPettyCash->id) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Esta orden pertenece a otra caja chica y no puede ser eliminada.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Esta orden pertenece a otra caja chica y no puede ser eliminada.');
            }
            DB::beginTransaction();
            try {
                $proformaId = null;
                if (Schema::hasColumn('sales', 'proforma_id') && $order->proforma_id) {
                    $proformaId = $order->proforma_id;
                    $proforma = Proforma::find($proformaId);
                    if ($proforma) {
                        $fillableFields = $proforma->getFillable();
                        $updateData = [];
                        if (in_array('converted_to_order', $fillableFields))  $updateData['converted_to_order']  = false;
                        if (in_array('is_converted', $fillableFields))        $updateData['is_converted']        = false;
                        if (in_array('converted_order_id', $fillableFields))  $updateData['converted_order_id']  = null;
                        if (in_array('converted_at', $fillableFields))        $updateData['converted_at']        = null;
                        if (in_array('status', $fillableFields))              $updateData['status']              = 'reservado';
                        if (!empty($updateData)) $proforma->update($updateData);
                    }
                }
                foreach ($order->items as $item) {
                    if ($item->menuItem) {
                        $item->menuItem->increment('stock', $item->quantity);
                    }
                }
                $order->items()->delete();
                $orderNumber = $order->transaction_number;
                $order->delete();
                $openPettyCash->update();
                DB::commit();
                Log::info("Orden eliminada: {$orderNumber} por " . auth()->user()->name . ($proformaId ? " (Proforma #{$proformaId} desmarcada)" : ''));
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "La orden {$orderNumber} ha sido eliminada exitosamente.",
                        'proforma_unmarked' => !is_null($proformaId)
                    ]);
                }
                return redirect()->route('orders.index')->with('success', "La orden {$orderNumber} ha sido eliminada exitosamente.");
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("Error al eliminar orden: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile()
            ]);
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la orden: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }
}
