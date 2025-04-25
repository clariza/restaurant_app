@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4 text-[#203363]">Detalles del Cierre de Caja Chica</h2>
    <div class="mb-4">
        <p><strong>Fecha:</strong> {{ $pettyCash->date }}</p>
        <p><strong>Monto Inicial:</strong> ${{ number_format($pettyCash->initial_amount, 2) }}</p>
        <p><strong>Monto Actual:</strong> ${{ number_format($pettyCash->current_amount, 2) }}</p>
        <p><strong>Notas:</strong> {{ $pettyCash->notes }}</p>
    </div>
    <a href="{{ route('petty-cash.index') }}" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">Volver</a>
</div>
@endsection