

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado y controles -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-[#203363]">
            <i class="fas fa-list-alt mr-2"></i> Historial de Ventas
        </h1>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="<?php echo e(route('menu.index')); ?>" 
               class="bg-[#6380a6] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Menú
            </a>
            
            <div class="relative">
                <input type="text" id="search-input" placeholder="Buscar..." 
                       class="border rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-[#203363]"
                       value="<?php echo e(request('search')); ?>">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Mensaje de éxito/error -->
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filtro por tipo -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Tipo:</label>
                    <select name="type" class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" <?php echo e(request('type') == 'all' ? 'selected' : ''); ?>>Todos</option>
                        <option value="Comer aquí" <?php echo e(request('type') == 'Comer aquí' ? 'selected' : ''); ?>>Comer aquí</option>
                        <option value="Para llevar" <?php echo e(request('type') == 'Para llevar' ? 'selected' : ''); ?>>Para llevar</option>
                        <option value="Recoger" <?php echo e(request('type') == 'Recoger' ? 'selected' : ''); ?>>Recoger</option>
                        <option value="proforma" <?php echo e(request('type') == 'proforma' ? 'selected' : ''); ?>>Proformas</option>
                    </select>
                </div>
                
                <!-- Filtro por fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Desde:</label>
                    <input type="date" 
                           name="date_from" 
                           class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]"
                           value="<?php echo e(request('date_from')); ?>">
                </div>
                
                <!-- Filtro por fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Hasta:</label>
                    <input type="date" 
                           name="date_to" 
                           class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]"
                           value="<?php echo e(request('date_to')); ?>">
                </div>
                
                <!-- Filtro por vendedor -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Vendedor:</label>
                    <select name="seller_id" class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" <?php echo e(request('seller_id') == 'all' ? 'selected' : ''); ?>>Todos</option>
                        <?php $__currentLoopData = $sellers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($seller->id); ?>" <?php echo e(request('seller_id') == $seller->id ? 'selected' : ''); ?>>
                                <?php echo e($seller->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <button type="submit" 
                        class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i> Aplicar Filtros
                </button>
                
                <button type="button" 
                        onclick="clearFilters()"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Limpiar Filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Listado de órdenes y proformas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Encabezados de la tabla -->
        <div class="grid grid-cols-12 bg-[#203363] text-white p-4 font-bold text-sm">
            <div class="col-span-2 md:col-span-1">ID</div>
            <div class="col-span-4 md:col-span-2">Fecha/Hora</div>
            <div class="hidden md:block md:col-span-2">Cliente</div>
            <div class="col-span-3 md:col-span-2">Tipo</div>
            <div class="hidden md:block md:col-span-1">Items</div>
            <div class="col-span-3 md:col-span-2">Total</div>
            <div class="hidden md:block md:col-span-2">Acciones</div>
        </div>
        
        <!-- Cuerpo de la tabla - ORDEN ASCENDENTE -->
        <?php $__empty_1 = true; $__currentLoopData = $orders->merge($proformas)->sortBy('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isProforma = $record instanceof \App\Models\Proforma;
                $badgeColor = $isProforma ? 'bg-[#EF476F]' : 'bg-[#203363]';
                $typeColor = [
                    'Comer aquí' => 'bg-[#FFD166] text-[#203363]',
                    'Para llevar' => 'bg-[#06D6A0] text-white',
                    'Recoger' => 'bg-[#118AB2] text-white',
                    'proforma' => 'bg-[#EF476F] text-white'
                ][$isProforma ? 'proforma' : $record->order_type];
            ?>
            
            <div class="grid grid-cols-12 p-4 border-b hover:bg-gray-50 items-center text-sm">
                <!-- ID -->
                <div class="col-span-2 md:col-span-1 font-medium">
                    <span class="inline-block w-6 h-6 rounded-full <?php echo e($badgeColor); ?> text-white text-xs flex items-center justify-center mr-1">
                        <?php echo e($isProforma ? 'P' : 'O'); ?>

                    </span>
                    <?php echo e($isProforma ? 'PROF-'.$record->id : $record->transaction_number); ?>

                </div>
                
                <!-- Fecha -->
                <div class="col-span-4 md:col-span-2">
                    <div><?php echo e($record->created_at->format('d/m/Y')); ?></div>
                    <div class="text-xs text-gray-500"><?php echo e($record->created_at->format('H:i')); ?></div>
                </div>
                
                <!-- Cliente (solo desktop) -->
                <div class="hidden md:block md:col-span-2 truncate">
                    <?php echo e($record->customer_name ?? 'N/A'); ?>

                </div>
                
                <!-- Tipo -->
                <div class="col-span-3 md:col-span-2">
                    <span class="px-2 py-1 rounded-full text-xs <?php echo e($typeColor); ?>">
                        <?php echo e($isProforma ? 'Proforma' : $record->order_type); ?>

                        <?php if(!$isProforma && $record->order_type == 'Comer aquí' && $record->table_number): ?>
                            (Mesa <?php echo e($record->table_number); ?>)
                        <?php endif; ?>
                    </span>
                </div>
                
                <!-- Items -->
                <div class="hidden md:block md:col-span-1 text-center">
                    <?php echo e($record->items->count()); ?>

                </div>
                
                <!-- Total -->
                <div class="col-span-3 md:col-span-2 font-bold">
                    $<?php echo e(number_format($record->total, 2)); ?>

                </div>
                
                <!-- Acciones (solo desktop) -->
                <div class="hidden md:flex md:col-span-2 space-x-2">
                    <a href="<?php echo e($isProforma ? route('proformas.show', $record->id) : route('orders.show', $record->id)); ?>" 
                       class="text-[#203363] hover:text-[#47517c] p-1"
                       title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    
                    <button class="text-[#203363] hover:text-[#47517c] p-1"
                            onclick="printOrder('<?php echo e($isProforma ? 'proforma' : 'order'); ?>', '<?php echo e($record->id); ?>')"
                            title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    
                    <?php if($isProforma && method_exists($record, 'canBeConverted') && $record->canBeConverted()): ?>
                        <button class="text-green-600 hover:text-green-800 p-1"
                                onclick="convertToOrder('<?php echo e($record->id); ?>')"
                                title="Convertir a orden">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php if(!$isProforma && $hasOpenPettyCash): ?>
                        <button class="text-red-600 hover:text-red-800 p-1"
                                onclick="deleteOrder('<?php echo e($record->id); ?>', '<?php echo e($record->transaction_number); ?>')"
                                title="Eliminar orden">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Acciones móvil -->
                <div class="md:hidden col-span-12 mt-2 pt-2 border-t flex justify-end space-x-3">
                    <a href="<?php echo e($isProforma ? route('proformas.show', $record->id) : route('orders.show', $record->id)); ?>" 
                       class="text-[#203363] hover:text-[#47517c] text-sm flex items-center">
                        <i class="fas fa-eye mr-1"></i> Ver
                    </a>
                    
                    <button class="text-[#203363] hover:text-[#47517c] text-sm flex items-center"
                            onclick="printOrder('<?php echo e($isProforma ? 'proforma' : 'order'); ?>', '<?php echo e($record->id); ?>')">
                        <i class="fas fa-print mr-1"></i> Imprimir
                    </button>
                    
                    <?php if($isProforma && method_exists($record, 'canBeConverted') && $record->canBeConverted()): ?>
                        <button class="text-green-600 hover:text-green-800 text-sm flex items-center"
                                onclick="convertToOrder('<?php echo e($record->id); ?>')">
                            <i class="fas fa-exchange-alt mr-1"></i> Convertir
                        </button>
                    <?php endif; ?>
                    
                    <?php if(!$isProforma && $hasOpenPettyCash): ?>
                        <button class="text-red-600 hover:text-red-800 text-sm flex items-center"
                                onclick="deleteOrder('<?php echo e($record->id); ?>', '<?php echo e($record->transaction_number); ?>')">
                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                <p class="text-lg">No se encontraron órdenes o proformas</p>
                <p class="text-sm">Intenta con otros criterios de búsqueda</p>
            </div>
        <?php endif; ?>
        
        <!-- Paginación -->
        <?php if($orders->count() > 0 || $proformas->count() > 0): ?>
            <div class="p-4 border-t">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-2 md:mb-0">
                        Mostrando <?php echo e($orders->firstItem() ?? $proformas->firstItem() ?? 0); ?> a 
                        <?php echo e($orders->lastItem() ?? $proformas->lastItem() ?? 0); ?> de 
                        <?php echo e($orders->total() + $proformas->total()); ?> registros
                    </div>
                    <div class="flex space-x-1">
                        <?php echo e($orders->withQueryString()->links()); ?>

                        <?php if(request('type') === 'all' || request('type') === 'proforma'): ?>
                            <?php echo e($proformas->withQueryString()->links()); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Formulario oculto para eliminación -->
<form id="delete-order-form" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<!-- Scripts para manejar interacciones -->
<script>
    // Aplicar filtros al enviar el formulario
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData).toString();
        window.location.href = "<?php echo e(route('orders.index')); ?>?" + params;
    });
    
    // Limpiar filtros
    function clearFilters() {
        window.location.href = "<?php echo e(route('orders.index')); ?>";
    }
    
    // Búsqueda en tiempo real con debounce
    let searchTimer;
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const searchValue = this.value.trim();
            const url = new URL(window.location.href);
            
            if (searchValue) {
                url.searchParams.set('search', searchValue);
            } else {
                url.searchParams.delete('search');
            }
            
            window.location.href = url.toString();
        }, 500);
    });
    
    // Función para imprimir orden/proforma
    function printOrder(type, id) {
        const url = type === 'proforma' 
            ? `/proformas/${id}/print` 
            : `/orders/${id}/print`;
            
        window.open(url, '_blank');
    }
    
    // Función para eliminar orden
    function deleteOrder(orderId, orderNumber) {
        Swal.fire({
            title: '¿Eliminar orden?',
            html: `¿Estás seguro de eliminar la orden <strong>${orderNumber}</strong>?<br><br>
                   <span class="text-red-600">Esta acción revertirá el stock de los productos y no se puede deshacer.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Error: ${error.message || 'Error al eliminar la orden'}`
                    );
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        title: '¡Eliminada!',
                        text: result.value.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', result.value.message, 'error');
                }
            }
        });
    }
    
    // Función para convertir proforma a orden
    function convertToOrder(proformaId) {
        Swal.fire({
            title: '¿Convertir proforma a orden?',
            text: "Esta acción registrará la venta en la caja chica actual",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#203363',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, convertir',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/proformas/${proformaId}/convert`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Error: ${error.message || 'Error en la solicitud'}`
                    );
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        title: '¡Conversión exitosa!',
                        html: `Orden creada: <strong>${result.value.order_number}</strong>`,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', result.value.message, 'error');
                }
            }
        });
    }
</script>

<style>
    /* Estilos personalizados para la paginación */
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
    }
    
    .pagination li {
        margin: 0 2px;
    }
    
    .pagination li a,
    .pagination li span {
        display: block;
        padding: 5px 10px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }
    
    .pagination li.active span {
        background-color: #203363;
        color: white;
        border-color: #203363;
    }
    
    .pagination li a:hover {
        background-color: #f8fafc;
    }
    
    /* Asegurar que la tabla sea responsive */
    @media (max-width: 768px) {
        .grid-cols-12 > div {
            padding: 8px 4px;
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/orders/index.blade.php ENDPATH**/ ?>