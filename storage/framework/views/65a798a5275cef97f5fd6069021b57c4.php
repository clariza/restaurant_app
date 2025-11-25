

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <!-- Encabezado -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold mb-2 text-[var(--primary-color)]">
                Detalles de Compra #<?php echo e($purchase->reference_number); ?>

            </h1>
            <p class="text-sm text-[var(--text-light)]">
                Información completa de la compra
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('purchases.index')); ?>" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Volver</span>
            </a>
            <?php if($purchase->status === 'pending' && auth()->user()->role === 'admin'): ?>
                <a href="<?php echo e(route('purchases.edit', $purchase->id)); ?>" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                    <i class="fas fa-edit"></i>
                    <span>Editar</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información General -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Información del Proveedor -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6">
            <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 flex items-center">
                <i class="fas fa-truck mr-2"></i>
                Proveedor
            </h3>
            <div class="space-y-3">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-[var(--primary-color)] text-white flex items-center justify-center font-bold text-lg mr-3">
                        <?php echo e(substr($purchase->supplier->name, 0, 1)); ?>

                    </div>
                    <div>
                        <p class="font-semibold text-[var(--text-color)]"><?php echo e($purchase->supplier->name); ?></p>
                        <p class="text-sm text-[var(--text-light)]">NIT: <?php echo e($purchase->supplier->nit ?? 'N/A'); ?></p>
                    </div>
                </div>
                <?php if($purchase->supplier->phone): ?>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-phone text-[var(--text-light)] mr-2 w-5"></i>
                        <span><?php echo e($purchase->supplier->phone); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($purchase->supplier->email): ?>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-envelope text-[var(--text-light)] mr-2 w-5"></i>
                        <span><?php echo e($purchase->supplier->email); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($purchase->supplier->address): ?>
                    <div class="flex items-start text-sm">
                        <i class="fas fa-map-marker-alt text-[var(--text-light)] mr-2 w-5 mt-1"></i>
                        <span><?php echo e($purchase->supplier->address); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información de la Compra -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6">
            <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Información de Compra
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-[var(--text-light)]">Referencia:</span>
                    <span class="font-semibold"><?php echo e($purchase->reference_number); ?></span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-[var(--text-light)]">Fecha:</span>
                    <span class="font-semibold"><?php echo e($purchase->purchase_date->format('d/m/Y')); ?></span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-[var(--text-light)]">Estado:</span>
                    <?php
                        $statusConfig = [
                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock', 'text' => 'Pendiente'],
                            'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'check-circle', 'text' => 'Completado'],
                            'cancelled' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'times-circle', 'text' => 'Cancelado']
                        ];
                        $config = $statusConfig[$purchase->status] ?? $statusConfig['pending'];
                    ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($config['class']); ?>">
                        <i class="fas fa-<?php echo e($config['icon']); ?> mr-1"></i>
                        <?php echo e($config['text']); ?>

                    </span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-[var(--text-light)]">Total Items:</span>
                    <span class="font-semibold"><?php echo e($purchase->stocks->sum('quantity')); ?></span>
                </div>
            </div>
        </div>

   
    </div>

    <!-- Detalle de Productos -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)]">
        <div class="bg-gradient-to-r from-[var(--primary-color)] to-[var(--primary-light)] text-white p-4">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-boxes mr-2"></i>
                Productos Comprados
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-[var(--gray-light)]">
                        <th class="px-4 py-3 text-left font-semibold text-[var(--text-color)]">Producto</th>
                        <th class="px-4 py-3 text-center font-semibold text-[var(--text-color)]">Cantidad</th>
                        <th class="px-4 py-3 text-right font-semibold text-[var(--text-color)]">Costo Unit.</th>
                        <th class="px-4 py-3 text-center font-semibold text-[var(--text-color)]">Descuento</th>
                        <th class="px-4 py-3 text-right font-semibold text-[var(--text-color)]">Costo Final</th>
                        <th class="px-4 py-3 text-right font-semibold text-[var(--text-color)]">Total</th>
                        <th class="px-4 py-3 text-right font-semibold text-[var(--text-color)]">P. Venta</th>
                        <th class="px-4 py-3 text-center font-semibold text-[var(--text-color)]">Margen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--gray-light)]">
                    <?php $__currentLoopData = $purchase->stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <?php if($stock->item && $stock->item->image): ?>
                                        <img src="<?php echo e($stock->item->image && filter_var($stock->item->image, FILTER_VALIDATE_URL) 
                                                    ? $stock->item->image 
                                                    : asset('storage/' . $stock->item->image)); ?>" 
                                             alt="<?php echo e($stock->item->name); ?>"
                                             class="w-10 h-10 rounded object-cover mr-3"
                                             onerror="this.src='<?php echo e(asset('images/placeholder.png')); ?>'">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-semibold text-[var(--text-color)]">
                                            <?php echo e($stock->item->name ?? 'Producto no encontrado'); ?>

                                        </p>
                                        <?php if($stock->item && $stock->item->category): ?>
                                            <small class="text-[var(--text-light)]">
                                                <?php echo e($stock->item->category->name); ?>

                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold">
                                <?php echo e($stock->quantity); ?>

                            </td>
                            <td class="px-4 py-3 text-right">
                                Bs. <?php echo e(number_format($stock->unit_cost, 2)); ?>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($stock->discount > 0): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <?php echo e($stock->discount); ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                Bs. <?php echo e(number_format($stock->unit_cost * (1 - $stock->discount / 100), 2)); ?>

                            </td>
                            <td class="px-4 py-3 text-right font-bold text-green-600">
                                Bs. <?php echo e(number_format($stock->total_cost, 2)); ?>

                            </td>
                            <td class="px-4 py-3 text-right">
                                Bs. <?php echo e(number_format($stock->selling_price, 2)); ?>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($stock->profit_margin >= 30): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <?php echo e(number_format($stock->profit_margin, 1)); ?>%
                                    </span>
                                <?php elseif($stock->profit_margin >= 15): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <?php echo e(number_format($stock->profit_margin, 1)); ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <?php echo e(number_format($stock->profit_margin, 1)); ?>%
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-[var(--primary-color)]">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-bold text-[var(--text-color)]">
                            TOTAL:
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 text-lg">
                            Bs. <?php echo e(number_format($purchase->total_amount, 2)); ?>

                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/purchases/show.blade.php ENDPATH**/ ?>