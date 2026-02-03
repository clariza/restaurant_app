

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <!-- Título con estilo consistente -->
    <h1 class="text-xl font-bold mb-6 text-[var(--primary-color)] relative pb-2 section-title">
        Nuevo Servicio de Delivery
        <span class="absolute bottom-0 left-0 w-10 h-1 bg-[var(--primary-color)] rounded"></span>
    </h1>
    
    <!-- Formulario con estilos del sistema -->
    <form action="<?php echo e(route('deliveries.store')); ?>" method="POST" class="bg-white rounded-lg shadow-md p-6">
        <?php echo csrf_field(); ?>
        
        <!-- Grid de 2 columnas para pantallas medianas/grandes -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Nombre del servicio -->
            <div class="mb-4">
                <label for="name" class="input-label">Nombre del Servicio</label>
                <input type="text" name="name" id="name" 
                       class="modal-input" 
                       placeholder="Ingrese el nombre del servicio"
                       required>
            </div>
            
            <!-- Descripción -->
            <div class="mb-4">
                <label for="description" class="input-label">Descripción</label>
                <textarea name="description" id="description" 
                       class="modal-input" 
                       placeholder="Ingrese la descripción del servicio"
                       rows="3"></textarea>
            </div>
        </div>
        
        <!-- Botones de acción alineados a la derecha -->
        <div class="flex justify-end mt-6 space-x-4">
            <!-- Botón Cancelar -->
            <a href="<?php echo e(route('deliveries.index')); ?>" 
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 inline-flex items-center">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            
            <!-- Botón Guardar -->
            <button type="submit" 
                    class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--primary-light)] transition duration-200 inline-flex items-center">
                <i class="fas fa-save mr-2"></i>Guardar Servicio
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/deliveries/create.blade.php ENDPATH**/ ?>