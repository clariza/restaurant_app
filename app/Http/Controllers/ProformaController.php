<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proforma;
use App\Models\ProformaItem;
use Illuminate\Support\Facades\DB;
use App\Models\PettyCash;
use App\Models\Sale;
use App\Models\SaleItem;

class ProformaController extends Controller
{
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
            ]);

            // Crear items de la proforma
            foreach ($validated['items'] as $item) {
                ProformaItem::create([
                    'proforma_id' => $proforma->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $proforma->id,
                'message' => 'Proforma creada correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la proforma: ' . $e->getMessage()
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
}
