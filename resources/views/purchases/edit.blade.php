@extends('layouts.app')
@section('content')
<div class="container mx-auto p-6">
    <!-- Mensajes flash -->
    @if(session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 transition-all duration-500 ease-in-out">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="document.getElementById('success-alert').remove()">
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
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="document.getElementById('error-alert').remove()">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
    @endif

    <!-- Encabezado con título -->
    <div class="mb-6">
        <h1 class="text-xl font-bold mb-4 text-[var(--primary-color)] relative pb-2 section-title">
            Editar Compra
        </h1>
        <div class="flex items-center gap-2 text-sm text-[var(--text-light)]">
            <i class="fas fa-info-circle"></i>
            <span>Referencia: <strong>{{ $purchase->reference_number ?? 'N/A' }}</strong> | Estado: <strong class="text-yellow-600">{{ ucfirst($purchase->status) }}</strong></span>
        </div>
    </div>
  
    <!-- Formulario de compra -->
    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchase-form">
        @csrf
        @method('PUT')
        
        <!-- Información General -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6 mb-6">
            <h2 class="text-lg font-semibold text-[var(--text-color)] mb-4 pb-2 border-b border-[var(--gray-light)]">
                <i class="fas fa-file-invoice mr-2"></i>Información General
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Proveedor -->
                <div class="form-group">
                    <label for="proveedor" class="block text-sm font-semibold text-[var(--text-color)] mb-2">
                        <i class="fas fa-truck mr-1 text-[var(--primary-color)]"></i>Proveedor
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="proveedor" name="supplier_id" 
                            class="w-full border-2 border-[var(--gray-light)] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[var(--primary-color)] transition-colors bg-white" 
                            required>
                        <option value="">Seleccione un proveedor</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                    data-nit="{{ $supplier->nit }}" 
                                    data-address="{{ $supplier->address }}"
                                    {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- NIT - AHORA EDITABLE -->
                <div class="form-group">
                    <label for="nit" class="block text-sm font-semibold text-[var(--text-color)] mb-2">
                        <i class="fas fa-id-card mr-1 text-[var(--primary-color)]"></i>NIT
                    </label>
                    <input type="text" id="nit" name="supplier_nit"
                           value="{{ $purchase->supplier->nit ?? '' }}"
                           class="w-full border-2 border-[var(--gray-light)] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                           placeholder="Ingrese NIT">
                </div>

                <!-- Número de referencia -->
                <div class="form-group">
                    <label for="numeroReferencia" class="block text-sm font-semibold text-[var(--text-color)] mb-2">
                        <i class="fas fa-hashtag mr-1 text-[var(--primary-color)]"></i>Número de Referencia
                    </label>
                    <input type="text" id="numeroReferencia" name="reference_number" 
                           value="{{ $purchase->reference_number }}"
                           class="w-full border-2 border-[var(--gray-light)] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[var(--primary-color)] transition-colors"
                           placeholder="Ej: REF-2024-001">
                </div>

                <!-- Fecha de compra -->
                <div class="form-group">
                    <label for="fechaCompra" class="block text-sm font-semibold text-[var(--text-color)] mb-2">
                        <i class="fas fa-calendar-alt mr-1 text-[var(--primary-color)]"></i>Fecha de Compra
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="fechaCompra" name="purchase_date" 
                           value="{{ $purchase->purchase_date->format('Y-m-d\TH:i') }}" 
                           class="w-full border-2 border-[var(--gray-light)] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                           required>
                </div>

                <!-- Dirección del proveedor - AHORA EDITABLE -->
                <div class="form-group col-span-2">
                    <label for="supplier-address" class="block text-sm font-semibold text-[var(--text-color)] mb-2">
                        <i class="fas fa-map-marker-alt mr-1 text-[var(--primary-color)]"></i>Dirección
                    </label>
                    <input type="text" id="supplier-address" name="supplier_address"
                           value="{{ $purchase->supplier->address ?? '' }}"
                           class="w-full border-2 border-[var(--gray-light)] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[var(--primary-color)] transition-colors"
                           placeholder="Ingrese la dirección del proveedor">
                </div>
            </div>
        </div>

        <!-- Sección de productos -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6 mb-6">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-[var(--gray-light)]">
                <h2 class="text-lg font-semibold text-[var(--text-color)]">
                    <i class="fas fa-boxes mr-2"></i>Productos de la Compra
                </h2>
                <a href="{{ route('items.create') }}" 
                   class="text-[var(--primary-color)] hover:text-[var(--primary-light)] text-sm font-medium flex items-center gap-2 transition duration-200">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nuevo Producto</span>
                </a>
            </div>

            <!-- Búsqueda de productos -->
            <div class="mb-4">
                <div class="relative" id="search-container">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-[var(--text-light)]"></i>
                    </div>
                    <input type="search" id="product-search" 
                           placeholder="Buscar producto por nombre o descripción..." 
                           class="w-full pl-10 pr-4 py-3 border-2 border-[var(--gray-light)] rounded-lg text-sm focus:outline-none focus:border-[var(--primary-color)] transition-colors"
                           autocomplete="off">
                    <div id="search-results" class="absolute z-20 top-full left-0 right-0 mt-1 bg-white shadow-lg rounded-lg hidden max-h-60 overflow-y-auto border-2 border-[var(--gray-light)]"></div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="overflow-x-auto rounded-lg border-2 border-[var(--gray-light)]">
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
                            <th class="px-3 py-3 text-center font-semibold w-12">
                                <i class="fas fa-trash-alt"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body" class="divide-y divide-[var(--gray-light)]">
                        <!-- Filas de productos existentes -->
                        @foreach($purchase->stocks as $index => $stock)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-3 border-r border-[var(--gray-light)]">
                                <div class="font-medium text-[var(--text-color)]">{{ $stock->item->name ?? 'Producto eliminado' }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-tag mr-1"></i>{{ $stock->item->category->name ?? 'Sin categoría' }}
                                </div>
                                <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $stock->product_id }}">
                                <input type="hidden" name="products[{{ $index }}][stock_id]" value="{{ $stock->id }}">
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="number" name="products[{{ $index }}][quantity]" value="{{ $stock->quantity }}" min="1" 
                                       class="w-20 text-center quantity-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors">
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="number" name="products[{{ $index }}][unit_cost]" value="{{ $stock->unit_cost }}"
                                       class="w-24 text-center unit-cost-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                                       step="0.01" min="0" required>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" name="products[{{ $index }}][discount]" value="{{ $stock->discount }}"
                                           class="w-16 text-center discount-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                                           step="0.1" min="0" max="100">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <!-- AHORA EDITABLE -->
                                <input type="number" name="products[{{ $index }}][unit_cost_after_discount]"
                                       class="w-24 text-center unit-cost-after-discount border-2 border-blue-300 bg-blue-50 rounded-lg px-2 py-1.5 font-medium text-[var(--primary-color)] focus:outline-none focus:border-blue-500 transition-colors" 
                                       value="{{ number_format($stock->unit_cost * (1 - $stock->discount / 100), 2) }}"
                                       step="0.01" min="0">
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <!-- AHORA EDITABLE -->
                                <input type="number" name="products[{{ $index }}][line_total]"
                                       class="w-24 text-center line-total border-2 border-green-300 bg-green-50 rounded-lg px-2 py-1.5 font-bold text-green-700 focus:outline-none focus:border-green-500 transition-colors" 
                                       value="{{ number_format($stock->total_cost, 2) }}"
                                       step="0.01" min="0">
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <!-- AHORA EDITABLE -->
                                <input type="number" name="products[{{ $index }}][profit_margin]"
                                       class="w-20 text-center profit-margin-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors"
                                       value="{{ number_format($stock->profit_margin, 2) }}"
                                       step="0.01" min="-100" max="1000">
                                <div class="profit-margin-display font-semibold text-xs mt-1 px-2 py-0.5 rounded" style="color: {{ $stock->profit_margin >= 30 ? '#28a745' : ($stock->profit_margin >= 15 ? '#ffc107' : '#dc3545') }}; background-color: {{ $stock->profit_margin >= 30 ? '#d4edda' : ($stock->profit_margin >= 15 ? '#fff3cd' : '#f8d7da') }};">
                                    {{ number_format($stock->profit_margin, 2) }}%
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="number" name="products[{{ $index }}][selling_price]" value="{{ $stock->selling_price }}"
                                       class="w-24 text-center selling-price border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                                       step="0.01" min="0" required>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="date" name="products[{{ $index }}][expiry_date]" 
                                       value="{{ $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : '' }}"
                                       class="w-full text-xs border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors">
                            </td>
                            <td class="px-3 py-3 text-center">
                                <button type="button" class="text-red-500 hover:text-white hover:bg-red-500 p-2 rounded-lg transition-all duration-150 remove-product">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totales - AHORA EDITABLES -->
            <div class="mt-6 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-4 border-2 border-[var(--primary-color)]">
                <div class="flex justify-end space-x-8 text-sm text-[var(--text-color)] font-semibold items-center">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-boxes text-[var(--primary-color)]"></i>
                        <span>Total Productos:</span>
                        <input type="number" id="total-products-input" name="total_products" 
                               value="{{ $purchase->stocks->count() }}" min="0"
                               class="w-20 text-center font-bold text-[var(--primary-color)] text-lg border-2 border-[var(--primary-color)] rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]">
                        <span id="total-products" class="font-bold text-[var(--primary-color)] text-lg">({{ $purchase->stocks->count() }})</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calculator text-green-600"></i>
                        <span>Importe Total Neto:</span>
                        <span class="text-green-600">Bs.</span>
                        <input type="number" id="total-amount-input" name="total_amount"
                               value="{{ number_format($purchase->total_amount, 2, '.', '') }}" min="0" step="0.01"
                               class="w-32 text-center font-bold text-green-600 text-lg border-2 border-green-600 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600">
                        <span id="total-amount" class="font-bold text-green-600 text-lg">(Bs. {{ number_format($purchase->total_amount, 2) }})</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('purchases.show', $purchase->id) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg transition duration-200 flex items-center space-x-2 font-medium">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </a>
            <button type="submit" 
                    class="bg-[var(--primary-color)] hover:bg-[var(--primary-light)] text-white px-8 py-3 rounded-lg transition duration-200 flex items-center space-x-2 font-medium shadow-lg">
                <i class="fas fa-save"></i>
                <span>Actualizar Compra</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productRowCounter = {{ $purchase->stocks->count() }};
    const productsTableBody = document.getElementById('products-table-body');
    const totalProductsSpan = document.getElementById('total-products');
    const totalProductsInput = document.getElementById('total-products-input');
    const totalAmountSpan = document.getElementById('total-amount');
    const totalAmountInput = document.getElementById('total-amount-input');

    // Actualizar NIT y dirección del proveedor al cambiar
    document.getElementById('proveedor').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const nit = selectedOption.getAttribute('data-nit') || '';
        const address = selectedOption.getAttribute('data-address') || '';
        
        document.getElementById('nit').value = nit;
        document.getElementById('supplier-address').value = address;
    });

    // Agregar event listeners a productos existentes
    document.querySelectorAll('#products-table-body tr').forEach(row => {
        row.querySelectorAll('.quantity-input, .unit-cost-input, .discount-input, .selling-price, .unit-cost-after-discount, .line-total, .profit-margin-input').forEach(input => {
            input.addEventListener('input', updateRowCalculations);
            input.addEventListener('change', updateRowCalculations);
        });
        
        row.querySelector('.remove-product').addEventListener('click', function() {
            if (confirm('¿Está seguro de eliminar este producto de la compra?')) {
                row.remove();
                updateTotals();
            }
        });
    });

    // Actualizar totales cuando se editan manualmente
    totalProductsInput.addEventListener('input', function() {
        totalProductsSpan.textContent = '(' + this.value + ')';
    });

    totalAmountInput.addEventListener('input', function() {
        const value = parseFloat(this.value) || 0;
        totalAmountSpan.textContent = '(Bs. ' + value.toFixed(2) + ')';
    });

    function getProductsData() {
        const products = [];
        document.querySelectorAll('#products-table-body tr').forEach(row => {
            const productId = row.querySelector('input[name*="[product_id]"]').value;
            const stockId = row.querySelector('input[name*="[stock_id]"]')?.value;
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
            const sellingPrice = parseFloat(row.querySelector('.selling-price').value) || 0;
            const expiryDate = row.querySelector('input[type="date"]').value;
            const unitCostAfterDiscount = parseFloat(row.querySelector('.unit-cost-after-discount').value) || 0;
            const lineTotal = parseFloat(row.querySelector('.line-total').value) || 0;
            const profitMargin = parseFloat(row.querySelector('.profit-margin-input').value) || 0;
            
            if (productId && quantity > 0) {
                const product = {
                    product_id: productId,
                    quantity: quantity,
                    unit_cost: unitCost,
                    discount: discount,
                    selling_price: sellingPrice,
                    expiry_date: expiryDate || null,
                    unit_cost_after_discount: unitCostAfterDiscount,
                    line_total: lineTotal,
                    profit_margin: profitMargin
                };
                
                if (stockId) {
                    product.stock_id = stockId;
                }
                
                products.push(product);
            }
        });
        return products;
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function updateRowCalculations(event) {
        const row = event.target.closest('tr');
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
        const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
        const sellingPrice = parseFloat(row.querySelector('.selling-price').value) || 0;
        const unitCostAfterDiscountInput = row.querySelector('.unit-cost-after-discount');
        const lineTotalInput = row.querySelector('.line-total');
        const profitMarginInput = row.querySelector('.profit-margin-input');
        const profitMarginDisplay = row.querySelector('.profit-margin-display');
        
        // Calcular valores automáticamente si se editan los campos base
        if (event.target.classList.contains('quantity-input') || 
            event.target.classList.contains('unit-cost-input') || 
            event.target.classList.contains('discount-input')) {
            
            if (unitCost > 0) {
                const discountAmount = unitCost * (discount / 100);
                const unitCostAfterDiscount = unitCost - discountAmount;
                const lineTotal = quantity * unitCostAfterDiscount;
                
                unitCostAfterDiscountInput.value = unitCostAfterDiscount.toFixed(2);
                lineTotalInput.value = lineTotal.toFixed(2);
                
                if (sellingPrice > 0 && unitCostAfterDiscount > 0) {
                    const profitMargin = ((sellingPrice - unitCostAfterDiscount) / unitCostAfterDiscount) * 100;
                    profitMarginInput.value = profitMargin.toFixed(2);
                    profitMarginDisplay.textContent = profitMargin.toFixed(2) + '%';
                    
                    // Actualizar colores del margen
                    if (profitMargin < 0) {
                        profitMarginDisplay.style.color = '#dc3545';
                        profitMarginDisplay.style.backgroundColor = '#f8d7da';
                    } else if (profitMargin < 20) {
                        profitMarginDisplay.style.color = '#ffc107';
                        profitMarginDisplay.style.backgroundColor = '#fff3cd';
                    } else {
                        profitMarginDisplay.style.color = '#28a745';
                        profitMarginDisplay.style.backgroundColor = '#d4edda';
                    }
                } else {
                    profitMarginInput.value = '0.00';
                    profitMarginDisplay.textContent = '-';
                    profitMarginDisplay.style.color = '#6c757d';
                    profitMarginDisplay.style.backgroundColor = '#e9ecef';
                }
            }
        }
        
        // Si se edita el margen de utilidad, recalcular precio de venta
        if (event.target.classList.contains('profit-margin-input')) {
            const unitCostAfterDiscount = parseFloat(unitCostAfterDiscountInput.value) || 0;
            const newProfitMargin = parseFloat(profitMarginInput.value) || 0;
            
            if (unitCostAfterDiscount > 0) {
                const newSellingPrice = unitCostAfterDiscount * (1 + newProfitMargin / 100);
                row.querySelector('.selling-price').value = newSellingPrice.toFixed(2);
                profitMarginDisplay.textContent = newProfitMargin.toFixed(2) + '%';
                
                // Actualizar colores
                if (newProfitMargin < 0) {
                    profitMarginDisplay.style.color = '#dc3545';
                    profitMarginDisplay.style.backgroundColor = '#f8d7da';
                } else if (newProfitMargin < 20) {
                    profitMarginDisplay.style.color = '#ffc107';
                    profitMarginDisplay.style.backgroundColor = '#fff3cd';
                } else {
                    profitMarginDisplay.style.color = '#28a745';
                    profitMarginDisplay.style.backgroundColor = '#d4edda';
                }
            }
        }
        
        // Si se edita el precio de venta, recalcular margen
        if (event.target.classList.contains('selling-price')) {
            const unitCostAfterDiscount = parseFloat(unitCostAfterDiscountInput.value) || 0;
            const newSellingPrice = parseFloat(sellingPrice) || 0;
            
            if (unitCostAfterDiscount > 0 && newSellingPrice > 0) {
                const profitMargin = ((newSellingPrice - unitCostAfterDiscount) / unitCostAfterDiscount) * 100;
                profitMarginInput.value = profitMargin.toFixed(2);
                profitMarginDisplay.textContent = profitMargin.toFixed(2) + '%';
                
                // Actualizar colores
                if (profitMargin < 0) {
                    profitMarginDisplay.style.color = '#dc3545';
                    profitMarginDisplay.style.backgroundColor = '#f8d7da';
                } else if (profitMargin < 20) {
                    profitMarginDisplay.style.color = '#ffc107';
                    profitMarginDisplay.style.backgroundColor = '#fff3cd';
                } else {
                    profitMarginDisplay.style.color = '#28a745';
                    profitMarginDisplay.style.backgroundColor = '#d4edda';
                }
            }
        }
        
        // Si se edita el costo después de descuento, recalcular el total de línea
        if (event.target.classList.contains('unit-cost-after-discount')) {
            const newUnitCostAfterDiscount = parseFloat(unitCostAfterDiscountInput.value) || 0;
            const lineTotal = quantity * newUnitCostAfterDiscount;
            lineTotalInput.value = lineTotal.toFixed(2);
            
            // Recalcular margen si hay precio de venta
            if (sellingPrice > 0 && newUnitCostAfterDiscount > 0) {
                const profitMargin = ((sellingPrice - newUnitCostAfterDiscount) / newUnitCostAfterDiscount) * 100;
                profitMarginInput.value = profitMargin.toFixed(2);
                profitMarginDisplay.textContent = profitMargin.toFixed(2) + '%';
                
                // Actualizar colores
                if (profitMargin < 0) {
                    profitMarginDisplay.style.color = '#dc3545';
                    profitMarginDisplay.style.backgroundColor = '#f8d7da';
                } else if (profitMargin < 20) {
                    profitMarginDisplay.style.color = '#ffc107';
                    profitMarginDisplay.style.backgroundColor = '#fff3cd';
                } else {
                    profitMarginDisplay.style.color = '#28a745';
                    profitMarginDisplay.style.backgroundColor = '#d4edda';
                }
            }
        }
        
        updateTotals();
    }

    function updateTotals() {
        const rows = productsTableBody.querySelectorAll('tr');
        let totalProducts = 0;
        let totalAmount = 0;
        
        rows.forEach(row => {
            totalProducts++;
            const lineTotal = parseFloat(row.querySelector('.line-total').value) || 0;
            totalAmount += lineTotal;
        });
        
        totalProductsSpan.textContent = '(' + totalProducts + ')';
        totalProductsInput.value = totalProducts;
        totalAmountSpan.textContent = '(Bs. ' + totalAmount.toFixed(2) + ')';
        totalAmountInput.value = totalAmount.toFixed(2);
    }

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Búsqueda de productos
    document.getElementById('product-search').addEventListener('input', debounce(function(e) {
        const searchTerm = e.target.value.trim();
        const searchResults = document.getElementById('search-results');
        
        if (searchTerm.length > 2) {
            fetch(`/purchases/search-products?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
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

    // Función para agregar producto a la tabla
    function addProductToTable(product) {
        productRowCounter++;
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors duration-150';
        row.innerHTML = `
            <td class="px-3 py-3 border-r border-[var(--gray-light)]">
                <div class="font-medium text-[var(--text-color)]">${escapeHtml(product.name)}</div>
                <div class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-tag mr-1"></i>${escapeHtml(product.category || 'Sin categoría')}
                </div>
                <input type="hidden" name="products[${productRowCounter}][product_id]" value="${product.id}">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][quantity]" value="1" min="1" 
                       class="w-20 text-center quantity-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][unit_cost]" value="0.00"
                       class="w-24 text-center unit-cost-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                       step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <div class="flex items-center justify-center gap-1">
                    <input type="number" name="products[${productRowCounter}][discount]" value="0"
                           class="w-16 text-center discount-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                           step="0.1" min="0" max="100" placeholder="0.0">
                    <span class="text-gray-500">%</span>
                </div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][unit_cost_after_discount]"
                       class="w-24 text-center unit-cost-after-discount border-2 border-blue-300 bg-blue-50 rounded-lg px-2 py-1.5 font-medium text-[var(--primary-color)] focus:outline-none focus:border-blue-500 transition-colors" 
                       step="0.01" min="0" placeholder="0.00">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][line_total]"
                       class="w-24 text-center line-total border-2 border-green-300 bg-green-50 rounded-lg px-2 py-1.5 font-bold text-green-700 focus:outline-none focus:border-green-500 transition-colors" 
                       step="0.01" min="0" placeholder="0.00">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][profit_margin]" value="0.00"
                       class="w-20 text-center profit-margin-input border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors"
                       step="0.01" min="-100" max="1000">
                <div class="profit-margin-display font-semibold text-xs mt-1 px-2 py-0.5 rounded bg-gray-100" style="color: #6c757d;">-</div>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" name="products[${productRowCounter}][selling_price]" value="${(product.price || 0).toFixed(2)}" 
                       class="w-24 text-center selling-price border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors" 
                       step="0.01" min="0" required>
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="date" name="products[${productRowCounter}][expiry_date]" 
                       class="w-full text-xs border-2 border-[var(--gray-light)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary-color)] transition-colors">
            </td>
            <td class="px-3 py-3 text-center">
                <button type="button" class="text-red-500 hover:text-white hover:bg-red-500 p-2 rounded-lg transition-all duration-150 remove-product">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        
        productsTableBody.appendChild(row);
        
        // Agregar event listeners
        row.querySelectorAll('.quantity-input, .unit-cost-input, .discount-input, .selling-price, .unit-cost-after-discount, .line-total, .profit-margin-input').forEach(input => {
            input.addEventListener('input', updateRowCalculations);
            input.addEventListener('change', updateRowCalculations);
        });
        
        row.querySelector('.remove-product').addEventListener('click', function() {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                row.remove();
                updateTotals();
            }
        });

        updateRowCalculations({ target: row.querySelector('.unit-cost-input') });
        updateTotals();
    }

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

    // Manejar el envío del formulario
    document.getElementById('purchase-form').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        
        // Validar proveedor
        const supplierId = document.getElementById('proveedor').value;
        if (!supplierId) {
            e.preventDefault();
            alert('Por favor seleccione un proveedor');
            return;
        }

        // Validar que haya productos
        const products = getProductsData();
        if (products.length === 0) {
            e.preventDefault();
            alert('Debe tener al menos un producto en la compra');
            return;
        }

        // Validar que todos los productos tengan cantidad
        const invalidProducts = products.filter(p => !p.quantity || p.quantity <= 0);
        if (invalidProducts.length > 0) {
            e.preventDefault();
            alert('Todos los productos deben tener una cantidad mayor a 0');
            return;
        }

        // Mostrar estado de carga
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';
    });

    // Inicializar totales
    updateTotals();
});
</script>
@endpush
@endsection