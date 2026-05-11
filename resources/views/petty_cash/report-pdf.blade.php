<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            width: 72mm;
            margin: 0 auto;
            padding: 2mm;
            -webkit-print-color-adjust: exact;
        }

        /* ── Encabezado ── */
        .header {
            text-align: center;
            margin-bottom: 4px;
        }
        .header .title {
            font-weight: bold;
            font-size: 14px;
        }
        .header .subtitle {
            font-size: 11px;
        }
        .header .caja-id {
            font-size: 11px;
            font-weight: bold;
            margin-top: 1px;
        }

        /* ── Separadores ── */
        .divider {
            border-top: 1px dashed #000;
            margin: 3px 0;
        }
        .divider-solid {
            border-top: 1px solid #000;
            margin: 3px 0;
        }

        /* ── Filas clave-valor ── */
        .item-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 11px;
        }
        .item-row span:first-child {
            font-weight: bold;
        }

        /* ── Filas de comparación ── */
        .cmp-header {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        .cmp-header .col-method { flex: 0 0 30%; }
        .cmp-header .col-num    { flex: 0 0 23%; text-align: right; }

        .cmp-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin: 1px 0;
            border-bottom: 1px dotted #bbb;
            padding-bottom: 1px;
        }
        .cmp-row .col-method { flex: 0 0 30%; }
        .cmp-row .col-num    { flex: 0 0 23%; text-align: right; }

        .cmp-total {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: bold;
            border-top: 1px solid #000;
            margin-top: 2px;
            padding-top: 2px;
        }
        .cmp-total .col-method { flex: 0 0 30%; }
        .cmp-total .col-num    { flex: 0 0 23%; text-align: right; }

        /* ── Alerta de diferencias ── */
        .alert {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            padding: 2px;
            margin: 2px 0;
            border: 1px solid #000;
        }
        .alert.dashed  { border-style: dashed; }
        .alert.double  { border-style: double; }

        /* ── Totales (igual que .total-row del ticket) ── */
        .total-row {
            font-weight: bold;
            margin-top: 4px;
            font-size: 12px;
        }

        /* ── Saldo final destacado ── */
        .saldo-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 13px;
            margin-top: 4px;
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        /* ── Gastos ── */
        .expense-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin: 2px 0;
            border-bottom: 1px dotted #bbb;
            padding-bottom: 1px;
        }
        .expense-row .expense-name { flex: 1; }
        .expense-row .expense-desc {
            display: block;
            font-size: 9px;
            color: #555;
            padding-left: 6px;
        }
        .expense-row .expense-amount {
            font-weight: bold;
            white-space: nowrap;
            padding-left: 4px;
        }
        .expense-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 11px;
            border-top: 1px solid #000;
            margin-top: 2px;
            padding-top: 2px;
        }

        /* ── Notas (igual que .notes del ticket) ── */
        .notes {
            margin-top: 4px;
            font-size: 11px;
            white-space: pre-wrap;
        }

        /* ── Títulos de sección ── */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
            margin: 3px 0 2px 0;
        }

        /* ── Firmas ── */
        .sig-table {
            width: 100%;
            margin-top: 6mm;
        }
        .sig-table td {
            width: 50%;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }
        .sig-line {
            border-top: 1px solid #000;
            padding-top: 2px;
            margin-top: 6mm;
        }
        .sig-name {
            font-size: 8px;
            font-weight: normal;
            color: #333;
            margin-top: 1px;
        }

        /* ── Footer (igual que .footer del ticket) ── */
        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: 10px;
        }

        /* ── Línea de corte ── */
        .cut-line {
            text-align: center;
            font-size: 9px;
            color: #999;
            margin-top: 5px;
            letter-spacing: 2px;
        }

        @page {
            size: 72mm auto;
            margin: 0;
        }
        @media print {
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

@php
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
@endphp

{{-- ══ ENCABEZADO ══ --}}
<div class="header">
    <div class="title">REPORTE CAJA CHICA</div>
    <div class="caja-id">Caja #{{ str_pad($pettyCash->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="subtitle">{{ $date }} &nbsp;|&nbsp; {{ $user->name }}</div>
</div>
<div class="divider"></div>

{{-- ══ INFORMACIÓN GENERAL ══ --}}
<div class="item-row">
    <span>Apertura:</span>
    <span>{{ \Carbon\Carbon::parse($pettyCash->date)->format('d/m/Y H:i') }}</span>
</div>

@if($pettyCash->closed_at)
<div class="item-row">
    <span>Cierre:</span>
    <span>{{ \Carbon\Carbon::parse($pettyCash->closed_at)->format('d/m/Y H:i') }}</span>
</div>
@endif

<div class="item-row">
    <span>Responsable:</span>
    <span>{{ $user->name }}</span>
</div>
<div class="item-row">
    <span>Estado:</span>
    <span>CERRADA</span>
</div>
<div class="item-row">
    <span>Ventas registradas:</span>
    <span>{{ $pettyCash->sales()->count() }}</span>
</div>
<div class="divider"></div>

{{-- ══ SISTEMA vs CAJA ══ --}}
<div class="section-title">SISTEMA vs CAJA</div>

@if($hasInconsistencies)
    <div class="alert {{ abs($diffTotal) < 0.01 ? '' : ($diffTotal > 0 ? 'dashed' : 'double') }}">
        @if(abs($diffTotal) < 0.01)
            ✓ Diferencias parciales se compensan
        @elseif($diffTotal > 0)
            ▲ SOBRANTE: +Bs. {{ number_format($diffTotal, 2) }}
        @else
            ✗ FALTANTE: Bs. {{ number_format($diffTotal, 2) }}
        @endif
    </div>
@else
    <div class="alert">✓ Montos coinciden exactamente</div>
@endif

<div class="cmp-header">
    <span class="col-method">MÉTODO</span>
    <span class="col-num">SIST.</span>
    <span class="col-num">CAJA</span>
    <span class="col-num">DIF.</span>
</div>

<div class="cmp-row">
    <span class="col-method">Efectivo</span>
    <span class="col-num">{{ number_format($salesCashSystem, 2) }}</span>
    <span class="col-num">{{ number_format($salesCashBox, 2) }}</span>
    <span class="col-num"><b>{{ $diffSign($diffCash) }}{{ number_format($diffCash, 2) }}</b></span>
</div>
<div class="cmp-row">
    <span class="col-method">QR</span>
    <span class="col-num">{{ number_format($salesQRSystem, 2) }}</span>
    <span class="col-num">{{ number_format($salesQRBox, 2) }}</span>
    <span class="col-num"><b>{{ $diffSign($diffQR) }}{{ number_format($diffQR, 2) }}</b></span>
</div>
<div class="cmp-row">
    <span class="col-method">Tarjeta</span>
    <span class="col-num">{{ number_format($salesCardSystem, 2) }}</span>
    <span class="col-num">{{ number_format($salesCardBox, 2) }}</span>
    <span class="col-num"><b>{{ $diffSign($diffCard) }}{{ number_format($diffCard, 2) }}</b></span>
</div>

<div class="cmp-total">
    <span class="col-method">TOTAL</span>
    <span class="col-num">{{ number_format($totalSalesSystem, 2) }}</span>
    <span class="col-num">{{ number_format($totalSalesBox, 2) }}</span>
    <span class="col-num">{{ $diffSign($diffTotal) }}{{ number_format($diffTotal, 2) }}</span>
</div>

<div class="divider"></div>


{{-- ══ NOTAS DE APERTURA ══ --}}
@if(!empty($pettyCash->opening_notes))
    <div class="section-title">APERTURA</div>
    <div class="notes">{{ $pettyCash->opening_notes }}</div>
    <div class="divider"></div>
@endif

{{-- ══ NOTAS DE CIERRE ══ --}}
@if(!empty($pettyCash->notes))
    <div class="section-title">CIERRE</div>
    <div class="notes">{{ $pettyCash->notes }}</div>
    <div class="divider"></div>
@endif

{{-- ══ DETALLE DE GASTOS ══ --}}
@if($pettyCash->expenses()->count() > 0)
    <div class="section-title">DETALLE DE GASTOS</div>

    @foreach($pettyCash->expenses as $index => $expense)
    <div class="expense-row">
        <div class="expense-name">
            {{ $index + 1 }}. {{ $expense->expense_name }}
            @if(!empty($expense->description))
                <span class="expense-desc">{{ $expense->description }}</span>
            @endif
        </div>
        <div class="expense-amount">Bs{{ number_format($expense->amount, 2) }}</div>
    </div>
    @endforeach

    <div class="expense-total">
        <span>TOTAL GASTOS:</span>
        <span>Bs{{ number_format($totalExpenses, 2) }}</span>
    </div>
    <div class="divider"></div>
@endif

{{-- ══ FIRMAS ══ --}}
<table class="sig-table">
    <tr>
        <td>
            <div class="sig-line">Responsable</div>
            <div class="sig-name">{{ $user->name }}</div>
        </td>
        <td>
            <div class="sig-line">Supervisor</div>
            <div class="sig-name">&nbsp;</div>
        </td>
    </tr>
</table>

{{-- ══ FOOTER ══ --}}
<div class="divider"></div>
<div class="footer">
    Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}<br>
    Sistema de Gestión de Caja Chica
</div>
<div class="cut-line">- - - - ✂ - - - -</div>

</body>
</html>