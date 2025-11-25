<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja Chica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #203363;
            margin-bottom: 20px;
        }
        .filters {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #203363;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        .summary {
            background-color: #e5e7eb;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Reporte de Caja Chica</h1>
    
    <div class="filters">
        <strong>Filtros aplicados:</strong><br>
        <?php if(!empty($filters['date_from']) || !empty($filters['date_to'])): ?>
            Período: <?php echo e($filters['date_from'] ?? 'Inicio'); ?> - <?php echo e($filters['date_to'] ?? 'Actual'); ?><br>
        <?php endif; ?>
        <?php if(!empty($filters['status'])): ?>
            Estado: <?php echo e($filters['status'] === 'open' ? 'Abierta' : 'Cerrada'); ?><br>
        <?php endif; ?>
        Fecha de generación: <?php echo e(date('d/m/Y H:i')); ?>

    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cajero</th>
                <th class="text-right">Ventas Efectivo</th>
                <th class="text-right">Ventas QR</th>
                <th class="text-right">Ventas Tarjeta</th>
                <th class="text-right">Gastos</th>
                <th class="text-right">Saldo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $pettyCashes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pettyCash): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $totalVentas = $pettyCash->total_sales_cash + $pettyCash->total_sales_qr + $pettyCash->total_sales_card;
                $saldo = $totalVentas - $pettyCash->total_expenses;
            ?>
            <tr>
                <td><?php echo e($pettyCash->date); ?></td>
                <td><?php echo e($pettyCash->user->name ?? 'N/A'); ?></td>
                <td class="text-right">$<?php echo e(number_format($pettyCash->total_sales_cash, 2)); ?></td>
                <td class="text-right">$<?php echo e(number_format($pettyCash->total_sales_qr, 2)); ?></td>
                <td class="text-right">$<?php echo e(number_format($pettyCash->total_sales_card, 2)); ?></td>
                <td class="text-right">$<?php echo e(number_format($pettyCash->total_expenses, 2)); ?></td>
                <td class="text-right">$<?php echo e(number_format($saldo, 2)); ?></td>
                <td><?php echo e($pettyCash->status === 'open' ? 'Abierta' : 'Cerrada'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="summary">
        <strong>Resumen:</strong><br>
        Total registros: <?php echo e($pettyCashes->count()); ?><br>
        Total ventas: $<?php echo e(number_format($totalSales, 2)); ?><br>
        Total gastos: $<?php echo e(number_format($totalExpenses, 2)); ?><br>
        Saldo neto: $<?php echo e(number_format($totalSales - $totalExpenses, 2)); ?>

    </div>

    <div class="footer">
        Generado el <?php echo e(date('d/m/Y H:i:s')); ?>

    </div>
</body>
</html><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/report-pdf.blade.php ENDPATH**/ ?>