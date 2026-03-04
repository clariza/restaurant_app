<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proforma;
use App\Models\ProformaItem;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PettyCash;
use App\Models\Sale;
use App\Models\SaleItem;

class ProformaController extends Controller
{
    /**
     * Obtener una proforma específica con sus items
     * Ruta: GET /proformas/{id}
     */
    public function show($id)
    {
        try {
            // Obtener proforma con sus items y la información del menú
            $proforma = Proforma::with([
                'items.menuItem:id,name,price,stock,stock_type,stock_unit,min_stock,manage_inventory',
                'order:id,transaction_number'
            ])->findOrFail($id);

            // ✅ VERIFICAR SI YA FUE CONVERTIDA
            $isConverted = $proforma->converted_to_order == true;
            $canConvert = !$isConverted && $proforma->canBeConverted();

            // Preparar respuesta base
            $responseData = [
                'success' => true,
                'proforma' => $proforma,
                'is_converted' => $isConverted,
                'can_convert' => $canConvert,
                'message' => 'Proforma obtenida exitosamente'
            ];

            if ($isConverted) {
                $responseData['reason'] = 'already_converted';
                $responseData['converted_order_id'] = $proforma->converted_order_id;

                if ($proforma->order) {
                    $responseData['converted_order_number'] = $proforma->order->transaction_number;
                }
            }
            // ✅ SI NO PUEDE CONVERTIRSE POR STOCK
            elseif (!$canConvert && $proforma->status !== 'cancelled') {
                $responseData['reason'] = 'insufficient_stock';
                $responseData['stock_issues'] = [];

                foreach ($proforma->items as $item) {
                    $menuItem = $item->menuItem;
                    if ($menuItem && $menuItem->manage_inventory && $menuItem->stock < $item->quantity) {
                        $responseData['stock_issues'][] = [
                            'item_name' => $item->name,
                            'required' => $item->quantity,
                            'available' => $menuItem->stock
                        ];
                    }
                }
            }
            // ✅ VERIFICAR CAJA CHICA
            elseif ($canConvert) {
                $openPettyCash = \App\Models\PettyCash::where('status', 'open')->first();
                if (!$openPettyCash) {
                    $responseData['can_convert'] = false;
                    $responseData['reason'] = 'no_open_petty_cash';
                }
            }

            return response()->json($responseData, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proforma no encontrada',
                'error' => 'NOT_FOUND'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la proforma',
                'error' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * Marcar proforma como convertida
     * Ruta: POST /proformas/{id}/mark-converted
     */
    public function markAsConverted(Request $request, $id)
    {
        try {
            $request->validate([
                'order_id' => 'required|integer|exists:sales,id'
            ]);

            $proforma = Proforma::findOrFail($id);

            if ($proforma->isConverted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proforma ya fue convertida',
                    'order_id' => $proforma->converted_order_id
                ], 400);
            }

            // ✅ MARCAR COMO CONVERTIDA
            $proforma->update([
                'converted_to_order' => true,
                'converted_order_id' => $request->order_id,
                'status' => 'completed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proforma marcada como convertida exitosamente',
                'proforma_id' => $id,
                'order_id' => $request->order_id
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proforma no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar la proforma como convertida'
            ], 500);
        }
    }

    /**
     * 🔥 NUEVO: Crear proforma con descuento de stock
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validar datos
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
                'order_type' => 'required|string',
                'subtotal' => 'required|numeric',
                'tax' => 'required|numeric',
                'total' => 'required|numeric',
                'items' => 'required|array',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric',
                'items.*.quantity' => 'required|integer|min:1'
            ]);

            // ✅ VERIFICAR STOCK ANTES DE CREAR LA PROFORMA
            $stockIssues = [];
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::where('name', $item['name'])->first();
                
                if (!$menuItem) {
                    throw new \Exception("El ítem '{$item['name']}' no existe en el menú.");
                }

                // Verificar stock disponible si el item tiene gestión de inventario
                if ($menuItem->manage_inventory && $menuItem->stock < $item['quantity']) {
                    $stockIssues[] = [
                        'item' => $menuItem->name,
                        'available' => $menuItem->stock,
                        'required' => $item['quantity']
                    ];
                }
            }

            if (!empty($stockIssues)) {
                $errorMessage = "Stock insuficiente para los siguientes items:\n";
                foreach ($stockIssues as $issue) {
                    $errorMessage .= "• {$issue['item']}: Disponible {$issue['available']}, Requerido {$issue['required']}\n";
                }
                throw new \Exception($errorMessage);
            }

            // Crear la proforma
            $proforma = Proforma::create([
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'notes' => $validated['notes'],
                'order_type' => $validated['order_type'],
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'],
                'total' => $validated['total'],
                'status' => 'reservado',
                'user_id' => auth()->id(),
                'branch_id' => session('branch_id'),
            ]);

            // ✅ CREAR ITEMS Y DESCONTAR STOCK
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::where('name', $item['name'])->first();

                // Crear item de la proforma
                ProformaItem::create([
                    'proforma_id' => $proforma->id,
                    'menu_item_id' => $menuItem->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ]);

                // ✅ DESCONTAR STOCK si el item gestiona inventario
                if ($menuItem->manage_inventory) {
                    // Crear movimiento de inventario
                    InventoryMovement::create([
                        'menu_item_id' => $menuItem->id,
                        'user_id' => Auth::id(),
                        'movement_type' => 'subtraction',
                        'quantity' => $item['quantity'],
                        'old_stock' => $menuItem->stock,
                        'new_stock' => $menuItem->stock - $item['quantity'],
                        'notes' => "Proforma PROF-{$proforma->id} - Reserva para {$validated['customer_name']}" . 
                                   (session('branch_name') ? " - Sucursal: " . session('branch_name') : '')
                    ]);

                    // Actualizar stock
                    $menuItem->decrement('stock', $item['quantity']);
                    
                    \Log::info("Stock descontado para proforma", [
                        'menu_item' => $menuItem->name,
                        'quantity' => $item['quantity'],
                        'old_stock' => $menuItem->stock + $item['quantity'],
                        'new_stock' => $menuItem->stock,
                        'proforma_id' => $proforma->id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $proforma->id,
                'message' => 'Proforma creada correctamente y stock reservado'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear proforma:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la proforma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 NUEVO: Eliminar/Cancelar proforma y restaurar stock
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $proforma = Proforma::with('items.menuItem')->findOrFail($id);

            // Verificar que no esté convertida
            if ($proforma->converted_to_order) {
                throw new \Exception('No se puede eliminar una proforma que ya fue convertida a orden');
            }

            // ✅ RESTAURAR STOCK de todos los items
            foreach ($proforma->items as $item) {
                $menuItem = $item->menuItem;

                if ($menuItem && $menuItem->manage_inventory) {
                    // Crear movimiento de inventario (devolución)
                    InventoryMovement::create([
                        'menu_item_id' => $menuItem->id,
                        'user_id' => Auth::id(),
                        'movement_type' => 'addition',
                        'quantity' => $item->quantity,
                        'old_stock' => $menuItem->stock,
                        'new_stock' => $menuItem->stock + $item->quantity,
                        'notes' => "Cancelación de Proforma PROF-{$proforma->id} - Stock restaurado" .
                                   (session('branch_name') ? " - Sucursal: " . session('branch_name') : '')
                    ]);

                    // Restaurar stock
                    $menuItem->increment('stock', $item->quantity);

                    \Log::info("Stock restaurado por cancelación de proforma", [
                        'menu_item' => $menuItem->name,
                        'quantity' => $item->quantity,
                        'old_stock' => $menuItem->stock - $item->quantity,
                        'new_stock' => $menuItem->stock,
                        'proforma_id' => $proforma->id
                    ]);
                }
            }

            // Marcar como cancelada en lugar de eliminar físicamente
            $proforma->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proforma cancelada exitosamente y stock restaurado'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Proforma no encontrada'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al cancelar proforma:', [
                'error' => $e->getMessage(),
                'proforma_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function convertToOrder(Proforma $proforma)
    {
        // Verificar si la proforma puede ser convertida
        if (!$proforma->canBeConverted()) {
            return response()->json([
                'success' => false,
                'message' => $proforma->converted_to_order
                    ? 'Esta proforma ya fue convertida a orden anteriormente.'
                    : 'Esta proforma no puede ser convertida (posiblemente está cancelada).'
            ], 400);
        }

        // Verificar caja chica abierta
        $openPettyCash = PettyCash::where('status', 'open')->first();
        if (!$openPettyCash) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una caja chica abierta para registrar la venta.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Crear la nueva orden de venta
            $order = Sale::create([
                'user_id' => $proforma->user_id,
                'petty_cash_id' => $openPettyCash->id,
                'customer_name' => $proforma->customer_name,
                'phone' => $proforma->customer_phone,
                'order_type' => $proforma->order_type,
                'table_number' => $proforma->table_number,
                'subtotal' => $proforma->subtotal,
                'discount' => 0,
                'tax' => $proforma->tax,
                'total' => $proforma->total,
                'transaction_number' => Sale::generateTransactionNumber(),
                'payment_method' => 'Efectivo',
                'order_notes' => $proforma->notes,
            ]);

            // Copiar los items de la proforma a la orden
            foreach ($proforma->items as $proformaItem) {
                SaleItem::create([
                    'sale_id' => $order->id,
                    'name' => $proformaItem->name,
                    'quantity' => $proformaItem->quantity,
                    'price' => $proformaItem->price,
                    'total' => $proformaItem->price * $proformaItem->quantity,
                ]);
            }

            // Marcar la proforma como convertida
            $proforma->update([
                'converted_to_order' => true,
                'converted_order_id' => $order->id,
                'status' => 'completed'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proforma convertida a orden exitosamente. Número de orden: ' . $order->transaction_number,
                'order_number' => $order->transaction_number
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al convertir la proforma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si una proforma puede ser convertida
     * Ruta: GET /proformas/{id}/can-convert
     */
    public function canBeConverted($id)
    {
        try {
            $proforma = Proforma::with('items.menuItem')->findOrFail($id);

            // Verificar si ya fue convertida
            if ($proforma->order_id) {
                return response()->json([
                    'success' => false,
                    'can_convert' => false,
                    'reason' => 'already_converted',
                    'message' => 'Esta proforma ya fue convertida'
                ]);
            }

            // Verificar disponibilidad de stock
            $stockIssues = [];
            foreach ($proforma->items as $item) {
                $menuItem = $item->menuItem;

                if ($menuItem && $menuItem->manage_inventory) {
                    if ($menuItem->stock < $item->quantity) {
                        $stockIssues[] = [
                            'item_name' => $item->item_name,
                            'required' => $item->quantity,
                            'available' => $menuItem->stock
                        ];
                    }
                }
            }

            if (!empty($stockIssues)) {
                return response()->json([
                    'success' => false,
                    'can_convert' => false,
                    'reason' => 'insufficient_stock',
                    'message' => 'No hay suficiente stock para algunos items',
                    'stock_issues' => $stockIssues
                ]);
            }

            // Verificar si hay caja chica abierta
            $openPettyCash = \App\Models\PettyCash::where('status', 'Abierto')
                ->where('user_id', auth()->id())
                ->first();

            if (!$openPettyCash) {
                return response()->json([
                    'success' => false,
                    'can_convert' => false,
                    'reason' => 'no_open_petty_cash',
                    'message' => 'No hay caja chica abierta'
                ]);
            }

            return response()->json([
                'success' => true,
                'can_convert' => true,
                'message' => 'La proforma puede ser convertida'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'can_convert' => false,
                'reason' => 'server_error',
                'message' => 'Error al verificar la proforma'
            ], 500);
        }
    }

    /**
     * Listar proformas con filtros
     * Ruta: GET /proformas
     */
    public function index(Request $request)
    {
        try {
            $query = Proforma::with(['items', 'order'])
                ->orderBy('created_at', 'desc');

            // Filtrar por estado de conversión
            if ($request->has('converted')) {
                if ($request->converted === 'true') {
                    $query->where('converted_to_order', true);
                } else if ($request->converted === 'false') {
                    $query->where(function ($q) {
                        $q->where('converted_to_order', false)
                            ->orWhereNull('converted_to_order');
                    });
                }
            }

            // Filtrar por status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filtrar por fecha
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Búsqueda por cliente
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            // Paginación
            $perPage = $request->get('per_page', 15);
            $proformas = $query->paginate($perPage);

            // Si es request AJAX, retornar JSON
            if ($request->wantsJson() || $request->has('json')) {
                return response()->json([
                    'success' => true,
                    'proformas' => $proformas
                ]);
            }

            // Si no, retornar vista (si existe)
            if (view()->exists('proformas.index')) {
                return view('proformas.index', compact('proformas'));
            }

            return response()->json([
                'success' => true,
                'proformas' => $proformas
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener proformas'
                ], 500);
            }

            return back()->with('error', 'Error al cargar las proformas');
        }
    }
}