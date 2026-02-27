<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Caja Chica - <?php echo e($date); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 15px;
            background: white;
            color: #333;
            font-size: 11px;
        }

        .report-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Header Compacto */
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #203363;
        }

        .header h1 {
            color: #203363;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header .subtitle {
            color: #666;
            font-size: 11px;
        }

        /* Grid de 2 columnas para info general */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        /* Tablas compactas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10px;
        }

        table th {
            background: #203363;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        table td {
            padding: 5px 8px;
            border-bottom: 1px solid #dee2e6;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Sección de título compacto */
        .section-title {
            background: #203363;
            color: white;
            padding: 5px 10px;
            margin: 10px 0 5px 0;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        /* Comparación compacta */
        .comparison-table {
            width: 100%;
            margin-bottom: 12px;
        }

        .comparison-table th {
            background: #e9ecef;
            color: #203363;
            padding: 6px 8px;
            font-weight: bold;
            border-bottom: 2px solid #203363;
            font-size: 10px;
        }

        .comparison-table td {
            padding: 5px 8px;
            font-size: 10px;
        }

        .comparison-table .total-row {
            background: #f8f9fa;
            font-weight: bold;
            border-top: 2px solid #203363;
        }

        .diff-positive {
            color: #155724;
            font-weight: bold;
        }

        .diff-negative {
            color: #721c24;
            font-weight: bold;
        }

        .diff-neutral {
            color: #0c5460;
            font-weight: bold;
        }

        /* Alerta compacta */
        .alert-compact {
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-compact.warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .alert-compact.success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-compact.danger {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        /* Resumen final compacto */
        .summary-compact {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }

        .summary-compact table {
            margin: 0;
        }

        .summary-compact td {
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding: 4px 8px;
            font-size: 10px;
        }

        .summary-compact .final-row {
            font-size: 12px;
            font-weight: bold;
            border-top: 2px solid rgba(255,255,255,0.5);
            padding-top: 6px;
        }

        /* Footer compacto */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        /* Firmas en una línea */
        .signature-compact {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
            font-size: 9px;
        }

        .signature-box {
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 5px;
        }

        .no-expenses {
            text-align: center;
            padding: 15px;
            color: #666;
            font-style: italic;
            font-size: 10px;
        }

        /* Optimización para impresión */
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none !important;
            }

            @page {
                margin: 0.5cm;
                size: letter portrait;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        .print-button {
            background: #203363;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-bottom: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .print-button:hover {
            background: #152546;
        }

        /* Info box compacto */
        .info-box {
            background: #f8f9fa;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #203363;
        }

        .info-value {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="report-container">
        
        <button onclick="window.print()" class="print-button no-print">
            Imprimir Reporte
        </button>

        
        <div class="header">
            <h1>REPORTE DE CAJA CHICA</h1>
            <div class="subtitle">
                <?php echo e($date); ?> | <?php echo e($user->name); ?> | Caja #<?php echo e(str_pad($pettyCash->id, 6, '0', STR_PAD_LEFT)); ?>

            </div>
        </div>

        
        <div class="info-grid">
            
            <div>
                <?php
                    // Calcular ventas del sistema
                    $salesCashSystem = $pettyCash->sales()->where('payment_method', 'Efectivo')->sum('total');
                    $salesQRSystem = $pettyCash->sales()->where('payment_method', 'QR')->sum('total');
                    $salesCardSystem = $pettyCash->sales()->where('payment_method', 'Tarjeta')->sum('total');
                    $totalSalesSystem = $salesCashSystem + $salesQRSystem + $salesCardSystem;
                    
                    // Ventas de la caja
                    $salesCashBox = $pettyCash->total_sales_cash ?? 0;
                    $salesQRBox = $pettyCash->total_sales_qr ?? 0;
                    $salesCardBox = $pettyCash->total_sales_card ?? 0;
                    $totalSalesBox = $salesCashBox + $salesQRBox + $salesCardBox;
                    
                    // Diferencias
                    $diffCash = $salesCashBox - $salesCashSystem;
                    $diffQR = $salesQRBox - $salesQRSystem;
                    $diffCard = $salesCardBox - $salesCardSystem;
                    $diffTotal = $totalSalesBox - $totalSalesSystem;
                    
                    $hasInconsistencies = abs($diffCash) > 0.01 || abs($diffQR) > 0.01 || abs($diffCard) > 0.01;
                ?>

                <div class="section-title">COMPARACIÓN: SISTEMA VS CAJA</div>

                
                <?php if($hasInconsistencies): ?>
                    <div class="alert-compact <?php echo e(abs($diffTotal) < 0.01 ? 'success' : ($diffTotal > 0 ? 'warning' : 'danger')); ?>">
                        <strong>
                            <?php if(abs($diffTotal) < 0.01): ?>
                                ✓ Coincide
                            <?php elseif($diffTotal > 0): ?>
                                Sobrante: +Bs. <?php echo e(number_format($diffTotal, 2)); ?>

                            <?php else: ?>
                                ✗ Faltante: Bs. <?php echo e(number_format($diffTotal, 2)); ?>

                            <?php endif; ?>
                        </strong>
                    </div>
                <?php else: ?>
                    <div class="alert-compact success">
                        <strong>✓ Los montos coinciden exactamente</strong>
                    </div>
                <?php endif; ?>

                
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th width="30%">Método</th>
                            <th width="25%" class="text-right">Sistema</th>
                            <th width="25%" class="text-right">Caja</th>
                            <th width="20%" class="text-right">Dif.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Efectivo</td>
                            <td class="text-right"><?php echo e(number_format($salesCashSystem, 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($salesCashBox, 2)); ?></td>
                            <td class="text-right <?php echo e(abs($diffCash) < 0.01 ? 'diff-neutral' : ($diffCash > 0 ? 'diff-positive' : 'diff-negative')); ?>">
                                <?php echo e($diffCash > 0 ? '+' : ''); ?><?php echo e(number_format($diffCash, 2)); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>QR</td>
                            <td class="text-right"><?php echo e(number_format($salesQRSystem, 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($salesQRBox, 2)); ?></td>
                            <td class="text-right <?php echo e(abs($diffQR) < 0.01 ? 'diff-neutral' : ($diffQR > 0 ? 'diff-positive' : 'diff-negative')); ?>">
                                <?php echo e($diffQR > 0 ? '+' : ''); ?><?php echo e(number_format($diffQR, 2)); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>Tarjeta</td>
                            <td class="text-right"><?php echo e(number_format($salesCardSystem, 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($salesCardBox, 2)); ?></td>
                            <td class="text-right <?php echo e(abs($diffCard) < 0.01 ? 'diff-neutral' : ($diffCard > 0 ? 'diff-positive' : 'diff-negative')); ?>">
                                <?php echo e($diffCard > 0 ? '+' : ''); ?><?php echo e(number_format($diffCard, 2)); ?>

                            </td>
                        </tr>
                        <tr class="total-row">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-right"><strong><?php echo e(number_format($totalSalesSystem, 2)); ?></strong></td>
                            <td class="text-right"><strong><?php echo e(number_format($totalSalesBox, 2)); ?></strong></td>
                            <td class="text-right <?php echo e(abs($diffTotal) < 0.01 ? 'diff-neutral' : ($diffTotal > 0 ? 'diff-positive' : 'diff-negative')); ?>">
                                <strong><?php echo e($diffTotal > 0 ? '+' : ''); ?><?php echo e(number_format($diffTotal, 2)); ?></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            
            <div>
                <div class="section-title">INFORMACIÓN GENERAL</div>
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Apertura:</span>
                        <span class="info-value"><?php echo e(\Carbon\Carbon::parse($pettyCash->date)->format('d/m/Y H:i')); ?></span>
                    </div>
                    <?php if($pettyCash->closed_at): ?>
                    <div class="info-row">
                        <span class="info-label">Cierre:</span>
                        <span class="info-value"><?php echo e(\Carbon\Carbon::parse($pettyCash->closed_at)->format('d/m/Y H:i')); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label">Responsable:</span>
                        <span class="info-value"><?php echo e($user->name); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <span class="info-value" style="color: #28a745; font-weight: bold;">CERRADA</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ventas:</span>
                        <span class="info-value"><?php echo e($pettyCash->sales()->count()); ?> registradas</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gastos:</span>
                        <span class="info-value"><?php echo e($pettyCash->expenses()->count()); ?> registrados</span>
                    </div>
                </div>

                
                <div class="summary-compact">
                    <table>
                        <tr>
                            <td>Total Ventas (Caja):</td>
                            <td class="text-right"><strong>Bs. <?php echo e(number_format($totalSalesBox, 2)); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Total Gastos:</td>
                            <td class="text-right"><strong>Bs. <?php echo e(number_format($totalExpenses, 2)); ?></strong></td>
                        </tr>
                        <tr class="final-row">
                            <td>SALDO FINAL:</td>
                            <td class="text-right"><strong>Bs. <?php echo e(number_format($totalSalesBox - $totalExpenses, 2)); ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        
        <?php if($pettyCash->expenses()->count() > 0): ?>
        <div class="section-title">DETALLE DE GASTOS</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">Nombre</th>
                    <th width="40%">Descripción</th>
                    <th width="20%" class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $pettyCash->expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($expense->expense_name); ?></td>
                    <td><?php echo e($expense->description ?? '-'); ?></td>
                    <td class="text-right">Bs. <?php echo e(number_format($expense->amount, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL GASTOS:</td>
                    <td class="text-right" style="color: #dc3545;">
                        Bs. <?php echo e(number_format($totalExpenses, 2)); ?>

                    </td>
                </tr>
            </tbody>
        </table>
        <?php endif; ?>

        
        <div class="signature-compact">
            <div class="signature-box">
                <strong>Responsable de Caja</strong><br>
                <?php echo e($user->name); ?>

            </div>
            <div class="signature-box">
                <strong>Supervisor/Gerente</strong>
            </div>
        </div>

        
        <div class="footer">
            Reporte generado el <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?> | Sistema de Gestión de Caja Chica
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\User\Documents\laravel_clary\restaurant_app\resources\views/petty_cash/print.blade.php ENDPATH**/ ?>