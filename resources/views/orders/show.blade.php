@extends('layouts.app')
@section('content')
<style>
    :root {
        --primary-color: #203363;
        --secondary-color: #6380a6;
        --tertiary-color: #a4b6ce;
    }

    .order-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #47517c 100%);
        color: white;
        padding: 25px;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .order-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .order-info-simple {
        padding: 40px;
        background: #f8f9fa;
    }

    .info-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 35px 45px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .info-row-simple {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
        align-items: center;
    }

    .info-row-simple:last-child {
        border-bottom: none;
    }

    .info-label-simple {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 15px;
        text-align: right;
        padding-right: 15px;
    }

    .info-label-simple i {
        margin-right: 8px;
    }

    .info-value-simple {
        color: #212529;
        font-size: 15px;
        font-weight: 500;
        padding-left: 15px;
        border-left: 3px solid var(--tertiary-color);
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead {
        background-color: var(--primary-color);
        color: white;
    }

    .items-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
    }

    .items-table td {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .items-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .items-table tfoot {
        background-color: #f8f9fa;
        font-weight: 700;
    }

    .items-table tfoot td {
        padding: 20px 15px;
        font-size: 18px;
        color: var(--primary-color);
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-bottom: 25px;
    }

    .btn {
        padding: 12px 20px;
        border-radius: 8px;
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
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-secondary:hover {
        background-color: #47517c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 128, 166, 0.3);
    }

    .notes-section {
        margin-top: 20px;
        padding: 20px;
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
    }

    .notes-label {
        font-size: 12px;
        color: #856404;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .notes-content {
        color: #212529;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Navegación inferior */
    .navigation-footer {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 20px;
        border-top: 3px solid var(--primary-color);
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        z-index: 100;
        margin-top: 30px;
        border-radius: 12px 12px 0 0;
    }

    .nav-btn {
        padding: 14px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        min-width: 160px;
        justify-content: center;
    }

    .nav-btn-enabled {
        background-color: var(--primary-color);
        color: white;
    }

    .nav-btn-enabled:hover {
        background-color: #47517c;
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(32, 51, 99, 0.3);
    }

    .nav-btn-disabled {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .nav-info {
        text-align: center;
        flex-grow: 1;
    }

    .nav-info .order-number {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .nav-info .order-date {
        font-size: 13px;
        color: #6c757d;
    }

    @media print {
        .action-buttons,
        .navigation-footer,
        .no-print {
            display: none !important;
        }
        
        .order-card {
            box-shadow: none;
            page-break-inside: avoid;
        }
    }

    @media (max-width: 768px) {
        .navigation-footer {
            flex-direction: column;
            gap: 15px;
        }

        .nav-btn {
            width: 100%;
        }

        .info-row-simple {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .info-label-simple {
            min-width: auto;
        }
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Botones de acción superiores -->
    <div class="action-buttons">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i>
            Volver al listado
        </a>
        
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i>
            Imprimir
        </button>
    </div>

    <!-- Tarjeta principal de la orden -->
    <div class="order-card">
        <!-- Header de la orden -->
        <div class="order-header">
            <h1 class="text-3xl font-bold mb-2">
                <i class="fas fa-receipt mr-3"></i>
                Orden #{{ $order->transaction_number }}
            </h1>
            <p class="text-sm opacity-90">
                <i class="fas fa-calendar-alt mr-2"></i>
                {{ $order->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <!-- Información de la orden -->
        <div class="order-info-simple">
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-calendar mr-2"></i>Fecha:
                </span>
                <span class="info-value-simple">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-shopping-bag mr-2"></i>Tipo de Orden:
                </span>
                <span class="info-value-simple">{{ ucfirst($order->order_type) }}</span>
            </div>
            
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-user-tie mr-2"></i>Atendido por:
                </span>
                <span class="info-value-simple">{{ $order->user->name }}</span>
            </div>

            @if($order->customer_name)
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-user mr-2"></i>Cliente:
                </span>
                <span class="info-value-simple">{{ $order->customer_name }}</span>
            </div>
            @endif

            @if($order->phone)
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-phone mr-2"></i>Teléfono:
                </span>
                <span class="info-value-simple">{{ $order->phone }}</span>
            </div>
            @endif

            @if($order->table_number)
            <div class="info-row-simple">
                <span class="info-label-simple">
                    <i class="fas fa-chair mr-2"></i>Mesa:
                </span>
                <span class="info-value-simple">#{{ $order->table_number }}</span>
            </div>
            @endif
        </div>
        
        <!-- Items de la orden -->
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4 text-[#203363]">
                <i class="fas fa-list-ul mr-2"></i>
                Ítems del Pedido
            </h2>
            
            <div class="overflow-x-auto">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="50%">
                                <i class="fas fa-utensils mr-2"></i>
                                Producto
                            </th>
                            <th width="15%" class="text-right">
                                <i class="fas fa-sort-numeric-up mr-2"></i>
                                Cantidad
                            </th>
                            <th width="17%" class="text-right">
                                <i class="fas fa-dollar-sign mr-2"></i>
                                Precio
                            </th>
                            <th width="18%" class="text-right">
                                <i class="fas fa-calculator mr-2"></i>
                                Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="font-medium">{{ $item->menuItem->name }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">Bs. {{ number_format($item->price, 2) }}</td>
                            <td class="text-right font-semibold">Bs. {{ number_format($item->quantity * $item->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">
                                <i class="fas fa-coins mr-2"></i>
                                TOTAL:
                            </td>
                            <td class="text-right">
                                Bs. {{ number_format($order->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($order->order_notes)
            <div class="notes-section">
                <div class="notes-label">
                    <i class="fas fa-sticky-note mr-2"></i>
                    Notas del pedido
                </div>
                <div class="notes-content">{{ $order->order_notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Navegación inferior entre órdenes -->
    <div class="navigation-footer no-print">
        <!-- Botón Anterior -->
        @if(isset($previousOrder) && $previousOrder)
            <a href="{{ route('orders.show', $previousOrder->id) }}" class="nav-btn nav-btn-enabled">
                <i class="fas fa-chevron-left"></i>
                <span>Anterior</span>
            </a>
        @else
            <span class="nav-btn nav-btn-disabled">
                <i class="fas fa-chevron-left"></i>
                <span>Anterior</span>
            </span>
        @endif

        <!-- Información central -->
        <div class="nav-info">
            <div class="order-number">
                <i class="fas fa-receipt mr-2"></i>
                Orden #{{ $order->transaction_number }}
            </div>
            <div class="order-date">
                {{ $order->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Botón Siguiente -->
        @if(isset($nextOrder) && $nextOrder)
            <a href="{{ route('orders.show', $nextOrder->id) }}" class="nav-btn nav-btn-enabled">
                <span>Siguiente</span>
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="nav-btn nav-btn-disabled">
                <span>Siguiente</span>
                <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </div>
</div>

<!-- Script para navegación con teclado -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Navegación con teclas de flecha
        document.addEventListener('keydown', function(e) {
            // Flecha izquierda - Orden anterior
            if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                @if(isset($previousOrder) && $previousOrder)
                    window.location.href = "{{ route('orders.show', $previousOrder->id) }}";
                @endif
            }
            
            // Flecha derecha - Orden siguiente
            if (e.key === 'ArrowRight' || e.keyCode === 39) {
                @if(isset($nextOrder) && $nextOrder)
                    window.location.href = "{{ route('orders.show', $nextOrder->id) }}";
                @endif
            }
        });
    });
</script>
@endsection