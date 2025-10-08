@extends('layouts.app')

@section('content')
<div class="container mx-auto pt-0 px-6 pb-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Editar Producto</h1>
    <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-[var(--table-data-color)]">Nombre</label>
            <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" 
                   class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-[var(--table-data-color)]">Descripción</label>
            <textarea name="description" id="description" 
                      class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">{{ old('description', $item->description) }}</textarea>
        </div>
        
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-[var(--table-data-color)]">Precio</label>
            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $item->price) }}" 
                   class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-[var(--table-data-color)]">Categoría</label>
            <select name="category_id" id="category_id" 
                    class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('category_id', $item->category_id) == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Sección mejorada para imagen -->
        <div class="mb-6 p-4 border border-[var(--tertiary-color)] rounded-md bg-gray-50">
            <h3 class="text-lg font-medium text-[var(--primary-color)] mb-4">Imagen del Producto</h3>
            
            <!-- Imagen actual -->
            @if($item->image)
            <div class="mb-4 p-3 bg-white border border-[var(--tertiary-color)] rounded-md">
                <label class="block text-sm font-medium text-[var(--table-data-color)] mb-2">Imagen actual</label>
                <div class="flex items-center gap-4">
                    @if(filter_var($item->image, FILTER_VALIDATE_URL))
                        <img src="{{ $item->image }}" alt="{{ $item->name }}" class="h-24 w-24 object-cover rounded border">
                        <div class="text-sm text-gray-600">
                            <p class="font-medium">Tipo: URL externa</p>
                            <p class="text-xs text-gray-500 break-all">{{ Str::limit($item->image, 50) }}</p>
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="h-24 w-24 object-cover rounded border">
                        <div class="text-sm text-gray-600">
                            <p class="font-medium">Tipo: Archivo local</p>
                            <p class="text-xs text-gray-500">{{ basename($item->image) }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-[var(--table-data-color)] mb-2">
                    {{ $item->image ? 'Cambiar imagen (dejar vacío para mantener la actual)' : 'Agregar imagen' }}
                </label>
                <div class="flex gap-4">
                    <div class="flex items-center">
                        <input type="radio" name="image_method" id="method_upload" value="upload" checked
                               class="h-4 w-4 text-[var(--primary-color)] focus:ring-[var(--primary-color)] border-gray-300">
                        <label for="method_upload" class="ml-2 block text-sm text-[var(--table-data-color)]">
                            Subir nueva imagen
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="image_method" id="method_url" value="url"
                               class="h-4 w-4 text-[var(--primary-color)] focus:ring-[var(--primary-color)] border-gray-300">
                        <label for="method_url" class="ml-2 block text-sm text-[var(--table-data-color)]">
                            URL de imagen
                        </label>
                    </div>
                </div>
            </div>

            <!-- Campo para subir imagen -->
            <div id="upload-section" class="mb-4" style="display: none;">
                <label for="image_file" class="block text-sm font-medium text-[var(--table-data-color)] mb-2">
                    Seleccionar nueva imagen desde tu computadora
                </label>
                <div class="mt-1 flex items-center gap-4">
                    <label for="image_file" class="cursor-pointer bg-white px-4 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm text-sm font-medium text-[var(--table-data-color)] hover:bg-gray-50 focus:outline-none">
                        <i class="fas fa-upload mr-2"></i>Elegir archivo
                    </label>
                    <input type="file" name="image_file" id="image_file" accept="image/*" class="hidden">
                    <span id="file-name" class="text-sm text-gray-500">Ningún archivo seleccionado</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                </p>
            </div>

            <!-- Campo para URL de imagen -->
            <div id="url-section" class="mb-4" style="display: none;">
                <label for="image_url" class="block text-sm font-medium text-[var(--table-data-color)]">URL de la Imagen</label>
                <input type="text" name="image_url" id="image_url" 
                       class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm"
                       placeholder="https://ejemplo.com/imagen.jpg">
                <p class="mt-1 text-xs text-gray-500">
                    Ingresa la URL completa de la imagen
                </p>
            </div>

            <!-- Vista previa de la nueva imagen -->
            <div id="image-preview" class="mt-4" style="display: none;">
                <label class="block text-sm font-medium text-[var(--table-data-color)] mb-2">Vista previa de la nueva imagen</label>
                <div class="border-2 border-dashed border-[var(--tertiary-color)] rounded-md p-4 text-center bg-white">
                    <img id="preview-img" src="" alt="Vista previa" class="max-h-48 mx-auto rounded">
                </div>
            </div>
        </div>
        
        <!-- Sección para gestión de inventario -->
        <div class="mb-6 p-4 border border-[var(--tertiary-color)] rounded-md bg-gray-50">
            <h3 class="text-lg font-medium text-[var(--primary-color)] mb-4">Configuración de Inventario</h3>
            
            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="manage_inventory" id="manage_inventory" value="1" 
                           {{ old('manage_inventory', $item->manage_inventory) ? 'checked' : '' }}
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
            <div id="inventory-fields" class="space-y-4" style="display: {{ old('manage_inventory', $item->manage_inventory) ? 'block' : 'none' }};">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="stock" class="block text-sm font-medium text-[var(--table-data-color)]">Stock Actual</label>
                        <input type="number" step="0.01" name="stock" id="stock" 
                               value="{{ old('stock', $item->stock ?? 0) }}" 
                               class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">
                            Cantidad actual disponible en inventario
                        </p>
                    </div>
                    
                    <div>
                        <label for="min_stock" class="block text-sm font-medium text-[var(--table-data-color)]">Stock Mínimo</label>
                        <input type="number" step="0.01" name="min_stock" id="min_stock" 
                               value="{{ old('min_stock', $item->min_stock ?? 5) }}" 
                               class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">
                            Nivel de alerta para reabastecimiento
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="stock_type" class="block text-sm font-medium text-[var(--table-data-color)]">Tipo de Stock</label>
                        <select name="stock_type" id="stock_type" 
                                class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                            <option value="discrete" {{ old('stock_type', $item->stock_type) == 'discrete' ? 'selected' : '' }}>
                                Discreto (unidades)
                            </option>
                            <option value="continuous" {{ old('stock_type', $item->stock_type) == 'continuous' ? 'selected' : '' }}>
                                Continuo (peso/volumen)
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="stock_unit" class="block text-sm font-medium text-[var(--table-data-color)]">Unidad de Medida</label>
                        <input type="text" name="stock_unit" id="stock_unit" 
                               value="{{ old('stock_unit', $item->stock_unit ?? 'unidades') }}" 
                               class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                    </div>
                </div>
                
                @if($item->manage_inventory && $item->stock < $item->min_stock)
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-300 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Stock bajo</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                El stock actual ({{ $item->stock }}) está por debajo del mínimo ({{ $item->min_stock }}). 
                                Considera reabastecer pronto.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="flex justify-end gap-3">
            <a href="{{ route('items.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            <button type="submit" 
                    class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Actualizar
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
    
    // Elementos para gestión de imágenes
    const methodKeep = document.getElementById('method_keep');
    const methodUpload = document.getElementById('method_upload');
    const methodUrl = document.getElementById('method_url');
    const uploadSection = document.getElementById('upload-section');
    const urlSection = document.getElementById('url-section');
    const imageFile = document.getElementById('image_file');
    const imageUrl = document.getElementById('image_url');
    const fileName = document.getElementById('file-name');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    // Mostrar/ocultar campos de inventario
    manageInventoryCheckbox.addEventListener('change', function() {
        inventoryFields.style.display = this.checked ? 'block' : 'none';
    });
    
    // Cambiar unidad de medida automáticamente según el tipo de stock
    stockTypeSelect.addEventListener('change', function() {
        if (this.value === 'discrete') {
            stockUnitInput.value = 'unidades';
        } else {
            stockUnitInput.value = 'gr/ml';
        }
    });
    
    // Cambiar entre métodos de imagen
    if (methodKeep) {
        methodKeep.addEventListener('change', function() {
            if (this.checked) {
                uploadSection.style.display = 'none';
                urlSection.style.display = 'none';
                imagePreview.style.display = 'none';
                imageFile.value = '';
                imageUrl.value = '';
                fileName.textContent = 'Ningún archivo seleccionado';
            }
        });
    }
    
    methodUpload.addEventListener('change', function() {
        if (this.checked) {
            uploadSection.style.display = 'block';
            urlSection.style.display = 'none';
            imageUrl.value = '';
            imagePreview.style.display = 'none';
        }
    });
    
    methodUrl.addEventListener('change', function() {
        if (this.checked) {
            uploadSection.style.display = 'none';
            urlSection.style.display = 'block';
            imageFile.value = '';
            fileName.textContent = 'Ningún archivo seleccionado';
            imagePreview.style.display = 'none';
        }
    });
    
    // Mostrar nombre del archivo seleccionado y vista previa
    imageFile.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            fileName.textContent = file.name;
            
            // Mostrar vista previa
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            fileName.textContent = 'Ningún archivo seleccionado';
            imagePreview.style.display = 'none';
        }
    });
    
    // Vista previa para URL
    imageUrl.addEventListener('blur', function() {
        if (this.value) {
            previewImg.src = this.value;
            imagePreview.style.display = 'block';
            
            // Ocultar vista previa si la imagen no carga
            previewImg.onerror = function() {
                imagePreview.style.display = 'none';
            };
        } else {
            imagePreview.style.display = 'none';
        }
    });
});
</script>
@endsection