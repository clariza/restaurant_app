@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Título con estilo consistente -->
    <h1 class="text-xl font-bold mb-6 text-[var(--primary-color)] relative pb-2 section-title">
        Editar Compra
        <span class="absolute bottom-0 left-0 w-10 h-1 bg-[var(--primary-color)] rounded"></span>
    </h1>
    
    <!-- Formulario con estilos del sistema -->
    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')
        
        <!-- Grid de 2 columnas para pantallas medianas/grandes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Proveedor -->
            <div class="mb-4">
                <label for="supplier_id" class="input-label">Proveedor</label>
                <select name="supplier_id" id="supplier_id" class="modal-input" required>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Producto -->
            <div class="mb-4">
                <label for="product" class="input-label">Producto</label>
                <input type="text" name="product" id="product" 
                       class="modal-input" 
                       placeholder="Ingrese el nombre del producto"
                       value="{{ old('product', $purchase->product) }}"
                       required>
            </div>
            
            <!-- Precio -->
            <div class="mb-4">
                <label for="price" class="input-label">Precio (S/)</label>
                <input type="number" name="price" id="price" step="0.01" 
                       class="modal-input" 
                       placeholder="0.00"
                       value="{{ old('price', $purchase->price) }}"
                       required>
            </div>
            
            <!-- Cantidad -->
            <div class="mb-4">
                <label for="quantity" class="input-label">Cantidad</label>
                <input type="number" name="quantity" id="quantity" 
                       class="modal-input" 
                       placeholder="Ingrese la cantidad"
                       value="{{ old('quantity', $purchase->quantity) }}"
                       required>
            </div>
            
            <!-- Fecha de Compra (mantenemos editable pero oculto por defecto) -->
            <div class="mb-4">
                <label for="purchase_date" class="input-label">Fecha de Compra</label>
                <input type="date" name="purchase_date" id="purchase_date" 
                       class="modal-input"
                       value="{{ old('purchase_date', \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d')) }}"
                       required>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="flex justify-end mt-6 space-x-4">
            <!-- Botón Volver Atrás -->
            <a href="{{ route('purchases.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Volver Atrás
            </a>
            
            <!-- Botón Guardar Cambios -->
            <button type="submit" 
                    class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--primary-light)] transition duration-200 inline-flex items-center">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection