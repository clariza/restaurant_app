@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Lista de Ventas</h1>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[#203363] text-white">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Cliente</th>
                    <th class="px-6 py-3 text-left">Teléfono</th>
                    <th class="px-6 py-3 text-left">Tipo de Pedido</th>
                    <th class="px-6 py-3 text-left">Vendedor</th>
                    <th class="px-6 py-3 text-left">Total</th>
                    <th class="px-6 py-3 text-left">Fecha</th>
                    <th class="px-6 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($sales as $sale)
                    <tr>
                        <td class="px-6 py-4">{{ $sale->id }}</td>
                        <td class="px-6 py-4">{{ $sale->customer_name }}</td>
                        <td class="px-6 py-4">{{ $sale->phone }}</td>
                        <td class="px-6 py-4">{{ $sale->order_type }}</td>
                        <td class="px-6 py-4">{{ $sale->user?->name ?? 'No asignado' }}</td>
                        <td class="px-6 py-4">${{ number_format($sale->total, 2) }}</td>
                        <td class="px-6 py-4">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-500 hover:text-blue-700">Ver Detalles</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection