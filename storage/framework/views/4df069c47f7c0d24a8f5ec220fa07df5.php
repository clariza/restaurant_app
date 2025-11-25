

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Lista de Productos</h1>
    
    <!-- Botón para crear producto -->
    <a href="<?php echo e(route('items.create')); ?>" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg mb-4 inline-block hover:bg-[var(--secondary-color)] transition duration-200">
        <i class="fas fa-plus mr-2"></i>Crear Producto
    </a>

    <!-- Lista de Productos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[var(--primary-color)]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-6 py-4 text-[var(--table-data-color)]"><?php echo e($item->name); ?></td>
                    <td class="px-6 py-4 text-[var(--table-data-color)]"><?php echo e($item->description); ?></td>
                    <td class="px-6 py-4 text-[var(--table-data-color)]"><?php echo e($item->price); ?></td>
                    <td class="px-6 py-4 text-[var(--table-data-color)]"><?php echo e($item->category->name); ?></td>
                    <td class="px-6 py-4">
                        <!-- Icono de Editar -->
                        <a href="<?php echo e(route('items.edit', $item->id)); ?>" class="text-[var(--primary-color)] hover:text-[var(--secondary-color)] mr-2">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <!-- Icono de Eliminar -->
                        <form action="<?php echo e(route('items.destroy', $item->id)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash mr-1"></i>Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/items/index.blade.php ENDPATH**/ ?>