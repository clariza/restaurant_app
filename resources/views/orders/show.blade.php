@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#203363]">
            <i class="fas fa-receipt mr-2"></i> Detalle de Orden #{{ $order->transaction_number }}
        </h1>
        
        <div class="flex space-x-2">
            <a href="{{ route('orders.index') }}" 
               class="bg-[#6380a6] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            
            <button onclick="window.print()" 
                    class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Datos de la orden -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500">Fecha</p>
                <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Tipo</p>
                <p class="font-medium">{{ $order->order_type }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Atendido por</p>
                <p class="font-medium">{{ $order->user->name }}</p>
            </div>
        </div>
        
        <!-- Items de la orden -->
        <div class="border rounded-lg overflow-hidden mb-6">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-3">Producto</th>
                        <th class="text-right p-3">Cantidad</th>
                        <th class="text-right p-3">Precio</th>
                        <th class="text-right p-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-b">
                        <td class="p-3">{{ $item->menuItem->name }}</td>
                        <td class="text-right p-3">{{ $item->quantity }}</td>
                        <td class="text-right p-3">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right p-3">${{ number_format($item->quantity * $item->price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="text-right p-3 font-bold">Total:</td>
                        <td class="text-right p-3 font-bold">${{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection