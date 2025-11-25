<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-[#203363]">Gestión de Inventario</h2>
    <div class="flex space-x-2">
        <a href="<?php echo e(route('inventory.movements')); ?>" 
           class="bg-[#6380a6] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
            <i class="fas fa-history mr-2"></i> Historial de Movimientos
        </a>
    </div>
</div>
<?php if(session('success')): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
    <ul>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>
<!-- Alerta informativa -->
<?php if($items->isEmpty()): ?>
<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm">
                No hay productos configurados para gestión de inventario. 
                <a href="<?php echo e(route('items.create')); ?>" class="font-medium underline hover:text-yellow-800">
                    Crear un producto con inventario habilitado
                </a>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Panel izquierdo: Lista de productos -->
    <div class="col-span-1 bg-white p-4 rounded-lg shadow-lg">
        <div class="mb-4">
            <input type="text" id="inventory-search" placeholder="Buscar producto..." 
                   class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
        </div>
        
        <!-- Filtros adicionales -->
        <div class="mb-4">
            <select id="stock-filter" class="w-full border rounded-lg p-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#203363]">
                <option value="all">Todos los productos</option>
                <option value="low-stock">Solo bajo stock</option>
                <option value="out-of-stock">Sin stock</option>
                <option value="in-stock">Con stock</option>
            </select>
        </div>
        
        <div class="overflow-y-auto" style="max-height: 60vh;">
            <?php if($items->count() > 0): ?>
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Producto</th>
                        <th class="text-right py-2">Stock</th>
                    </tr>
                </thead>
                <tbody id="inventory-items">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($item->manage_inventory): ?>
                    <tr class="border-b hover:bg-gray-50 cursor-pointer inventory-item" 
                        data-id="<?php echo e($item->id); ?>"
                        data-name="<?php echo e($item->name); ?>"
                        data-stock="<?php echo e($item->stock); ?>"
                        data-min-stock="<?php echo e($item->min_stock); ?>"
                        data-category="<?php echo e($item->category->name); ?>"
                        data-stock-type="<?php echo e($item->stock_type); ?>"
                        data-stock-unit="<?php echo e($item->stock_unit); ?>">
                        <td class="py-2">
                            <div class="font-medium"><?php echo e($item->name); ?></div>
                            <div class="text-sm text-gray-500">
                                <?php echo e($item->category->name); ?>

                                <span class="inline-block ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                    <i class="fas fa-boxes mr-1"></i>Inventario
                                </span>
                            </div>
                        </td>
                        <td class="text-right py-2">
                            <span class="font-bold <?php echo e($item->stock < $item->min_stock ? 'text-red-600' : ($item->stock == 0 ? 'text-red-700' : 'text-[#203363]')); ?>">
                                <?php echo e($item->stock); ?> <?php echo e($item->stock_type == 'discrete' ? ($item->stock_unit ?? 'unid.') : ($item->stock_unit ?? 'gr/ml')); ?>

                            </span>
                            <?php if($item->stock == 0): ?>
                                <span class="text-xs text-red-700 block font-semibold">(Sin stock)</span>
                            <?php elseif($item->stock < $item->min_stock): ?>
                                <span class="text-xs text-red-500 block">(Bajo stock)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-4"></i>
                <p>No hay productos con gestión de inventario</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel central: Formulario de actualización -->
    <div class="col-span-2 bg-white p-4 rounded-lg shadow-lg">
        <h3 class="text-lg font-bold text-[#203363] mb-4" id="selected-item-title">Seleccione un producto</h3>
        
        <div id="no-selection-message" class="text-center py-8 text-gray-500">
            <i class="fas fa-mouse-pointer text-4xl mb-4"></i>
            <p>Seleccione un producto de la lista para gestionar su inventario</p>
        </div>
        
        <form id="update-stock-form" action="<?php echo e(route('inventory.update-stock')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="item_id" id="item_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold text-[#203363] mb-1">Producto</label>
                    <input type="text" id="item_name" class="w-full border rounded-lg p-2 bg-gray-100" readonly>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-[#203363] mb-1">Stock actual</label>
                    <input type="text" id="current_stock" class="w-full border rounded-lg p-2 bg-gray-100" readonly>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold text-[#203363] mb-1">Tipo de movimiento</label>
                    <select name="movement_type" id="movement_type" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="addition">Ingreso de stock</option>
                        <option value="subtraction">Salida de stock</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-[#203363] mb-1">Cantidad</label>
                    <input type="number" name="quantity" id="quantity" 
                           class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#203363]" 
                           min="0.01" step="0.01" required>
                    <div id="quantity-helper" class="text-xs text-gray-500 mt-1"></div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-[#203363] mb-1">Notas</label>
                <textarea name="notes" id="notes" rows="2" 
                          class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#203363]"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
                    <i class="fas fa-save mr-2"></i> Guardar movimiento
                </button>
            </div>
        </form>
        
        <!-- Sección de historial reciente -->
        <div id="recent-movements-section" class="mt-8" style="display: none;">
            <h4 class="text-md font-bold text-[#203363] mb-2">Últimos movimientos</h4>
            <div class="overflow-y-auto" style="max-height: 30vh;">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Fecha</th>
                            <th class="text-left py-2">Tipo</th>
                            <th class="text-right py-2">Cantidad</th>
                            <th class="text-right py-2">Nuevo stock</th>
                        </tr>
                    </thead>
                    <tbody id="recent-movements">
                        <!-- Se llenará con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateForm = document.getElementById('update-stock-form');
    const noSelectionMessage = document.getElementById('no-selection-message');
    const recentMovementsSection = document.getElementById('recent-movements-section');
    
    // Validación del formulario de actualización de stock
    updateForm.addEventListener('submit', function(e) {
        const quantityInput = document.getElementById('quantity');
        const movementType = document.getElementById('movement_type');
        const currentStock = parseFloat(document.getElementById('current_stock').value.split(' ')[0]) || 0;
        const quantity = parseFloat(quantityInput.value);
        
        // Validar cantidad
        if (isNaN(quantity) || quantity <= 0) {
            e.preventDefault();
            alert('La cantidad debe ser un número válido mayor a 0');
            quantityInput.focus();
            return;
        }
        
        // Validar que no sea resta mayor al stock disponible
        if (movementType.value === 'subtraction' && quantity > currentStock) {
            e.preventDefault();
            alert(`No puede restar más cantidad de la que hay en stock. Stock disponible: ${currentStock}`);
            quantityInput.focus();
            return;
        }
        
        // Confirmar la acción
        const action = movementType.value === 'addition' ? 'agregar' : 'quitar';
        const confirmation = confirm(`¿Está seguro que desea ${action} ${quantity} unidades?`);
        if (!confirmation) {
            e.preventDefault();
        }
    });

    // Actualizar validación en tiempo real
    document.getElementById('quantity').addEventListener('input', function() {
        const quantity = parseFloat(this.value);
        const movementType = document.getElementById('movement_type').value;
        const currentStockText = document.getElementById('current_stock').value;
        const currentStock = parseFloat(currentStockText.split(' ')[0]) || 0;
        const helper = document.getElementById('quantity-helper');
        
        if (movementType === 'subtraction' && quantity > currentStock) {
            this.setCustomValidity('No hay suficiente stock disponible');
            helper.textContent = `⚠️ Stock insuficiente. Disponible: ${currentStock}`;
            helper.className = 'text-xs text-red-500 mt-1';
        } else {
            this.setCustomValidity('');
            if (quantity > 0) {
                const newStock = movementType === 'addition' ? currentStock + quantity : currentStock - quantity;
                helper.textContent = `Nuevo stock será: ${newStock}`;
                helper.className = 'text-xs text-blue-500 mt-1';
            } else {
                helper.textContent = '';
            }
        }
    });

    // Selección de producto
    document.querySelectorAll('.inventory-item').forEach(item => {
        item.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');
            const itemStock = this.getAttribute('data-stock');
            const itemCategory = this.getAttribute('data-category');
            const stockType = this.getAttribute('data-stock-type');
            const stockUnit = this.getAttribute('data-stock-unit') || (stockType === 'discrete' ? 'unid.' : 'gr/ml');
            
            // Mostrar formulario y ocultar mensaje
            updateForm.style.display = 'block';
            recentMovementsSection.style.display = 'block';
            noSelectionMessage.style.display = 'none';
            
            // Actualizar formulario
            document.getElementById('selected-item-title').textContent = itemName;
            document.getElementById('item_id').value = itemId;
            document.getElementById('item_name').value = `${itemName} (${itemCategory})`;
            document.getElementById('current_stock').value = `${itemStock} ${stockUnit}`;
            
            // Actualizar step del input según el tipo
            const quantityInput = document.getElementById('quantity');
            if (stockType === 'discrete') {
                quantityInput.step = '1';
                quantityInput.min = '1';
            } else {
                quantityInput.step = '0.01';
                quantityInput.min = '0.01';
            }
            
            // Limpiar campos
            quantityInput.value = '';
            document.getElementById('notes').value = '';
            document.getElementById('quantity-helper').textContent = '';
            
            // Quitar selección previa
            document.querySelectorAll('.inventory-item').forEach(i => {
                i.classList.remove('bg-[#203363]', 'text-white');
            });
            
            // Resaltar selección actual
            this.classList.add('bg-[#203363]', 'text-white');
            
            // Cargar movimientos recientes
            loadRecentMovements(itemId);
        });
    });
    
    // Búsqueda de productos
    document.getElementById('inventory-search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterItems();
    });
    
    // Filtro por estado de stock
    document.getElementById('stock-filter').addEventListener('change', function() {
        filterItems();
    });
    
    function filterItems() {
        const searchTerm = document.getElementById('inventory-search').value.toLowerCase();
        const stockFilter = document.getElementById('stock-filter').value;
        
        document.querySelectorAll('.inventory-item').forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const category = item.getAttribute('data-category').toLowerCase();
            const stock = parseFloat(item.getAttribute('data-stock'));
            const minStock = parseFloat(item.getAttribute('data-min-stock'));
            
            // Filtro de búsqueda
            const matchesSearch = name.includes(searchTerm) || category.includes(searchTerm);
            
            // Filtro de stock
            let matchesStockFilter = true;
            switch(stockFilter) {
                case 'low-stock':
                    matchesStockFilter = stock < minStock && stock > 0;
                    break;
                case 'out-of-stock':
                    matchesStockFilter = stock <= 0;
                    break;
                case 'in-stock':
                    matchesStockFilter = stock > 0;
                    break;
            }
            
            if (matchesSearch && matchesStockFilter) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Cambiar validación al cambiar tipo de movimiento
    document.getElementById('movement_type').addEventListener('change', function() {
        document.getElementById('quantity').dispatchEvent(new Event('input'));
    });
    
    // Cargar movimientos recientes
    function loadRecentMovements(itemId) {
    const url = `/inventory/${itemId}/movements`;
    console.log('Cargando movimientos para item:', itemId);
    console.log('URL completa:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error text:', text);
                    throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            const tbody = document.getElementById('recent-movements');
            tbody.innerHTML = '';
            
            if (!data || data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="4" class="py-4 text-center text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>No hay movimientos registrados
                    </td>
                `;
                tbody.appendChild(row);
                return;
            }
            
            data.forEach(movement => {
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50';
                
                const movementTypeText = movement.movement_type === 'addition' ? 'Ingreso' : 'Salida';
                
                // Obtener la unidad del producto
                const stockUnit = movement.menu_item?.stock_unit || 'unid.';
                
                row.innerHTML = `
                    <td class="py-2 text-sm">${new Date(movement.created_at).toLocaleString('es-ES')}</td>
                    <td class="py-2">
                        <span class="px-2 py-1 text-xs rounded-full ${movement.movement_type === 'addition' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${movementTypeText}
                        </span>
                    </td>
                    <td class="text-right py-2 ${movement.movement_type === 'addition' ? 'text-green-600' : 'text-red-600'} font-medium">
                        ${movement.movement_type === 'addition' ? '+' : '-'}${movement.quantity} ${stockUnit}
                    </td>
                    <td class="text-right py-2 font-bold">${movement.new_stock} ${stockUnit}</td>
                `;
                
                tbody.appendChild(row);
            });
            
            console.log('Movimientos cargados exitosamente');
        })
        .catch(error => {
            console.error('Error completo:', error);
            const tbody = document.getElementById('recent-movements');
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-4 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <div>Error al cargar movimientos</div>
                        <div class="text-xs mt-1">${error.message}</div>
                    </td>
                </tr>
            `;
        });
}
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/inventory/index.blade.php ENDPATH**/ ?>