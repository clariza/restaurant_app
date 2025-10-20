@extends('layouts.app')
@section('content')
<div class="container mx-auto p-6">
    {{-- DEBUG: Descomentar para ver qué variables llegan --}}
    {{-- 
    <div class="bg-yellow-100 p-4 mb-4 rounded">
        <p><strong>DEBUG:</strong></p>
        <p>Previous Sale: {{ $previousSale ? $previousSale->id : 'NULL' }}</p>
        <p>Current Sale: {{ $sale->id }}</p>
        <p>Next Sale: {{ $nextSale ? $nextSale->id : 'NULL' }}</p>
    </div>
    --}}

    <!-- Navegación entre ventas -->
    <div class="flex justify-between items-center mb-6">
        {{-- @if(isset($previousSale) && $previousSale) --}}
            <a href="{{ route('sales.show', $previousSale->id) }}" 
               class="flex items-center px-4 py-2 bg-[#203363] text-white rounded-lg hover:bg-[#47517c] transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Venta Anterior 
            </a>
        {{-- @else --}}
            <span class="flex items-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                <i class="fas fa-arrow-left mr-2"></i> Sin ventas anteriores
            </span>
        {{-- @endif --}}

        <a href="{{ route('sales.index') }}" 
           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <i class="fas fa-list mr-2"></i> Volver al listado
        </a>

        {{-- @if(isset($nextSale) && $nextSale) --}}
            <a href="{{ route('sales.show', $nextSale->id) }}" 
               class="flex items-center px-4 py-2 bg-[#203363] text-white rounded-lg hover:bg-[#47517c] transition-colors">
                Venta Siguiente <i class="fas fa-arrow-right ml-2"></i>
            </a>
        {{-- @else --}}
            <span class="flex items-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                Sin ventas siguientes <i class="fas fa-arrow-right ml-2"></i>
            </span>
        {{-- @endif --}}
    </div>

    <h1 class="text-2xl font-bold mb-6">Detalles de la Venta #{{ $sale->id }}</h1>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Información del Cliente</h2>
        <p><strong>Nombre:</strong> {{ $sale->customer_name }}</p>
        <p><strong>Teléfono:</strong> {{ $sale->phone ?? 'No proporcionado' }}</p>
        <p><strong>Tipo de Pedido:</strong> {{ $sale->order_type }}</p>
        <p><strong>Total:</strong> ${{ number_format($sale->total, 2) }}</p>
        <p><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Vendedor:</strong> {{ $sale->user ? $sale->user->name : 'No asignado' }}</p>
        
        <h2 class="text-xl font-bold mt-6 mb-4">Ítems del Pedido</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left border border-gray-200 font-semibold">Producto</th>
                        <th class="px-6 py-3 text-left border border-gray-200 font-semibold">Cantidad</th>
                        <th class="px-6 py-3 text-left border border-gray-200 font-semibold">Precio Unitario</th>
                        <th class="px-6 py-3 text-left border border-gray-200 font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sale->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 border border-gray-200">{{ $item->name }}</td>
                            <td class="px-6 py-4 border border-gray-200">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 border border-gray-200">${{ number_format($item->price, 2) }}</td>
                            <td class="px-6 py-4 border border-gray-200">${{ number_format($item->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 border border-gray-200">
                                No hay ítems en esta venta
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection