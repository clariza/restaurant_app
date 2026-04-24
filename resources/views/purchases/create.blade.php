@extends('layouts.app')
@section('content')
<div class="container mx-auto p-6">
    <!-- Mensajes flash -->
    @if(session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 transition-all duration-500 ease-in-out">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('success-alert').remove()">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
    @endif
    @if(session('error'))
    <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 transition-all duration-500 ease-in-out">
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('error-alert').remove()">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
    @endif
   <div class="mb-6">
        <h1 class="text-xl font-bold mb-4 text-[var(--primary-color)] relative pb-2 section-title">
            Agregar Compra
        </h1>
    </div>
  
    <!-- Formulario de compra -->
    <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form" class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6">
    @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Proveedor -->
            <div>
                <label for="proveedor" class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                    Proveedor: <span class="text-[var(--red)]">*</span>
                </label>
                <div class="flex items-center border border-[var(--gray-light)] rounded text-[var(--text-color)] text-sm">
                    <span class="px-3 border-r border-[var(--gray-light)]">
                        <i class="fas fa-user"></i>
                    </span>
                    <select id="proveedor" name="supplier_id" class="flex-grow py-2 px-3 focus:outline-none bg-transparent" aria-required="true">
                        <option value="">Seleccione</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                    data-nit="{{ $supplier->nit }}" 
                                    data-address="{{ $supplier->address }}">
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('suppliers.create') }}" class="ml-2 mr-1 text-[var(--blue)] hover:text-[var(--primary-light)] focus:outline-none transition duration-200">
                        <i class="fas fa-plus-circle fa-lg"></i>
                    </a>
                </div>
                <p class="mt-4 text-xs font-semibold text-[var(--text-light)]">
                    Dirección: <span id="supplier-address" class="font-normal text-[var(--text-color)]">-</span>
                </p>
            </div>

            <!-- Sucursal -->
            <div>
                <label for="sucursal" class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                    Sucursal: <span class="text-[var(--red)]">*</span>
                </label>
                <div class="flex items-center border border-[var(--gray-light)] rounded text-[var(--text-color)] text-sm">
                    <span class="px-3 border-r border-[var(--gray-light)]">
                        <i class="fas fa-store"></i>
                    </span>
                    <select id="sucursal" name="branch_id" class="flex-grow py-2 px-3 focus:outline-none bg-transparent" aria-required="true">
                        <option value="">Seleccione</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" 
                                    {{ (isset($activeBranchId) && (int) $activeBranchId === (int) $branch->id) || (!isset($activeBranchId) && $branch->is_main) ? 'selected' : '' }}
                                    data-city="{{ $branch->city }}" 
                                    data-address="{{ $branch->address }}">
                                {{ $branch->name }} {{ $branch->is_main ? '(Principal)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('branches.create') }}" class="ml-2 mr-1 text-[var(--blue)] hover:text-[var(--primary-light)] focus:outline-none transition duration-200">
                        <i class="fas fa-plus-circle fa-lg"></i>
                    </a>
                    @endif
                </div>
                <p class="mt-4 text-xs font-semibold text-[var(--text-light)]">
                    Ciudad: <span id="branch-city" class="font-normal text-[var(--text-color)]">{{ optional($branches->firstWhere('id', $activeBranchId ?? null) ?? $branches->where('is_main', true)->first())->city ?? '-' }}</span>
                </p>
            </div>

            <!-- NIT -->
            <div>
                <label for="nit" class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                    NIT:
                </label>
                <div class="flex items-center border border-[var(--gray-light)] rounded text-[var(--text-color)] text-sm bg-gray-50">
                    <span class="px-3 border-r border-[var(--gray-light)]">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input type="text" id="nit" 
                           class="flex-grow py-2 px-3 focus:outline-none bg-transparent" 
                           readonly 
                           placeholder="-">
                </div>
            </div>

            <!-- Numero de referencia -->
            <div>
                <label for="numeroReferencia" class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                    Número de referencia:
                    <span class="inline-block text-[var(--blue)] text-xs font-bold cursor-pointer" title="Información adicional">
                        <i class="fas fa-info-circle"></i>
                    </span>
                </label>
                <input type="text" id="numeroReferencia" name="reference_number" 
                       class="w-full border border-[var(--gray-light)] rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--primary-color)]">
            </div>

            <!-- Fecha de compra -->
            <div>
                <label for="fechaCompra" class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                    Fecha de compra: <span class="text-[var(--red)]">*</span>
                </label>
                <div class="flex items-center border border-[var(--gray-light)] rounded text-[var(--text-color)] text-sm">
                    <span class="px-3 border-r border-[var(--gray-light)]">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="datetime-local" id="fechaCompra" name="purchase_date" 
                           value="{{ now()->format('Y-m-d\TH:i') }}" 
                           class="flex-grow py-2 px-3 focus:outline-none bg-transparent" aria-required="true">
                </div>
            </div>
        </div>

        <!-- Sección de productos -->
        <div class="border border-[var(--primary-color)] rounded-md p-4 mb-6">
            <div class="flex flex-col md:flex-row items-center md:items-stretch justify-between gap-4 mb-4">
                <div class="flex items-center border border-[var(--gray-light)] rounded flex-grow max-w-lg relative" id="search-container">
                    <button type="button" class="px-3 text-[var(--text-light)] hover:text-[var(--text-color)] focus:outline-none transition duration-200">
                        <i class="fas fa-search"></i>
                    </button>
                    <input type="search" id="product-search" placeholder="Buscar producto por nombre o descripción" 
                           class="flex-grow px-3 py-2 text-sm text-[var(--text-color)] placeholder-[var(--text-light)] focus:outline-none"
                           autocomplete="off">
                    <div id="search-results" class="absolute z-20 top-full left-0 right-0 mt-1 bg-white shadow-lg rounded-md hidden max-h-60 overflow-y-auto border border-gray-200"></div>
                </div>
                <button type="button" id="open-product-modal-btn" class="text-[var(--primary-color)] hover:text-[var(--primary-light)] text-sm font-normal flex items-center space-x-1 transition duration-200">
    <i class="fas fa-plus"></i>
    <span>Agregar nuevo producto</span>
</button>
            </div>

            <!-- Tabla actualizada con nuevas columnas -->
            <div class="overflow-x-auto rounded-lg border border-[var(--gray-light)] shadow-sm">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-gradient-to-r from-[var(--primary-color)] to-[var(--primary-light)] text-white">
                            <th class="px-3 py-3 text-left font-semibold border-r border-white/20 min-w-[200px]">
                                <i class="fas fa-box mr-2"></i>PRODUCTO
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-sort-numeric-up mr-1"></i>CANTIDAD
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-dollar-sign mr-1"></i>COSTO UNITARIO<br/>(ANTES DE DESC.)
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-percentage mr-1"></i>DESCUENTO
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-dollar-sign mr-1"></i>DESCUENTO<br/>(Bs.)
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-dollar-sign mr-1"></i>COSTO UNITARIO<br/>(DESPUÉS DE DESC.)
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20">
                                <i class="fas fa-calculator mr-1"></i>TOTAL
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-chart-line mr-1"></i>MARGEN DE<br/>UTILIDAD
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-tag mr-1"></i>PRECIO DE<br/>VENTA
                            </th>
                            <th class="px-3 py-3 text-center font-semibold border-r border-white/20 whitespace-nowrap">
                                <i class="fas fa-calendar-times mr-1"></i>FECHA DE<br/>CADUCIDAD
                            </th>
                            <th class="px-3 py-3 text-center font-semibold w-24">
                                <i class="fas fa-cog"></i> ACCIONES
                            </th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body" class="divide-y divide-[var(--gray-light)]">
                        <!-- Fila vacía con mensaje inicial -->
                        <tr id="empty-table-message">
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-box-open text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">No hay productos agregados</p>
                                    <button type="button" id="add-empty-row-btn" 
                                            class="bg-[var(--primary-color)] hover:bg-[var(--primary-light)] text-white px-6 py-2 rounded-lg transition duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>Agregar Producto</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Botón flotante para agregar más productos -->
           <div id="add-product-footer" class="hidden mt-6 pt-4 border-t-2 border-dashed border-[var(--gray-light)] flex justify-end">
    <button type="button" id="add-product-btn" 
            class="bg-gradient-to-r from-[var(--primary-color)] to-[var(--primary-light)] hover:from-[var(--primary-light)] hover:to-[var(--primary-color)] text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center space-x-2 shadow-md hover:shadow-xl transform hover:scale-105">
        <i class="fas fa-plus-circle text-lg"></i>
        <span class="font-semibold">Agregar Producto</span>
        <i class="fas fa-arrow-down ml-2 text-sm animate-bounce"></i>
    </button>
</div>

            <!-- Totales -->
            <div class="flex justify-end space-x-8 text-sm text-[var(--text-color)] font-semibold mt-4 pt-4 border-t border-[var(--gray-light)]">
                <div class="flex items-center space-x-2">
                    <span>Total Productos:</span>
                    <span class="font-bold text-[var(--primary-color)] text-base" id="total-products">0</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span>Importe Total Neto:</span>
                    <span class="font-bold text-[var(--primary-color)] text-base" id="total-amount">Bs. 0.00</span>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('purchases.index') }}" class="bg-[var(--gray-light)] hover:bg-gray-300 text-[var(--text-color)] px-6 py-2 rounded-lg transition duration-200">
                Cancelar
            </a>
            <button type="submit" class="bg-[var(--primary-color)] hover:bg-[var(--primary-light)] text-white px-6 py-2 rounded-lg transition duration-200">
                Guardar Compra
            </button>
        </div>
    </form>
    <!-- ===================== MODAL: CREAR PRODUCTO ===================== -->
<div id="product-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <!-- Backdrop -->
    <div id="product-modal-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Modal panel -->
    <div id="product-modal-panel" 
         class="relative z-10 w-full max-w-2xl mx-4 bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-[var(--primary-color)] to-[var(--primary-light)] px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i class="fas fa-box text-white text-lg"></i>
                </div>
                <h2 class="text-white text-lg font-bold tracking-wide">Crear Nuevo Producto</h2>
            </div>
            <button type="button" id="close-product-modal" 
                    class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-all duration-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body: Formulario -->
        <form id="modal-product-form" class="p-6 space-y-5">
            @csrf

            <!-- Nombre -->
            <div>
                <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                    Nombre <span class="text-[var(--red)]">*</span>
                </label>
                <input type="text" name="name" id="modal-product-name"
                       class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition"
                       placeholder="Nombre del producto" required>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">Descripción</label>
                <textarea name="description" id="modal-product-description" rows="2"
                          class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition resize-none"
                          placeholder="Descripción opcional"></textarea>
            </div>

            <!-- Precio y Categoría -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                        Precio <span class="text-[var(--red)]">*</span>
                    </label>
                    <div class="flex items-center border border-[var(--gray-light)] rounded-lg text-sm overflow-hidden focus-within:ring-2 focus-within:ring-[var(--primary-color)] transition">
                        <span class="px-3 py-2 bg-gray-50 border-r border-[var(--gray-light)] text-[var(--text-light)] font-semibold">Bs.</span>
                        <input type="number" step="0.01" min="0" name="price" id="modal-product-price"
                               class="flex-grow px-3 py-2 focus:outline-none bg-transparent"
                               placeholder="0.00" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                        Categoría <span class="text-[var(--red)]">*</span>
                    </label>
                    <select name="category_id" id="modal-product-category"
                            class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition bg-white" required>
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Imagen -->
            <div class="border border-[var(--gray-light)] rounded-xl p-4 bg-gray-50">
                <p class="text-xs font-semibold text-[var(--text-light)] mb-3 flex items-center gap-2">
                    <i class="fas fa-image text-[var(--primary-color)]"></i> Imagen del Producto
                </p>
                <!-- Tabs método -->
                <div class="flex gap-3 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-[var(--text-color)]">
                        <input type="radio" name="modal_image_method" value="upload" checked
                               class="accent-[var(--primary-color)]" id="modal-method-upload">
                        <span>Subir archivo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-[var(--text-color)]">
                        <input type="radio" name="modal_image_method" value="url"
                               class="accent-[var(--primary-color)]" id="modal-method-url">
                        <span>URL</span>
                    </label>
                </div>
                <!-- Upload -->
                <div id="modal-upload-section">
                    <label for="modal-image-file"
                           class="inline-flex items-center gap-2 cursor-pointer bg-white border border-[var(--gray-light)] rounded-lg px-4 py-2 text-xs text-[var(--text-color)] hover:bg-gray-100 transition">
                        <i class="fas fa-upload text-[var(--primary-color)]"></i> Elegir imagen
                    </label>
                    <input type="file" name="image_file" id="modal-image-file" accept="image/*" class="hidden">
                    <span id="modal-file-name" class="ml-2 text-xs text-gray-400">Sin archivo</span>
                </div>
                <!-- URL -->
                <div id="modal-url-section" class="hidden">
                    <input type="text" name="image_url" id="modal-image-url"
                           class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition"
                           placeholder="https://ejemplo.com/imagen.jpg">
                </div>
                <!-- Preview -->
                <div id="modal-image-preview" class="mt-3 hidden">
                    <img id="modal-preview-img" src="" alt="Vista previa"
                         class="max-h-32 rounded-lg border border-[var(--gray-light)] mx-auto">
                </div>
            </div>

            <!-- Inventario -->
            <div class="border border-[var(--gray-light)] rounded-xl p-4 bg-gray-50">
                <label class="flex items-center gap-2 cursor-pointer mb-1">
                    <input type="checkbox" name="manage_inventory" id="modal-manage-inventory" value="1"
                           class="accent-[var(--primary-color)] h-4 w-4 rounded">
                    <span class="text-xs font-semibold text-[var(--text-color)]">Gestionar inventario para este producto</span>
                </label>
                <p class="text-xs text-gray-400 mb-3">Si está marcado, podrás realizar movimientos de inventario</p>

                <div id="modal-inventory-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">Stock Inicial</label>
                        <input type="number" step="0.01" name="stock" id="modal-stock" value="0" min="0"
                               class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">Stock Mínimo</label>
                        <input type="number" step="0.01" name="min_stock" id="modal-min-stock" value="5" min="0"
                               class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">Tipo de Stock</label>
                        <select name="stock_type" id="modal-stock-type"
                                class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition bg-white">
                            <option value="discrete">Discreto (unidades)</option>
                            <option value="continuous">Continuo (peso/volumen)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">Unidad de Medida</label>
                        <input type="text" name="stock_unit" id="modal-stock-unit" value="unidades"
                               class="w-full border border-[var(--gray-light)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] transition">
                    </div>
                </div>
            </div>

            <!-- Errores de validación -->
            <div id="modal-errors" class="hidden bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 text-xs space-y-1"></div>

            <!-- Footer botones -->
            <div class="flex justify-end gap-3 pt-2 border-t border-[var(--gray-light)]">
                <button type="button" id="cancel-product-modal"
                        class="px-5 py-2 rounded-lg border border-[var(--gray-light)] text-sm text-[var(--text-color)] hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" id="modal-submit-btn"
                        class="px-6 py-2 rounded-lg bg-[var(--primary-color)] hover:bg-[var(--primary-light)] text-white text-sm font-semibold transition flex items-center gap-2 shadow-md">
                    <i class="fas fa-save"></i>
                    <span>Guardar Producto</span>
                </button>
            </div>
        </form>
    </div>
