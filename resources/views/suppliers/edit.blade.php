@extends('layouts.app')
@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Editar Proveedor</h1>
    
    <!-- Formulario para editar un proveedor -->
    <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')
        
        <!-- Nombre -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-[var(--table-data-color)]">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" 
                   class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" 
                   value="{{ old('name', $supplier->name) }}" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- NIT -->
        <div class="mb-4">
            <label for="nit" class="block text-sm font-medium text-[var(--table-data-color)]">
                NIT <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nit" id="nit" 
                   class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" 
                   value="{{ old('nit', $supplier->nit) }}" required>
            @error('nit')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Dirección -->
        <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-[var(--table-data-color)]">
                Dirección <span class="text-red-500">*</span>
            </label>
            <textarea name="address" id="address" rows="2"
                      class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm resize-vertical" 
                      placeholder="Ingrese la dirección completa del proveedor" required>{{ old('address', $supplier->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Contacto -->
        <div class="mb-4">
            <label for="contact" class="block text-sm font-medium text-[var(--table-data-color)]">
                Contacto <span class="text-red-500">*</span>
            </label>
            <input type="text" name="contact" id="contact" 
                   class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" 
                   placeholder="Nombre de la persona de contacto" 
                   value="{{ old('contact', $supplier->contact) }}" required>
            @error('contact')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Teléfono -->
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-[var(--table-data-color)]">
                Teléfono <span class="text-red-500">*</span>
            </label>
            <input type="tel" name="phone" id="phone" 
                   class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" 
                   placeholder="Ej: +591 70123456" 
                   value="{{ old('phone', $supplier->phone) }}" required>
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Notas -->
        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-[var(--table-data-color)]">
                Notas
                <span class="text-gray-500 text-xs">(Opcional)</span>
            </label>
            <textarea name="notes" id="notes" rows="4"
                      class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm resize-vertical" 
                      placeholder="Agregue cualquier información adicional sobre el proveedor (términos de pago, observaciones, etc.)">{{ old('notes', $supplier->notes) }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Este campo es opcional y puede incluir información adicional como términos de pago, condiciones especiales, etc.</p>
        </div>
        
        <!-- Botones -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('suppliers.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition duration-200">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            <button type="submit" 
                    class="bg-[var(--primary-color)] text-white px-6 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Actualizar Proveedor
            </button>
        </div>
    </form>
</div>
@endsection