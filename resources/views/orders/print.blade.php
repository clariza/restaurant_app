<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $order->daily_order_number ?: $order->transaction_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .ticket-wrapper {
            background: #fff;
            width: 72mm;
            padding: 2mm;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .header { text-align: center; margin-bottom: 3px; }
        .title { font-weight: bold; font-size: 14px; }
        .subtitle { font-size: 11px; }
        .divider { border-top: 1px dashed #000; margin: 3px 0; }
        .item-row { display: flex; justify-content: space-between; margin: 2px 0; }
        .total-row { font-weight: bold; margin-top: 4px; }
        .footer { text-align: center; margin-top: 5px; font-size: 10px; }
        .notes { margin-top: 4px; font-size: 11px; white-space: pre-wrap; }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 10px;
            font-family: sans-serif;
        }
        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-primary { background: #203363; color: #fff; }
        .btn-secondary { background: #6b7280; color: #fff; }

        @page {
            size: 72mm auto;
            margin: 0;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: #fff;
            }
            .ticket-wrapper {
                box-shadow: none;
            }
            .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
@php
    $orderNumber = $order->daily_order_number ?: $order->transaction_number;
    $paymentLabel = $order->payment_method ?: 'Efectivo';
    $formattedDate = $order->created_at->format('j/n/Y H:i');
    $typeText = $order->order_type;
    if (($order->order_type ?? '') === 'Comer aquí' && $order->table_number) {
        $typeText .= ' ' . $order->table_number;
    }
@endphp
<div class="ticket-wrapper">
    <div class="header">
        <div class="title">RESTAURANTE MIQUNA</div>
        <div class="subtitle">{{ $formattedDate }}</div>
    </div>

    <div class="divider"></div>

    <div class="item-row">
        <span>Vendedor:</span>
        <span>{{ $order->user->name ?? 'Usuario' }}</span>
    </div>
    <div class="item-row">
        <span>Pedido:</span>
        <span>{{ $orderNumber }}</span>
    </div>

    <div class="divider"></div>

    <div class="item-row">
        <span>Tipo:</span>
        <span>{{ $typeText }}</span>
    </div>

    @if($order->customer_name)
    <div class="item-row">
        <span>Cliente:</span>
        <span>{{ $order->customer_name }}</span>
    </div>
    @endif

    <div class="divider"></div>

    @foreach($order->items as $item)
    <div class="item-row">
        <span>{{ $item->quantity }}x {{ \Illuminate\Support\Str::limit($item->name ?? ($item->menuItem->name ?? 'Producto'), 20, '') }}</span>
        <span>Bs {{ number_format($item->price * $item->quantity, 2) }}</span>
    </div>
    @endforeach

    <div class="divider"></div>

    <div class="item-row">
        <span>Subtotal:</span>
        <span>Bs{{ number_format($order->subtotal, 2) }}</span>
    </div>
    <div class="item-row">
        <span>Impuesto:</span>
        <span>Bs{{ number_format($order->tax, 2) }}</span>
    </div>
    <div class="item-row total-row">
        <span>TOTAL:</span>
        <span>Bs{{ number_format($order->total, 2) }}</span>
    </div>

    <div class="item-row">
        <span>{{ $paymentLabel }}:</span>
        <span>Bs{{ number_format($order->total, 2) }}</span>
    </div>

    @php $orderNotes = $order->order_notes ?? null; @endphp
    @if($orderNotes)
    <div class="divider"></div>
    <div class="notes">{{ $orderNotes }}</div>
    @endif

    <div class="divider"></div>
    <div class="footer">¡Gracias por su preferencia!</div>

    <div class="action-buttons">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Volver</a>
        <button onclick="window.print()" class="btn btn-primary">Imprimir</button>
    </div>
</div>
</body>
</html>
