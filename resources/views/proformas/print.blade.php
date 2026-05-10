<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Proforma PROF-{{ $proforma->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .ticket-wrapper {
            background: white;
            width: 320px;
            padding: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .header { text-align: center; margin-bottom: 6px; }
        .title   { font-weight: bold; font-size: 14px; letter-spacing: 1px; }
        .subtitle { font-size: 11px; color: #444; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .item-row { display: flex; justify-content: space-between; margin: 2px 0; font-size: 12px; }
        .item-row .label { flex-shrink: 0; }
        .item-row .value { text-align: right; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; margin: 3px 0; }
        .notes { font-size: 11px; white-space: pre-wrap; margin: 4px 0; }
        .footer { text-align: center; margin-top: 6px; font-size: 11px; }
        .action-buttons { display: flex; gap: 10px; justify-content: center; margin-top: 16px; }
        .btn {
            padding: 8px 20px; border: none; border-radius: 5px; font-size: 13px;
            font-family: sans-serif; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-primary  { background: #203363; color: white; }
        .btn-secondary { background: #ccc; color: #333; }
        @media print {
            body { background: white; padding: 0; display: block; }
            .ticket-wrapper { box-shadow: none; }
            .action-buttons { display: none; }
            @page { size: 72mm auto; margin: 2mm; }
        }
    </style>
</head>
<body>
<div class="ticket-wrapper">
    <div class="header">
        <div class="title">{{ strtoupper(config('app.name', 'RESTAURANTE')) }}</div>
        <div class="subtitle">{{ $proforma->created_at->format('d/m/Y H:i') }}</div>
    </div>

    <div class="divider"></div>

    <div class="item-row">
        <span class="label">Proforma:</span>
        <span class="value">PROF-{{ $proforma->id }}</span>
    </div>
    <div class="item-row">
        <span class="label">Tipo:</span>
        <span class="value">{{ $proforma->order_type ?: 'N/A' }}</span>
    </div>
    @if($proforma->customer_name)
    <div class="item-row">
        <span class="label">Cliente:</span>
        <span class="value">{{ $proforma->customer_name }}</span>
    </div>
    @endif

    <div class="divider"></div>

    @foreach($proforma->items as $item)
    <div class="item-row">
        <span class="label">{{ $item->quantity }}x {{ \Illuminate\Support\Str::limit($item->name, 22) }}</span>
        <span class="value">Bs {{ number_format($item->quantity * $item->price, 2) }}</span>
    </div>
    @endforeach

    <div class="divider"></div>

    <div class="item-row">
        <span class="label">Subtotal:</span>
        <span class="value">Bs {{ number_format($proforma->subtotal, 2) }}</span>
    </div>
    <div class="item-row">
        <span class="label">Impuesto:</span>
        <span class="value">Bs {{ number_format($proforma->tax, 2) }}</span>
    </div>
    <div class="total-row">
        <span>TOTAL:</span>
        <span>Bs {{ number_format($proforma->total, 2) }}</span>
    </div>

    @if($proforma->notes)
    <div class="divider"></div>
    <div class="notes">{{ $proforma->notes }}</div>
    @endif

    <div class="divider"></div>
    <div class="footer">Proforma pendiente de cobro</div>

    <div class="action-buttons">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">&#8592; Volver</a>
        <button onclick="window.print()" class="btn btn-primary">&#128424; Imprimir</button>
    </div>
</div>
</body>
</html>
