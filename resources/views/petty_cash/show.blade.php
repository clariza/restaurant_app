@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-color: #203363;
        --secondary-color: #6380a6;
        --tertiary-color: #a4b6ce;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
    }

    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .detail-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #47517c 100%);
        color: white;
        padding: 20px 25px;
        border-bottom: 3px solid var(--secondary-color);
    }

    .detail-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }

    .detail-header .subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-top: 5px;
    }

    .detail-body {
        padding: 25px;
    }

    /* Tarjetas de resumen */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 20px;
        color: white;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .summary-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    .summary-box.sales {
        background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
    }

    .summary-box.expenses {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .summary-box.balance {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }

    .summary-box.qr {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .summary-box.card {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .summary-box .icon {
        font-size: 32px;
        margin-bottom: 10px;
        opacity: 0.9;
    }

    .summary-box .label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .summary-box .amount {
        font-size: 26px;
        font-weight: 700;
    }

    /* Sección de información detallada */
    .info-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .info-section h3 {
        color: var(--primary-color);
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--tertiary-color);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
    }

    .info-label i {
        margin-right: 8px;
        color: var(--primary-color);
    }

    .info-value {
        font-weight: 500;
        color: #212529;
    }

    /* Tabla de gastos */
    .expenses-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .expenses-table table {
        width: 100%;
        margin: 0;
    }

    .expenses-table th {
        background-color: var(--primary-color);
        color: white;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 13px;
        text-align: left;
    }

    .expenses-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e9ecef;
        font-size: 14px;
    }

    .expenses-table tr:last-child td {
        border-bottom: none;
    }

    .expenses-table tr:hover {
        background-color: #f8f9fa;
    }

    .no-expenses {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }

    .no-expenses i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    /* Comparación de ventas */
    .comparison-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .comparison-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-radius: 6px;
        border-left: 4px solid var(--primary-color);
    }

    .comparison-label {
        font-weight: 600;
        color: var(--primary-color);
    }

    .comparison-values {
        display: flex;
        gap: 30px;
        align-items: center;
    }

    .value-item {
        text-align: center;
    }

    .value-label {
        font-size: 11px;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .value-amount {
        font-size: 16px;
        font-weight: 700;
        color: #212529;
    }

    .difference {
        padding: 4px 12px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 13px;
    }

    .difference.positive {
        background-color: #d4edda;
        color: #155724;
    }

    .difference.negative {
        background-color: #f8d7da;
        color: #721c24;
    }

    .difference.neutral {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    /* Botones de acción */
    .action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: #47517c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(32, 51, 99, 0.3);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    .btn-success {
        background-color: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.open {
        background-color: #d4edda;
        color: #155724;
    }

    .status-badge.closed {
        background-color: #f8d7da;
        color: #721c24;
    }

    @media print {
        .action-buttons, .btn {
            display: none !important;
        }
    }
</style>

<div class="container py-4">
    <div class="detail-card">
        <!-- Header -->
        <div class="detail-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2>
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Detalles del Cierre de Caja Chica
                    </h2>
                    <div class="subtitle">
                        <i class="fas fa-calendar mr-2"></i>{{ \Carbon\Carbon::parse($pettyCash->date)->format('d/m/Y') }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-user mr-2"></i>{{ $pettyCash->user->name ?? 'Usuario no disponible' }}
                    </div>
                </div>
                <span class="status-badge {{ $pettyCash->status == 'open' ? 'open' : 'closed' }}">
                    <i class="fas fa-circle mr-1"></i>{{ ucfirst($pettyCash->status) }}
                </span>
            </div>
        </div>

        <!-- Body -->
        <div class="detail-body">
            <!-- Resumen de Totales -->
            <div class="summary-grid">
                <div class="summary-box sales">
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="label">Efectivo</div>
                    <div class="amount">Bs. {{ number_format($pettyCash->total_sales_cash, 2) }}</div>
                </div>

                <div class="summary-box qr">
                    <div class="icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="label">QR</div>
                    <div class="amount">Bs. {{ number_format($pettyCash->total_sales_qr, 2) }}</div>
                </div>

                <div class="summary-box card">
                    <div class="icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="label">Tarjeta</div>
                    <div class="amount">Bs. {{ number_format($pettyCash->total_sales_card, 2) }}</div>
                </div>

                <div class="summary-box expenses">
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="label">Gastos</div>
                    <div class="amount">Bs. {{ number_format($pettyCash->total_expenses, 2) }}</div>
                </div>

                <div class="summary-box balance">
                    <div class="icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="label">Balance Final</div>
                    <div class="amount">Bs. {{ number_format($pettyCash->total_general, 2) }}</div>
                </div>
            </div>

            <!-- Comparación Sistema vs Caja -->
            @php
                $salesFromSystem = $pettyCash->sales()
                    ->where('payment_method', 'Efectivo')
                    ->sum('total');
                $diffCash = $pettyCash->total_sales_cash - $salesFromSystem;
            @endphp

            <div class="comparison-section">
                <h3>
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Comparación: Sistema vs Caja Chica
                </h3>
                
                <div class="comparison-row">
                    <span class="comparison-label">
                        <i class="fas fa-money-bill-wave mr-2"></i>Ventas en Efectivo
                    </span>
                    <div class="comparison-values">
                        <div class="value-item">
                            <div class="value-label">Sistema</div>
                            <div class="value-amount">Bs. {{ number_format($salesFromSystem, 2) }}</div>
                        </div>
                        <div class="value-item">
                            <div class="value-label">Caja</div>
                            <div class="value-amount">Bs. {{ number_format($pettyCash->total_sales_cash, 2) }}</div>
                        </div>
                        <span class="difference {{ abs($diffCash) < 0.01 ? 'neutral' : ($diffCash > 0 ? 'positive' : 'negative') }}">
                            {{ $diffCash > 0 ? '+' : '' }}Bs. {{ number_format($diffCash, 2) }}
                        </span>
                    </div>
                </div>

                @php
                    $totalSalesSystem = $pettyCash->sales()->sum('total');
                    $totalSalesCaja = $pettyCash->total_sales_cash + $pettyCash->total_sales_qr + $pettyCash->total_sales_card;
                @endphp

                <div class="comparison-row">
                    <span class="comparison-label">
                        <i class="fas fa-chart-line mr-2"></i>Total de Ventas
                    </span>
                    <div class="comparison-values">
                        <div class="value-item">
                            <div class="value-label">Sistema</div>
                            <div class="value-amount">Bs. {{ number_format($totalSalesSystem, 2) }}</div>
                        </div>
                        <div class="value-item">
                            <div class="value-label">Caja</div>
                            <div class="value-amount">Bs. {{ number_format($totalSalesCaja, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="info-section">
                <h3>
                    <i class="fas fa-info-circle mr-2"></i>
                    Información del Cierre
                </h3>
                
                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-calendar-alt"></i>Fecha de Apertura
                    </span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($pettyCash->date)->format('d/m/Y H:i') }}</span>
                </div>

                @if($pettyCash->closed_at)
                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-calendar-check"></i>Fecha de Cierre
                    </span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($pettyCash->closed_at)->format('d/m/Y H:i') }}</span>
                </div>

                @endif

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-hashtag"></i>ID de Caja
                    </span>
                    <span class="info-value">#{{ str_pad($pettyCash->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-shopping-cart"></i>Total de Ventas
                    </span>
                    <span class="info-value">{{ $pettyCash->sales()->count() }} ventas registradas</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-receipt"></i>Total de Gastos
                    </span>
                    <span class="info-value">{{ $pettyCash->expenses()->count() }} gastos registrados</span>
                </div>

                @if($pettyCash->notes)
                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-sticky-note"></i>Notas
                    </span>
                    <span class="info-value">{{ $pettyCash->notes }}</span>
                </div>
                @endif
            </div>

            <!-- Tabla de Gastos -->
            @if($pettyCash->expenses()->count() > 0)
            <div class="expenses-table">
                <h3 class="px-3 pt-3 pb-2" style="color: var(--primary-color); font-weight: 600;">
                    <i class="fas fa-list-ul mr-2"></i>Detalle de Gastos
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Nombre</th>
                            <th width="35%">Descripción</th>
                            <th width="15%">Fecha</th>
                            <th width="20%" class="text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pettyCash->expenses as $index => $expense)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $expense->expense_name }}</strong></td>
                            <td>{{ $expense->description ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                            <td class="text-right" style="color: var(--danger-color); font-weight: 600;">
                                Bs. {{ number_format($expense->amount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                        <tr style="background-color: #f8f9fa; font-weight: 700;">
                            <td colspan="4" class="text-right">TOTAL:</td>
                            <td class="text-right" style="color: var(--danger-color);">
                                Bs. {{ number_format($pettyCash->expenses()->sum('amount'), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <div class="expenses-table">
                <div class="no-expenses">
                    <i class="fas fa-inbox"></i>
                    <p class="mb-0">No se registraron gastos en este cierre de caja</p>
                </div>
            </div>
            @endif

            <!-- Botones de Acción -->
            <div class="action-buttons">
                <a href="{{ route('petty-cash.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Listado
                </a>
                
                <a href="{{ route('petty-cash.print', $pettyCash) }}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print"></i>
                    Imprimir Reporte
                </a>

                @if($pettyCash->status == 'open')
                <button type="button" class="btn btn-success" onclick="showCloseModal()">
                    <i class="fas fa-lock"></i>
                    Cerrar Caja
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function showCloseModal() {
        Swal.fire({
            title: '¿Cerrar esta caja?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#203363',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cerrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir al modal de cierre o ejecutar acción
                window.location.href = "{{ route('petty-cash.index') }}";
            }
        });
    }
</script>
@endsection