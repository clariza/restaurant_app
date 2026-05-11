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

    {{-- Encabezado --}}
    <div class="header">
        <div class="title">RESTAURANTE MIQUNA</div>
        <div class="subtitle">{{ $order->created_at->format('d/m/Y H:i') }}</div>
    </div>

    <hr class="divider">

    {{-- Info de la orden --}}
    <div class="item-row">
        <span>Vendedor:</span>
        <span>{{ $order->user->name }}</span>
    </div>
    <div class="item-row">
        <span>Pedido:</span>
        <span>#{{ $order->transaction_number }}</span>
    </div>

    <hr class="divider">

    {{-- Tipo de pedido --}}
    @if($order->order_type)
    <div class="item-row">
        <span>Tipo:</span>
        <span>
            @if(strtolower($order->order_type) === 'comer aquí' || strtolower($order->order_type) === 'comer aqui')
                Para la Mesa{{ $order->table_number ? ' ' . $order->table_number : '' }}
            @else
                {{ ucfirst($order->order_type) }}
            @endif
        </span>
    </div>
    @endif

    {{-- Cliente --}}
    @if($order->customer_name)
    <div class="item-row">
        <span>Cliente:</span>
        <span>{{ $order->customer_name }}</span>
    </div>
    @endif

    {{-- Teléfono --}}
    @if($order->phone)
    <div class="item-row">
        <span>Tel:</span>
        <span>{{ $order->phone }}</span>
    </div>
    @endif

    <hr class="divider">

    {{-- Ítems --}}
    @foreach($order->items as $item)
    <div class="item-row">
        <span>{{ $item->quantity }}x {{ Str::limit($item->menuItem->name, 20) }}</span>
        <span>Bs {{ number_format($item->price * $item->quantity, 2) }}</span>
    </div>
    @endforeach

    <hr class="divider">

    {{-- Totales --}}
    <div class="item-row">
        <span>Subtotal:</span>
        <span>Bs {{ number_format($order->total, 2) }}</span>
    </div>
    <div class="item-row">
        <span>Impuesto:</span>
        <span>Bs 0.00</span>
    </div>
    <div class="item-row total-row">
        <span>TOTAL:</span>
        <span>Bs {{ number_format($order->total, 2) }}</span>
    </div>

    {{-- Métodos de pago --}}
    @if($order->paymentMethods && $order->paymentMethods->count() > 0)
        @foreach($order->paymentMethods as $payment)
        <div class="item-row">
            <span>{{ $payment->method }}:</span>
            <span>Bs {{ number_format($payment->amount, 2) }}</span>
        </div>
        @endforeach

        {{-- Cambio (si aplica) --}}
        @php
            $totalPaid = $order->paymentMethods->sum('amount');
            $change = $totalPaid - $order->total;
        @endphp
        @if($change > 0)
        <div class="item-row total-row">
            <span>CAMBIO:</span>
            <span>Bs {{ number_format($change, 2) }}</span>
        </div>
        @endif
    @endif

    {{-- Notas del pedido --}}
    @if($order->order_notes)
    <hr class="divider">
    <div class="notes">Notas del pedido: {{ $order->order_notes }}</div>
    @endif

    {{-- Notas del cliente --}}
    @if($order->customer_notes)
    <hr class="divider">
    <div class="notes">Notas del cliente: {{ $order->customer_notes }}</div>
    @endif

    <hr class="divider">
    <div class="footer">¡Gracias por su preferencia!</div>

</body>
</html>