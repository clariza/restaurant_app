

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <!-- Navegación entre ventas -->
    <div class="flex justify-between items-center mb-6">
        <?php if($previousSale): ?>
            <a href="<?php echo e(route('sales.show', $previousSale->id)); ?>" class="flex items-center px-4 py-2 bg-[#203363] text-white rounded-lg hover:bg-[#47517c] transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Venta Anterior 
            </a>
        <?php else: ?>
            <span class="flex items-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                <i class="fas fa-arrow-left mr-2"></i> Sin ventas anteriores
            </span>
        <?php endif; ?>

        <a href="<?php echo e(route('sales.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <i class="fas fa-list mr-2"></i> Volver al listado
        </a>

        <?php if($nextSale): ?>
            <a href="<?php echo e(route('sales.show', $nextSale->id)); ?>" class="flex items-center px-4 py-2 bg-[#203363] text-white rounded-lg hover:bg-[#47517c] transition-colors">
                Venta Siguiente  <i class="fas fa-arrow-right ml-2"></i>
            </a>
        <?php else: ?>
            <span class="flex items-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                Sin ventas siguientes <i class="fas fa-arrow-right ml-2"></i>
            </span>
        <?php endif; ?>
    </div>

    <h1 class="text-2xl font-bold mb-6">Detalles de la Venta #<?php echo e($sale->id); ?></h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Información del Cliente</h2>
        <p><strong>Nombre:</strong> <?php echo e($sale->customer_name); ?></p>
        <p><strong>Teléfono:</strong> <?php echo e($sale->phone); ?></p>
        <p><strong>Tipo de Pedido:</strong> <?php echo e($sale->order_type); ?></p>
        <p><strong>Total:</strong> $<?php echo e(number_format($sale->total, 2)); ?></p>
        <p><strong>Fecha:</strong> <?php echo e($sale->created_at->format('d/m/Y H:i')); ?></p>
        <p><strong>Vendedor:</strong> <?php echo e($sale->user->name); ?></p>

        <h2 class="text-xl font-bold mt-6 mb-4">Ítems del Pedido</h2>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left">Producto</th>
                    <th class="px-6 py-3 text-left">Cantidad</th>
                    <th class="px-6 py-3 text-left">Precio Unitario</th>
                    <th class="px-6 py-3 text-left">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo e($item->name); ?></td>
                        <td class="px-6 py-4"><?php echo e($item->quantity); ?></td>
                        <td class="px-6 py-4">$<?php echo e(number_format($item->price, 2)); ?></td>
                        <td class="px-6 py-4">$<?php echo e(number_format($item->total, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/sales/show.blade.php ENDPATH**/ ?>