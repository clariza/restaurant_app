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

    <!-- Encabezado con título -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold mb-2 text-[var(--primary-color)] relative pb-2 section-title">
                Editar Compra #{{ $purchase->reference_number }}
            </h1>
            <p class="text-sm text-[var(--text-light)]">Modifique los detalles de la compra</p>
        </div>
        <a href="{{ route('purchases.show', $purchase->id) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
  
    <!-- Formulario de compra -->
    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchase-form" class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6">
        @csrf
        @method('PUT')
        
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
                                    data-address="{{ $supplier->address }}"
                                    {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="mt-4 text-xs font-semibold text-[var(--text-light)]">
                    Dirección: <span id="supplier-address" class="font-normal text-[var(--text-color)]">{{ $purchase->supplier->address ?? '-' }}</span>
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
                           value="{{ $purchase->supplier->nit ?? '-' }}"
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
                       value="{{ $purchase->reference_number }}"
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
                           value="{{ $purchase->purchase_date->format('Y-m-d\TH:i') }}" 
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
                <a href="{{ route('items.create') }}" class="text-[var(--primary-color)] hover:text-[var(--primary-light)] text-sm font-normal flex items-center space-x-1 transition duration-200">
                    <i class="fas fa-plus"></i>
                    <span>Agregar nuevo producto</span>
                </a>
            </div>

            <!-- Tabla de productos -->
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
                                       class="w-20 text-center quantity-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]">
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="number" name="products[{{ $index }}][unit_cost]" value="{{ $stock->unit_cost }}"
                                       class="w-24 text-center unit-cost-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                                       step="0.01" min="0" required>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" name="products[{{ $index }}][discount]" value="{{ $stock->discount }}"
                                           class="w-16 text-center discount-input border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                                           step="0.1" min="0" max="100">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="number" 
                                       class="w-24 text-center unit-cost-after-discount bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-medium text-[var(--primary-color)]" 
                                       value="{{ number_format($stock->unit_cost * (1 - $stock->discount / 100), 2) }}"
                                       step="0.01" readonly>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="number" 
                                       class="w-24 text-center line-total bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-bold text-[var(--primary-color)]" 
                                       value="{{ number_format($stock->total_cost, 2) }}"
                                       step="0.01" readonly>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <div class="profit-margin font-semibold text-sm" style="color: {{ $stock->profit_margin >= 30 ? '#28a745' : ($stock->profit_margin >= 15 ? '#ffc107' : '#dc3545') }};">
                                    {{ number_format($stock->profit_margin, 2) }}%
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="number" name="products[{{ $index }}][selling_price]" value="{{ $stock->selling_price }}"
                                       class="w-24 text-center selling-price border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]" 
                                       step="0.01" min="0" required>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                                <input type="date" name="products[{{ $index }}][expiry_date]" 
                                       value="{{ $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : '' }}"
                                       class="w-full text-xs border border-[var(--gray-light)] rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]">
                            </td>
                            <td class="px-3 py-3 text-center">
                                <button type="button" class="text-[var(--red)] hover:text-red-700 hover:bg-red-50 p-2 rounded transition-colors duration-150 remove-product">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totales -->
            <div class="flex justify-end space-x-8 text-sm text-[var(--text-color)] font-semibold mt-4 pt-4 border-t border-[var(--gray-light)]">
                <div class="flex items-center space-x-2">
                    <span>Total Productos:</span>
                    <span class="font-bold text-[var(--primary-color)] text-base" id="total-products">{{ $purchase->stocks->count() }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span>Importe Total Neto:</span>
                    <span class="font-bold text-[var(--primary-color)] text-base" id="total-amount">Bs. {{ number_format($purchase->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('purchases.show', $purchase->id) }}" class="bg-[var(--gray-light)] hover:bg-gray-300 text-[var(--text-color)] px-6 py-2 rounded-lg transition duration-200">
                Cancelar
            </a>
            <button type="submit" class="bg-[var(--primary-color)] hover:bg-[var(--primary-light)] text-white px-6 py-2 rounded-lg transition duration-200">
                Actualizar Compra
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let productRowCounter = {{ $purchase->stocks->count() }};
    const productsTableBody = document.getElementById('products-table-body');
    const totalProductsSpan = document.getElementById('total-products');
    const totalAmountSpan = document.getElementById('total-amount');

    // Actualizar NIT y dirección del proveedor
    document.getElementById('proveedor').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const nit = selectedOption.getAttribute('data-nit') || '-';
        const address = selectedOption.getAttribute('data-address') || '-';
        
        document.getElementById('nit').value = nit;
        document.getElementById('supplier-address').textContent = address;
    });

    // Agregar event listeners a productos existentes
    document.querySelectorAll('#products-table-body tr').forEach(row => {
        row.querySelectorAll('.quantity-input, .unit-cost-input, .discount-input, .selling-price').forEach(input => {
            input.addEventListener('input', updateRowCalculations);
            input.addEventListener('change', updateRowCalculations);
        });
        
        row.querySelector('.remove-product').addEventListener('click', function() {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                row.remove();
                updateTotals();
            }
        });
    });

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

        // Validar que haya productos
        const products = getProductsData();
        if (products.length === 0) {
            alert('Debe agregar al menos un producto');
            return;
        }

        // Preparar datos del formulario
        const formData = new FormData(form);

        // Mostrar estado de carga
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';

        // Enviar solicitud
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                alert(data.message);
                window.location.href = data.redirect_url || "{{ route('purchases.index') }}";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar la compra. Por favor, intente nuevamente.');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Actualizar Compra';
        });
    });

    // El resto del código JavaScript es igual al de create.blade.php
    // (funciones: getProductsData, debounce, addProductToTable, updateRowCalculations, updateTotals, escapeHtml, búsqueda de productos)
    
    // Copiar aquí todas las funciones del create.blade.php
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
            
            if (productId && quantity > 0 && unitCost > 0) {
                const product = {
                    product_id: productId,
                    quantity: quantity,
                    unit_cost: unitCost,
                    discount: discount,
                    selling_price: sellingPrice,
                    expiry_date: expiryDate || null
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
                <input type="number" 
                       class="w-24 text-center unit-cost-after-discount bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-medium text-[var(--primary-color)]" 
                       step="0.01" readonly placeholder="0.00">
            </td>
            <td class="px-3 py-3 text-center border-r border-[var(--gray-light)]">
                <input type="number" 
                       class="w-24 text-center line-total bg-gray-50 border border-[var(--gray-light)] rounded px-2 py-1 font-bold text-[var(--primary-color)]" 
                       step="0.01" readonly placeholder="0.00">
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
                <button type="button" class="text-[var(--red)] hover:text-red-700 hover:bg-red-50 p-2 rounded transition-colors duration-150 remove-product">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        
        productsTableBody.appendChild(row);
        
        // Agregar event listeners
        row.querySelectorAll('.quantity-input, .unit-cost-input, .discount-input, .selling-price').forEach(input => {
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

    function updateRowCalculations(event) {
        const row = event.target.closest('tr');
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
        const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
        const sellingPrice = parseFloat(row.querySelector('.selling-price').value) || 0;
        
        if (unitCost > 0) {
            const discountAmount = unitCost * (discount / 100);
            const unitCostAfterDiscount = unitCost - discountAmount;
            const lineTotal = quantity * unitCostAfterDiscount;
            
            row.querySelector('.unit-cost-after-discount').value = unitCostAfterDiscount.toFixed(2);
            row.querySelector('.line-total').value = lineTotal.toFixed(2);
            
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
        
        totalProductsSpan.textContent = totalProducts;
        totalAmountSpan.textContent = 'Bs. ' + totalAmount.toFixed(2);
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
                .then(response => {
                    if (!response.ok) throw new Error('Error en la respuesta del servidor');
                    return response.json();
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
</script>
@endpush
@endsection