
<?php $__env->startSection('content'); ?>
<style>
    :root {
        --primary-color: #203363;
        --secondary-color: #6380a6;
        --tertiary-color: #a4b6ce;
    }

    .order-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #47517c 100%);
        color: white;
        padding: 25px;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .order-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .order-info-simple {
        padding: 40px;
        background: #f8f9fa;
    }

    .info-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 35px 45px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .info-row-simple {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
        align-items: center;
    }

    .info-row-simple:last-child {
        border-bottom: none;
    }

    .info-label-simple {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 15px;
        text-align: right;
        padding-right: 15px;
    }

    .info-label-simple i {
        margin-right: 8px;
    }

    .info-value-simple {
        color: #212529;
        font-size: 15px;
        font-weight: 500;
        padding-left: 15px;
        border-left: 3px solid var(--tertiary-color);
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead {
        background-color: var(--primary-color);
        color: white;
    }

    .items-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
    }

    .items-table td {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .items-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .items-table tfoot {
        background-color: #f8f9fa;
        font-weight: 700;
    }

    .items-table tfoot td {
        padding: 20px 15px;
        font-size: 18px;
        color: var(--primary-color);
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-bottom: 25px;
    }

    .btn {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: #47517c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(32, 51, 99, 0.3);
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-secondary:hover {
        background-color: #47517c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 128, 166, 0.3);
    }

    .notes-section {
        margin-top: 20px;
        padding: 20px;
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
    }

    .notes-label {
        font-size: 12px;
        color: #856404;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .notes-content {
        color: #212529;
        font-size: 14px;
        line-height: 1.6;
    }

    .navigation-footer {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 20px 30px;
        border-top: 3px solid var(--primary-color);
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        z-index: 100;
        margin-top: 30px;
        border-radius: 12px 12px 0 0;
    }

    .nav-info {
        text-align: left;
        flex-shrink: 0;
    }

    .nav-info .order-number {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-info .order-date {
        font-size: 13px;
        color: #6c757d;
        padding-left: 28px;
    }

    .nav-buttons-container {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-left: auto;
    }

    .nav-btn {
        padding: 14px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        min-width: 140px;
        justify-content: center;
    }

    .nav-btn-enabled {
        background-color: var(--primary-color);
        color: white;
    }

    .nav-btn-enabled:hover {
        background-color: #47517c;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(32, 51, 99, 0.3);
    }

    .nav-btn-disabled {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .nav-separator {
        width: 2px;
        height: 35px;
        background: linear-gradient(to bottom, transparent, var(--tertiary-color), transparent);
        margin: 0 4px;
    }

    /* ============================================
       ESTILOS PARA IMPRESIÓN TIPO TICKET TÉRMICO
       ============================================ */
    @media print {
        /* Ocultar elementos de navegación y header */
        .action-buttons,
        .navigation-footer,
        .no-print,
        .order-card,
        .container,
        header,
        nav,
        .navbar,
        .header,
        footer {
            display: none !important;
        }

        /* Resetear estilos del body */
        body {
            font-family: 'Courier New', monospace !important;
            font-size: 12px !important;
            width: 72mm !important;
            margin: 0 !important;
            padding: 2mm !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Mostrar el contenido del ticket */
        #thermal-ticket {
            display: block !important;
        }

        /* Estilos del ticket térmico */
        .thermal-header {
            text-align: center;
            margin-bottom: 3px;
        }

        .thermal-title {
            font-weight: bold;
            font-size: 14px;
            margin: 0;
        }

        .thermal-subtitle {
            font-size: 11px;
            margin: 2px 0;
        }

        .thermal-divider {
            border-top: 1px dashed #000;
            margin: 3px 0;
        }

        .thermal-item-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 12px;
        }

        .thermal-total-row {
            font-weight: bold;
            margin-top: 4px;
            font-size: 13px;
        }

        .thermal-footer {
            text-align: center;
            margin-top: 5px;
            font-size: 10px;
        }

        .thermal-notes {
            margin-top: 4px;
            font-size: 11px;
            white-space: pre-wrap;
        }

        /* Configuración de página */
        @page {
            size: 72mm auto;
            margin: 0;
        }
    }

    /* Ocultar ticket térmico en pantalla */
    #thermal-ticket {
        display: none;
    }

    @media (max-width: 992px) {
        .navigation-footer {
            padding: 18px 20px;
        }

        .nav-btn {
            min-width: 120px;
            padding: 12px 20px;
            font-size: 14px;
        }

        .nav-info .order-number {
            font-size: 16px;
        }

        .nav-info .order-date {
            font-size: 12px;
        }
    }

    @media (max-width: 768px) {
        .navigation-footer {
            flex-direction: column;
            gap: 15px;
            padding: 15px;
        }

        .nav-info {
            text-align: center;
            width: 100%;
        }

        .nav-info .order-date {
            padding-left: 0;
        }

        .nav-buttons-container {
            width: 100%;
            justify-content: center;
            margin-left: 0;
        }

        .nav-btn {
            flex: 1;
            min-width: auto;
            max-width: 150px;
        }

        .nav-separator {
            height: 30px;
        }

        .info-row-simple {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .info-label-simple {
            min-width: auto;
            text-align: left;
        }
    }

    @media (max-width: 480px) {
        .nav-btn {
            padding: 12px 16px;
            font-size: 13px;
            gap: 6px;
        }

        .nav-btn span {
            display: none;
        }

        .nav-btn i {
            margin: 0;
            font-size: 1.1rem;
        }

        .nav-separator {
            height: 25px;
            margin: 0 8px;
        }

        .nav-info .order-number {
            font-size: 15px;
        }

        .nav-info .order-date {
            font-size: 11px;
        }
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Botones de acción superiores -->
    <div class="action-buttons">
        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-list"></i>
            Volver al listado
        </a>
        
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i>
            Imprimir
        </button>
    </div>

    <!-- Tarjeta principal de la orden (VISTA WEB) -->
    <div class="order-card">
        <div class="order-header">
            <h1 class="text-3xl font-bold mb-2">
                <i class="fas fa-receipt mr-3"></i>
                Orden #<?php echo e($order->transaction_number); ?>

            </h1>
            <p class="text-sm opacity-90">
                <i class="fas fa-calendar-alt mr-2"></i>
                <?php echo e($order->created_at->format('d/m/Y H:i')); ?>

            </p>
        </div>

        <div class="order-info-simple">
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-calendar mr-2"></i>Fecha:
                </span>
                <span class="info-value-simple"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></span>
            </div>
            
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-shopping-bag mr-2"></i>Tipo de Orden:
                </span>
                <span class="info-value-simple"><?php echo e(ucfirst($order->order_type)); ?></span>
            </div>
            
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-user-tie mr-2"></i>Atendido por:
                </span>
                <span class="info-value-simple"><?php echo e($order->user->name); ?></span>
            </div>

            <?php if($order->customer_name): ?>
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-user mr-2"></i>Cliente:
                </span>
                <span class="info-value-simple"><?php echo e($order->customer_name); ?></span>
            </div>
            <?php endif; ?>

            <?php if($order->phone): ?>
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-phone mr-2"></i>Teléfono:
                </span>
                <span class="info-value-simple"><?php echo e($order->phone); ?></span>
            </div>
            <?php endif; ?>

            <?php if($order->table_number): ?>
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-chair mr-2"></i>Mesa:
                </span>
                <span class="info-value-simple">#<?php echo e($order->table_number); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4 text-[#203363]">
                <i class="fas fa-list-ul mr-2"></i>
                Ítems del Pedido
            </h2>
            
            <div class="overflow-x-auto">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="50%">
                                <i class="fas fa-utensils mr-2"></i>
                                Producto
                            </th>
                            <th width="15%" class="text-right">
                                <i class="fas fa-sort-numeric-up mr-2"></i>
                                Cantidad
                            </th>
                            <th width="17%" class="text-right">
                                <i class="fas fa-dollar-sign mr-2"></i>
                                Precio
                            </th>
                            <th width="18%" class="text-right">
                                <i class="fas fa-calculator mr-2"></i>
                                Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-medium"><?php echo e($item->menuItem->name); ?></td>
                            <td class="text-right"><?php echo e($item->quantity); ?></td>
                            <td class="text-right">Bs. <?php echo e(number_format($item->price, 2)); ?></td>
                            <td class="text-right font-semibold">Bs. <?php echo e(number_format($item->quantity * $item->price, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">
                                <i class="fas fa-coins mr-2"></i>
                                TOTAL:
                            </td>
                            <td class="text-right">
                                Bs. <?php echo e(number_format($order->total, 2)); ?>

                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if($order->order_notes): ?>
            <div class="notes-section">
                <div class="notes-label">
                    <i class="fas fa-sticky-note mr-2"></i>
                    Notas del pedido
                </div>
                <div class="notes-content"><?php echo e($order->order_notes); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navegación inferior -->
    <div class="navigation-footer no-print">
        <div class="nav-info">
            <div class="order-number">
                <i class="fas fa-receipt"></i>
                Orden #<?php echo e($order->transaction_number); ?>

            </div>
            <div class="order-date">
                <?php echo e($order->created_at->format('d/m/Y H:i')); ?>

            </div>
        </div>

        <div class="nav-buttons-container">
            <?php if(isset($previousOrder) && $previousOrder): ?>
                <a href="<?php echo e(route('orders.show', $previousOrder->id)); ?>" class="nav-btn nav-btn-enabled">
                    <i class="fas fa-chevron-left"></i>
                    <span>Anterior</span>
                </a>
            <?php else: ?>
                <span class="nav-btn nav-btn-disabled">
                    <i class="fas fa-chevron-left"></i>
                    <span>Anterior</span>
                </span>
            <?php endif; ?>

            <div class="nav-separator"></div>

            <?php if(isset($nextOrder) && $nextOrder): ?>
                <a href="<?php echo e(route('orders.show', $nextOrder->id)); ?>" class="nav-btn nav-btn-enabled">
                    <span>Siguiente</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="nav-btn nav-btn-disabled">
                    <span>Siguiente</span>
                    <i class="fas fa-chevron-right"></i>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ✅ CONTENIDO PARA IMPRESIÓN TIPO TICKET TÉRMICO -->
<div id="thermal-ticket">
    <div class="thermal-header">
        <div class="thermal-title">RESTAURANTE MIQUNA</div>
        <div class="thermal-subtitle"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></div>
    </div>
    <div class="thermal-divider"></div>
    
    <div class="thermal-item-row">
        <span>Vendedor:</span>
        <span><?php echo e($order->user->name); ?></span>
    </div>
    <div class="thermal-item-row">
        <span>Pedido:</span>
        <span><?php echo e($order->transaction_number); ?></span>
    </div>
    <div class="thermal-divider"></div>
    
    <?php if($order->order_type): ?>
    <div class="thermal-item-row">
        <span>Tipo:</span>
        <span><?php echo e(ucfirst($order->order_type)); ?><?php if($order->table_number): ?> Mesa <?php echo e($order->table_number); ?><?php endif; ?></span>
    </div>
    <?php endif; ?>
    
    <?php if($order->customer_name): ?>
    <div class="thermal-item-row">
        <span>Cliente:</span>
        <span><?php echo e($order->customer_name); ?></span>
    </div>
    <?php endif; ?>
    
    <div class="thermal-divider"></div>
    
    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="thermal-item-row">
        <span><?php echo e($item->quantity); ?>x <?php echo e(Str::limit($item->menuItem->name, 20)); ?></span>
        <span>Bs.<?php echo e(number_format($item->price * $item->quantity, 2)); ?></span>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <div class="thermal-divider"></div>
    
    <div class="thermal-item-row">
        <span>Subtotal:</span>
        <span>Bs.<?php echo e(number_format($order->total, 2)); ?></span>
    </div>
    <div class="thermal-item-row">
        <span>Impuesto:</span>
        <span>Bs.0.00</span>
    </div>
    <div class="thermal-item-row thermal-total-row">
        <span>TOTAL:</span>
        <span>Bs.<?php echo e(number_format($order->total, 2)); ?></span>
    </div>
    
    <?php if($order->order_notes): ?>
    <div class="thermal-divider"></div>
    <div class="thermal-notes">Notas del pedido: <?php echo e($order->order_notes); ?></div>
    <?php endif; ?>
    
    <div class="thermal-divider"></div>
    <div class="thermal-footer">
        ¡Gracias por su preferencia!
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Navegación con teclas de flecha
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                <?php if(isset($previousOrder) && $previousOrder): ?>
                    window.location.href = "<?php echo e(route('orders.show', $previousOrder->id)); ?>";
                <?php endif; ?>
            }
            
            if (e.key === 'ArrowRight' || e.keyCode === 39) {
                <?php if(isset($nextOrder) && $nextOrder): ?>
                    window.location.href = "<?php echo e(route('orders.show', $nextOrder->id)); ?>";
                <?php endif; ?>
            }
        });

        // Feedback visual al presionar teclas
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                const prevBtn = document.querySelector('.nav-btn-enabled:has(.fa-chevron-left)');
                if (prevBtn) {
                    prevBtn.style.transform = 'scale(0.95)';
                    setTimeout(() => prevBtn.style.transform = '', 150);
                }
            }
            
            if (e.key === 'ArrowRight' || e.keyCode === 39) {
                const nextBtn = document.querySelector('.nav-btn-enabled:has(.fa-chevron-right)');
                if (nextBtn) {
                    nextBtn.style.transform = 'scale(0.95)';
                    setTimeout(() => nextBtn.style.transform = '', 150);
                }
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/orders/show.blade.php ENDPATH**/ ?>