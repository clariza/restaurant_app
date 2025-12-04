

<?php $__env->startSection('content'); ?>
<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4 text-[#203363]">Editar Cierre de Caja Chica</h2>
    <form action="<?php echo e(route('petty-cash.update', $pettyCash)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="mb-4">
            <label for="initial_amount" class="block text-sm font-medium text-[#203363]">Monto Inicial</label>
            <input type="number" step="0.01" name="initial_amount" id="initial_amount" class="w-full border rounded-lg p-2" value="<?php echo e($pettyCash->initial_amount); ?>" required>
        </div>
        <div class="mb-4">
            <label for="current_amount" class="block text-sm font-medium text-[#203363]">Monto Actual</label>
            <input type="number" step="0.01" name="current_amount" id="current_amount" class="w-full border rounded-lg p-2" value="<?php echo e($pettyCash->current_amount); ?>" required>
        </div>
        <div class="mb-4">
            <label for="date" class="block text-sm font-medium text-[#203363]">Fecha</label>
            <input type="date" name="date" id="date" class="w-full border rounded-lg p-2" value="<?php echo e($pettyCash->date); ?>" required>
        </div>
        <div class="mb-4">
            <label for="notes" class="block text-sm font-medium text-[#203363]">Notas</label>
            <textarea name="notes" id="notes" class="w-full border rounded-lg p-2"><?php echo e($pettyCash->notes); ?></textarea>
        </div>
        <button type="submit" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">Actualizar</button>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/edit.blade.php ENDPATH**/ ?>