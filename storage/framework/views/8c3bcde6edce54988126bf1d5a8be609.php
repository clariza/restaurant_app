<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Caja Chica - <?php echo e($date); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header img { max-width: 150px; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 12px; color: #555; margin-bottom: 15px; }
        .company-info { margin-bottom: 15px; text-align: center; }
        .details { margin: 15px 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .totals { margin-top: 20px; }
        .totals .table { width: 60%; margin-left: auto; }
        .signature { margin-top: 50px; }
        .section-title { font-weight: bold; margin: 10px 0 5px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE CIERRE DE CAJA</div>
        <div class="subtitle">Fecha: <?php echo e($date); ?></div>
    </div>

    <div class="company-info">
        <!-- <div><strong><?php echo e(config('app.name')); ?></strong></div> -->
        <div>Vendedor: <?php echo e($user->name); ?></div>
    </div>

    <div class="details">
        <div class="section-title">INFORMACIÓN DEL CIERRE</div>
        <table class="table">
            <tr>
                <th width="30%">Fecha de Cierre:</th>
                <td><?php echo e($pettyCash->date); ?></td>
            </tr>
            <tr>
                <th>Estado:</th>
                <td>Cerrada</td>
            </tr>
            <tr>
                <th>Generado por:</th>
                <td><?php echo e($user->name); ?></td>
            </tr>
        </table>
    </div>

    <div class="details">
        <div class="section-title">VENTAS POR MÉTODO DE PAGO</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $salesByPaymentMethod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($method); ?></td>
                    <td class="text-right">$<?php echo e(number_format($amount, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <th>TOTAL VENTAS</th>
                    <th class="text-right">$<?php echo e(number_format($totalSales, 2)); ?></th>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="details">
        <div class="section-title">GASTOS</div>
        <table class="table">
            <tr>
                <th width="80%">Total Gastos</th>
                <td class="text-right">$<?php echo e(number_format($totalExpenses, 2)); ?></td>
            </tr>
        </table>
    </div>

    <div class="totals">
        <table class="table">
            <tr>
                <th width="80%">SALDO FINAL</th>
                <th class="text-right">$<?php echo e(number_format($totalSales - $totalExpenses, 2)); ?></th>
            </tr>
        </table>
    </div>

   
</body>
</html><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/print.blade.php ENDPATH**/ ?>