</div>
<!-- ===================== FIN MODAL ===================== -->
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let productRowCounter = 0;
    const productsTableBody = document.getElementById('products-table-body');
    const totalProductsSpan = document.getElementById('total-products');
    const totalAmountSpan = document.getElementById('total-amount');
    const emptyMessage = document.getElementById('empty-table-message');
    const addProductFooter = document.getElementById('add-product-footer');

    // Actualizar NIT y dirección del proveedor
    document.getElementById('proveedor').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const nit = selectedOption.getAttribute('data-nit') || '-';
        const address = selectedOption.getAttribute('data-address') || '-';
        
        document.getElementById('nit').value = nit;
        document.getElementById('supplier-address').textContent = address;
    });
     // Actualizar ciudad de la sucursal
    document.getElementById('sucursal').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const city = selectedOption.getAttribute('data-city') || '-';
        
        document.getElementById('branch-city').textContent = city;
    });

    // Botón para agregar fila vacía desde el mensaje inicial
    document.getElementById('add-empty-row-btn').addEventListener('click', function() {
        addEmptyProductRow();
    });

    // Botón para agregar más productos (footer)
    document.getElementById('add-product-btn').addEventListener('click', function() {
        addEmptyProductRow();
    });

    // Función para agregar una fila vacía
    function addEmptyProductRow() {
        // Ocultar mensaje de tabla vacía
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }
        
        // Mostrar botón de agregar en el footer
        addProductFooter.classList.remove('hidden');

        productRowCounter++;
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors duration-150';
        row.innerHTML = `
            <td class="px-3 py-3 border-r border-[var(--gray-light)]">
                <div class="relative">
                    <input type="text" 
                           class="product-name-input w-full border border-[var(--gray-light)] rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                           placeholder="Escriba para buscar producto..."
                           autocomplete="off"
                           data-row-id="${productRowCounter}">
                    <div class="product-dropdown absolute z-30 top-full left-0 right-0 mt-1 bg-white shadow-lg rounded-md hidden max-h-48 overflow-y-auto border border-gray-200"></div>
                    <input type="hidden" name="products[${productRowCounter}][product_id]" class="product-id-input">
                    <div class="product-info mt-2 hidden">
                        <div class="font-medium text-[var(--text-color)] text-sm product-display-name"></div>
                        <div class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-tag mr-1"></i><span class="product-category">-</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][quantity]" value="1" min="1" 
                       class="w-20 text-center quantity-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][unit_cost]" 
                       class="w-24 text-center unit-cost-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                       step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <div class="flex items-center justify-center gap-1">
                    <input type="number" name="products[${productRowCounter}][discount]" value="0"
                           class="w-16 text-center discount-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                           step="0.1" min="0" max="100" placeholder="0.0">
                    <span class="text-gray-500">%</span>
                </div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <div class="flex items-center justify-center gap-1">
                    <input type="number" name="products[${productRowCounter}][discount_amount]" 
                    class="w-24 text-center discount-amount border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                    step="0.01" min="0" placeholder="0.00">
                    <span class="text-gray-500">Bs.</span>
                </div>
            </td>
           
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][unit_cost_after_discount]" 
                       class="w-24 text-center unit-cost-after-discount bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-medium text-[var(--primary-color)]" 
                       step="0.01" min="0" readonly placeholder="0.00">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][line_total]" 
                       class="w-24 text-center line-total bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-bold text-[var(--primary-color)]" 
                       step="0.01" min="0" readonly placeholder="0.00">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <div class="profit-margin font-semibold text-sm" style="color: #6c757d;">-</div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][selling_price]" value="0.00"
                       class="w-24 text-center selling-price border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                       step="0.01" min="0" required>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="date" name="products[${productRowCounter}][expiry_date]" 
                       class="w-full text-xs border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]">
            </td>
            <td class="px-3 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button type="button" class="text-[var(--primary-color)] hover:text-white hover:bg-[var(--primary-color)] p-2 rounded transition-all duration-150 add-row-below" title="Agregar producto debajo">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                  
                    <button type="button" class="text-[var(--red)] hover:text-white hover:bg-[var(--red)] p-2 rounded transition-all duration-150 remove-product" title="Eliminar producto">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        `;
        
        productsTableBody.appendChild(row);
        
        // Agregar funcionalidad de búsqueda inline
        setupInlineProductSearch(row);
        
        // Agregar event listeners para cálculos
        row.querySelectorAll('.quantity-input, .unit-cost-input, .discount-input, .selling-price').forEach(input => {
            input.addEventListener('input', updateRowCalculations);
            input.addEventListener('change', updateRowCalculations);
        });
        row.querySelector('.discount-amount').addEventListener('input', updateRowFromDiscountAmount);
        row.querySelector('.discount-amount').addEventListener('change', updateRowFromDiscountAmount);
        
        // Botón eliminar
        row.querySelector('.remove-product').addEventListener('click', function() {
            row.remove();
            updateTotals();
            checkEmptyTable();
        });

        // Botón agregar fila debajo
        row.querySelector('.add-row-below').addEventListener('click', function() {
            addEmptyProductRowAfter(row);
        });

        // Focus en el input de búsqueda
        row.querySelector('.product-name-input').focus();
        
        updateTotals();
    }

    function addEmptyProductRowAfter(referenceRow) {
        addEmptyProductRow();

        const rows = Array.from(productsTableBody.querySelectorAll('tr:not(#empty-table-message)'));
        const newRow = rows[rows.length - 1];

        if (referenceRow && newRow && referenceRow !== newRow) {
            referenceRow.insertAdjacentElement('afterend', newRow);
            newRow.querySelector('.product-name-input')?.focus();
        }
    }

    function extractErrorMessage(payload, fallbackMessage) {
        if (!payload) {
            return fallbackMessage;
        }

        if (typeof payload === 'string') {
            return payload;
        }

        if (payload.message) {
            return payload.message;
        }

        if (payload.errors) {
            const validationErrors = Object.values(payload.errors).flat();
            if (validationErrors.length > 0) {
                return validationErrors.join('\n');
            }
        }

        return fallbackMessage;
    }

    // Configurar búsqueda inline de productos
    function setupInlineProductSearch(row) {
        const searchInput = row.querySelector('.product-name-input');
        const dropdown = row.querySelector('.product-dropdown');
        const productIdInput = row.querySelector('.product-id-input');
        const productInfo = row.querySelector('.product-info');
        const productDisplayName = row.querySelector('.product-display-name');
        const productCategory = row.querySelector('.product-category');
        const sellingPriceInput = row.querySelector('.selling-price');

        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (searchTerm.length > 0) {
                dropdown.innerHTML = '<div class="p-3 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Buscando...</div>';
                dropdown.classList.remove('hidden');
                
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('purchases.searchProducts') }}?search=${encodeURIComponent(searchTerm)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(async response => {
                        const payload = await response.json().catch(() => null);
                        if (!response.ok) {
                            throw new Error(extractErrorMessage(payload, 'Error al buscar productos'));
                        }

                        return payload;
                    })
                    .then(products => {
                        if (products && products.length > 0) {
                            let html = '';
                            products.forEach(product => {
                                html += `
                                    <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 flex justify-between items-center transition-colors duration-150 product-option" 
                                         data-id="${product.id}"
                                         data-name="${escapeHtml(product.name)}"
                                         data-price="${parseFloat(product.price) || 0}"
                                         data-category="${escapeHtml(product.category || 'Sin categoría')}">
                                        <div>
                                            <div class="font-medium text-[var(--text-color)]">${escapeHtml(product.name)}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-tag mr-1"></i>${escapeHtml(product.category || 'Sin categoría')}
                                            </div>
                                        </div>
                                        <span class="text-sm font-semibold text-[var(--primary-color)]">Bs. ${(parseFloat(product.price) || 0).toFixed(2)}</span>
                                    </div>
                                `;
                            });
                            dropdown.innerHTML = html;
                            
                            // Agregar event listeners a las opciones
                            dropdown.querySelectorAll('.product-option').forEach(option => {
                                option.addEventListener('click', function() {
                                    const id = this.getAttribute('data-id');
                                    const name = this.getAttribute('data-name');
                                    const price = parseFloat(this.getAttribute('data-price'));
                                    const category = this.getAttribute('data-category');
                                    
                                    // Asignar valores
                                    productIdInput.value = id;
                                    searchInput.value = '';
                                    searchInput.classList.add('hidden');
                                    productDisplayName.textContent = name;
                                    productCategory.textContent = category;
                                    productInfo.classList.remove('hidden');
                                    sellingPriceInput.value = price.toFixed(2);
                                    dropdown.classList.add('hidden');
                                    
                                    // Trigger cálculos
                                    updateRowCalculations({ target: row.querySelector('.unit-cost-input') });
                                });
                            });
                        } else {
                            dropdown.innerHTML = '<div class="p-3 text-gray-500 text-center"><i class="fas fa-search mr-2"></i>No se encontraron productos</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        dropdown.innerHTML = '<div class="p-3 text-red-500 text-center"><i class="fas fa-exclamation-triangle mr-2"></i>Error al buscar</div>';
                    });
                }, 300);
            } else {
                dropdown.classList.add('hidden');
            }
        });

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!row.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }

    // Verificar si la tabla está vacía
    function checkEmptyTable() {
        const rows = productsTableBody.querySelectorAll('tr:not(#empty-table-message)');
        if (rows.length === 0) {
            emptyMessage.style.display = '';
            addProductFooter.classList.add('hidden');
        }
    }

    // Manejar el envío del formulario
    document.getElementById('purchase-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Validar proveedor
        const supplierId = document.getElementById('proveedor').value;
        if (!supplierId) {
            alert('Por favor seleccione un proveedor');
            return;
        }
        const branchId = document.getElementById('sucursal').value;
        if (!branchId) {
            alert('Por favor seleccione una sucursal');
            return;
        }
        // Validar que haya productos
        const products = getProductsData();
        if (products.length === 0) {
            alert('Debe agregar al menos un producto');
            return;
        }

        // Validar que todos los productos tengan costo unitario
        const invalidProducts = products.filter(p => !p.unit_cost || p.unit_cost <= 0);
        if (invalidProducts.length > 0) {
            alert('Todos los productos deben tener un costo unitario mayor a 0');
            return;
        }

        // Preparar datos del formulario
        const formData = {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            supplier_id: supplierId,
            branch_id: branchId,
            reference_number: document.querySelector('[name="reference_number"]').value,
            purchase_date: document.querySelector('[name="purchase_date"]').value,
            products: products
        };

        // Mostrar estado de carga
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';

        // Enviar solicitud
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(async response => {
            const payload = await response.json().catch(() => null);

            if (!response.ok) {
                throw new Error(extractErrorMessage(payload, 'Error en la respuesta del servidor'));
            }

            return payload;
        })
        .then(data => {
            if (data.success) {
                const redirectUrl = data.redirect_url || "{{ route('purchases.index') }}";

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Compra registrada exitosamente',
                        text: data.message || 'La compra se guardo correctamente.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#203363',
                        allowOutsideClick: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = redirectUrl;
                    });
                } else {
                    alert('Compra registrada exitosamente');
                    window.location.href = redirectUrl;
                }
            } else {
                throw new Error(data.message || 'Error al guardar la compra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error al guardar la compra. Por favor, intente nuevamente.');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Guardar Compra';
        });
    });

    // Obtener datos de productos
    function getProductsData() {
        const products = [];
        document.querySelectorAll('#products-table-body tr:not(#empty-table-message)').forEach(row => {
            const productId = row.querySelector('.product-id-input').value;
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
            const sellingPrice = parseFloat(row.querySelector('.selling-price').value) || 0;
            const expiryDate = row.querySelector('input[type="date"]').value;
            
            if (productId && quantity > 0 && unitCost > 0) {
                products.push({
                    product_id: productId,
                    quantity: quantity,
                    unit_cost: unitCost,
                    discount: discount,
                    selling_price: sellingPrice,
                    expiry_date: expiryDate || null
                });
            }
        });
        return products;
    }

    // Actualizar cálculos de fila
    function updateRowCalculations(event) {
        const row = event.target.closest('tr');
        if (!row || row.id === 'empty-table-message') return;
        
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
        const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
        const sellingPrice = parseFloat(row.querySelector('.selling-price').value) || 0;
        
        if (unitCost > 0) {
            const discountAmount = unitCost * (discount / 100);
            const unitCostAfterDiscount = unitCost - discountAmount;
            const lineTotal = quantity * unitCostAfterDiscount;

            const totalDiscountAmount = discountAmount * quantity;
            
            row.querySelector('.unit-cost-after-discount').value = unitCostAfterDiscount.toFixed(2);
            row.querySelector('.line-total').value = lineTotal.toFixed(2);


            const discountAmountInput = row.querySelector('.discount-amount');
            if (discountAmountInput && document.activeElement !== discountAmountInput) {
                discountAmountInput.value = totalDiscountAmount.toFixed(2);
            }
            
            if (sellingPrice > 0 && unitCostAfterDiscount > 0) {
                const profitMargin = ((sellingPrice - unitCostAfterDiscount) / unitCostAfterDiscount) * 100;
                const profitMarginElement = row.querySelector('.profit-margin');
                profitMarginElement.textContent = profitMargin.toFixed(2) + '%';
                
                if (profitMargin < 0) {
                    profitMarginElement.style.color = '#dc3545';
                } else if (profitMargin < 20) {
                    profitMarginElement.style.color = '#ffc107';
                } else {
                    profitMarginElement.style.color = '#28a745';
                }
            } else {
                row.querySelector('.profit-margin').textContent = '-';
                row.querySelector('.profit-margin').style.color = '#6c757d';
            }
        } else {
            row.querySelector('.unit-cost-after-discount').value = '';
            row.querySelector('.line-total').value = '';
            row.querySelector('.profit-margin').textContent = '-';
             const discountAmountInput = row.querySelector('.discount-amount');
                if (discountAmountInput) discountAmountInput.value = '';
            }
        
            updateTotals();
    }
    function updateRowFromDiscountAmount(event) {
        const row = event.target.closest('tr');
        if (!row || row.id === 'empty-table-message') return;

            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
            const totalDiscountAmount = parseFloat(row.querySelector('.discount-amount').value) || 0;
            const sellingPrice = parseFloat(row.querySelector('.selling-price').value) || 0;

            if (unitCost <= 0) return;

            // Calcular descuento por unidad y porcentaje
            const discountPerUnit = quantity > 0 ? totalDiscountAmount / quantity : 0;
            const discountPercent = (discountPerUnit / unitCost) * 100;

            // Validar que el descuento no supere el costo total
        if (discountPerUnit > unitCost) {
            alert('El descuento en Bs. no puede superar el costo unitario total.');
            row.querySelector('.discount-amount').value = (unitCost * quantity).toFixed(2);
            return;
        }

        const unitCostAfterDiscount = unitCost - discountPerUnit;
        const lineTotal = quantity * unitCostAfterDiscount;

    // Sincronizar porcentaje sin disparar su listener
        const discountInput = row.querySelector('.discount-input');
        if (discountInput && document.activeElement !== discountInput) {
            discountInput.value = discountPercent.toFixed(2);
        }

    // Actualizar campos readonly
    row.querySelector('.unit-cost-after-discount').value = unitCostAfterDiscount.toFixed(2);
    row.querySelector('.line-total').value = lineTotal.toFixed(2);

    // Actualizar margen de utilidad
    if (sellingPrice > 0 && unitCostAfterDiscount > 0) {
        const profitMargin = ((sellingPrice - unitCostAfterDiscount) / unitCostAfterDiscount) * 100;
        const profitMarginElement = row.querySelector('.profit-margin');
        profitMarginElement.textContent = profitMargin.toFixed(2) + '%';

        if (profitMargin < 0) {
            profitMarginElement.style.color = '#dc3545';
        } else if (profitMargin < 20) {
            profitMarginElement.style.color = '#ffc107';
        } else {
            profitMarginElement.style.color = '#28a745';
        }
    } else {
        row.querySelector('.profit-margin').textContent = '-';
        row.querySelector('.profit-margin').style.color = '#6c757d';
    }

    updateTotals();
}
    // Actualizar totales
