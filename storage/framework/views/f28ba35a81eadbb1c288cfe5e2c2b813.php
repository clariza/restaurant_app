

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-[#203363]">Configuración de Mesas</h1>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <a href="<?php echo e(url()->previous()); ?>" class="bg-[#6380a6] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200 inline-flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Atrás
            </a>
            <?php if($tablesEnabled): ?>
                <button type="button" onclick="openBulkStateModalDirect()" class="bg-[#f59e0b] text-white px-4 py-2 rounded-lg hover:bg-[#d97706] transition duration-200 inline-flex items-center justify-center">
                    <i class="fas fa-sync-alt mr-2"></i> Cambiar Todas
                </button>
                <button type="button" onclick="openCreateModalDirect()" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200 inline-flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i> Crear Mesa
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Configuración de gestión de mesas -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-lg font-medium text-[#203363] mb-4">Habilitar Gestión de Mesas</h2>
            <form action="<?php echo e(route('settings.update')); ?>" method="POST" id="settings-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="tables_enabled" value="0">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="tables_enabled" value="1" 
                           class="sr-only peer" <?php echo e($settings->tables_enabled ? 'checked' : ''); ?>>
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#203363]/50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#203363]"></div>
                    <span class="ms-3 text-sm font-medium text-[#203363]">
                        <?php echo e($settings->tables_enabled ? 'Activado' : 'Desactivado'); ?>

                    </span>
                </label>
                <button type="submit" class="ml-4 bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200">
                    Guardar Configuración
                </button>
            </form>
        </div>
    </div>

    <?php if($tablesEnabled): ?>
    <!-- Modal para crear mesas (INDEPENDIENTE) -->
    <div id="createTableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-[#203363]">Crear Nueva Mesa</h3>
                <form action="<?php echo e(route('tables.store')); ?>" method="POST" class="mt-2">
                    <?php echo csrf_field(); ?>
                    <div class="mt-4">
                        <label for="number" class="block text-sm font-medium text-[#203363]">Número de Mesa</label>
                        <input type="text" name="number" id="number" class="mt-1 block w-full px-3 py-2 border border-[#a4b6ce] rounded-md shadow-sm focus:outline-none focus:ring-[#203363] focus:border-[#203363] sm:text-sm" required>
                    </div>
                    <div class="mt-4">
                        <label for="state" class="block text-sm font-medium text-[#203363]">Estado</label>
                        <select name="state" id="state" class="mt-1 block w-full px-3 py-2 border border-[#a4b6ce] rounded-md shadow-sm focus:outline-none focus:ring-[#203363] focus:border-[#203363] sm:text-sm" required>
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Reservada">Reservada</option>
                            <option value="No Disponible">No Disponible</option>
                        </select>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="closeCreateModalDirect()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 hover:bg-gray-600 transition duration-200">Cancelar</button>
                        <button type="submit" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para cambio masivo de estado (INDEPENDIENTE) -->
    <div id="bulkStateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-[#203363]">
                        <i class="fas fa-sync-alt mr-2"></i>Cambiar Estado de Todas las Mesas
                    </h3>
                    <button onclick="closeBulkStateModalDirect()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                    <p class="text-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Esta acción cambiará el estado de <strong>todas las mesas</strong> registradas en el sistema.
                    </p>
                </div>

                <form id="bulkStateForm" onsubmit="handleBulkStateSubmit(event)">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label for="bulk_state" class="block text-sm font-medium text-[#203363] mb-2">
                            Seleccione el nuevo estado:
                        </label>
                        <select name="state" id="bulk_state" class="mt-1 block w-full px-3 py-2 border border-[#a4b6ce] rounded-md shadow-sm focus:outline-none focus:ring-[#203363] focus:border-[#203363] sm:text-sm" required>
                            <option value="">-- Seleccione un estado --</option>
                            <option value="Disponible" class="text-green-600">✓ Disponible</option>
                            <option value="Ocupada" class="text-red-600">● Ocupada</option>
                            <option value="Reservada" class="text-yellow-600">◐ Reservada</option>
                            <option value="No Disponible" class="text-gray-600">✗ No Disponible</option>
                        </select>
                    </div>

                    <!-- Estadísticas actuales -->
                    <div id="currentStats" class="mb-4 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm font-medium text-[#203363] mb-2">Estado actual de las mesas:</p>
                        <div id="statsContent" class="text-xs text-gray-600">
                            Cargando estadísticas...
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeBulkStateModalDirect()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" class="bg-[#f59e0b] text-white px-4 py-2 rounded-lg hover:bg-[#d97706] transition duration-200">
                            <i class="fas fa-check mr-2"></i>Aplicar a Todas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de mesas -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-[#203363]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tables-tbody">
                    <?php $__empty_1 = true; $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4 text-[#203363]">Mesa <?php echo e($table->number); ?></td>
                        <td class="px-6 py-4 text-[#203363]">
                            <span class="px-2 py-1 rounded-full text-xs 
                                <?php if($table->state == 'Disponible'): ?> bg-green-100 text-green-800
                                <?php elseif($table->state == 'Ocupada'): ?> bg-red-100 text-red-800
                                <?php elseif($table->state == 'Reservada'): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                <?php echo e($table->state); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo e(route('tables.edit', $table->id)); ?>" class="text-[#203363] hover:text-[#47517c] mr-2">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </a>
                            <form action="<?php echo e(route('tables.destroy', $table->id)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('¿Estás seguro de que deseas eliminar esta mesa?')">
                                    <i class="fas fa-trash mr-1"></i>Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            No hay mesas registradas. Crea una nueva mesa para comenzar.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <p class="text-[#203363]">La gestión de mesas está actualmente desactivada. Actívala para comenzar a gestionar las mesas de tu restaurante.</p>
    </div>
    <?php endif; ?>
</div>

<script>
    console.log('🚀 Script de tables/index.blade.php cargado');

    // ============================================
    // FUNCIONES PARA MODALES DE ESTA PÁGINA
    // ============================================

    // Funciones del modal de crear mesa
    function openCreateModalDirect() {
        console.log('✅ Abriendo modal de crear mesa (DIRECTO)');
        const modal = document.getElementById('createTableModal');
        if (modal) {
            modal.classList.remove('hidden');
        } else {
            console.error('❌ Modal createTableModal no encontrado');
        }
    }

    function closeCreateModalDirect() {
        console.log('✅ Cerrando modal de crear mesa (DIRECTO)');
        const modal = document.getElementById('createTableModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Funciones del modal de cambio masivo
    function openBulkStateModalDirect() {
        console.log('✅ Abriendo modal de cambio masivo (DIRECTO)');
        const modal = document.getElementById('bulkStateModal');
        if (modal) {
            modal.classList.remove('hidden');
            loadTableStatsDirect();
        } else {
            console.error('❌ Modal bulkStateModal no encontrado');
        }
    }

    function closeBulkStateModalDirect() {
        console.log('✅ Cerrando modal de cambio masivo (DIRECTO)');
        const modal = document.getElementById('bulkStateModal');
        const form = document.getElementById('bulkStateForm');
        
        if (modal) {
            modal.classList.add('hidden');
        }
        
        if (form) {
            form.reset();
        }
    }

    // Cargar estadísticas de mesas
    async function loadTableStatsDirect() {
        console.log('📊 Cargando estadísticas de mesas...');
        const statsContent = document.getElementById('statsContent');
        
        if (!statsContent) {
            console.error('❌ Elemento statsContent no encontrado');
            return;
        }

        try {
            const response = await fetch('<?php echo e(route("tables.stats")); ?>', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const statsHtml = `
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            <span>Disponible: <strong>${data.stats.Disponible || 0}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            <span>Ocupada: <strong>${data.stats.Ocupada || 0}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                            <span>Reservada: <strong>${data.stats.Reservada || 0}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-gray-500 rounded-full mr-2"></span>
                            <span>No Disponible: <strong>${data.stats['No Disponible'] || 0}</strong></span>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-200">
                        <span>Total de mesas: <strong>${data.total}</strong></span>
                    </div>
                `;
                statsContent.innerHTML = statsHtml;
                console.log('✅ Estadísticas cargadas correctamente');
            } else {
                throw new Error('Error al cargar estadísticas');
            }
        } catch (error) {
            console.error('❌ Error al cargar estadísticas:', error);
            statsContent.innerHTML = '<span class="text-red-500">Error al cargar estadísticas</span>';
        }
    }

    // Manejar el envío del formulario de cambio masivo
    async function handleBulkStateSubmit(event) {
        event.preventDefault();
        console.log('📤 Enviando cambio masivo de estado...');
        
        const form = event.target;
        const formData = new FormData(form);
        const newState = formData.get('state');
        
        if (!newState) {
            alert('Por favor seleccione un estado');
            return;
        }
        
        // // Confirmación
        // if (!confirm(`¿Está seguro de que desea cambiar TODAS las mesas al estado "${newState}"?`)) {
        //     return;
        // }
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
            
            const response = await fetch('<?php echo e(route("tables.bulk-state")); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Error al actualizar las mesas');
            }
            
            // Mostrar mensaje de éxito
            alert(`✓ ${data.message}\nMesas actualizadas: ${data.updated_count}`);
            
            // Recargar la página
            window.location.reload();
            
        } catch (error) {
            console.error('❌ Error:', error);
            alert('Error: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    // Configuración del formulario de settings
    document.getElementById('settings-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Guardando...';
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Error al guardar');
            }
            
            window.location.reload();
            
        } catch (error) {
            console.error('Error:', error);
            alert(error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Cerrar modales al hacer clic fuera
    window.addEventListener('click', function(event) {
        const createModal = document.getElementById('createTableModal');
        const bulkModal = document.getElementById('bulkStateModal');
        
        if (event.target === createModal) {
            closeCreateModalDirect();
        }
        if (event.target === bulkModal) {
            closeBulkStateModalDirect();
        }
    });

    console.log('✅ Script de tables/index.blade.php inicializado correctamente');
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/tables/index.blade.php ENDPATH**/ ?>