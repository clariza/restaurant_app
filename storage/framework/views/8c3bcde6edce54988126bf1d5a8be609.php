<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <<meta name="viewport" content="width=80mm, initial-scale=1.0">
    <title>Ticket Caja Chica - <?php echo e($date); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            background: white;
            color: #000;
            font-size: 10px;
            width: 72mm;
            margin: 0;
            padding: 2mm 3mm;
        }

        /* ── Encabezado ────────────────────────────────── */
        .header {
            text-align: center;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px dashed #000;
        }
        .header h1 {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 1px;
        }
        .header .subtitle {
            font-size: 8px;
            color: #333;
        }
        .header .caja-id {
            font-size: 9px;
            font-weight: bold;
            margin-top: 1px;
        }

        /* ── Sección título ────────────────────────────── */
        .section-title {
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
            margin: 2mm 0 1mm 0;
            padding: 1mm 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* ── Filas clave-valor ─────────────────────────── */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 1mm 0;
            border-bottom: 1px dotted #ccc;
            font-size: 8.5px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
        }
        .info-value {
            text-align: right;
        }

        /* ── Tabla de comparación ──────────────────────── */
        .cmp-header {
            display: flex;
            font-size: 7px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 1mm 0;
        }
        .cmp-row {
            display: flex;
            font-size: 7.5px;
            padding: 0.8mm 0;
            border-bottom: 1px dotted #bbb;
        }
        .cmp-row.total {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: none;
            margin-top: 1mm;
            padding-top: 1.5mm;
        }
        .col-method { flex: 2.5; }
        .col-sys    { flex: 2; text-align: right; }
        .col-box    { flex: 2; text-align: right; }
        .col-diff   { flex: 1.8; text-align: right; }

        /* ── Alerta de diferencias ─────────────────────── */
        .alert {
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            padding: 1.5mm;
            margin: 1.5mm 0;
            border: 1px solid #000;
        }
        .alert.success { border-style: solid; }
        .alert.warning { border-style: dashed; }
        .alert.danger  { border-style: double; }

        /* ── Resumen financiero ────────────────────────── */
        .summary-box {
            border: 1px solid #000;
            padding: 2mm;
            margin: 2mm 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 8.5px;
            padding: 0.8mm 0;
            border-bottom: 1px dotted #999;
        }
        .summary-row:last-child {
            border-bottom: none;
            font-size: 10px;
            font-weight: bold;
            padding-top: 1.5mm;
            border-top: 1px solid #000;
            margin-top: 1mm;
        }

        /* ── Gastos ────────────────────────────────────── */
        .expense-row {
            font-size: 8px;
            padding: 1mm 0;
            border-bottom: 1px dotted #bbb;
        }
        .expense-row .exp-top {
            display: flex;
            justify-content: space-between;
        }
        .expense-row .exp-desc {
            font-size: 7px;
            color: #555;
            padding-left: 3mm;
            margin-top: 0.5mm;
        }
        .expense-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 8.5px;
            padding: 1mm 0;
            border-top: 1px solid #000;
            margin-top: 1mm;
        }

        /* ── Notas ─────────────────────────────────────── */
        .note-box {
            border-left: 2px solid #000;
            padding: 1mm 2mm;
            margin: 1.5mm 0;
            font-size: 7.5px;
            line-height: 1.4;
        }
        .note-box .note-label {
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
        }

        /* ── Firmas ────────────────────────────────────── */
        .signatures {
            display: flex;
            gap: 4mm;
            margin-top: 6mm;
        }
        .sig-box {
            flex: 1;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #000;
            padding-top: 1mm;
            font-size: 7px;
            font-weight: bold;
        }
        .sig-name {
            font-size: 6.5px;
            margin-top: 0.5mm;
            color: #333;
        }

        /* ── Separadores ───────────────────────────────── */
        .dashed-sep {
            border: none;
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }
        .dotted-sep {
            border: none;
            border-top: 1px dotted #999;
            margin: 2mm 0;
        }

        /* ── Footer ────────────────────────────────────── */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #555;
            margin-top: 3mm;
            padding-top: 2mm;
            border-top: 1px dashed #000;
        }
        .cut-line {
            text-align: center;
            font-size: 7.5px;
            color: #999;
            margin-top: 3mm;
            letter-spacing: 2px;
        }

        /* ── Colores diferencias ───────────────────────── */
        .diff-positive { font-weight: bold; }  /* sobrante */
        .diff-negative { font-weight: bold; }  /* faltante */
        .diff-neutral  { font-weight: bold; }  /* exacto   */

        /* ── Botón de impresión (oculto al imprimir) ───── */
        .print-button {
            display: block;
            margin: 0 auto 3mm auto;
            background: #203363;
            color: white;
            border: none;
            padding: 5px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 10px;
        }
        .print-button:hover { background: #152546; }

        /* ── Media print ───────────────────────────────── */
        @media print {
           body {
        width: 72mm;
        margin: 0;
        padding: 1mm 3mm;
    }
    .no-print { display: none !important; }
           
        }
        @page {
            size: 80mm auto;
            margin: 2mm 0;
        }
    </style>
</head>
<body>

    
    <button onclick="window.print()" class="print-button no-print">🖨 Imprimir Ticket</button>

    <?php
        /* ── Cálculos (igual que en print.blade.php) ── */
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
        $diffTotal = $salesCashBox - $salesCashSystem;

        $hasInconsistencies = abs($diffCash) > 0.01 || abs($diffQR) > 0.01 || abs($diffCard) > 0.01;

        /* Helper: clase CSS para diferencia */
        $diffClass = fn($d) => abs($d) < 0.01 ? 'diff-neutral' : ($d > 0 ? 'diff-positive' : 'diff-negative');
        $diffSign  = fn($d) => $d > 0 ? '+' : '';
    ?>

    
    <div class="header">
        <h1>REPORTE CAJA CHICA</h1>
        <div class="caja-id">Caja #<?php echo e(str_pad($pettyCash->id, 6, '0', STR_PAD_LEFT)); ?></div>
        <div class="subtitle"><?php echo e($date); ?> &nbsp;|&nbsp; <?php echo e($user->name); ?></div>
    </div>

    
    <div class="section-title">INFORMACIÓN GENERAL</div>

    <table width="100%" style="font-size:9px; border-bottom:1px dotted #ccc;">
    <tr>
        <td><strong>Apertura:</strong></td>
        <td align="right"><?php echo e(\Carbon\Carbon::parse($pettyCash->date)->format('d/m/Y H:i')); ?></td>
    </tr>
</table>
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
        <span class="info-value" style="font-weight:bold;">CERRADA</span>
    </div>
    <div class="info-row">
        <span class="info-label">Ventas registradas:</span>
        <span class="info-value"><?php echo e($pettyCash->sales()->count()); ?></span>
    </div>

    
    <div class="section-title">SISTEMA vs CAJA</div>

    
    <?php if($hasInconsistencies): ?>
        <div class="alert <?php echo e(abs($diffTotal) < 0.01 ? 'success' : ($diffTotal > 0 ? 'warning' : 'danger')); ?>">
            <?php if(abs($diffTotal) < 0.01): ?>
                ✓ Diferencias parciales se compensan
            <?php elseif($diffTotal > 0): ?>
                ▲ SOBRANTE: +Bs. <?php echo e(number_format($diffTotal, 2)); ?>

            <?php else: ?>
                ✗ FALTANTE: Bs. <?php echo e(number_format($diffTotal, 2)); ?>

            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert success">
            ✓ Montos coinciden exactamente
        </div>
    <?php endif; ?>

    
    <div class="cmp-header">
        <span class="col-method">MÉTODO</span>
        <span class="col-sys">SIST.</span>
        <span class="col-box">CAJA</span>
        <span class="col-diff">DIF.</span>
    </div>

    
    <div class="cmp-row">
        <span class="col-method">Efectivo</span>
        <span class="col-sys"><?php echo e(number_format($salesCashSystem, 2)); ?></span>
        <span class="col-box"><?php echo e(number_format($salesCashBox, 2)); ?></span>
        <span class="col-diff <?php echo e($diffClass($diffCash)); ?>">
            <?php echo e($diffSign($diffCash)); ?><?php echo e(number_format($diffCash, 2)); ?>

        </span>
    </div>

    
    <div class="cmp-row">
        <span class="col-method">QR</span>
        <span class="col-sys"><?php echo e(number_format($salesQRSystem, 2)); ?></span>
        <span class="col-box"><?php echo e(number_format($salesQRBox, 2)); ?></span>
        <span class="col-diff <?php echo e($diffClass($diffQR)); ?>">
            <?php echo e($diffSign($diffQR)); ?><?php echo e(number_format($diffQR, 2)); ?>

        </span>
    </div>

    
    <div class="cmp-row">
        <span class="col-method">Tarjeta</span>
        <span class="col-sys"><?php echo e(number_format($salesCardSystem, 2)); ?></span>
        <span class="col-box"><?php echo e(number_format($salesCardBox, 2)); ?></span>
        <span class="col-diff <?php echo e($diffClass($diffCard)); ?>">
            <?php echo e($diffSign($diffCard)); ?><?php echo e(number_format($diffCard, 2)); ?>

        </span>
    </div>

    
    <div class="cmp-row total">
        <span class="col-method">TOTAL</span>
        <span class="col-sys"><?php echo e(number_format($salesCashSystem, 2)); ?></span>
        <span class="col-box"><?php echo e(number_format($salesCashBox, 2)); ?></span>
        <span class="col-diff <?php echo e($diffClass($diffTotal)); ?>">
            <?php echo e($diffSign($diffTotal)); ?><?php echo e(number_format($diffTotal, 2)); ?>

        </span>
    </div>

    
    <div class="section-title">RESUMEN FINANCIERO</div>

    <div class="summary-box">
        <div class="summary-row">
            <span>Total Ventas (Caja):</span>
            <span>Bs. <?php echo e(number_format($totalSalesBox, 2)); ?></span>
        </div>
        <div class="summary-row">
            <span>Total Gastos:</span>
            <span>Bs. <?php echo e(number_format($totalExpenses, 2)); ?></span>
        </div>
        <div class="summary-row">
            <span>SALDO FINAL:</span>
            <span>Bs. <?php echo e(number_format($totalSalesBox - $totalExpenses, 2)); ?></span>
        </div>
    </div>

    
    <?php if(!empty($pettyCash->opening_notes)): ?>
        <div class="section-title">NOTAS DE APERTURA</div>
        <div class="note-box">
            <div class="note-label">📝 Observaciones:</div>
            <?php echo e($pettyCash->opening_notes); ?>

        </div>
    <?php endif; ?>

    
    <?php if(!empty($pettyCash->notes)): ?>
        <div class="section-title">NOTAS DE CIERRE</div>
        <div class="note-box">
            <div class="note-label">📝 Observaciones:</div>
            <?php echo e($pettyCash->notes); ?>

        </div>
    <?php endif; ?>

    
    <?php if($pettyCash->expenses()->count() > 0): ?>
        <div class="section-title">DETALLE DE GASTOS</div>

        <?php $__currentLoopData = $pettyCash->expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="expense-row">
                <div class="exp-top">
                    <span><?php echo e($index + 1); ?>. <?php echo e($expense->expense_name); ?></span>
                    <span><strong>Bs. <?php echo e(number_format($expense->amount, 2)); ?></strong></span>
                </div>
                <?php if(!empty($expense->description)): ?>
                    <div class="exp-desc"><?php echo e($expense->description); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="expense-total">
            <span>TOTAL GASTOS:</span>
            <span>Bs. <?php echo e(number_format($totalExpenses, 2)); ?></span>
        </div>
    <?php endif; ?>

    
    <hr class="dashed-sep">

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line">Responsable</div>
            <div class="sig-name"><?php echo e($user->name); ?></div>
        </div>
        <div class="sig-box">
            <div class="sig-line">Supervisor</div>
            <div class="sig-name">&nbsp;</div>
        </div>
    </div>

    
    <div class="footer">
        Generado: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?><br>
        Sistema de Gestión de Caja Chica
    </div>

    <div class="cut-line">- - - - ✂ - - - -</div>

</body>
</html><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/print.blade.php ENDPATH**/ ?>