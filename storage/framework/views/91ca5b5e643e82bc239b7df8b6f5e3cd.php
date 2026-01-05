

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-store mr-3 text-[#203363]"></i>
                Gestión de Sucursales
            </h1>
            <p class="text-gray-600 mt-2">Administra las sucursales de tu negocio</p>
        </div>
        <a href="<?php echo e(route('branches.create')); ?>" 
           class="bg-[#203363] text-white px-6 py-3 rounded-lg hover:bg-[#1a2850] transition duration-200 flex items-center shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>
            Nueva Sucursal
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if(session('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tabla de Sucursales -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#203363]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-hashtag mr-1"></i> Código
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-store mr-1"></i> Nombre
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-map-marker-alt mr-1"></i> Ubicación
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-phone mr-1"></i> Contacto
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-star mr-1"></i> Principal
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-toggle-on mr-1"></i> Estado
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i> Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <!-- Código -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900">
                                    <?php echo e($branch->code); ?>

                                </span>
                            </td>

                            <!-- Nombre -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-[#203363] rounded-full flex items-center justify-center">
                                        <i class="fas fa-store text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e($branch->name); ?>

                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Ubicación -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <?php if($branch->address): ?>
                                        <div class="flex items-center mb-1">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-2 w-4"></i>
                                            <?php echo e($branch->address); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if($branch->city || $branch->state): ?>
                                        <div class="flex items-center text-gray-500">
                                            <i class="fas fa-city text-gray-400 mr-2 w-4"></i>
                                            <?php echo e($branch->city); ?><?php if($branch->city && $branch->state): ?>, <?php endif; ?><?php echo e($branch->state); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if(!$branch->address && !$branch->city && !$branch->state): ?>
                                        <span class="text-gray-400 italic">Sin ubicación</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Contacto -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <?php if($branch->phone): ?>
                                        <div class="flex items-center mb-1 text-gray-900">
                                            <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                            <?php echo e($branch->phone); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if($branch->email): ?>
                                        <div class="flex items-center text-gray-500">
                                            <i class="fas fa-envelope text-gray-400 mr-2 w-4"></i>
                                            <?php echo e($branch->email); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if(!$branch->phone && !$branch->email): ?>
                                        <span class="text-gray-400 italic">Sin contacto</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Principal -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if($branch->is_main): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i>
                                        Principal
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Estado -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick="toggleStatus(<?php echo e($branch->id); ?>, <?php echo e($branch->is_active ? 'true' : 'false'); ?>)"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all duration-200
                                               <?php echo e($branch->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200'); ?>"
                                        id="status-btn-<?php echo e($branch->id); ?>">
                                    <i class="fas fa-<?php echo e($branch->is_active ? 'check-circle' : 'times-circle'); ?> mr-1"></i>
                                    <span id="status-text-<?php echo e($branch->id); ?>">
                                        <?php echo e($branch->is_active ? 'Activa' : 'Inactiva'); ?>

                                    </span>
                                </button>
                            </td>

                            <!-- Acciones -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?php echo e(route('branches.show', $branch)); ?>" 
                                       class="text-blue-600 hover:text-blue-800 transition duration-200"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('branches.edit', $branch)); ?>" 
                                       class="text-yellow-600 hover:text-yellow-800 transition duration-200"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (! ($branch->is_main)): ?>
                                        <form action="<?php echo e(route('branches.destroy', $branch)); ?>" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar esta sucursal?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 transition duration-200"
                                                    title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-store text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg mb-4">No hay sucursales registradas</p>
                                    <a href="<?php echo e(route('branches.create')); ?>" 
                                       class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200">
                                        <i class="fas fa-plus-circle mr-2"></i>Crear primera sucursal
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if($branches->hasPages()): ?>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <?php echo e($branches->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleStatus(branchId, currentStatus) {
    fetch(`/branches/${branchId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById(`status-btn-${branchId}`);
            const text = document.getElementById(`status-text-${branchId}`);
            
            if (data.is_active) {
                btn.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all duration-200 bg-green-100 text-green-800 hover:bg-green-200';
                text.textContent = 'Activa';
                btn.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + text.outerHTML;
            } else {
                btn.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all duration-200 bg-red-100 text-red-800 hover:bg-red-200';
                text.textContent = 'Inactiva';
                btn.innerHTML = '<i class="fas fa-times-circle mr-1"></i>' + text.outerHTML;
            }
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al cambiar el estado', 'error');
    });
}

function showNotification(message, type) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/branches/index.blade.php ENDPATH**/ ?>