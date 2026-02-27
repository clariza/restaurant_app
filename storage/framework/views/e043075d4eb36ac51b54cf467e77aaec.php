<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <!-- Título con estilo consistente -->
    <h1 class="text-xl font-bold mb-6 text-[var(--primary-color)] relative pb-2 section-title">
        Crear Gasto
        <span class="absolute bottom-0 left-0 w-10 h-1 bg-[var(--primary-color)] rounded"></span>
    </h1>
    
    <!-- Formulario con estilos del sistema -->
    <form action="<?php echo e(route('expenses.store')); ?>" method="POST" class="bg-white rounded-lg shadow-md p-6">
        <?php echo csrf_field(); ?>
        
        <!-- Grid de 2 columnas para pantallas medianas/grandes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre de gasto -->
            <div class="mb-4">
                <label for="expense_name" class="input-label">Nombre de Gasto</label>
                <input type="text" name="expense_name" id="expense_name" 
                       class="modal-input" 
                       placeholder="Ingrese el nombre del gasto"
                       required>
            </div>
            
            <!-- Descripción -->
            <div class="mb-4">
                <label for="description" class="input-label">Descripción</label>
                <input type="text" name="description" id="description" 
                       class="modal-input" 
                       placeholder="Ingrese la descripción del gasto"
                       required>
            </div>
            
            <!-- Monto -->
            <div class="mb-4">
                <label for="amount" class="input-label">Monto (S/)</label>
                <input type="number" name="amount" id="amount" step="0.01" 
                       class="modal-input" 
                       placeholder="0.00"
                       required>
            </div>
        </div>
        
        <!-- Botones de acción alineados a la derecha -->
        <div class="flex justify-end mt-6 space-x-4">
            <!-- Botón Volver Atrás -->
            <a href="<?php echo e(route('expenses.index')); ?>" 
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Volver Atrás
            </a>
            
            <!-- Botón Guardar -->
            <button type="submit" 
                    class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--primary-light)] transition duration-200 inline-flex items-center">
                <i class="fas fa-save mr-2"></i>Guardar Gasto
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\laravel_clary\restaurant_app\resources\views/expenses/create.blade.php ENDPATH**/ ?>