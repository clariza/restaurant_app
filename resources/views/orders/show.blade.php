@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Navegación entre órdenes -->
    <div class="flex justify-between items-center mb-6">
        @if(isset($previousOrder) && $previousOrder)
            <a href="{{ route('orders.show', $previousOrder->id) }}" 
               class="flex items-center px-4 py-2 bg-[#203363] text-white rounded-lg hover:bg-[#47517c] transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Orden Anterior 
            </a>
        @else
            <span class="flex items-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                <i class="fas fa-arrow-left mr-2"></i> Sin órdenes anteriores
            </span>
        @endif

        <h1 class="text-2xl font-bold text-[#203363]">
            <i class="fas fa-receipt mr-2"></i> Detalle de Orden #{{ $order->transaction_number }}
        </h1>

        @if(isset($nextOrder) && $nextOrder)
            <a href="{{ route('orders.show', $nextOrder->id) }}" 
               class="flex items-center px-4 py-2 bg-[#203363] text-white rounded-lg hover:bg-[#47517c] transition-colors">
                Orden Siguiente <i class="fas fa-arrow-right ml-2"></i>
            </a>
        @else
            <span class="flex items-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                Sin órdenes siguientes <i class="fas fa-arrow-right ml-2"></i>
            </span>
        @endif
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end space-x-2 mb-6">
        <a href="{{ route('orders.index') }}" 
           class="bg-[#6380a6] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
            <i class="fas fa-list mr-2"></i> Volver al listado
        </a>
        
        <button onclick="window.print()" 
                class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
            <i class="fas fa-print mr-2"></i> Imprimir
        </button>
    </div>

    <!-- Datos de la orden -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 text-[#203363]">Información de la Orden</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500">Fecha</p>
                <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Tipo de Orden</p>
                <p class="font-medium">{{ $order->order_type }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Atendido por</p>
                <p class="font-medium">{{ $order->user->name }}</p>
            </div>

            @if($order->customer_name)
            <div>
                <p class="text-sm text-gray-500">Cliente</p>
                <p class="font-medium">{{ $order->customer_name }}</p>
            </div>
            @endif

            @if($order->phone)
            <div>
                <p class="text-sm text-gray-500">Teléfono</p>
                <p class="font-medium">{{ $order->phone }}</p>
            </div>
            @endif

            @if($order->table_number)
            <div>
                <p class="text-sm text-gray-500">Mesa</p>
                <p class="font-medium">{{ $order->table_number }}</p>
            </div>
            @endif
        </div>
        
        <!-- Items de la orden -->
        <h2 class="text-xl font-bold mb-4 text-[#203363]">Ítems del Pedido</h2>
        <div class="border rounded-lg overflow-hidden mb-6">
            <table class="w-full">
                <thead class="bg-[#203363] text-white">
                    <tr>
                        <th class="text-left p-3">Producto</th>
                        <th class="text-right p-3">Cantidad</th>
                        <th class="text-right p-3">Precio</th>
                        <th class="text-right p-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $item->menuItem->name }}</td>
                        <td class="text-right p-3">{{ $item->quantity }}</td>
                        <td class="text-right p-3">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right p-3">${{ number_format($item->quantity * $item->price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="text-right p-3 font-bold text-lg">Total:</td>
                        <td class="text-right p-3 font-bold text-lg text-[#203363]">
                            ${{ number_format($order->total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($order->order_notes)
        <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
            <p class="text-sm text-gray-500 mb-1">Notas del pedido:</p>
            <p class="text-gray-700">{{ $order->order_notes }}</p>
        </div>
        @endif
    </div>
</div>

<!-- Estilos para impresión -->
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection