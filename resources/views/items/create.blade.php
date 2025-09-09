@extends('layouts.app')

@section('content')
<div class="container mx-auto pt-0 px-6 pb-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Crear Producto</h1>
    <form action="{{ route('items.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-[var(--table-data-color)]">Nombre</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-[var(--table-data-color)]">Descripción</label>
            <textarea name="description" id="description" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm"></textarea>
        </div>
        
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-[var(--table-data-color)]">Precio</label>
            <input type="number" step="0.01" name="price" id="price" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-[var(--table-data-color)]">Categoría</label>
            <select name="category_id" id="category_id" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                <option value="">Seleccionar categoría</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-[var(--table-data-color)]">URL de la Imagen</label>
            <input type="text" name="image" id="image" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
        </div>
        
        <!-- Nueva sección para gestión de inventario -->
        <div class="mb-6 p-4 border border-[var(--tertiary-color)] rounded-md bg-gray-50">
            <h3 class="text-lg font-medium text-[var(--primary-color)] mb-4">Configuración de Inventario</h3>
            
            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="manage_inventory" id="manage_inventory" value="1" 
                           class="h-4 w-4 text-[var(--primary-color)] focus:ring-[var(--primary-color)] border-gray-300 rounded">
                    <label for="manage_inventory" class="ml-2 block text-sm text-[var(--table-data-color)]">
                        <strong>Gestionar inventario para este producto</strong>
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Si está marcado, podrás realizar movimientos de inventario para este producto
                </p>
            </div>
            
            <!-- Campos de inventario que se muestran/ocultan según el checkbox -->
            <div id="inventory-fields" class="space-y-4" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="stock" class="block text-sm font-medium text-[var(--table-data-color)]">Stock Inicial</label>
                        <input type="number" step="0.01" name="stock" id="stock" value="0" 
                               class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="min_stock" class="block text-sm font-medium text-[var(--table-data-color)]">Stock Mínimo</label>
                        <input type="number" step="0.01" name="min_stock" id="min_stock" value="5" 
                               class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="stock_type" class="block text-sm font-medium text-[var(--table-data-color)]">Tipo de Stock</label>
                        <select name="stock_type" id="stock_type" 
                                class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                            <option value="discrete">Discreto (unidades)</option>
                            <option value="continuous">Continuo (peso/volumen)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="stock_unit" class="block text-sm font-medium text-[var(--table-data-color)]">Unidad de Medida</label>
                        <input type="text" name="stock_unit" id="stock_unit" value="unidades" 
                               class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Guardar
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const manageInventoryCheckbox = document.getElementById('manage_inventory');
    const inventoryFields = document.getElementById('inventory-fields');
    const stockTypeSelect = document.getElementById('stock_type');
    const stockUnitInput = document.getElementById('stock_unit');
    
    // Mostrar/ocultar campos de inventario
    manageInventoryCheckbox.addEventListener('change', function() {
        if (this.checked) {
            inventoryFields.style.display = 'block';
        } else {
            inventoryFields.style.display = 'none';
        }
    });
    
    // Cambiar unidad de medida automáticamente según el tipo de stock
    stockTypeSelect.addEventListener('change', function() {
        if (this.value === 'discrete') {
            stockUnitInput.value = 'unidades';
        } else {
            stockUnitInput.value = 'gr/ml';
        }
    });
});
</script>
@endsection