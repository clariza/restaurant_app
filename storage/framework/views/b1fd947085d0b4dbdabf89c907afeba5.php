<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page {
            size: 80mm auto;   /* ancho fijo, alto automático */
            margin: 2mm 4mm;   /* márgenes físicos */
        }
        @media print {
        body {
            width: 80mm;
            margin: 0;
            padding: 0;
        }
    }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            width: 80mm;        /* 80mm exactos en px a 96dpi: 80 * 2.8346 = ~226 */
            margin: 0 auto;
            padding: 0 4mm;      /* margen lateral pequeño */
        }

        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 4mm;
            padding-bottom: 3mm;
            border-bottom: 1px dashed #000;
        }
        .header h1 { font-size: 13px; font-weight: bold; letter-spacing: 1px; }
        .header .caja-id { font-size: 9px; font-weight: bold; margin-top: 2px; }
        .header .subtitle { font-size: 8px; }

        /* Títulos de sección */
        .section-title {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
            padding: 2mm 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin: 3mm 0 2mm 0;
        }

        /* Tablas generales */
        table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    word-wrap: break-word;
}

        /* Filas de info clave-valor */
        .info-table td {
            font-size: 9px;
            padding: 1.2mm 0;
            border-bottom: 1px dotted #ccc;
        }
        .info-table tr:last-child td { border-bottom: none; }
        .info-table .label { font-weight: bold; width: 45%; }
        .info-table .value { text-align: right; }

        /* Tabla comparación */
        .cmp-table th {
            font-size: 7.5px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 1mm 0;
            text-align: left;
        }
        .cmp-table th.r, .cmp-table td.r { text-align: right; }
        .cmp-table td {
            font-size: 8px;
            padding: 1mm 0;
            border-bottom: 1px dotted #bbb;
        }
        .cmp-table tr.total td {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: none;
            padding-top: 1.5mm;
        }

        /* Alerta */
        .alert {
            text-align: center;
            font-size: 8.5px;
            font-weight: bold;
            padding: 2mm;
            margin: 2mm 0;
            border: 1px solid #000;
        }
        .alert.dashed { border-style: dashed; }
        .alert.double  { border-style: double; }

        /* Resumen financiero */
        .summary-box {
            border: 1px solid #000;
            padding: 2mm;
            margin: 2mm 0;
        }
        .summary-table td {
            font-size: 9px;
            padding: 1mm 0;
            border-bottom: 1px dotted #999;
        }
        .summary-table tr:last-child td {
            font-size: 11px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: none;
            padding-top: 2mm;
        }

        /* Gastos */
        .expense-name  { font-size: 8.5px; }
        .expense-desc  { font-size: 7.5px; color: #555; padding-left: 3mm; }
        .expense-total td {
            font-weight: bold;
            font-size: 9px;
            padding-top: 1.5mm;
            border-top: 1px solid #000;
        }

        /* Notas */
        .note-box {
            border-left: 2px solid #000;
            padding: 1.5mm 2mm;
            margin: 2mm 0;
            font-size: 8px;
            line-height: 1.5;
        }
        .note-label {
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }

        /* Firmas */
        .sig-table td {
            width: 50%;
            text-align: center;
            padding-top: 6mm;
            font-size: 7.5px;
            font-weight: bold;
        }
        .sig-line {
            border-top: 1px solid #000;
            padding-top: 1.5mm;
        }
        .sig-name { font-size: 7px; color: #333; margin-top: 0.5mm; }

        /* Separadores */
        .dashed-sep { border-top: 1px dashed #000; margin: 3mm 0; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #555;
            margin-top: 4mm;
            padding-top: 3mm;
            border-top: 1px dashed #000;
        }
        .cut-line {
            text-align: center;
            font-size: 8px;
            color: #999;
            margin-top: 4mm;
            letter-spacing: 2px;
        }
        td, th {
    overflow: hidden;
    word-break: break-word;
}
    </style>
</head>
<body>

<?php
    $salesCashSystem  = $pettyCash->sales()->where('payment_method', 'Efectivo')->sum('total');
    $salesQRSystem    = $pettyCash->sales()->where('payment_method', 'QR')->sum('total');
    $salesCardSystem  = $pettyCash->sales()->where('payment_method', 'Tarjeta')->sum('total');
    $totalSalesSystem = $salesCashSystem + $salesQRSystem + $salesCardSystem;

    $salesCashBox  = $pettyCash->total_sales_cash ?? 0;
    $salesQRBox    = $pettyCash->total_sales_qr   ?? 0;
    $salesCardBox  = $pettyCash->total_sales_card ?? 0;
    $totalSalesBox = $salesCashBox + $salesQRBox + $salesCardBox;

    $diffCash  = $salesCashBox  - $salesCashSystem;
    $diffQR    = $salesQRBox    - $salesQRSystem;
    $diffCard  = $salesCardBox  - $salesCardSystem;
    $diffTotal = $totalSalesBox - $totalSalesSystem;

    $hasInconsistencies = abs($diffCash) > 0.01 || abs($diffQR) > 0.01 || abs($diffCard) > 0.01;
    $diffSign = fn($d) => $d > 0 ? '+' : '';
?>


<div class="header">
    <h1>REPORTE CAJA CHICA</h1>
    <div class="caja-id">Caja #<?php echo e(str_pad($pettyCash->id, 6, '0', STR_PAD_LEFT)); ?></div>
    <div class="subtitle"><?php echo e($date); ?> &nbsp;|&nbsp; <?php echo e($user->name); ?></div>
</div>


<div class="section-title">INFORMACIÓN GENERAL</div>
<table class="info-table">
    <tr>
        <td class="label">Apertura:</td>
        <td class="value"><?php echo e(\Carbon\Carbon::parse($pettyCash->date)->format('d/m/Y H:i')); ?></td>
    </tr>
    <?php if($pettyCash->closed_at): ?>
    <tr>
        <td class="label">Cierre:</td>
        <td class="value"><?php echo e(\Carbon\Carbon::parse($pettyCash->closed_at)->format('d/m/Y H:i')); ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td class="label">Responsable:</td>
        <td class="value"><?php echo e($user->name); ?></td>
    </tr>
    <tr>
        <td class="label">Estado:</td>
        <td class="value"><strong>CERRADA</strong></td>
    </tr>
    <tr>
        <td class="label">Ventas registradas:</td>
        <td class="value"><?php echo e($pettyCash->sales()->count()); ?></td>
    </tr>
</table>


<div class="section-title">SISTEMA vs CAJA</div>

<?php if($hasInconsistencies): ?>
    <div class="alert <?php echo e(abs($diffTotal) < 0.01 ? '' : ($diffTotal > 0 ? 'dashed' : 'double')); ?>">
        <?php if(abs($diffTotal) < 0.01): ?>
            ✓ Diferencias parciales se compensan
        <?php elseif($diffTotal > 0): ?>
            ▲ SOBRANTE: +Bs. <?php echo e(number_format($diffTotal, 2)); ?>

        <?php else: ?>
            ✗ FALTANTE: Bs. <?php echo e(number_format($diffTotal, 2)); ?>

        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="alert">✓ Montos coinciden exactamente</div>
<?php endif; ?>

<table class="cmp-table">
    <thead>
        <tr>
            <th style="width:35%">MÉTODO</th>
            <th class="r" style="width:22%">SIST.</th>
            <th class="r" style="width:22%">CAJA</th>
            <th class="r" style="width:21%">DIF.</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Efectivo</td>
            <td class="r"><?php echo e(number_format($salesCashSystem, 2)); ?></td>
            <td class="r"><?php echo e(number_format($salesCashBox, 2)); ?></td>
            <td class="r"><strong><?php echo e($diffSign($diffCash)); ?><?php echo e(number_format($diffCash, 2)); ?></strong></td>
        </tr>
        <tr>
            <td>QR</td>
            <td class="r"><?php echo e(number_format($salesQRSystem, 2)); ?></td>
            <td class="r"><?php echo e(number_format($salesQRBox, 2)); ?></td>
            <td class="r"><strong><?php echo e($diffSign($diffQR)); ?><?php echo e(number_format($diffQR, 2)); ?></strong></td>
        </tr>
        <tr>
            <td>Tarjeta</td>
            <td class="r"><?php echo e(number_format($salesCardSystem, 2)); ?></td>
            <td class="r"><?php echo e(number_format($salesCardBox, 2)); ?></td>
            <td class="r"><strong><?php echo e($diffSign($diffCard)); ?><?php echo e(number_format($diffCard, 2)); ?></strong></td>
        </tr>
        <tr class="total">
            <td>TOTAL</td>
            <td class="r"><?php echo e(number_format($totalSalesSystem, 2)); ?></td>
            <td class="r"><?php echo e(number_format($totalSalesBox, 2)); ?></td>
            <td class="r"><?php echo e($diffSign($diffTotal)); ?><?php echo e(number_format($diffTotal, 2)); ?></td>
        </tr>
    </tbody>
</table>


<div class="section-title">RESUMEN FINANCIERO</div>
<div class="summary-box">
    <table class="summary-table">
        <tr>
            <td>Total Ventas (Caja):</td>
            <td style="text-align:right">Bs. <?php echo e(number_format($totalSalesBox, 2)); ?></td>
        </tr>
        <tr>
            <td>Total Gastos:</td>
            <td style="text-align:right">Bs. <?php echo e(number_format($totalExpenses, 2)); ?></td>
        </tr>
        <tr>
            <td>SALDO FINAL:</td>
            <td style="text-align:right">Bs. <?php echo e(number_format($totalSalesBox - $totalExpenses, 2)); ?></td>
        </tr>
    </table>
</div>


<?php if(!empty($pettyCash->opening_notes)): ?>
    <div class="section-title">NOTAS DE APERTURA</div>
    <div class="note-box">
        <div class="note-label">Observaciones:</div>
        <?php echo e($pettyCash->opening_notes); ?>

    </div>
<?php endif; ?>


<?php if(!empty($pettyCash->notes)): ?>
    <div class="section-title">NOTAS DE CIERRE</div>
    <div class="note-box">
        <div class="note-label">Observaciones:</div>
        <?php echo e($pettyCash->notes); ?>

    </div>
<?php endif; ?>


<?php if($pettyCash->expenses()->count() > 0): ?>
    <div class="section-title">DETALLE DE GASTOS</div>
    <table>
        <?php $__currentLoopData = $pettyCash->expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="expense-name" style="padding:1.2mm 0; border-bottom:1px dotted #bbb;">
                <?php echo e($index + 1); ?>. <?php echo e($expense->expense_name); ?>

                <?php if(!empty($expense->description)): ?>
                    <br><span class="expense-desc"><?php echo e($expense->description); ?></span>
                <?php endif; ?>
            </td>
            <td style="text-align:right; padding:1.2mm 0; border-bottom:1px dotted #bbb; font-size:8.5px;">
                <strong>Bs. <?php echo e(number_format($expense->amount, 2)); ?></strong>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr class="expense-total">
            <td>TOTAL GASTOS:</td>
            <td style="text-align:right">Bs. <?php echo e(number_format($totalExpenses, 2)); ?></td>
        </tr>
    </table>
<?php endif; ?>


<hr class="dashed-sep">
<table class="sig-table">
    <tr>
        <td>
            <div class="sig-line">Responsable</div>
            <div class="sig-name"><?php echo e($user->name); ?></div>
        </td>
        <td>
            <div class="sig-line">Supervisor</div>
            <div class="sig-name">&nbsp;</div>
        </td>
    </tr>
</table>


<div class="footer">
    Generado: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?><br>
    Sistema de Gestión de Caja Chica
</div>
<div class="cut-line">- - - - ✂ - - - -</div>

</body>
</html><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/ticket-pdf.blade.php ENDPATH**/ ?>