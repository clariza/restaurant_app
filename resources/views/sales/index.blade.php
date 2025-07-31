@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Encabezado con título -->
    <div class="mb-6">
        <h1 class="text-xl font-bold mb-4 text-[#203363] relative pb-2 section-title">
            Lista de Ventas
            <span class="absolute bottom-0 left-0 w-10 h-1 bg-[#203363] rounded"></span>
        </h1>
    </div>

    <!-- Tabla de ventas -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#203363]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Teléfono</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Tipo de Pedido</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Vendedor</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($sales as $sale)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $sale->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $sale->customer_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $sale->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $sale->order_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $sale->user?->name ?? 'No asignado' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">${{ number_format($sale->total, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('sales.show', $sale->id) }}" 
                               class="text-white bg-[#203363] hover:bg-[#2a4283] px-3 py-1 rounded-md text-sm font-medium transition duration-200 inline-flex items-center">
                                <i class="fas fa-eye mr-1.5 text-xs"></i>Ver Detalles
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection