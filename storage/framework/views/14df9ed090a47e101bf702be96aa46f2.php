

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Editar Mesa</h1>
    <form action="<?php echo e(route('tables.update', $table->id)); ?>" method="POST" class="bg-white rounded-lg shadow-md p-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="mb-4">
            <label for="number" class="block text-sm font-medium text-[var(--table-data-color)]">Número</label>
            <input type="number" name="number" id="number" value="<?php echo e($table->number); ?>" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="state" class="block text-sm font-medium text-[var(--table-data-color)]">Estado</label>
            <select name="state" id="state" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                <option value="Disponible" <?php echo e($table->state == 'Disponible' ? 'selected' : ''); ?>>Disponible</option>
                <option value="Ocupada" <?php echo e($table->state == 'Ocupada' ? 'selected' : ''); ?>>Ocupada</option>
                <option value="Reservada" <?php echo e($table->state == 'Reservada' ? 'selected' : ''); ?>>Reservada</option>
            </select>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Actualizar
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/tables/edit.blade.php ENDPATH**/ ?>