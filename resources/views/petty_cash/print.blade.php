<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Caja Chica - {{ $date }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header img { max-width: 150px; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 12px; color: #555; margin-bottom: 15px; }
        .company-info { margin-bottom: 15px; text-align: center; }
        .details { margin: 15px 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .totals { margin-top: 20px; }
        .totals .table { width: 60%; margin-left: auto; }
        .signature { margin-top: 50px; }
        .section-title { font-weight: bold; margin: 10px 0 5px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE CIERRE DE CAJA</div>
        <div class="subtitle">Fecha: {{ $date }}</div>
    </div>

    <div class="company-info">
        <!-- <div><strong>{{ config('app.name') }}</strong></div> -->
        <div>Vendedor: {{ $user->name }}</div>
    </div>

    <div class="details">
        <div class="section-title">INFORMACIÓN DEL CIERRE</div>
        <table class="table">
            <tr>
                <th width="30%">Fecha de Cierre:</th>
                <td>{{ $pettyCash->date }}</td>
            </tr>
            <tr>
                <th>Estado:</th>
                <td>Cerrada</td>
            </tr>
            <tr>
                <th>Generado por:</th>
                <td>{{ $user->name }}</td>
            </tr>
        </table>
    </div>

    <div class="details">
        <div class="section-title">VENTAS POR MÉTODO DE PAGO</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesByPaymentMethod as $method => $amount)
                <tr>
                    <td>{{ $method }}</td>
                    <td class="text-right">${{ number_format($amount, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <th>TOTAL VENTAS</th>
                    <th class="text-right">${{ number_format($totalSales, 2) }}</th>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="details">
        <div class="section-title">GASTOS</div>
        <table class="table">
            <tr>
                <th width="80%">Total Gastos</th>
                <td class="text-right">${{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="totals">
        <table class="table">
            <tr>
                <th width="80%">SALDO FINAL</th>
                <th class="text-right">${{ number_format($totalSales - $totalExpenses, 2) }}</th>
            </tr>
        </table>
    </div>

   
</body>
</html>