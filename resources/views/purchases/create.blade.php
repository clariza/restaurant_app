@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Registrar Compra</h1>

    <!-- Formulario para crear una compra -->
    <form action="{{ route('purchases.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        <div class="mb-4">
            <label for="supplier_id" class="block text-sm font-medium text-[var(--table-data-color)]">Proveedor</label>
            <select name="supplier_id" id="supplier_id" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="product" class="block text-sm font-medium text-[var(--table-data-color)]">Producto</label>
            <input type="text" name="product" id="product" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-[var(--table-data-color)]">Precio</label>
            <input type="number" step="0.01" name="price" id="price" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="quantity" class="block text-sm font-medium text-[var(--table-data-color)]">Cantidad</label>
            <input type="number" name="quantity" id="quantity" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="purchase_date" class="block text-sm font-medium text-[var(--table-data-color)]">Fecha de Compra</label>
            <input type="date" name="purchase_date" id="purchase_date" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Guardar
            </button>
        </div>
    </form>
</div>
@endsection