function updateTotals() {
        const rows = productsTableBody.querySelectorAll('tr:not(#empty-table-message)');
        let totalProducts = 0;
        let totalAmount = 0;
        
        rows.forEach(row => {
            const productId = row.querySelector('.product-id-input').value;
            if (productId) {
                totalProducts++;
                const lineTotal = parseFloat(row.querySelector('.line-total').value) || 0;
                totalAmount += lineTotal;
            }
        });
        
        totalProductsSpan.textContent = totalProducts;
        totalAmountSpan.textContent = 'Bs. ' + totalAmount.toFixed(2);
    }

    // Escapar HTML
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Búsqueda global de productos (mantener funcionalidad existente)
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Agregar producto desde búsqueda global
    function addProductToTable(product) {
        // Ocultar mensaje vacío
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }
        addProductFooter.classList.remove('hidden');

        productRowCounter++;
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors duration-150';
        row.innerHTML = `
            <td class="px-3 py-3 border-r border-[var(--gray-light)]">
                <div class="font-medium text-[var(--text-color)]">${escapeHtml(product.name)}</div>
                <div class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-tag mr-1"></i>${escapeHtml(product.category || 'Sin categoría')}
                </div>
                <input type="hidden" name="products[${productRowCounter}][product_id]" value="${product.id}" class="product-id-input">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][quantity]" value="1" min="1" 
                       class="w-20 text-center quantity-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][unit_cost]" 
                       class="w-24 text-center unit-cost-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                       step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <div class="flex items-center justify-center gap-1">
                    <input type="number" name="products[${productRowCounter}][discount]" value="0"
                           class="w-16 text-center discount-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                           step="0.1" min="0" max="100" placeholder="0.0">
                    <span class="text-gray-500">%</span>
                </div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <div class="flex items-center justify-center gap-1">
                    <input type="number" name="products[${productRowCounter}][discount_amount]" 
                    class="w-24 text-center discount-amount border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                    step="0.01" min="0" placeholder="0.00">
                    <span class="text-gray-500">Bs.</span>
                </div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][unit_cost_after_discount]" 
                       class="w-24 text-center unit-cost-after-discount bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-medium text-[var(--primary-color)]" 
                       step="0.01" min="0" readonly placeholder="0.00">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][line_total]" 
                       class="w-24 text-center line-total bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-bold text-[var(--primary-color)]" 
                       step="0.01" min="0" readonly placeholder="0.00">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <div class="profit-margin font-semibold text-sm" style="color: #28a745;">-</div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][selling_price]" value="${(product.price || 0).toFixed(2)}" 
                       class="w-24 text-center selling-price border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                       step="0.01" min="0" required>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="date" name="products[${productRowCounter}][expiry_date]" 
                       class="w-full text-xs border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]">
            </td>
            <td class="px-3 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button type="button" class="text-[var(--primary-color)] hover:text-white hover:bg-[var(--primary-color)] p-2 rounded transition-all duration-150 add-row-below" title="Agregar producto debajo">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                    <button type="button" class="text-[var(--red)] hover:text-white hover:bg-[var(--red)] p-2 rounded transition-all duration-150 remove-product" title="Eliminar producto">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        `;
        
        productsTableBody.appendChild(row);
        
        // Agregar event listeners
        row.querySelectorAll('.quantity-input, .unit-cost-input, .discount-input, .selling-price').forEach(input => {
            input.addEventListener('input', updateRowCalculations);
            input.addEventListener('change', updateRowCalculations);
        });
        row.querySelector('.discount-amount').addEventListener('input', updateRowFromDiscountAmount);
        row.querySelector('.discount-amount').addEventListener('change', updateRowFromDiscountAmount);
        
        row.querySelector('.remove-product').addEventListener('click', function() {
            row.remove();
            updateTotals();
            checkEmptyTable();
        });

        // Botón agregar fila debajo
        row.querySelector('.add-row-below').addEventListener('click', function() {
            addEmptyProductRowAfter(row);
        });

        updateRowCalculations({ target: row.querySelector('.unit-cost-input') });
        updateTotals();
    }

    // Búsqueda global de productos
    document.getElementById('product-search').addEventListener('input', debounce(function(e) {
        const searchTerm = e.target.value.trim();
        const searchResults = document.getElementById('search-results');
        
        if (searchTerm.length > 0) {
            fetch(`{{ route('purchases.searchProducts') }}?search=${encodeURIComponent(searchTerm)}`)
                .then(async response => {
                    const payload = await response.json().catch(() => null);

                    if (!response.ok) {
                        throw new Error(extractErrorMessage(payload, 'Error en la respuesta del servidor'));
                    }

                    return payload;
                })
                .then(products => {
                    if (products && products.length > 0) {
                        let html = '';
                        products.forEach(product => {
                            html += `
                                <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 flex justify-between items-center transition-colors duration-150" 
                                     onclick="window.selectSearchProduct(${product.id}, '${escapeHtml(product.name)}', ${parseFloat(product.price) || 0}, '${escapeHtml(product.category || 'Sin categoría')}')">
                                    <div>
                                        <div class="font-medium text-[var(--text-color)]">${escapeHtml(product.name)}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-tag mr-1"></i>${escapeHtml(product.category || 'Sin categoría')}
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-[var(--primary-color)]">Bs. ${(parseFloat(product.price) || 0).toFixed(2)}</span>
                                </div>
                            `;
                        });
                        searchResults.innerHTML = html;
                        searchResults.classList.remove('hidden');
                    } else {
                        searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No se encontraron productos</div>';
                        searchResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.innerHTML = '<div class="p-3 text-red-500 text-center">Error al buscar productos</div>';
                    searchResults.classList.remove('hidden');
                });
        } else {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
        }
    }, 300));

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        const searchContainer = document.getElementById('search-container');
        const searchResults = document.getElementById('search-results');
        
        if (!searchContainer.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Función global para seleccionar producto
    window.selectSearchProduct = function(id, name, price, category) {
        addProductToTable({
            id: id,
            name: name,
            price: price,
            category: category
        });
        
        document.getElementById('product-search').value = '';
        document.getElementById('search-results').classList.add('hidden');
    };
});
// ===================== MODAL: CREAR PRODUCTO =====================
(function () {
    const modal        = document.getElementById('product-modal');
    const backdrop     = document.getElementById('product-modal-backdrop');
    const panel        = document.getElementById('product-modal-panel');
    const openBtn      = document.getElementById('open-product-modal-btn');
    const closeBtn     = document.getElementById('close-product-modal');
    const cancelBtn    = document.getElementById('cancel-product-modal');
    const form         = document.getElementById('modal-product-form');
    const submitBtn    = document.getElementById('modal-submit-btn');
    const errorsBox    = document.getElementById('modal-errors');

    // Imagen
    const methodUpload   = document.getElementById('modal-method-upload');
    const methodUrl      = document.getElementById('modal-method-url');
    const uploadSection  = document.getElementById('modal-upload-section');
    const urlSection     = document.getElementById('modal-url-section');
    const imageFile      = document.getElementById('modal-image-file');
    const imageUrl       = document.getElementById('modal-image-url');
    const fileName       = document.getElementById('modal-file-name');
    const imagePreview   = document.getElementById('modal-image-preview');
    const previewImg     = document.getElementById('modal-preview-img');

    // Inventario
    const manageInv      = document.getElementById('modal-manage-inventory');
    const invFields      = document.getElementById('modal-inventory-fields');
    const stockType      = document.getElementById('modal-stock-type');
    const stockUnit      = document.getElementById('modal-stock-unit');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            backdrop.classList.add('opacity-100');
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.add('scale-95', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            resetForm();
        }, 300);
    }

    function resetForm() {
        form.reset();
        errorsBox.classList.add('hidden');
        errorsBox.innerHTML = '';
        imagePreview.classList.add('hidden');
        fileName.textContent = 'Sin archivo';
        uploadSection.classList.remove('hidden');
        urlSection.classList.add('hidden');
        invFields.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i><span>Guardar Producto</span>';
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    // Imagen: cambiar método
    methodUpload.addEventListener('change', function () {
        uploadSection.classList.remove('hidden');
        urlSection.classList.add('hidden');
        imageUrl.value = '';
        imagePreview.classList.add('hidden');
    });
    methodUrl.addEventListener('change', function () {
        uploadSection.classList.add('hidden');
        urlSection.classList.remove('hidden');
        imageFile.value = '';
        fileName.textContent = 'Sin archivo';
        imagePreview.classList.add('hidden');
    });
    imageFile.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            fileName.textContent = this.files[0].name;
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    imageUrl.addEventListener('blur', function () {
        if (this.value) {
            previewImg.src = this.value;
            imagePreview.classList.remove('hidden');
            previewImg.onerror = () => imagePreview.classList.add('hidden');
        }
    });

    // Inventario toggle
    manageInv.addEventListener('change', function () {
        invFields.classList.toggle('hidden', !this.checked);
    });
    stockType.addEventListener('change', function () {
        stockUnit.value = this.value === 'discrete' ? 'unidades' : 'gr/ml';
    });

    // Submit con FormData (soporta archivos)
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errorsBox.classList.add('hidden');
        errorsBox.innerHTML = '';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Guardando...</span>';

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('name',        document.getElementById('modal-product-name').value);
        formData.append('description', document.getElementById('modal-product-description').value);
        formData.append('price',       document.getElementById('modal-product-price').value);
        formData.append('category_id', document.getElementById('modal-product-category').value);

        if (methodUpload.checked && imageFile.files[0]) {
            formData.append('image_file', imageFile.files[0]);
        } else if (methodUrl.checked && imageUrl.value) {
            formData.append('image_url', imageUrl.value);
        }

        if (manageInv.checked) {
            formData.append('manage_inventory', '1');
            formData.append('stock',      document.getElementById('modal-stock').value);
            formData.append('min_stock',  document.getElementById('modal-min-stock').value);
            formData.append('stock_type', document.getElementById('modal-stock-type').value);
            formData.append('stock_unit', document.getElementById('modal-stock-unit').value);
        }

        fetch("{{ route('items.store') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        })
        .then(async response => {
            const payload = await response.json().catch(() => null);
            if (!response.ok) {
                // Errores de validación Laravel (422)
                if (response.status === 422 && payload?.errors) {
                    const msgs = Object.values(payload.errors).flat();
                    errorsBox.innerHTML = msgs.map(m => `<p>• ${m}</p>`).join('');
                    errorsBox.classList.remove('hidden');
                } else {
                    throw new Error(payload?.message || 'Error al guardar el producto');
                }
                return;
            }
            // Éxito: cerrar modal y mostrar alerta
            closeModal();
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Producto creado',
                    text: 'El producto fue creado correctamente.',
                    confirmButtonColor: '#203363',
                    timer: 2500,
                    timerProgressBar: true
                });
            } else {
                alert('Producto creado correctamente.');
            }
        })
        .catch(error => {
            errorsBox.innerHTML = `<p>• ${error.message}</p>`;
            errorsBox.classList.remove('hidden');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i><span>Guardar Producto</span>';
        });
    });
})();
// ===================== FIN MODAL =====================
</script>
@endpush
@endsection