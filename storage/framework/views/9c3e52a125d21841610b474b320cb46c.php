<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden <?php echo e($order->transaction_number); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Header */
        .header {
            padding: 40px 40px 30px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .company-info h1 {
            font-size: 24px;
            font-weight: 600;
            color: #203363;
            margin-bottom: 8px;
        }
        
        .company-info p {
            font-size: 14px;
            color: #666;
            margin: 3px 0;
        }
        
        .order-badge {
            text-align: right;
        }
        
        .order-number {
            font-size: 14px;
            color: #666;
            margin-bottom: 4px;
        }
        
        .order-id {
            font-size: 28px;
            font-weight: 700;
            color: #203363;
        }
        
        /* Order Info Grid */
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px 40px;
            background: #fafafa;
        }
        
        .info-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        
        .info-group .value {
            font-size: 15px;
            color: #333;
            font-weight: 500;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .badge-comer {
            background-color: #FFD166;
            color: #203363;
        }
        
        .badge-llevar {
            background-color: #06D6A0;
            color: white;
        }
        
        .badge-recoger {
            background-color: #118AB2;
            color: white;
        }
        
        /* Items Table */
        .items-section {
            padding: 40px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table thead {
            border-bottom: 2px solid #e0e0e0;
        }
        
        .items-table th {
            text-align: left;
            padding: 12px 0;
            font-size: 12px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table th:nth-child(2),
        .items-table th:nth-child(3),
        .items-table th:nth-child(4) {
            text-align: right;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .items-table td {
            padding: 16px 0;
            font-size: 15px;
            color: #333;
        }
        
        .items-table td:nth-child(2),
        .items-table td:nth-child(3),
        .items-table td:nth-child(4) {
            text-align: right;
        }
        
        .item-name {
            font-weight: 500;
        }
        
        /* Totals */
        .totals-section {
            padding: 0 40px 40px;
        }
        
        .totals {
            max-width: 350px;
            margin-left: auto;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 15px;
            color: #333;
        }
        
        .total-row.subtotal {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .total-row.grand-total {
            border-top: 2px solid #203363;
            padding-top: 16px;
            margin-top: 8px;
            font-size: 20px;
            font-weight: 700;
            color: #203363;
        }
        
        /* Footer */
        .footer {
            padding: 30px 40px;
            background: #fafafa;
            border-top: 1px solid #e0e0e0;
        }
        
        .footer-message {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .notes-box {
            background: white;
            border-left: 3px solid #FFD166;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        
        .notes-box label {
            font-size: 12px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 6px;
        }
        
        .notes-box p {
            color: #333;
            font-size: 14px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            padding: 30px 40px;
            border-top: 1px solid #e0e0e0;
        }
        
        .btn {
            padding: 12px 28px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #203363;
            color: white;
        }
        
        .btn-primary:hover {
            background: #47517c;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(32, 51, 99, 0.2);
        }
        
        .btn-secondary {
            background: white;
            color: #666;
            border: 1px solid #e0e0e0;
        }
        
        .btn-secondary:hover {
            background: #f5f5f5;
            border-color: #d0d0d0;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .action-buttons {
                display: none;
            }
            
            @page {
                margin: 1.5cm;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 0;
            }
            
            .container {
                border-radius: 0;
            }
            
            .header,
            .order-info,
            .items-section,
            .totals-section,
            .footer,
            .action-buttons {
                padding-left: 20px;
                padding-right: 20px;
            }
            
            .header-top {
                flex-direction: column;
                gap: 20px;
            }
            
            .order-badge {
                text-align: left;
            }
            
            .order-info {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .items-table {
                font-size: 13px;
            }
            
            .items-table th,
            .items-table td {
                padding: 10px 0;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="company-info">
                    <h1><?php echo e(config('app.name', 'Restaurante')); ?></h1>
                    <p><?php echo e(config('restaurant.address', 'Dirección no configurada')); ?></p>
                    <p>Tel: <?php echo e(config('restaurant.phone', 'N/A')); ?> | RUC: <?php echo e(config('restaurant.ruc', 'N/A')); ?></p>
                </div>
                <div class="order-badge">
                    <div class="order-number">ORDEN</div>
                    <div class="order-id"><?php echo e($order->transaction_number); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Order Info -->
        <div class="order-info">
            <div class="info-group">
                <label>Fecha y Hora</label>
                <div class="value"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></div>
            </div>
            
            <div class="info-group">
                <label>Tipo de Orden</label>
                <div class="value">
                    <span class="badge 
                        <?php if($order->order_type == 'Comer aquí'): ?> badge-comer
                        <?php elseif($order->order_type == 'Para llevar'): ?> badge-llevar
                        <?php else: ?> badge-recoger
                        <?php endif; ?>">
                        <?php echo e($order->order_type); ?>

                    </span>
                </div>
            </div>
            
            <?php if($order->order_type == 'Comer aquí' && $order->table_number): ?>
            <div class="info-group">
                <label>Mesa</label>
                <div class="value">Mesa <?php echo e($order->table_number); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($order->customer_name): ?>
            <div class="info-group">
                <label>Cliente</label>
                <div class="value"><?php echo e($order->customer_name); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($order->phone): ?>
            <div class="info-group">
                <label>Teléfono</label>
                <div class="value"><?php echo e($order->phone); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($order->user): ?>
            <div class="info-group">
                <label>Atendido por</label>
                <div class="value"><?php echo e($order->user->name); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($order->payment_method): ?>
            <div class="info-group">
                <label>Método de Pago</label>
                <div class="value"><?php echo e(ucfirst($order->payment_method)); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Items -->
        <div class="items-section">
            <div class="section-title">Productos</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cant.</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="item-name"><?php echo e($item->menuItem->name ?? 'Producto eliminado'); ?></td>
                        <td><?php echo e($item->quantity); ?></td>
                        <td>$<?php echo e(number_format($item->price, 2)); ?></td>
                        <td>$<?php echo e(number_format($item->quantity * $item->price, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        <!-- Totals -->
        <div class="totals-section">
            <div class="totals">
                <div class="total-row subtotal">
                    <span>Subtotal</span>
                    <span>$<?php echo e(number_format($order->subtotal, 2)); ?></span>
                </div>
                
                <?php if($order->tax > 0): ?>
                <div class="total-row">
                    <span>Impuesto (<?php echo e(config('restaurant.tax_rate', 13)); ?>%)</span>
                    <span>$<?php echo e(number_format($order->tax, 2)); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if($order->discount > 0): ?>
                <div class="total-row">
                    <span>Descuento</span>
                    <span>-$<?php echo e(number_format($order->discount, 2)); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="total-row grand-total">
                    <span>Total</span>
                    <span>$<?php echo e(number_format($order->total, 2)); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">
                Gracias por su preferencia
            </div>
            
            <?php if($order->notes): ?>
            <div class="notes-box">
                <label>Notas</label>
                <p><?php echo e($order->notes); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Imprimir
            </button>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/orders/print.blade.php ENDPATH**/ ?>