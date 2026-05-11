@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-[#203363]">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Detalle de Proforma
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
            <a href="{{ route('proformas.print', $proforma->id) }}" target="_blank" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
                <i class="fas fa-print mr-2"></i>Imprimir
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Proforma</p>
                <p class="font-semibold">PROF-{{ $proforma->id }}</p>
            </div>
            <div>
                <p class="text-gray-500">Fecha</p>
                <p class="font-semibold">{{ $proforma->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Estado</p>
                <p>
                    <span class="px-2 py-1 rounded-full text-xs {{ $isConverted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $isConverted ? 'Convertida' : 'Pendiente' }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-gray-500">Cliente</p>
                <p class="font-semibold">{{ $proforma->customer_name ?: 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Teléfono</p>
                <p class="font-semibold">{{ $proforma->customer_phone ?: 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Tipo</p>
                <p class="font-semibold">{{ $proforma->order_type ?: 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-[#203363] text-white px-4 py-3 font-semibold">Items</div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="text-left px-4 py-2">Producto</th>
                    <th class="text-right px-4 py-2">Cant.</th>
                    <th class="text-right px-4 py-2">Precio</th>
                    <th class="text-right px-4 py-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proforma->items as $item)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $item->name }}</td>
                    <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-right">Bs {{ number_format($item->price, 2) }}</td>
                    <td class="px-4 py-2 text-right">Bs {{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Sin items</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="max-w-sm ml-auto space-y-2 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>Bs {{ number_format($proforma->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span>Impuesto</span><span>Bs {{ number_format($proforma->tax, 2) }}</span></div>
            <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>Bs {{ number_format($proforma->total, 2) }}</span></div>
        </div>
        @if($proforma->notes)
        <div class="mt-4 text-sm">
            <p class="text-gray-500 mb-1">Notas</p>
            <p>{{ $proforma->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
