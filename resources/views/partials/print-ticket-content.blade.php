<div class="header">
    <div class="title">{{ $ticket['title'] }}</div>
    <div class="subtitle">{{ $ticket['date'] }}</div>
</div>

<div class="divider"></div>

<div class="item-row">
    <span>Vendedor:</span>
    <span>{{ $ticket['seller'] }}</span>
</div>
<div class="item-row">
    <span>Pedido:</span>
    <span>{{ $ticket['order_number'] }}</span>
</div>

<div class="divider"></div>

@if(!empty($ticket['type']))
<div class="item-row">
    <span>Tipo:</span>
    <span>{{ $ticket['type'] }}</span>
</div>
@endif

@if(!empty($ticket['customer']))
<div class="item-row">
    <span>Cliente:</span>
    <span>{{ $ticket['customer'] }}</span>
</div>
@endif

<div class="divider"></div>

@foreach($ticket['items'] as $item)
<div class="item-row">
    <span>{{ $item['quantity'] }}x {{ $item['name'] }}</span>
    <span>Bs {{ number_format($item['amount'], 2) }}</span>
</div>
@endforeach

<div class="divider"></div>

<div class="item-row">
    <span>Subtotal:</span>
    <span>Bs{{ number_format($ticket['subtotal'], 2) }}</span>
</div>
<div class="item-row">
    <span>Impuesto:</span>
    <span>Bs{{ number_format($ticket['tax'], 2) }}</span>
</div>
<div class="item-row total-row">
    <span>TOTAL:</span>
    <span>Bs{{ number_format($ticket['total'], 2) }}</span>
</div>

@foreach($ticket['payments'] as $payment)
<div class="item-row">
    <span>{{ $payment['label'] }}:</span>
    <span>Bs{{ number_format($payment['amount'], 2) }}</span>
</div>
@endforeach

@if(!empty($ticket['notes']))
<div class="divider"></div>
<div class="notes">{{ $ticket['notes'] }}</div>
@endif

<div class="divider"></div>
<div class="footer">¡Gracias por su preferencia!</div>
