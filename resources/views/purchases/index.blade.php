@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Encabezado con título y botón -->
    <div class="mb-6">
        <h1 class="text-xl font-bold mb-4 text-[var(--primary-color)] relative pb-2 section-title">
            Lista de Compras
            <span class="absolute bottom-0 left-0 w-10 h-1 bg-[var(--primary-color)] rounded"></span>
        </h1>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('purchases.create') }}" 
               class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--primary-light)] transition duration-200 inline-flex items-center
                      @unless($hasOpenPettyCash) opacity-50 cursor-not-allowed @endunless"
               @unless($hasOpenPettyCash) onclick="return false;" @endunless>
                <i class="fas fa-plus-circle mr-2"></i>Registrar Compra
            </a>
            
            @unless($hasOpenPettyCash)
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-lg">
                    <p class="font-medium text-sm"><i class="fas fa-exclamation-circle mr-2"></i>No hay caja chica abierta</p>
                </div>
            @endunless
        </div>
    </div>
    
    <!-- Tabla de compras -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)]">
        <table class="min-w-full divide-y divide-[var(--gray-light)]">
            <thead class="bg-[var(--primary-color)]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Proveedor</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-[var(--gray-light)]">
                @foreach($purchases as $purchase)
                <tr class="hover:bg-[var(--background-color)] transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--text-color)]">{{ $purchase->product }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[var(--text-color)]">S/ {{ number_format($purchase->price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--text-color)]">{{ $purchase->quantity }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--text-light)]">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--text-color)]">{{ $purchase->supplier->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('purchases.edit', $purchase->id) }}" 
                           class="text-[var(--blue)] hover:text-[var(--primary-light)] mr-3 transition duration-200">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-[var(--red)] hover:text-[var(--primary-light)] transition duration-200"
                                    onclick="return confirm('¿Estás seguro de eliminar esta compra?')">
                                <i class="fas fa-trash-alt mr-1"></i>Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection