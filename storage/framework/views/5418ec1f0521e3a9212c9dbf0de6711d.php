<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            width: 74mm;
            padding: 3mm;
        }

        .header        { text-align: center; margin-bottom: 2px; }
        .title         { font-weight: bold; font-size: 13px; letter-spacing: 1px; }
        .subtitle      { font-size: 10px; margin: 2px 0; }
        .divider       { border: none; border-top: 1px dashed #000; margin: 3px 0; }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 11px;
        }
        .item-row span:first-child { flex: 1; padding-right: 4px; }
        .item-row span:last-child  { text-align: right; white-space: nowrap; }

        .total-row  { font-weight: bold; margin-top: 3px; font-size: 12px; }
        .footer     { text-align: center; margin-top: 4px; font-size: 10px; }
        .notes      { margin-top: 3px; font-size: 10px; white-space: pre-wrap; word-break: break-word; }
    </style>
</head>
<body>

    
    <div class="header">
        <div class="title">RESTAURANTE MIQUNA</div>
        <div class="subtitle"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></div>
    </div>

    <hr class="divider">

    
    <div class="item-row">
        <span>Vendedor:</span>
        <span><?php echo e($order->user->name); ?></span>
    </div>
    <div class="item-row">
        <span>Pedido:</span>
        <span>#<?php echo e($order->transaction_number); ?></span>
    </div>

    <hr class="divider">

    
    <?php if($order->order_type): ?>
    <div class="item-row">
        <span>Tipo:</span>
        <span>
            <?php if(strtolower($order->order_type) === 'comer aquí' || strtolower($order->order_type) === 'comer aqui'): ?>
                Para la Mesa<?php echo e($order->table_number ? ' ' . $order->table_number : ''); ?>

            <?php else: ?>
                <?php echo e(ucfirst($order->order_type)); ?>

            <?php endif; ?>
        </span>
    </div>
    <?php endif; ?>

    
    <?php if($order->customer_name): ?>
    <div class="item-row">
        <span>Cliente:</span>
        <span><?php echo e($order->customer_name); ?></span>
    </div>
    <?php endif; ?>

    
    <?php if($order->phone): ?>
    <div class="item-row">
        <span>Tel:</span>
        <span><?php echo e($order->phone); ?></span>
    </div>
    <?php endif; ?>

    <hr class="divider">

    
    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="item-row">
        <span><?php echo e($item->quantity); ?>x <?php echo e(Str::limit($item->menuItem->name, 20)); ?></span>
        <span>Bs <?php echo e(number_format($item->price * $item->quantity, 2)); ?></span>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <hr class="divider">

    
    <div class="item-row">
        <span>Subtotal:</span>
        <span>Bs <?php echo e(number_format($order->total, 2)); ?></span>
    </div>
    <div class="item-row">
        <span>Impuesto:</span>
        <span>Bs 0.00</span>
    </div>
    <div class="item-row total-row">
        <span>TOTAL:</span>
        <span>Bs <?php echo e(number_format($order->total, 2)); ?></span>
    </div>

    
    <?php if($order->paymentMethods && $order->paymentMethods->count() > 0): ?>
        <?php $__currentLoopData = $order->paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="item-row">
            <span><?php echo e($payment->method); ?>:</span>
            <span>Bs <?php echo e(number_format($payment->amount, 2)); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php
            $totalPaid = $order->paymentMethods->sum('amount');
            $change = $totalPaid - $order->total;
        ?>
        <?php if($change > 0): ?>
        <div class="item-row total-row">
            <span>CAMBIO:</span>
            <span>Bs <?php echo e(number_format($change, 2)); ?></span>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <?php if($order->order_notes): ?>
    <hr class="divider">
    <div class="notes">Notas del pedido: <?php echo e($order->order_notes); ?></div>
    <?php endif; ?>

    
    <?php if($order->customer_notes): ?>
    <hr class="divider">
    <div class="notes">Notas del cliente: <?php echo e($order->customer_notes); ?></div>
    <?php endif; ?>

    <hr class="divider">
    <div class="footer">¡Gracias por su preferencia!</div>

</body>
</html><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/orders/ticket-pdf.blade.php ENDPATH**/ ?>